<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Entity\User;
use App\Event\EmailRegistrationUserEvent;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Route(path="/api")
 */
class UserController extends Controller
{
    /**
     * @Route(path="/users/registration", name="user_registration")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws HttpException
     */
    public function saveAction(
        Request $request, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator
    ): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $errors = $validator->validate($user);

        if(count($errors) > 0) {
            throw new HttpException(400, "Invalid data");
        }

        $now = new \DateTime('now', new \DateTimeZone('Europe/Ljubljana'));
        $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPassword());

        $user->setFirstname($user->getFirstname());
        $user->setLastname($user->getLastname());
        $user->setEmail($user->getEmail());
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setAdded($now);
        $user->setUpdated($now);
        $user->setLastLogin($now);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        // $event = new EmailRegistrationUserEvent($user);
        // $dispatcher = $this->get('event_dispatcher');
        // $dispatcher->dispatch(EmailRegistrationUserEvent::NAME, $event);


        try {
            $em->flush();
        } catch(\Doctrine\ORM\ORMException $e) {
            throw new HttpException(400, "Error saving data to database.");
        }
        
        $user = $serializer->serialize(['success' => true, 'code' => 1, 'data' => $user], 'json');

        return new JsonResponse($user, 201, [], true);
    }

     /**
     * @Route("/users/authentication", name="login_authentication")
     * @Method("POST")
     */
    public function loginAction(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data = $serializer->deserialize($request->getContent(), User::class, 'json');
        $errors = $validator->validate($data, null, ['login']);
        
        if(count($errors) > 0) {
            throw new HttpException(400, "Email and Password are required fields");
        }

        $user = $this->getDoctrine()->getRepository(User::class)
            ->findOneBy(['email'=> $data->getEmail()]);

        if (!$user) {
            throw $this->createNotFoundException();
        }
        
        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $data->getPassword());
            
        if (!$isValid) {
            throw new BadCredentialsException();
        }

        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode([
                'email' => $user->getEmail(),
                'exp' => time() + 3600 // 1 hour expiration
        ]);

        return new JsonResponse(['success' => true, 'code' => 1, 'token' => $token]);
    }
}

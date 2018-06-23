<?php

namespace App\Controller;

use App\Event\EmailRegistrationUserEvent;
use App\Entity\User;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Route("/api")
 */
class UserController extends Controller
{
    private $serializer;

    private $validator;

    public function __construct(
        SerializerInterface $serializer, 
        ValidatorInterface $validator
    )
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/users", name="add_user")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws HttpException
     */
    public function addAction(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $errors = $this->validator->validate($user);

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
        $user->setRoles(['ROLE_USER']);
        $user->setAdded($now);
        $user->setUpdated($now);
        $user->setLastLogin($now);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        // $event = new EmailRegistrationUserEvent($user);
        // $dispatcher = $this->get('event_dispatcher');
        // $dispatcher->dispatch(EmailRegistrationUserEvent::NAME, $event);

        if(!$user->getId()) {
            throw new HttpException(400, "Error saving data to database.");
        }
        $user = $this->serializer->serialize(['success' => true, 'code' => 1, 'data' => $user], 'json');

        return new JsonResponse($user, 201, [], true);
    }

    /**
     * @Route(path="/users", name="get_user_collection")
     * @Method("GET")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        dump($repository->getAll()); die;
        return new JsonResponse(['Franci Petek', 'Romelu Lukaku', 'Sergio Ramos']);
    }

    /**
     * @Route(path="/users/{id}", name="get_user")
     * @Method("GET")
     */
    public function getAction(string $id)
    {
        return new JsonResponse([$id => 'Franci petek']);
    }   

    /**
     * @Route(path="/users/{id}", name="edit_user")
     * @Method("PUT")
     */
    public function editAction(string $id)
    {
        return new JsonResponse(['id' => $id]);
    }

    /**
     * @Route(path="/users/{id}", name="delete_user")
     * @Method("DELETE")
     */
    public function deleteAction(string $id)
    {
        return new JsonResponse(['id' => $id]);
    }

     /**
     * @Route("/login", name="user_authentication")
     * @Method("POST")
     */
    public function loginAction(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $errors = $this->validator->validate($data, null, ['login']);
        
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

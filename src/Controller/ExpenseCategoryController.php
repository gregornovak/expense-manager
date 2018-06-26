<?php

namespace App\Controller;

use App\Entity\ExpensesCategories;
use App\Security\JwtAuthenticator;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api")
 */
class ExpenseCategoryController extends Controller
{
    private $serializer;

    private $validator;

    private $authenticator;

    private $userProvider;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        JwtAuthenticator $authenticator,
        UserProviderInterface $userProvider
    )
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->authenticator = $authenticator;
        $this->userProvider = $userProvider;
    }

	/**
	 * @Route("/expense-categories", name="get_expense_categories_collection")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
	{
        $user = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        $repository = $this->getDoctrine()->getRepository(ExpensesCategories::class);

        if(!$page || !is_numeric($page) || !$limit || !is_numeric($limit)) {
            $results = $repository->getAll($user->getId());
        } else {
            $results = $repository->getAll($user->getId(), (int)$page, (int)$limit);
        }

        if (!$results) {
            return new JsonResponse(['data' => []]);
        }
        
        $categories = $this->serializer->serialize([
            'data' => $results],
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
        );

        return new JsonResponse($categories, 200, [], true);
	}

	/**
	 * @Route("/expense-categories/{id}", name="get_expense_category")
     * @Method("GET")
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws HttpException
     */
    public function getCategory(Request $request, string $id): JsonResponse
    {
        $user = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(ExpensesCategories::class)->find($id);

        if(!$result) {
            throw new HttpException(404,'Resource does not exist.');
        }

        if($user->getId() != $result->getUser()->getId()) {
            throw new HttpException(400,'You do not have permission to view this resource.');
        }

        $category = $this->serializer->serialize([
            'data' => $result],
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
        );

		return new JsonResponse($category, 200, [], true);
	}

	/**
	 * @Route("/expense-categories", name="post_expense_category")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws HttpException
     */
    public function save(Request $request): JsonResponse
    {
        $data = $this->serializer
            ->deserialize(
                $request->getContent(), 
                ExpensesCategories::class, 
                'json'
            );

        $errors = $this->validator->validate($data);

        if(count($errors) > 0) {
            throw new HttpException(400, 'You must provide category property');
        }

        $user = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $now = new \DateTime('now', new \DateTimeZone('Europe/Ljubljana'));

        $expenseCategory = new ExpensesCategories();
        $expenseCategory->setCategory($data->getCategory());
        $expenseCategory->setUser($user);
        $expenseCategory->setAdded($now);
        $expenseCategory->setUpdated($now);

        $em = $this->getDoctrine()->getManager();
        $em->persist($expenseCategory);
        $em->flush();

        $response = $this->serializer->serialize([
            'data' => $expenseCategory],
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
            );

        return new JsonResponse($response, 201, [], true);
    }

	/**
	 * @Route("/expense-categories/{id}", name="update_expense_category")
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws HttpException
	 */
    public function update(Request $request, string $id): JsonResponse
    {
        $data = $this->serializer
            ->deserialize(
                $request->getContent(),
                ExpensesCategories::class,
                'json'
            );

        $errors = $this->validator->validate($data);

        if(count($errors) > 0) {
            throw new HttpException(400, 'You must provide category property');
        }

        $user = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(ExpensesCategories::class)
            ->find($id);

        if(!$category) {
            throw new HttpException(404, 'Resource not found.');
        }

        if($user->getId() != $category->getUser()->getId()) {
            throw new HttpException(400, 'You do not have permission to edit this resource.');
        }

        $category->setCategory($data->getCategory());
        $category->setUpdated(
            new \DateTime('now',
            new \DateTimeZone('Europe/Ljubljana'))
        );

        $em->flush();

        $category = $this->serializer->serialize([
            'data' => $category],
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
        );

        return new JsonResponse($category, 200, [], true);
    }

	/**
	 * @Route("/expense-categories/{id}", name="delete_expense_category")
     * @Method("DELETE")
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws HttpException
	 */
    public function delete(Request $request, string $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(ExpensesCategories::class)
            ->find($id);

        if(!$category) {
            throw new HttpException(404, 'Resource not found.');
        }

        $user = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        if($user->getId() != $category->getUser()->getId()) {
            throw new HttpException(400, 'You do not have permission to edit this resource.');
        }

        $em->remove($category);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}


<?php

namespace App\Controller;


use App\Entity\ExpensesCategories;
use App\Entity\User;
use App\Security\JwtAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
	 * @Route("/expense-categories", name="get_expense_categories")
     * @Method("GET")
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
	{
        $authenticatedUser = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($authenticatedUser->getId());

        if (!$user) {
            throw new HttpException(404, 'This user does not exist!');
        }

        $categories = $em->getRepository(ExpensesCategories::class)
            ->findCategoriesByUser($user->getId());

        if (!$categories) {
            throw new HttpException(404, 'No categories available for this user!');
        }

        $response = $this->serializer->serialize(
            $categories,
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
                ->enableMaxDepthChecks()
        );

		return new JsonResponse($response, 200, [], true);
	}

	/**
	 * @Route("/expense-categories/{id}", name="get_expense_category")
     * @Method("GET")
     * @param string $id
     * @return JsonResponse
     */
    public function get(string $id): JsonResponse
    {
        // if (!$id) {
        //     throw new HttpException(400, "Invalid id");
        // }

		// $em = $this->getDoctrine()->getManager();
		// $book = $em->getRepository(Book::class)->find($id);

        // if (!$book) {
		// 	throw new HttpException(400, "Invalid data");
		// }

		return new JsonResponse([$id => 'expense-category']);
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
        $authenticatedUser = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($authenticatedUser->getId());

        if (!$user) {
            throw new HttpException(404, 'This user does not exist!');
        }

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

        $now = new \DateTime('now', new \DateTimeZone('Europe/Ljubljana'));

        $expenseCategory = new ExpensesCategories();
        $expenseCategory->setCategory($data->getCategory());
        $expenseCategory->setAdded($now);
        $expenseCategory->setUpdated($now);
        $expenseCategory->setUser($user);

        $em->persist($expenseCategory);

        try {
            $em->flush();
        } catch(\Doctrine\ORM\ORMException $e) {
            throw new HttpException(400, 'Error saving data to database.');
        }

        $response = $this->serializer->serialize(['data' => $expenseCategory], 'json');

        return new JsonResponse($response, 201, [], true);
    }

	/**
	 * @Route("/books/edit/{id}", name="put_book")
     * @METHOD("PUT")
     * @param Request $request
     * @param string $id
     * @return JsonResponse
	 */
    public function update(Request $request, string $id): JsonResponse
    {
        // $em = $this->getDoctrine()->getManager();
        // $book = $em->getRepository(Book::class)->find($id);
        // $form = $this->createForm(BookType::class, $book, ['method' => 'PUT']);
        // $form->handleRequest($request);

        // if ($form->isValid()) {
        //     $em->persist($book);
        //     $em->flush();

        //     return $book;
        // }

        // throw new HttpException(400, "Invalid data");
    }

	/**
	 * @Route("/books/remove/{id}", name="delete_book")
     * @Method("DELETE")
     * @param string $id
     * @return JsonResponse
	 */
    public function delete(string $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository(Book::class)->find($id);
        $em->remove($book);
        $em->flush();

        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }

        return $book;
    }
}

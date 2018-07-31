<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerInterface;
use App\Entity\ExpensesCategories;

/**
 * @Route("/api")
 */
class ExpenseCategoryController extends Controller
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
	 * @Route("/expense-categories", name="get_expense_categories")
     * @Method("GET")
     * @return JsonResponse
     */
    public function index() : JsonResponse
	{
		$em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(ExpensesCategories::class)->findAll();

        if (!$categories) {
            throw new HttpException(404, '');
            return new JsonResponse(['success' => true, 'data' => []], 204);
        }

        // $books = $this->serializer->serialize(['success' => true, 'data' => $books], 'json');

		return new JsonResponse(['ena', 'dva', 'tri']);
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
        $data = $this->serializer
            ->deserialize(
                $request->getContent(),
                ExpensesCategories::class,
                'json'
            );

        $errors = $this->validator->validate($data);

        if(count($errors) > 0) {
            throw new HttpException(400, "You must provide category property");
        }

        $now = new \DateTime('now', new \DateTimeZone('Europe/Ljubljana'));

        $expenseCategory = new ExpensesCategories();
        $expenseCategory->setCategory($data->getCategory());
        $expenseCategory->setAdded($now);
        $expenseCategory->setUpdated($now);

        $em = $this->getDoctrine()->getManager();
        $em->persist($expenseCategory);

        try {
            $em->flush();
        } catch(\Doctrine\ORM\ORMException $e) {
            throw new HttpException(400, "Error saving data to database.");
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

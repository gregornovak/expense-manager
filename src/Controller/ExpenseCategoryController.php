<?php

namespace App\Controller;

use App\Entity\ExpensesCategories;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
	 * @Route("/expense-categories", name="get_expense_categories_collection")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
	{
        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        $repository = $this->getDoctrine()->getRepository(ExpensesCategories::class);

        if(!$page || !is_numeric($page) || !$limit || !is_numeric($limit)) {
            $results = $repository->getAll();
        } else {
            $results = $repository->getAll((int)$page, (int)$limit);
        }

        if (!$results) {
            return new JsonResponse(['success' => true, 'data' => []]);
        }
        
        $categories = $this->serializer->serialize(['success' => true, 'data' => $results], 'json');

        return new JsonResponse($categories, 200, [], true);
	}

	/**
	 * @Route("/expense-categories/{id}", name="get_expense_category")
     * @Method("GET")
     * @param string $id
     * @return JsonResponse
     * @throws NotFoundResourceException
     */
    public function get(string $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(ExpensesCategories::class)->find($id);

        if(!$result) {
            throw new HttpException(404,'Resource does not exist.');
        }

        $category = $this->serializer->serialize(['success' => true, 'data' => $result], 'json');

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

        $now = new \DateTime('now', new \DateTimeZone('Europe/Ljubljana'));

        $expenseCategory = new ExpensesCategories();
        $expenseCategory->setCategory($data->getCategory());
        $expenseCategory->setAdded($now);
        $expenseCategory->setUpdated($now);

        $em = $this->getDoctrine()->getManager();
        $em->persist($expenseCategory);
        $em->flush();

        $response = $this->serializer->serialize(['data' => $expenseCategory], 'json');

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

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(ExpensesCategories::class)
            ->find($id);

        if(!$category) {
            throw new HttpException(404, 'Resource not found.');
        }

        $category->setCategory($data->getCategory());
        $category->setUpdated(
            new \DateTime('now',
            new \DateTimeZone('Europe/Ljubljana'))
        );

        $em->flush();

        $category = $this->serializer->serialize(['success' => true, 'data' => $category], 'json');

        return new JsonResponse($category, 200, [], true);
    }

	/**
	 * @Route("/expense-categories/{id}", name="delete_expense_category")
     * @Method("DELETE")
     * @param string $id
     * @return JsonResponse
     * @throws HttpException
	 */
    public function delete(string $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(ExpensesCategories::class)
            ->find($id);

        if(!$category) {
            throw new HttpException(404, 'Resource not found.');
        }

        $em->remove($category);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}


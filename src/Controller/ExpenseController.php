<?php

namespace App\Controller;

use App\Entity\Expenses;
use App\Entity\ExpensesCategories;
use App\Entity\User;
use App\Security\JwtAuthenticator;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api")
 */
class ExpenseController extends Controller
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
     * @Route("/expenses", name="get_expenses_collection")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Check for user and load only his expenses
        $user = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        $repository = $this->getDoctrine()->getRepository(Expenses::class);

        if(!$page || !is_numeric($page) || !$limit || !is_numeric($limit)) {
            $results = $repository->getAll($user->getId());
        } else {
            $results = $repository->getAll($user->getId(), (int)$page, (int)$limit);
        }

        if (!$results) {
            return new JsonResponse(['success' => true, 'data' => []]);
        }

        $expenses = $this->serializer->serialize(
            ['data' => $results],
            'json',
            SerializationContext::create()
                ->setGroups(['Default', 'additional'])
                ->enableMaxDepthChecks()
        );

        return new JsonResponse($expenses, 200, [], true);
    }

    /**
     * @Route("/expenses/{id}", name="get_expense")
     * @Method("GET")
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws HttpException
     */
    public function getExpense(Request $request, string $id)
    {
        $authenticatedUser = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Expenses::class)->find($id);

        if(!$result) {
            throw new HttpException(404,'Resource does not exist.');
        }

        if($authenticatedUser->getId() != $result->getUser()->getId()) {
            throw new HttpException(400,'You do not have permission to view this resource.');
        }

        $expense = $this->serializer->serialize(['success' => true, 'data' => $result],
            'json',
            SerializationContext::create()
                ->setGroups(['Default', 'additional'])
                ->enableMaxDepthChecks()
        );

        return new JsonResponse($expense, 200, [], true);
    }

    /**
     * @Route("/expenses", name="post_expenses")
     * @Method("POST")
     * @param Request $request
     * @param UserProviderInterface $userProvider
     * @return JsonResponse
     * @throws HttpException
     */
    public function save(Request $request, UserProviderInterface $userProvider): JsonResponse
    {
        $authenticatedUser = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $userProvider
        );

        $related = json_decode($request->getContent());

        if((!isset($related->user) && empty($related->user)) ||
            (!isset($related->expenses_category) || empty($related->expenses_category))
        )
        {
            throw new HttpException(400, 'You must provide user and expenses_category id');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($related->user);

        if(!$user) {
            throw new HttpException(404, 'This user does not exist!');
        }

        if($user->getId() != $authenticatedUser->getId()) {
            throw new HttpException(400, 'You do not have permission to save expense as somebody else!');
        }

        $category = $em->getRepository(ExpensesCategories::class)
            ->find($related->expenses_category);

        if(!$category) {
            throw new HttpException(404, 'This expenses category does not exist!');
        }

        $data = $this->serializer
            ->deserialize(
                $request->getContent(),
                Expenses::class,
                'json',
                DeserializationContext::create()->setGroups(['Default'])
            );

        $errors = $this->validator->validate($data);

        if(count($errors) > 0) {
            throw new HttpException(400, 'You must provide all required properties.');
        }

        $now = new \DateTime('now', new \DateTimeZone('Europe/Ljubljana'));

        $expense = new Expenses();
        $expense->setUser($user);
        $expense->setExpensesCategory($category);
        $expense->setName($data->getName());
        $expense->setAdded($now);
        $expense->setUpdated($now);
        $expense->setAmount($data->getAmount());
        $expense->setCurrency($data->getCurrency());

        if($data->getCash() && gettype($data->getCash()) === "boolean") {
            $expense->setCash($data->getCash());
        }

        if($data->getPayee()) {
            $expense->setPayee($data->getPayee());
        }

        if($data->getStatus()) {
            $expense->setStatus($data->getStatus());
        }

        if($data->getDescription()) {
            $expense->setDescription($data->getDescription());
        }

        $em->persist($expense);
        $em->flush();


        $response = $this->serializer->serialize(
            ['data' => $expense],
            'json',
            SerializationContext::create()
                ->setGroups(['Default', 'additional'])
                ->enableMaxDepthChecks()
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
//    public function update(Request $request, string $id): JsonResponse
//    {
//        $data = $this->serializer
//            ->deserialize(
//                $request->getContent(),
//                ExpensesCategories::class,
//                'json'
//            );
//
//        $errors = $this->validator->validate($data);
//
//        if(count($errors) > 0) {
//            throw new HttpException(400, 'You must provide category property');
//        }
//
//        $em = $this->getDoctrine()->getManager();
//
//        $category = $em->getRepository(ExpensesCategories::class)
//            ->find($id);
//
//        if(!$category) {
//            throw new HttpException(404, 'Resource not found.');
//        }
//
//        $category->setCategory($data->getCategory());
//        $category->setUpdated(
//            new \DateTime('now',
//                new \DateTimeZone('Europe/Ljubljana'))
//        );
//
//        $em->flush();
//
//        $category = $this->serializer->serialize(['success' => true, 'data' => $category], 'json');
//
//        return new JsonResponse($category, 200, [], true);
//    }

    /**
     * @Route("/expense-categories/{id}", name="delete_expense_category")
     * @Method("DELETE")
     * @param string $id
     * @return JsonResponse
     * @throws HttpException
     */
//    public function delete(string $id): JsonResponse
//    {
//        $em = $this->getDoctrine()->getManager();
//        $category = $em->getRepository(ExpensesCategories::class)
//            ->find($id);
//
//        if(!$category) {
//            throw new HttpException(404, 'Resource not found.');
//        }
//
//        $em->remove($category);
//        $em->flush();
//
//        return new JsonResponse(['success' => true]);
//    }
}



<?php

namespace App\Controller;

use App\Entity\Expenses;
use App\Entity\ExpensesCategories;
use App\Entity\User;
use App\Security\JwtAuthenticator;
use App\Utils\PaginatorChecker;
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
    private $paginatorChecker;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        JwtAuthenticator $authenticator,
        UserProviderInterface $userProvider,
        PaginatorChecker $paginatorChecker
    )
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->authenticator = $authenticator;
        $this->userProvider = $userProvider;
        $this->paginatorChecker = $paginatorChecker;
    }

    /**
     * @Route("/expenses", name="get_expenses_collection")
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

        $repository = $this->getDoctrine()->getRepository(Expenses::class);

        if(is_numeric($page) &&
            is_numeric($limit) &&
            $this->paginatorChecker->isWithinRange($page, $limit)
        ) {
            $results = $repository->getAll($user->getId(), (int)$page, (int)$limit);
        } else {
            $results = $repository->getAll($user->getId());
        }

        if (!$results) {
            return new JsonResponse(['data' => []]);
        }

        $expenses = $this->serializer->serialize(
            $results,
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
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
    public function getExpense(Request $request, string $id): JsonResponse
    {
        $authenticatedUser = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $em = $this->getDoctrine()->getManager();
        $expense = $em->getRepository(Expenses::class)->find($id);

        if(!$expense) {
            throw new HttpException(404,'Resource does not exist.');
        }

        if($authenticatedUser->getId() != $expense->getUser()->getId()) {
            throw new HttpException(400,'You do not have permission to view this resource.');
        }

        $response = $this->serializer->serialize(['success' => true, 'data' => $expense],
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
                ->enableMaxDepthChecks()
        );

        return new JsonResponse($response, 200, [], true);
    }

    /**
     * @Route("/expenses", name="post_expenses")
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

        $related = json_decode($request->getContent());

        if(!isset($related->expenses_category) || empty($related->expenses_category)
        )
        {
            throw new HttpException(400, 'You must set expenses_category id');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($authenticatedUser->getId());

        if(!$user) {
            throw new HttpException(404, 'This user does not exist!');
        }

        $category = $em->getRepository(ExpensesCategories::class)
            ->findCategoryByUser($user->getId(), $related->expenses_category)[0];

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

        if($data->getCash()) {
            $expense->setCash($data->getCash());
        }

        $expense->setPayee($data->getPayee() ?? "");

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
                ->setGroups(['Default'])
                ->enableMaxDepthChecks()
        );

        return new JsonResponse($response, 201, [], true);
    }

    /**
     * @Route("/expenses/{id}", name="update_expense")
     * @Method("PUT")
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws HttpException
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $authenticatedUser = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $related = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($authenticatedUser->getId());

        if(!$user) {
            throw new HttpException(404, 'This user does not exist!');
        }

        if(isset($related->expenses_category) && !empty($related->expenses_category)) {
            $category = $em->getRepository(ExpensesCategories::class)
                ->findCategoryByUser($user->getId(), $related->expenses_category)[0];

            if(!$category) {
                throw new HttpException(404, 'This expenses category does not exist!');
            }
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

        $expense = $em->getRepository(Expenses::class)->find($id);

        if(!$expense) {
            throw new HttpException(404, 'No resource found.');
        }

        if($expense->getUser()->getId() != $authenticatedUser->getId()) {
            throw new HttpException(400,'You do not have permission to update this resource.');
        }

        if(isset($category)) {
            $expense->setExpensesCategory($category);
        }
        $expense->setName($data->getName());
        $expense->setUpdated(new \DateTime('now', new \DateTimeZone('Europe/Ljubljana')));
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

        $em->flush();

        $response = $this->serializer->serialize(
            ['data' => $expense],
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
                ->enableMaxDepthChecks()
        );

        return new JsonResponse($response, 200, [], true);
    }

    /**
     * @Route("/expenses/{id}", name="delete_expense")
     * @Method("DELETE")
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws HttpException
     */
    public function delete(Request $request, string $id): JsonResponse
    {
        $authenticatedUser = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($authenticatedUser->getId());

        if(!$user) {
            throw new HttpException(404, 'This user does not exist!');
        }

        $expense = $em->getRepository(Expenses::class)->find($id);

        if(!$expense) {
            throw new HttpException(404, 'Resource not found.');
        }

        if($expense->getUser()->getId() != $authenticatedUser->getId()) {
            throw new HttpException(400, 'You do not have permission to delete expense as somebody else!');
        }

        $em->remove($expense);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/monthly-expenses/{month}/{year}", name="get_expenses_by_month", defaults={"year"=null})
     * @Method("GET")
     * @param Request $request
     * @param string $month
     * @param string $year
     * @return JsonResponse
     */
    public function getByMonth(Request $request, string $month, ?string $year): JsonResponse
    {
        $user = $this->authenticator->getUser(
            $this->authenticator->getCredentials($request),
            $this->userProvider
        );

        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        $repository = $this->getDoctrine()->getRepository(Expenses::class);

        if(!$page || !is_numeric($page) || !$limit || !is_numeric($limit)) {
            $results = $repository->getExpensesByMonth($user->getId(), $month, $year);
        } else {
            $results = $repository->getExpensesByMonth($user->getId(), $month, $year, (int)$page, (int)$limit);
        }

        if (!$results) {
            return new JsonResponse(['data' => []]);
        }

        $expenses = $this->serializer->serialize(
            ['data' => $results],
            'json',
            SerializationContext::create()
                ->setGroups(['Default'])
                ->enableMaxDepthChecks()
        );

        return new JsonResponse($expenses, 200, [], true);
    }
}

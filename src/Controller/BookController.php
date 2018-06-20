<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
// use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializerInterface;
use App\Entity\Book;
use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api")
 */
class BookController extends Controller
{
	/**
	 * @Route("/books", name="get_books")
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getBooksAction(SerializerInterface $serializer) : JsonResponse
	{
		$em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(Book::class)->findAll();
        
        if (!$books) {
            return new JsonResponse(['success' => true, 'data' => []], 204);
        }
        
        $books = $serializer->serialize(['success' => true, 'data' => $books], 'json');

		return new JsonResponse($books, 200, [], true);
	}

	/**
	 * @Route("/books/{id}", name="get_book")
     * @param int $id
     * @return object|null
     */
    public function getBookAction(int $id): ?object
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }

		$em = $this->getDoctrine()->getManager();
		$book = $em->getRepository(Book::class)->find($id);

        if (!$book) {
			throw new HttpException(400, "Invalid data");
		}

		return $book;
	}

	/**
	 * @Route("/book/new", name="post_book")
     * @param Request $request
     * @return object|null
     */
    public function postBookAction(Request $request): ?object
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $book;
        }

        throw new HttpException(400, "Invalid data");
    }

	/**
	 * @Route("/books/edit/{id}", name="put_book")
     * @param Request $request
     * @param int $id
     * @return object|null
	 */
    public function putBookAction(Request $request, int $id): ?object
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository(Book::class)->find($id);
        $form = $this->createForm(BookType::class, $book, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($book);
            $em->flush();

            return $book;
        }

        throw new HttpException(400, "Invalid data");
    }

	/**
	 * @Route("/books/remove/{id}", name="delete_book")
     * @param int $id
     * @return object|null
	 */
    public function deleteBookAction(int $id): ?object
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


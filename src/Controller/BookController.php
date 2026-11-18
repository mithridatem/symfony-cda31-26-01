<?php

namespace App\Controller;

use App\Service\BookService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    public function __construct(
        private readonly BookService $bookService
    ) {}

    #[Route(path:'/books', name: 'app_books')]
    public function showallBooks(): Response
    {
        $books = $this->bookService->getBooks();

        return $this->render('book/show_all_books.html.twig', [
            'books' => $books
        ]);
    }

    #[Route(path: '/books/{id}', name:'app_book')]
    public function showBookById(int $id): Response
    {
        $book = $this->bookService->getBookById($id);

        return $this->render('book/show_book_id.html.twig', [
            'book' => $book
        ]);
    }
}

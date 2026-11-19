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

    #[Route(path: '/books', name: 'app_books')]
    public function showallBooks(): Response
    {
        $books = $this->bookService->getBooks();

        return $this->render('book/show_all_books.html.twig', [
            'books' => $books
        ]);
    }

    #[Route(path: '/books/{id}', name: 'app_book')]
    public function showBookById(int $id): Response
    {
        $book = $this->bookService->getBookById($id);

        return $this->render('book/show_book_id.html.twig', [
            'book' => $book
        ]);
    }

    #[Route(path: '/books/search/{title}', name: 'app_book_search')]
    public function showBooksByTitle(string $title): Response
    {
        $books = $this->bookService->getBooksByTitle($title);

        return $this->render('book/show_all_books.html.twig', [
            'books' => $books
        ]);
    }

    #[Route(path:'/books/recent/{date}', name: 'app_book_recent')]
    public function showBooksByAfterPublishAt(string $date): Response 
    {
        $books = $this->bookService->getBooksByAfterPublishAt($date);
        
        return $this->render('book/show_all_books.html.twig', [
            'books' => $books
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\DeleteBookType;
use App\Service\BookService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
            'books' => $books,
            
        ]);
    }

    #[Route(path: '/books/{id}', name: 'app_book')]
    public function showBookById(int $id): Response
    {
        $book = $this->bookService->getBookById($id);

        return $this->render('book/show_book_id.html.twig', [
            'book' => $book,
            'deleteForm' => $book && $this->isGranted('ROLE_ADMIN')
                ? $this->createDeleteForm($id)->createView()
                : null,
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

    #[Route(path: '/books/recent/{date}', name: 'app_book_recent')]
    public function showBooksByAfterPublishAt(string $date): Response
    {
        $books = $this->bookService->getBooksByAfterPublishAt($date);

        return $this->render('book/show_all_books.html.twig', [
            'books' => $books
        ]);
    }

    #[Route(path: '/books/category/{name}', name: 'app_book_category')]
    public function showBooksByCategoryName(string $name): Response
    {
        $books = $this->bookService->getBooksByCategoryName($name);

        return $this->render('book/show_all_books.html.twig', [
            'books' => $books
        ]);
    }


    #[Route(path: '/books/categories/{categories}', name: 'app_book_categories')]
    public function showBooksByCategoriesName(string $categories): Response
    {
        $books = $this->bookService->getBooksByCategoriesName($categories);
        return $this->render('book/show_all_books.html.twig', [
            'books' => $books
        ]);
    }

    #[Route(path: '/boooks', name: 'app_boooks')]
    public function test(
        #[MapQueryParameter] array $categories
    ): Response {

        $book = $this->bookService->getBooksByCategoriesNameV2($categories);

        return $this->render('book/show_all_books.html.twig', [
            'books' => $book
        ]);
    }

    #[Route(path: '/book/new', name: 'app_book_new')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'etes pas autorisé')]
    public function new(Request $request): Response
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $this->bookService->saveBook($book);

            $this->addFlash('notice', 'Le livre ' . $book->getTitle() . ' a été ajouté');

            return $this->redirectToRoute('app_book_new');
        }

        return $this->render('book/book_new_edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(path: '/book/{id}/edit', name: 'app_book_edit')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas l\'autorisation')]
    public function edit(Request $request, int $id): Response
    {
        $book = $this->bookService->getBookById($id);

        if (null == $book) {

            $this->addFlash('notice', 'Le livre n\'existe pas');

            return $this->redirectToRoute('app_book_new');
        }

        $form = $this->createForm(BookType::class, $book, [
            'label_save_edit' => "Editer"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $this->bookService->saveBook($book);

            $this->addFlash('notice', 'Le livre ' . $book->getTitle() . ' a été modifié');

            return $this->redirectToRoute('app_book_edit', ["id" => $id]);
        }

        return $this->render('book/book_new_edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(path: '/books/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, int $id): Response
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return new Response('Formulaire de suppression invalide.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $book = $this->bookService->getBookById($id);
            $this->bookService->deleteBook($book);
            $this->addFlash('notice', 'Le Livre a été supprimé');
        } catch (NotFoundHttpException $e) {
            $this->addFlash('notice', $e->getMessage());
        }
        return $this->redirectToRoute('app_books', status: Response::HTTP_SEE_OTHER);
    }

    //Méthode qui génére un Formulaire de suppression
    private function createDeleteForm(int $id): FormInterface
    {
        return $this->createForm(DeleteBookType::class, options: [
            'action' => $this->generateUrl('app_book_delete', ['id' => $id]),
            'csrf_token_id' => 'delete_book_'.$id,
        ]);
    }
}

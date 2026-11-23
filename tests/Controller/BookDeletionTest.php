<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class BookDeletionTest extends WebTestCase
{
    public function testBookDetailRendersAndSubmitsTheDeletionForm(): void
    {
        $client = $this->createAdminClient();
        $book = (new Book())->setTitle("L'île");
        new \ReflectionProperty(Book::class, 'id')->setValue($book, 42);

        $books = $this->createMock(BookService::class);
        $books->expects(self::exactly(2))->method('getBookById')->with(42)->willReturn($book);
        $books->expects(self::once())->method('deleteBook')->with($book);
        self::getContainer()->set(BookService::class, $books);

        $page = $client->request('GET', '/books/42');

        self::assertResponseIsSuccessful();
        $button = $page->selectButton('Supprimer');
        $form = $button->form();
        self::assertSame('POST', $form->getMethod());
        self::assertStringEndsWith('/books/42/delete', $form->getUri());
        self::assertSelectorExists('form[data-turbo-confirm="Supprimer le livre « L\'île » ?"]');

        $client->submit($form);

        self::assertResponseRedirects('/books', Response::HTTP_SEE_OTHER);
    }

    public function testDeletionRejectsAnInvalidCsrfToken(): void
    {
        $client = $this->createAdminClient();
        $books = $this->createMock(BookService::class);
        $books->expects(self::never())->method('deleteBook');
        self::getContainer()->set(BookService::class, $books);

        $client->request('POST', '/books/42/delete', ['delete_book' => ['_token' => 'invalid']]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    private function createAdminClient(): KernelBrowser
    {
        $client = self::createClient();
        $client->disableReboot();
        $admin = (new User())->setEmail('admin@example.test')->setPassword('unused')->setRoles(['ROLE_ADMIN']);

        $provider = $this->createStub(UserProviderInterface::class);
        $provider->method('supportsClass')->willReturn(true);
        $provider->method('refreshUser')->willReturn($admin);
        self::getContainer()->set('security.user.provider.concrete.app_user_provider', $provider);

        return $client->loginUser($admin);
    }
}

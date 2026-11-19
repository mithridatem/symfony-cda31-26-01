<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository
    ) {}

    public function getBookByTitle(string $title): ?Book
    {
        return $this->bookRepository->findOneBy(["title" => $title]);
    }

    public function getBooks(): array
    {
        return $this->bookRepository->findAll() ?? [];
    }

    public function getBookById(int $id): ?Book
    {
        return $this->bookRepository->find($id);
    }

    public function getBooksByTitle(string $title): array
    {
        return $this->bookRepository->findByTitle($title);
    }

    public function getBooksByAfterPublishAt(string $date): array
    {
        return $this
            ->bookRepository
            ->findPublishedAfter(
                new \DateTimeImmutable($date)
            );
    }
}

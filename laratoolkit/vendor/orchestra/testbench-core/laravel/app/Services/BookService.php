<?php

namespace App\Services;

use App\Models\Book;

class BookService
{
    public function getAllBooks(int $perPage = 10)
    {
        return Book::orderBy('id', 'DESC')->paginate($perPage);
    }

    public function findBook($slug)
    {
        return Book::where('slug', $slug)->findOrFail();
    }

    public function createBook(array $data)
    {
        return Book::create($data);
    }

    public function updateBook(array $data, Book $book)
    {
        return $book->update($data);
    }

    public function deleteBook($id)
    {
        return Book::destroy($id);
    }
}

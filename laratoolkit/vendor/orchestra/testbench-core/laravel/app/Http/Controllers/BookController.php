<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Services\BookService;

class BookController extends Controller
{
    protected $service;

    public function __construct(BookService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->getAllBooks();

        return view('books.index', compact('items'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(BookRequest $request)
    {
        $this->service->createBook($request->validated());

        return redirect()->route('books.index');
    }

    public function show($slug)
    {
        $item = $this->service->findBook($slug);

        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(BookRequest $request, Book $book)
    {
        $this->service->updateBook($request->validated(), $book);

        return redirect()->route('books.index');
    }

    public function destroy(Book $book)
    {
        $this->service->deleteBook($book);

        return redirect()->route('books.index');
    }
}
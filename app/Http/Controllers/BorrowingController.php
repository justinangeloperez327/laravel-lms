<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Requests\UpdateBorrowingRequest;
use App\Models\Borrowing;

final class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowings = Borrowing::with(['user', 'book'])->paginate(10);
        return inertia('borrowings/index', compact('borrowings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::all();
        $books = \App\Models\Book::where('available_copies', '>', 0)->get();
        return inertia('borrowings/create', compact('users', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request)
    {
        $validated = $request->validated();

        $borrowing = Borrowing::create($validated);

        // Decrease available copies of the book
        $borrowing->book->decrement('available_copies');

        return redirect()->route('borrowings.show', $borrowing)
            ->with('success', 'Borrowing created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'book']);
        return inertia('borrowings/show', compact('borrowing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Borrowing $borrowing)
    {
        $users = \App\Models\User::all();
        $books = \App\Models\Book::all();
        return inertia('borrowings/edit', compact('borrowing', 'users', 'books'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBorrowingRequest $request, Borrowing $borrowing)
    {
        $validated = $request->validated();

        // Adjust available copies if the book is changed
        if ($validated['book_id'] != $borrowing->book_id) {
            $borrowing->book->increment('available_copies');
            \App\Models\Book::find($validated['book_id'])->decrement('available_copies');
        }

        $borrowing->update($validated);

        return redirect()->route('borrowings.show', $borrowing)
            ->with('success', 'Borrowing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Borrowing $borrowing)
    {
        // Increase available copies of the book
        $borrowing->book->increment('available_copies');

        $borrowing->delete();

        return redirect()->route('borrowings.index')
            ->with('success', 'Borrowing deleted successfully.');
    }
}

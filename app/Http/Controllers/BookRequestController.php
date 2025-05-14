<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequestRequest;
use App\Http\Requests\UpdateBookRequestRequest;
use App\Models\BookRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class BookRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Librarians see all requests, users see only their own
        $user = Auth::user();

        if ($user->hasRole('librarian') || $user->hasRole('admin')) {
            $bookRequests = BookRequest::with('user')->paginate(10);
        } else {
            $bookRequests = BookRequest::where('user_id', $user->id)->paginate(10);
        }

        return inertia('book-requests/index', compact('bookRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('book-requests/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequestRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $bookRequest = BookRequest::create($validated);

        return redirect()->route('book-requests.show', $bookRequest)
            ->with('success', 'Book request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BookRequest $bookRequest)
    {
        $bookRequest->load('user');
        return inertia('book-requests/show', compact('bookRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookRequest $bookRequest)
    {
        return inertia('book-requests/edit', compact('bookRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequestRequest $request, BookRequest $bookRequest)
    {
        $validated = $request->validated();
        $bookRequest->update($validated);

        return redirect()->route('book-requests.show', $bookRequest)
            ->with('success', 'Book request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookRequest $bookRequest)
    {
        $bookRequest->delete();

        return redirect()->route('book-requests.index')
            ->with('success', 'Book request deleted successfully.');
    }
}

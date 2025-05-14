<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePenaltyRequest;
use App\Http\Requests\UpdatePenaltyRequest;
use App\Models\Penalty;

final class PenaltyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penalties = Penalty::with(['user', 'borrowing'])->paginate(10);
        return inertia('penalties/index', compact('penalties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::all();
        $borrowings = \App\Models\Borrowing::all();
        return inertia('penalties/create', compact('users', 'borrowings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenaltyRequest $request)
    {
        $validated = $request->validated();

        $penalty = Penalty::create($validated);

        return redirect()->route('penalties.show', $penalty)
            ->with('success', 'Penalty created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Penalty $penalty)
    {
        $penalty->load(['user', 'borrowing']);
        return inertia('penalties/show', compact('penalty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penalty $penalty)
    {
        $users = \App\Models\User::all();
        $borrowings = \App\Models\Borrowing::all();
        return inertia('penalties/edit', compact('penalty', 'users', 'borrowings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenaltyRequest $request, Penalty $penalty)
    {
        $validated = $request->validated();

        $penalty->update($validated);

        return redirect()->route('penalties.show', $penalty)
            ->with('success', 'Penalty updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penalty $penalty)
    {
        $penalty->delete();

        return redirect()->route('penalties.index')
            ->with('success', 'Penalty deleted successfully.');
    }
}

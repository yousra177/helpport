<?php

namespace App\Http\Controllers;

use App\Models\Problems;
use App\Models\Solutions;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SolutionDeletedNotification;

class SolutionsController extends Controller
{
    public function index(Problems $problem)
    {
        $solutions = $problem->solutions()->with(['user', 'problem'])->get();
        $user = Auth::user();

        return view(('user.solution'),
            compact('problem', 'solutions')
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'problem_id' => 'required|exists:problems,id',
            'content' => 'required|string|max:5000',
            'solution_attachments.*' => 'file|max:2048|mimes:jpg,jpeg,png,pdf,docx,zip',
        ]);

        $solution = new Solutions();
        $solution->problem_id = $request->problem_id;
        $solution->content = $request->content;
        $solution->user_id = Auth::id();

        if ($request->hasFile('solution_attachments')) {
            $paths = [];
            foreach ($request->file('solution_attachments') as $file) {
                $paths[] = $file->store('solutions', 'public');
            }
            $solution->solution_attachments = json_encode($paths);
        }

        $solution->save();

        return redirect()->back()->with('success', 'Solution added successfully.');
    }

    public function edit(Problems $problem, Solutions $solution)
    {
        return view('user.edit-solution', compact('problem', 'solution'));
    }

    public function update(Request $request, Problems $problem, Solutions $solution)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'solution_attachments.*' => 'file|max:2048|mimes:jpg,jpeg,png,pdf,docx,zip',
        ]);

        $solution->content = $request->content;

        if ($request->hasFile('solution_attachments')) {
            $paths = [];
            foreach ($request->file('solution_attachments') as $file) {
                $paths[] = $file->store('solutions', 'public');
            }
            $solution->solution_attachments = json_encode($paths);
        }

        $solution->save();

        return redirect()
            ->route('solutions.index', $problem->id)
            ->with('success', 'Solution updated successfully.');
    }

   public function destroy(Request $request, Problems $problem, Solutions $solution)
{
    $user = Auth::user();

    // IT_user or normal user: can only delete their own solution
    if ($solution->user_id === $user->id) {
        $solution->delete(); // soft delete
        return redirect()->back()->with('success', 'Your solution has been deleted.');
    }

    // Admin: can delete any solution
    if ($user->role === 'admin') {
        $request->validate(['delete_reason' => 'required|string|max:255']);
        $solution->delete_reason = $request->input('delete_reason');
        $solution->save();
        $solution->delete(); // soft delete

        if ($solution->user && $solution->user->id !== $user->id) {
            $solution->user->notify(new SolutionDeletedNotification($solution, $problem, $solution->delete_reason));
        }

        return redirect()->back()->with('success', 'Solution deleted by admin.');
    }

    // Chef_dep: can delete only within their department
    if (
        $user->role === 'chef_dep' &&
        $solution->user &&
        $solution->user->departement === $user->departement
    ) {
        $request->validate(['delete_reason' => 'required|string|max:255']);
        $solution->delete_reason = $request->input('delete_reason');
        $solution->save();
        $solution->delete(); // soft delete

        if ($solution->user && $solution->user->id !== $user->id) {
            $solution->user->notify(new SolutionDeletedNotification($solution, $problem, $solution->delete_reason));
        }

        return redirect()->back()->with('success', 'Solution deleted by department head.');
    }

    return redirect()->back()->with('error', 'You do not have permission to delete this solution.');
}



}

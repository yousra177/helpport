<?php

namespace App\Http\Controllers;

use App\Models\comments;
use App\Models\Problems;
use App\Models\Solutions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function index(Solutions $solution)
    {
        $comments = $solution->comments()->with(['user', 'problem', 'solution'])->get();
        $user = Auth::user();

        return view(
            $user->role === 'admin' ? 'admin.solution' :
            ($user->role === 'chef_dep' ? 'headdp.solution' : 'user.solution'),
            compact('solution', 'comments')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'problem_id' => 'required|exists:problems,id',
            'solution_id' => 'required|exists:solutions,id',
            'comment' => 'required|string|max:1000',
        ]);

        comments::create([
            'user_id' => Auth::id(),
            'problem_id' => $request->problem_id,
            'solution_id' => $request->solution_id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Comment added successfully!');
    }

    public function destroy(Problems $problem, Solutions $solution, comments $comment)
    {
        // Check if user can delete the comment
        if (Auth::id() !== $comment->user_id && Auth::user()->role !== 'admin' &&
            !(Auth::user()->role === 'chef_dep' && Auth::user()->departement === $comment->user->departement)) {
            return back()->with('error', 'You do not have permission to delete this comment.');
        }

        // Soft delete the comment
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }
}

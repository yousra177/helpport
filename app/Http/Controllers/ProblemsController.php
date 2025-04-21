<?php

namespace App\Http\Controllers;

use App\Models\Problems;
use App\Models\User;
use App\Notifications\ProblemCreatedNotification;
use App\Notifications\ProblemDeletedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProblemsController extends Controller
{
    protected $perPage = 10;

    protected const PROBLEM_TYPES = [
        'Software Application Issues',
        'Network Internet Problems',
        'Database Data Management Problems',
        'Security Access Control Problems',
        'Hardware Equipment Issues',
        'IT Support Service Requests',
        'Project Collaboration Problems'
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', $this->perPage);

        $problemsQuery = Problems::withCount('solutions')->whereNull('deleted_at');

        if ($user->role === 'admin') {
            $problemsQuery->where('approved', true);
        } elseif ($user->role === 'chef_dep') {
            $userIds = User::where('departement', $user->departement)->pluck('id');
            $problemsQuery->whereIn('user_id', $userIds)->where('approved', true);
        } else {
            $departmentUsers = User::where('departement', $user->departement)->pluck('id');
            $problemsQuery->whereIn('user_id', $departmentUsers)->where('approved', true);
        }

        $problems = $problemsQuery->latest()->paginate($perPage);

        $waitingProblems = Problems::where('user_id', $user->id)
            ->where('approved', false)
            ->latest()
            ->get();

        return view($this->getViewForRole($user->role), compact('problems', 'waitingProblems'));
    }

    public function create()
    {
        return view('user.creat-problem', [
            'problemTypes' => self::PROBLEM_TYPES
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'type' => ['required', 'string', Rule::in(self::PROBLEM_TYPES)],
            'problem_attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,zip|max:2048',
        ]);

        $problem = new Problems($request->only(['title', 'description', 'type']));
        $problem->user_id = Auth::id();
        $problem->approved = (Auth::user()->role === 'admin');

        if ($request->hasFile('problem_attachments')) {
            $problem->problem_attachments = collect($request->file('problem_attachments'))
                ->map(fn($file) => $file->store('problems/attachments', 'public'))
                ->toArray();
        }

        $problem->save();

        $this->notifyDepartmentUsers(
            $problem,
            $problem->approved ? 'approved' : 'created'
        );

        return redirect()->route('dashboard')->with(
            'success',
            $problem->approved ? 'Problem created successfully.' : 'Problem submitted for approval.'
        );
    }

    public function edit($id)
    {
        $problem = Problems::findOrFail($id);
        return view('user.edit-problem', [
            'problem' => $problem,
            'problemTypes' => self::PROBLEM_TYPES
        ]);
    }

    public function update(Request $request, $id)
    {
        $problem = Problems::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'type' => ['required', 'string', Rule::in(self::PROBLEM_TYPES)],
            'problem_attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,zip|max:2048',
        ]);

        if ($request->hasFile('problem_attachments')) {
            if (!empty($problem->problem_attachments)) {
                foreach ((array) $problem->problem_attachments as $attachment) {
                    Storage::disk('public')->delete($attachment);
                }
            }

            $problem->problem_attachments = collect($request->file('problem_attachments'))
                ->map(fn($file) => $file->store('problems/attachments', 'public'))
                ->toArray();
        }

        $problem->update($request->only(['title', 'description', 'type']));

        return redirect()->route('dashboard')->with('success', 'Problem updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $problem = Problems::findOrFail($id);
        $user = Auth::user();

        $canDelete = $user->role === 'admin'
            || ($user->role === 'chef_dep' && $problem->user->departement === $user->departement)
            || $problem->user_id === $user->id;

        if (!$canDelete) {
            return redirect()->back()->with('error', 'You do not have permission to delete this problem.');
        }

        if (in_array($user->role, ['admin', 'chef_dep'])) {
            $request->validate(['delete_reason' => 'required|string|max:255']);
            $problem->delete_reason = $request->input('delete_reason');
            $problem->status = 'hidden';
            $problem->save();
        }

        $problem->delete();

        if ($problem->user) {
            $problem->user->notify(new ProblemDeletedNotification($problem, $problem->delete_reason ?? 'Deleted by owner'));
            Log::info("Problem deletion notified to user {$problem->user_id}", ['problem_id' => $id]);
        }

        return redirect()->back()->with('success', 'Problem has been deleted.');
    }

    public function showApprovalPage(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', $this->perPage);

        $problemsQuery = Problems::where('approved', false);

        if ($user->role === 'chef_dep') {
            $userIds = User::where('departement', $user->departement)->pluck('id');
            $problemsQuery->whereIn('user_id', $userIds);
        }

        $problems = $problemsQuery->latest()->paginate($perPage);

        return view('headdp.approve_problems', compact('problems'));
    }

    public function approve($id)
    {
        $user = Auth::user();
        $problem = Problems::findOrFail($id);

        $canApprove = $user->role === 'admin'
            || ($user->role === 'chef_dep' && $problem->user->departement === $user->departement);

        if ($canApprove) {
            $problem->approved = true;
            $problem->save();

            $this->notifyDepartmentUsers($problem, 'approved');
            Log::info("Problem approved and notified", ['problem_id' => $id, 'approved_by' => $user->id]);

            return redirect()->route('dashboard')->with('success', 'Problem approved successfully!');
        }

        return redirect()->back()->with('error', 'You do not have permission to approve this problem.');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $user = Auth::user();
        $perPage = $request->input('per_page', $this->perPage);

        $problemsQuery = Problems::with(['user', 'solutions'])
            ->whereNull('deleted_at')
            ->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', "%$searchTerm%")
                    ->orWhere('description', 'like', "%$searchTerm%")
                    ->orWhere('type', 'like', "%$searchTerm%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%$searchTerm%"));
            });

        if ($user->role === 'admin') {
            $problemsQuery->where('approved', true);
        } elseif ($user->role === 'chef_dep') {
            $userIds = User::where('departement', $user->departement)->pluck('id');
            $problemsQuery->whereIn('user_id', $userIds)->where('approved', true);
        } else {
            $departmentUsers = User::where('departement', $user->departement)->pluck('id');
            $problemsQuery->whereIn('user_id', $departmentUsers)->where('approved', true);
        }

        $problems = $problemsQuery->latest()->paginate($perPage);

        return view($this->getViewForRole($user->role), [
            'problems' => $problems,
            'searchTerm' => $searchTerm,
            'waitingProblems' => Problems::where('user_id', $user->id)
                ->where('approved', false)
                ->latest()
                ->get()
        ]);
    }

    protected function getViewForRole($role)
    {
        return match ($role) {
            'admin' => 'admin.home',
            'chef_dep' => 'headdp.home',
            default => 'user.home',
        };
    }

    protected function notifyDepartmentUsers(Problems $problem, string $action)
    {
        try {
            if (!$problem->user) {
                Log::warning("Problem user is null. Skipping notification.", ['problem_id' => $problem->id]);
                return;
            }

            $department = $problem->user->departement;
            $users = User::where('departement', $department)
                ->where('id', '!=', $problem->user_id)
                ->get();

            foreach ($users as $user) {
                if (empty($user->email)) {
                    continue;
                }

                $user->notify(new ProblemCreatedNotification($problem, $action));
                Log::info("Notified user {$user->id} about {$action} problem", [
                    'problem_id' => $problem->id,
                    'user_email' => $user->email,
                    'department' => $department
                ]);
            }

            Log::info("Notified {$users->count()} users in {$department} about {$action} problem {$problem->id}");

        } catch (\Exception $e) {
            Log::error("Failed to notify department about {$action} problem", [
                'error' => $e->getMessage(),
                'problem_id' => $problem->id,
                'department' => $problem->user->departement ?? 'Unknown'
            ]);
        }
    }
}

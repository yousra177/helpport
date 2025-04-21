<?php

namespace App\Policies;

use App\Models\Problem;
use App\Models\Problems;
use App\Models\User;

class ProblemPolicy
{
    /**
     * Allow users to view the list of problems.
     */
    public function viewAny(User $user): bool
{
    if ($user->role === 'admin') {
        return true;
    }

    if ($user->role === 'chef_dep') {
        return Problems::whereHas('user', function ($query) use ($user) {
            $query->where('departement', $user->departement);
        })->exists();
    }

    return false;
}

public function view(User $user, Problems $problem): bool
{
    if ($user->role === 'admin') {
        return true;
    }

    if ($user->role === 'chef_dep') {
        return $problem->user->departement === $user->departement;
    }

    return $user->id === $problem->user_id;
}



    /**
     * Allow users to create new problems.
     */
    public function create(User $user): bool
    {
        return true; // ✅ Allow users to create problems
    }

    /**
     * Allow users to update their own problems or admins/head of departments to update any.
     */
    public function update(User $user, Problems $problem): bool
    {
        return $user->id === $problem->user_id || $user->role === 'admin' || $user->role === 'chef_dep';
    }

    /**
     * Allow users to delete their own problems or admins/head of departments to delete any.
     */
    public function delete(User $user, Problems $problem): bool
    {
        return $user->id === $problem->user_id || $user->role === 'admin' || $user->role === 'chef_dep';
    }

    /**
     * Allow restoring problems (if needed).
     */
    public function restore(User $user, Problems $problem): bool
    {
        return $user->role === 'admin' || $user->role === 'chef_dep'; // ✅ Fixed return logic
    }

    /**
     * Allow force deleting problems (if needed).
     */
    public function forceDelete(User $user, Problems $problem): bool
    {
        return $user->role === 'admin' || $user->role === 'chef_dep'; // ✅ Fixed return logic
    }
}

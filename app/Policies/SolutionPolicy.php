<?php

namespace App\Policies;

use App\Models\Solutions;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SolutionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'chef_dep') {
            return Solutions::whereHas('user', function ($query) use ($user) {
                $query->where('departement', $user->departement);
            })->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Solutions $Solution): bool
{
    if ($user->role === 'admin') {
        return true;
    }

    if ($user->role === 'chef_dep') {
        return $Solution->user->departement === $user->departement;
    }

    return $user->id === $Solution->user_id;
}

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Solutions $solution): bool
    {



        return $user->id === $solution->user_id || $user->role === 'admin' || $user->role === 'chef_dep';

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Solutions $solution): bool
    {
        return $user->id === $solution->user_id || $user->role === 'admin' || $user->role === 'chef_dep';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Solutions $solutions): bool
    {
        return $user->role === 'admin' || $user->role === 'chef_dep'; // ✅ Fixed return logic
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Solutions $solutions): bool
    {
        return $user->role === 'admin' || $user->role === 'chef_dep'; // ✅ Fixed return logic
    }
}

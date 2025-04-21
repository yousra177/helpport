<?php

namespace App\Policies;

use App\Models\comments;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'chef_dep'; // Allow admin and chef_dep to view any comment
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comments $comment): bool
    {
        // Admin can view all comments, chef_dep can only view comments from their department
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'chef_dep') {
            return $comment->solution->user->departement === $user->departement; // Only allow viewing comments for their department
        }

        return $user->id === $comment->user_id; // Users can view their own comments
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow any user to create comments
        return  $user->role === 'chef_dep' || $user->role === 'it_user';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comments $comment): bool
    {
        // Admin can update any comment, the comment owner can update their comment
        return $user->id === $comment->user_id || $user->role === 'admin' || $user->role === 'chef_dep';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comments $comment): bool
    {
        // Admin can delete any comment, the comment owner can delete their comment
        return $user->id === $comment->user_id || $user->role === 'admin' || $user->role === 'chef_dep';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comments $comment): bool
    {
        // Admin or chef_dep can restore comments
        return $user->role === 'admin' || $user->role === 'chef_dep';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, comments $comment): bool
    {
        // Admin or chef_dep can force delete comments
        return $user->role === 'admin' || $user->role === 'chef_dep';
    }
}

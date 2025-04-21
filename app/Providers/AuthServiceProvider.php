<?php

namespace App\Providers;

use App\Models\comments;
use App\Models\Solutions;
use App\Policies\CommentPolicy;
use App\Policies\ProblemPolicy;
use App\Policies\SolutionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Problems;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Problems::class => ProblemPolicy::class,
        Solutions::class => SolutionPolicy::class, // ✅ Register the SolutionPolicy
         comments::class => CommentPolicy::class, // Ensure this is added

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies(); // ✅ This is now valid

        // Define Gates
        Gate::define('update-problem', function (User $user, Problems $problem,Solutions $solution) {
            return $user->id === $problem->user_id || $user->role === 'admin'|| $user->role === 'chef_dep';
        });

        Gate::define('delete-problem', function (User $user, Problems $problem,Solutions $solution) {
            return $user->id === $problem->user_id || $user->role === 'chef_dep';
        });
    }
}

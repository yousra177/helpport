<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\HeaddepController;
use App\Http\Controllers\ProblemsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolutionsController;
use App\Models\Problems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Default login route
Route::get('/', function () {
    return view('login.index');
})->name('login');

// Dashboard for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ProblemsController::class, 'index'])->name('dashboard');
});

// Grouped routes for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');


// To:
Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notification', function () {
        return view('user.notif');
    })->name('notification');
});

// Routes for Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        $problems = Problems::latest()->get(); // Fetch problems from database
        return view('admin.home', compact('problems')); // Pass data to the view
    })->name('dashboard');

    Route::get('/create-user', [RegisteredUserController::class, 'create'])->name('create_user');
    Route::post('/create-user', [RegisteredUserController::class, 'store'])->name('store_user');
    Route::get('/users/{id}/edit', [RegisteredUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [RegisteredUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [RegisteredUserController::class, 'destroy'])->name('users.destroy');
});

// Routes for Head of Department
Route::middleware(['auth', 'role:chef_dep'])->prefix('head')->name('head.')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $problems = Problems::whereIn('user_id',
            \App\Models\User::where('departement', $user->departement)->pluck('id')
        )->latest()->get(); // ✅ Fetch problems only for the same department

        return view('headdp.home', compact('problems'));
    })->name('dashboard');

    Route::get('/create-user', [HeaddepController::class, 'create'])->name('create_user');
    Route::post('/create-user', [HeaddepController::class, 'store'])->name('store_user');
    Route::get('/users/{id}/edit', [HeaddepController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [HeaddepController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [HeaddepController::class, 'destroy'])->name('users.destroy');

    // ✅ Route to show the approval page
    Route::get('/approve-problems', [ProblemsController::class, 'showApprovalPage'])->name('approve_problems');

    // ✅ Route to approve a problem
    Route::post('/approve-problems/{id}', [ProblemsController::class, 'approve'])->name('problems.approve');

    Route::delete('/approve-problems/{id}', [ProblemsController::class, 'destroy'])->name('approve_problems.destroy');

});

// Routes for Problems
Route::middleware(['auth'])->prefix('problems')->name('problems.')->group(function () {
    Route::get('/', [ProblemsController::class, 'index'])->name('index');

    // Only normal users can create problems (admin & chef_dep CANNOT create)
    Route::get('/create', [ProblemsController::class, 'create'])->name('create');
    Route::post('/create', [ProblemsController::class, 'store'])->name('store');

    Route::get('/{id}/edit', [ProblemsController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProblemsController::class, 'update'])->name('update');
    Route::delete('/dashboard/{id}', [ProblemsController::class, 'destroy'])->name('destroy')->middleware('auth');
});

// Notifications
Route::patch('/notifications/{id}/read', function ($id) {
    $notification = Auth::user()->notifications()->find($id);
    if ($notification) {
        $notification->markAsRead();
    }
    return redirect()->back();
})->name('notifications.read');



Route::middleware(['auth'])->prefix('problems')->group(function () {
    // Show all solutions for a specific problem
    Route::get('{problem}/solutions', [SolutionsController::class, 'index'])->name('solutions.index');

    // Create a solution for a specific problem
    Route::post('{problem}/solutions', [SolutionsController::class, 'store'])->name('solutions.store');

    // Edit a specific solution of a problem
    Route::get('{problem}/solutions/{solution}/edit', [SolutionsController::class, 'edit'])->name('solutions.edit');

    // Update a solution
    Route::put('{problem}/solutions/{solution}', [SolutionsController::class, 'update'])->name('solutions.update');

    // Delete a solution
    Route::delete('{problem}/solutions/{solution}', [SolutionsController::class, 'destroy'])->name('solutions.destroy');


});

 Route::get('/comments', [CommentsController::class, 'index'])->name('comments.index');

 Route::post('/comments', [CommentsController::class, 'store'])->name('comments.store');

 Route::delete('/problems/{problem}/solutions/{solution}/comments/{comment}',
    [CommentsController::class, 'destroy'])
    ->name('comments.destroy');



    Route::get('/search', [ProblemsController::class, 'search'])->name('problems.search');

    Route::get('/test-email', function() {
        Mail::raw('This is a test email', function($message) {
            $message->to('yosimouloudji@gmail.com')
                    ->subject('Test Email');
        });

        return 'Email sent!';
    });

// Include authentication routes (e.g., login, registration, password reset)
require __DIR__.'/auth.php';

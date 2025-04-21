<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HeaddepController extends Controller
{
    /**
     * Display the form for creating a new user and show users in the authenticated user's department.
     */
    public function create(Request $request)
    {
        $authUser = Auth::user(); // Get the authenticated user

        // Retrieve users in the same department, excluding the authenticated user
        $query = User::where('departement', $authUser->departement)
                     ->where('id', '!=', $authUser->id); // Exclude the authenticated user

        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%")
                  ->orWhere('phone_number', 'LIKE', "%$search%")
                  ->orWhere('role', 'LIKE', "%$search%")
                  ->orWhere('departement', 'LIKE', "%$search%");
            });
        }

        $users = $query->get(); // Fetch results

        return view('headdp.creat-user', ['users' => $users]); // Pass filtered users to the view
    }
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $authUser = Auth::user(); // Get the authenticated user

        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_number' => 'required|string|max:15|unique:users,phone_number',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:it_user', // Restrict to allowable roles
        ]);

        // Attempt to create a new user
        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'password' => Hash::make($validated['password']),
                'departement' => $authUser->departement, // Automatically set the department
                'role' => $validated['role'],
            ]);

            return back()->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred during registration. Please try again.']);
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $id)
    {
        $authUser = Auth::user(); // Get the authenticated user

        // Retrieve the user if they belong to the same department
        $user = User::where('id', $id)
                    ->where('departement', $authUser->departement)
                    ->firstOrFail();

        return view('headdp.edit-user', compact('user')); // Pass the user to the edit view
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
        $authUser = Auth::user(); // Get the authenticated user

        // Retrieve the user if they belong to the same department
        $user = User::where('id', $id)
                    ->where('departement', $authUser->departement)
                    ->firstOrFail();

        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255|' . $id,
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:15|unique:users,phone_number,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:it_user', // Restrict to allowable roles
        ]);

        // Update the user's data
        $user->update($validated);

        // If a password is provided, hash it and update
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('head.create_user')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if user exists and delete
        if ($user) {
            $user->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view with a list of all users.
     */
    public function create(Request $request ): View
    {
        $authUser = Auth::user(); // Get the authenticated user

        // Retrieve all users except the authenticated user
        $users = User::where('id', '!=', $authUser->id)
            ->select('id', 'name', 'email', 'phone_number','role', 'departement') // Fetch only necessary fields
            ->get();


            $query = User::query();

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('name', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%$search%")
                      ->orWhere('phone_number', 'LIKE', "%$search%")
                      ->orWhere('role', 'LIKE', "%$search%")
                      ->orWhere('departement', 'LIKE', "%$search%");
            }

            $users = $query->get();

        return view('admin.creatusers', compact('users'));
    }

    /**
     * Display the edit view for a specific user.
     */
    public function edit($id): View
    {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
    }

    /**
     * Update an existing user's details.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'phone_number' => ['required', 'string', 'max:15', Rule::unique('users')->ignore($id)],
            'role' => ['required', Rule::in(['admin', 'chef_dep', 'it_user'])],
            'departement' => ['required', Rule::in(['general', 'deeis', 'diei', 'dda', 'dadam'])],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.create_user')->with('success', 'User updated successfully.');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:15', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'chef_dep', 'it_user'])],
            'departement' => ['required', Rule::in(['general', 'deeis', 'diei', 'dda', 'dadam'])],
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'departement' => $request->departement,
            ]);

            return back()->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.']);
        }
    }

    /**
     * Delete a user.
     */
    public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json(['success' => true]);
}

}

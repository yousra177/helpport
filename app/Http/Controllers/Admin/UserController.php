<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot delete an admin!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}

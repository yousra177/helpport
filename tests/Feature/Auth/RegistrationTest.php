<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('admin can access user creation form', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/create-user');

    $response->assertStatus(200);
    // ->assertSee('Create New User'); // Only include this if that exact text exists in the view
});

test('admin can create a new user', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/admin/create-user', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone_number' => '0555555555',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role' => 'it_user',
        'departement' => 'general',
    ]);

    $response->assertRedirect('/'); // Change this to whatever your actual redirect is

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'role' => 'it_user',
    ]);
});

test('non-admin cannot access user creation', function () {
    $user = User::factory()->create(['role' => 'it_user']);

    $this->actingAs($user)
        ->get('/admin/create-user')
        ->assertForbidden(); // Or assertRedirect('/')
});

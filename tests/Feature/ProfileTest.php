<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk()
             ->assertSee($user->name)
             ->assertSee($user->email);
});

test('profile information can be updated', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_number' => '0777777777',
    ]);

    $newEmail = 'updated@example.com';

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Updated Name',
            'email' => $newEmail,
            'phone_number' => '0666666666',
        ]);

    $response->assertSessionHasNoErrors()
             ->assertRedirect('/');

    $user->refresh();

    $this->assertEquals('Updated Name', $user->name);
    $this->assertEquals($newEmail, $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_number' => '0777777777',
    ]);

    $originalVerifiedAt = $user->email_verified_at;

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Same Email Update',
            'email' => $user->email,
            'phone_number' => '0777777777',
        ]);

    $response->assertSessionHasNoErrors()
             ->assertRedirect('/');

    $user->refresh();

    $this->assertEquals('Same Email Update', $user->name);
    $this->assertEquals($originalVerifiedAt->toDateTimeString(), $user->email_verified_at->toDateTimeString());
});

test('password can be updated', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPass123!'),
    ]);

    $response = $this
        ->actingAs($user)
        ->put('/password', [
            'current_password' => 'OldPass123!',
            'password' => 'NewPass456!',
            'password_confirmation' => 'NewPass456!',
        ]);

    $response->assertSessionHasNoErrors()
             ->assertRedirect('/');

    $this->assertTrue(Hash::check('NewPass456!', $user->fresh()->password));
});

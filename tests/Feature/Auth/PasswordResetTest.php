<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertOk()
             ->assertSee('Forgot your password? No problem.');
});

test('reset password link can be requested for valid email', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/forgot-password', ['email' => $user->email]);

    $response->assertSessionHasNoErrors()
             ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password link request fails for invalid email', function () {
    $response = $this->post('/forgot-password', ['email' => 'nonexistent@example.com']);

    $response->assertSessionHasErrors('email');
});

test('reset password link request fails for empty email', function () {
    $response = $this->post('/forgot-password', ['email' => '']);

    $response->assertSessionHasErrors('email');
});

test('reset password screen can be rendered with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $token = $notification->token;

        $response = $this->get('/reset-password/'.$token);

        $response->assertOk()
                 ->assertSee('Reset Password');

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();
    $newPassword = 'NewPassword123!';

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, $newPassword) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertSessionHasNoErrors()
                 ->assertRedirect('/login');

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));

        return true;
    });
});

test('password cannot be reset with invalid token', function () {
    $user = User::factory()->create();
    $newPassword = 'NewPassword123!';

    $response = $this->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $response->assertSessionHasErrors(['email' => 'This password reset token is invalid.']);
    $this->assertFalse(Hash::check($newPassword, $user->fresh()->password));
});

test('password reset fails when passwords do not match', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'WrongPassword!',
        ]);

        $response->assertSessionHasErrors('password');
        return true;
    });
});

test('password reset fails with weak password', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors('password');
        return true;
    });
});

test('password reset token is invalidated after use', function () {
    Notification::fake();

    $user = User::factory()->create();
    $newPassword = 'NewPassword123!';

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, $newPassword) {
        // First attempt - should succeed
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertSessionHasNoErrors();

        // Second attempt with same token - should fail
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'AnotherPassword123!',
            'password_confirmation' => 'AnotherPassword123!',
        ]);

        $response->assertSessionHasErrors(['email' => 'This password reset token is invalid.']);

        return true;
    });
});

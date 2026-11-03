<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('guest can view the forgot password request form', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk()
        ->assertSee('Reset Password')
        ->assertSee('Send Reset Link')
        ->assertSee('Email Address');
});

test('authenticated student is redirected away from forgot password page', function () {
    $student = User::create([
        'name' => 'Active Student',
        'email' => 'student@campus.ac.id',
        'password' => 'secret1234',
        'role' => 'student',
    ]);

    $response = $this->actingAs($student)->get(route('password.request'));

    $response->assertRedirect(route('student.dashboard'));
});

test('user can request a password reset link with a valid registered email', function () {
    Notification::fake();

    $user = User::create([
        'name' => 'Forgot Password Candidate',
        'email' => 'candidate.forgot@campus.ac.id',
        'password' => 'secret1234',
        'role' => 'student',
    ]);

    $response = $this->from(route('password.request'))->post(route('password.email'), [
        'email' => 'candidate.forgot@campus.ac.id',
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHas('success', 'A password reset link has been sent to your email address.');

    // Check password_reset_tokens table has an entry
    $tokenEntry = DB::table('password_reset_tokens')->where('email', 'candidate.forgot@campus.ac.id')->first();
    expect($tokenEntry)->not->toBeNull();
});

test('requesting password reset for non-existent email returns friendly error', function () {
    $response = $this->from(route('password.request'))->post(route('password.email'), [
        'email' => 'nobody@nowhere.com',
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHas('error', 'We could not find an account with that email address.');
});

test('guest can view the reset password form with valid token', function () {
    $response = $this->get(route('password.reset', ['token' => 'sample-test-token', 'email' => 'test@campus.ac.id']));

    $response->assertOk()
        ->assertSee('Set New Password')
        ->assertSee('New Password')
        ->assertSee('Confirm Password')
        ->assertSee('sample-test-token');
});

test('user can successfully reset their password and old sessions are invalidated', function () {
    $user = User::create([
        'name' => 'Reset Target User',
        'email' => 'target.user@campus.ac.id',
        'password' => 'oldpassword123',
        'role' => 'student',
    ]);

    // Simulate old session
    DB::table('sessions')->insert([
        'id' => 'old-session-before-reset',
        'user_id' => $user->id,
        'ip_address' => '10.0.0.1',
        'user_agent' => 'Old Device',
        'payload' => 'payload',
        'last_activity' => time(),
    ]);

    $token = Password::broker()->createToken($user);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => 'target.user@campus.ac.id',
        'password' => 'newBrandNewPassword123',
        'password_confirmation' => 'newBrandNewPassword123',
    ]);

    $response->assertRedirect(route('student.login'));
    $response->assertSessionHas('success', 'Your password has been reset successfully. Please log in with your new password.');

    // Password must be updated
    $user->refresh();
    expect(Hash::check('newBrandNewPassword123', $user->password))->toBeTrue()
        ->and(Hash::check('oldpassword123', $user->password))->toBeFalse();

    // Old session must be deleted
    expect(DB::table('sessions')->where('id', 'old-session-before-reset')->exists())->toBeFalse();
});

test('resetting password with invalid token returns error', function () {
    $user = User::create([
        'name' => 'Target User 2',
        'email' => 'target2@campus.ac.id',
        'password' => 'oldpassword123',
        'role' => 'student',
    ]);

    $response = $this->from(route('password.reset', ['token' => 'bad-token']))->post(route('password.update'), [
        'token' => 'invalid-token-123',
        'email' => 'target2@campus.ac.id',
        'password' => 'newBrandNewPassword123',
        'password_confirmation' => 'newBrandNewPassword123',
    ]);

    $response->assertRedirect(route('password.reset', ['token' => 'bad-token']));
    $response->assertSessionHas('error', 'This password reset link is invalid or has expired. Please request a new one.');

    $user->refresh();
    expect(Hash::check('oldpassword123', $user->password))->toBeTrue();
});

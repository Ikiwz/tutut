<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('registration triggers registered event and redirects to verification notice', function () {
    Event::fake([Registered::class]);

    $response = $this->post(route('student.register.submit'), [
        'name' => 'Pending Verification Student',
        'email' => 'pending@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
    ]);

    $response->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'pending@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasVerifiedEmail())->toBeFalse();

    Event::assertDispatched(Registered::class);
});

test('unverified student accessing dashboard is redirected to email verification notice', function () {
    $user = User::factory()->unverified()->create([
        'role' => 'student',
    ]);

    $response = $this->actingAs($user)->get(route('student.dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

test('verification notice displays candidate email and resend button', function () {
    $user = User::factory()->unverified()->create([
        'name' => 'Jane Unverified',
        'email' => 'jane.unverified@example.com',
        'role' => 'student',
    ]);

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertOk()
        ->assertSee('Verify Your Email')
        ->assertSee('jane.unverified@example.com')
        ->assertSee('Resend Verification Email');
});

test('verified student visiting verification notice is redirected to dashboard', function () {
    $user = User::factory()->create([
        'role' => 'student',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertRedirect(route('student.dashboard'));
});

test('email can be verified using signed verification URL', function () {
    Event::fake([Verified::class]);

    $user = User::factory()->unverified()->create([
        'role' => 'student',
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('success');
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create([
        'role' => 'student',
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email@example.com')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('student can resend verification notification with rate limit', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create([
        'role' => 'student',
    ]);

    $response = $this->actingAs($user)->post(route('verification.send'));

    $response->assertRedirect();
    $response->assertSessionHas('status', 'verification-link-sent');
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

test('candidate login, register, and admin login pages display official google oauth buttons', function () {
    $loginResponse = $this->get(route('student.login'));
    $loginResponse->assertOk()
        ->assertSee('Continue with Google')
        ->assertSee(route('auth.google'));

    $registerResponse = $this->get(route('student.register'));
    $registerResponse->assertOk()
        ->assertSee('Continue with Google')
        ->assertSee(route('auth.google'));

    $adminResponse = $this->get(route('admin.login'));
    $adminResponse->assertOk()
        ->assertSee('Continue with Google')
        ->assertSee(route('auth.google'));
});

test('google redirect returns error when credentials are not configured', function () {
    Config::set('services.google.client_id', null);
    Config::set('services.google.client_secret', null);

    $response = $this->get(route('auth.google'));

    $response->assertRedirect(route('student.login'))
        ->assertSessionHas('error');
});

test('google redirect initiates oauth redirection when configured', function () {
    Config::set('services.google.client_id', 'mock-google-client-id');
    Config::set('services.google.client_secret', 'mock-google-client-secret');

    $response = $this->get(route('auth.google'));

    $response->assertRedirect();
    $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
});

test('new candidate can register and authenticate via google callback', function () {
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-unique-98765');
    $socialiteUser->shouldReceive('getName')->andReturn('Budi Santoso');
    $socialiteUser->shouldReceive('getEmail')->andReturn('budi.santoso@campus.ac.id');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');
    $socialiteUser->shouldReceive('getNickname')->andReturn(null);

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('student.dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'budi.santoso@campus.ac.id')->first();
    expect($user)->not->toBeNull();
    expect($user->google_id)->toBe('google-unique-98765');
    expect($user->role)->toBe('student');
    expect($user->avatar)->toBe('https://lh3.googleusercontent.com/avatar.jpg');
});

test('existing candidate logs in seamlessly and updates google_id on google callback', function () {
    $existing = User::create([
        'name' => 'Existing Student',
        'email' => 'existing@campus.ac.id',
        'password' => 'secret123',
        'role' => 'student',
    ]);

    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-linked-112233');
    $socialiteUser->shouldReceive('getName')->andReturn('Existing Student');
    $socialiteUser->shouldReceive('getEmail')->andReturn('existing@campus.ac.id');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/existing-avatar.jpg');
    $socialiteUser->shouldReceive('getNickname')->andReturn(null);

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('student.dashboard'));
    $this->assertAuthenticatedAs($existing);

    $existing->refresh();
    expect($existing->google_id)->toBe('google-linked-112233');
    expect($existing->avatar)->toBe('https://lh3.googleusercontent.com/existing-avatar.jpg');
});

test('google callback handles errors or cancellation gracefully', function () {
    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andThrow(new Exception('Access denied'));

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('student.login'))
        ->assertSessionHas('error');
    $this->assertGuest();
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated guest accessing student private dashboard is redirected to student login', function () {
    $response = $this->get(route('student.dashboard'));

    $response->assertRedirect(route('student.login'));
});

test('unauthenticated guest accessing admin private dashboard is redirected to admin login', function () {
    $response = $this->get('/admin');

    $response->assertRedirect(route('admin.login'));
});

test('unauthenticated guest accessing root url is redirected to student login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('student.login'));
});

test('authenticated student cannot access auth pages and is redirected to student dashboard', function () {
    $student = User::create([
        'name' => 'Active Student',
        'email' => 'student@university.edu',
        'password' => 'secret123',
        'role' => 'student',
    ]);

    // Student Login Page
    $this->actingAs($student)->get(route('student.login'))
        ->assertRedirect(route('student.dashboard'));

    // Student Register Page
    $this->actingAs($student)->get(route('student.register'))
        ->assertRedirect(route('student.dashboard'));

    // Admin Login Page
    $this->actingAs($student)->get(route('admin.login'))
        ->assertRedirect(route('student.dashboard'));

    // Google OAuth Redirect
    $this->actingAs($student)->get(route('auth.google'))
        ->assertRedirect(route('student.dashboard'));

    // Root URL
    $this->actingAs($student)->get('/')
        ->assertRedirect(route('student.dashboard'));
});

test('authenticated admin cannot access auth pages and is redirected to admin dashboard', function () {
    $admin = User::create([
        'name' => 'Head Admin',
        'email' => 'admin@university.edu',
        'password' => 'secret123',
        'role' => 'admin',
    ]);

    // Admin Login Page
    $this->actingAs($admin)->get(route('admin.login'))
        ->assertRedirect(route('admin.dashboard'));

    // Student Login Page
    $this->actingAs($admin)->get(route('student.login'))
        ->assertRedirect(route('admin.dashboard'));

    // Student Register Page
    $this->actingAs($admin)->get(route('student.register'))
        ->assertRedirect(route('admin.dashboard'));

    // Google OAuth Redirect
    $this->actingAs($admin)->get(route('auth.google'))
        ->assertRedirect(route('admin.dashboard'));

    // Root URL
    $this->actingAs($admin)->get('/')
        ->assertRedirect(route('admin.dashboard'));
});

test('authenticated admin accessing student private dashboard is redirected to admin dashboard', function () {
    $admin = User::create([
        'name' => 'Head Admin 2',
        'email' => 'admin2@university.edu',
        'password' => 'secret123',
        'role' => 'admin',
    ]);

    $this->actingAs($admin)->get(route('student.dashboard'))
        ->assertRedirect(route('admin.dashboard'));
});

test('authenticated student private pages include strict back forward cache prevention headers', function () {
    $student = User::create([
        'name' => 'Candidate Student',
        'email' => 'candidate@university.edu',
        'password' => 'secret123',
        'role' => 'student',
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk();
    $cacheControl = (string) $response->headers->get('Cache-Control');

    expect($cacheControl)->toContain('no-cache')
        ->and($cacheControl)->toContain('no-store')
        ->and($cacheControl)->toContain('must-revalidate')
        ->and($response->headers->get('Pragma'))->toBe('no-cache')
        ->and($response->headers->get('Expires'))->toBe('Sun, 02 Jan 1990 00:00:00 GMT');
});

test('authenticated admin private pages include strict back forward cache prevention headers', function () {
    $admin = User::create([
        'name' => 'Security Admin',
        'email' => 'secadmin@university.edu',
        'password' => 'secret123',
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $cacheControl = (string) $response->headers->get('Cache-Control');

    expect($cacheControl)->toContain('no-cache')
        ->and($cacheControl)->toContain('no-store')
        ->and($cacheControl)->toContain('must-revalidate')
        ->and($response->headers->get('Pragma'))->toBe('no-cache')
        ->and($response->headers->get('Expires'))->toBe('Sun, 02 Jan 1990 00:00:00 GMT');
});

test('invalid student credentials flash toast error and do not attach validation error to email field', function () {
    $response = $this->from(route('student.login'))->post(route('student.login.submit'), [
        'email' => 'nonexistent@student.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertRedirect(route('student.login'));
    $response->assertSessionHas('error', 'The provided credentials do not match our records.');
    $response->assertSessionMissing('errors');
});

test('invalid admin credentials flash toast error and do not attach validation error to email field', function () {
    $response = $this->from(route('admin.login'))->post(route('admin.login.submit'), [
        'email' => 'nonexistent@admin.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertRedirect(route('admin.login'));
    $response->assertSessionHas('error', 'The provided credentials do not match our records.');
    $response->assertSessionMissing('errors');
});

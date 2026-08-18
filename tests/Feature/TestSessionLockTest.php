<?php

namespace Tests\Feature;

use App\Models\TestSession;
use App\Models\User;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestSessionLockTest extends TestCase
{
    use RefreshDatabase;
    public function test_locked_test_session_model_methods(): void
    {
        $futureSession = new TestSession([
            'title' => 'Future Exam',
            'is_active' => true,
            'starts_at' => now()->addHours(2),
            'ends_at' => now()->addHours(5),
        ]);

        $this->assertTrue($futureSession->isLocked());
        $this->assertFalse($futureSession->isOpen());
        $this->assertFalse($futureSession->isEnded());

        $openSession = new TestSession([
            'title' => 'Open Exam',
            'is_active' => true,
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addHours(1),
        ]);

        $this->assertFalse($openSession->isLocked());
        $this->assertTrue($openSession->isOpen());
        $this->assertFalse($openSession->isEnded());

        $endedSession = new TestSession([
            'title' => 'Ended Exam',
            'is_active' => true,
            'starts_at' => now()->subHours(5),
            'ends_at' => now()->subHours(1),
        ]);

        $this->assertFalse($endedSession->isLocked());
        $this->assertFalse($endedSession->isOpen());
        $this->assertTrue($endedSession->isEnded());
    }

    public function test_student_cannot_start_locked_exam(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $testSession = TestSession::create([
            'title' => 'Locked Test Schedule',
            'description' => 'Test lock',
            'is_active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($user)->post(route('exam.start', $testSession));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_student_can_see_locked_and_unlocked_tests_on_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $lockedSession = TestSession::create([
            'title' => 'Future Exam 2026',
            'description' => 'Will start later',
            'is_active' => true,
            'starts_at' => now()->addHours(5),
            'ends_at' => now()->addDays(2),
        ]);

        $openSession = TestSession::create([
            'title' => 'Ready Exam 2026',
            'description' => 'Can start now',
            'is_active' => true,
            'starts_at' => now()->subMinutes(10),
            'ends_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($user)->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('Future Exam 2026');
        $response->assertSee('Ready Exam 2026');
        $response->assertSee('Terkunci');
        $response->assertSee('Dibuka');
    }

    public function test_student_cannot_start_expired_exam(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $expiredSession = TestSession::create([
            'title' => 'Expired Exam',
            'description' => 'Ended yesterday',
            'is_active' => true,
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->post(route('exam.start', $expiredSession));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('error');
    }
}

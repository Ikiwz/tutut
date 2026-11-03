<?php

test('terms of service page is accessible and displays content', function () {
    $response = $this->get(route('terms'));

    $response->assertOk()
        ->assertSee('Terms of Service')
        ->assertSee('Acceptance of Terms')
        ->assertSee('Intellectual Property Rights');
});

test('privacy policy page is accessible and displays content', function () {
    $response = $this->get(route('privacy'));

    $response->assertOk()
        ->assertSee('Privacy Policy')
        ->assertSee('Information We Collect')
        ->assertSee('Data Storage');
});

test('exam rules and honor code page is accessible and displays section details', function () {
    $response = $this->get(route('rules'));

    $response->assertOk()
        ->assertSee('Official TOEFL Exam Rules')
        ->assertSee('Listening Comprehension')
        ->assertSee('Structure')
        ->assertSee('Reading Comprehension')
        ->assertSee('Strictly Prohibited Conduct');
});

test('faq and help center page is accessible and displays questions', function () {
    $response = $this->get(route('faq'));

    $response->assertOk()
        ->assertSee('Frequently Asked Questions')
        ->assertSee('Taking the TOEFL Exam')
        ->assertSee('Scoring');
});

test('candidate registration page contains valid links to terms and privacy', function () {
    $response = $this->get(route('student.register'));

    $response->assertOk()
        ->assertSee(route('terms'))
        ->assertSee(route('privacy'));
});

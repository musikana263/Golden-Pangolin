<?php

namespace Tests\Feature;

use App\Contact;
use App\Mail\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        config(['mail.contact_notify_to' => 'info@example.com']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name'         => 'Jane Traveller',
            'email'        => 'jane@example.com',
            'subject'      => 'Victoria Falls trip',
            'message'      => 'We would like a 5-day trip in March.',
            'form_started' => Crypt::encryptString((string) now()->subSeconds(30)->timestamp),
        ], $overrides);
    }

    public function test_human_submission_is_saved_and_emailed(): void
    {
        $this->post('/contact', $this->payload())->assertRedirect();

        $this->assertSame(1, Contact::count());
        Mail::assertSent(ContactSubmission::class);
    }

    public function test_honeypot_submission_is_dropped(): void
    {
        $this->post('/contact', $this->payload(['website' => 'http://spam.example']))
            ->assertSessionHas('success');

        $this->assertSame(0, Contact::count());
        Mail::assertNothingSent();
    }

    public function test_instant_submission_is_dropped(): void
    {
        $this->post('/contact', $this->payload([
            'form_started' => Crypt::encryptString((string) now()->timestamp),
        ]))->assertSessionHas('success');

        $this->assertSame(0, Contact::count());
        Mail::assertNothingSent();
    }

    public function test_missing_or_forged_timestamp_is_dropped(): void
    {
        $this->post('/contact', $this->payload(['form_started' => null]));
        $this->post('/contact', $this->payload(['form_started' => '1700000000']));

        $this->assertSame(0, Contact::count());
        Mail::assertNothingSent();
    }

    public function test_contact_page_includes_time_trap_field(): void
    {
        $this->get('/contact')->assertSee('name="form_started"', false);
    }
}

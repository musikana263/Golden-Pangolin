<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Mail\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function createForm(Request $request)
    {
        return view('contact');
    }

    public function ContactUs(Request $request)
    {
        // Honeypot — bots fill every field; humans never see this.
        if ($request->filled('website')) {
            return back()->with('success', 'We have received your message. Thank you for writing to us.');
        }

        $validated = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email:filter|max:180',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:4000',
        ]);

        // Explicit field whitelist — never `$request->all()` into a model.
        $contact = Contact::create($validated);

        // Notify staff. Wrapped so a flaky SMTP server never loses the
        // submission — it's already safely persisted above.
        $notify = config('mail.contact_notify_to') ?: config('mail.from.address');
        if ($notify) {
            try {
                Mail::to($notify)->send(new ContactSubmission($contact));
            } catch (\Throwable $e) {
                Log::warning('Contact form email failed', [
                    'contact_id' => $contact->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        session()->flash('success', 'We have received your message and would like to thank you for writing to us.');

        return back();
    }
}

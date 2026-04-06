<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Models\Setting;
use App\Mail\ContactReceived;
use App\Mail\ContactAutoReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Rate limiting: max 3 submissions per IP per 10 minutes
        $key = 'contact-form:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many submissions. Please wait {$seconds} seconds before trying again.",
            ]);
        }
        RateLimiter::hit($key, 600); // 10-minute window

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $inquiry = ContactInquiry::create($validated);

        // 1. Notify admin
        $adminEmail = Setting::get('contact_email', config('mail.from.address'));
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new ContactReceived($inquiry));
            } catch (\Exception $e) {
                Log::error("Failed to send admin contact email: " . $e->getMessage());
            }
        }

        // 2. Auto-reply to sender
        try {
            Mail::to($inquiry->email)->send(new ContactAutoReply($inquiry));
        } catch (\Exception $e) {
            Log::error("Failed to send auto-reply email: " . $e->getMessage());
        }

        return back()->with('success', 'Your message has been sent successfully! We\'ll be in touch soon.');
    }
}

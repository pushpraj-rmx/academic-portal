<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('public.contact');
    }

    public function store(ContactFormRequest $request): RedirectResponse
    {
        ContactSubmission::create($request->validated());

        return redirect()
            ->route('contact.index')
            ->with('status', 'Thank you for contacting us. We will get back to you soon.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    /**
     * Display the Terms of Service.
     */
    public function terms(): View
    {
        return view('legal.terms');
    }

    /**
     * Display the Privacy Policy.
     */
    public function privacy(): View
    {
        return view('legal.privacy');
    }

    /**
     * Display the Official Exam Rules & Honor Code.
     */
    public function rules(): View
    {
        return view('legal.rules');
    }

    /**
     * Display the Frequently Asked Questions & Help Center.
     */
    public function faq(): View
    {
        return view('legal.faq');
    }
}

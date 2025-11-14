<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;

class TwoFactorConfirmationController extends Controller
{
    /**
     * Show the two factor confirmation view.
     */
    public function show(): View
    {
        return view('auth.two-factor-confirm');
    }

    /**
     * Confirm the user's two factor authentication.
     */
    public function store(Request $request, ConfirmTwoFactorAuthentication $confirm): RedirectResponse
    {
        $confirm($request->user(), $request->input('code'));

        return redirect()->route('profile.edit')->with('status', 'two-factor-authentication-confirmed');
    }
}



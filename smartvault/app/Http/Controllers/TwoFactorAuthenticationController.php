<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable): RedirectResponse
    {
        if (! Features::canManageTwoFactorAuthentication()) {
            abort(403);
        }

        $enable($request->user());

        return back()->with('status', 'two-factor-authentication-enabled');
    }

    /**
     * Disable two factor authentication for the user.
     */
    public function destroy(Request $request, DisableTwoFactorAuthentication $disable): RedirectResponse
    {
        if (! Features::canManageTwoFactorAuthentication()) {
            abort(403);
        }

        $disable($request->user());

        return back()->with('status', 'two-factor-authentication-disabled');
    }
}



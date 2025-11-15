<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset request - sends new password directly.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $email = $request->session()->get('login_email');
        
        if (!$email) {
            return back()->withErrors(['email' => __('Please try logging in first, then click on forgot password.')]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $request->session()->forget('login_email');
            return back()->withErrors(['email' => __('We could not find a user with that email address.')]);
        }

        $newPassword = $this->generateSecurePassword();

        $user->forceFill([
            'password' => Hash::make($newPassword),
        ])->save();

        try {
            $userEmail = $user->email;
            
            Log::info('Attempting to send new password email to: ' . $userEmail);
            Log::info('Generated password for ' . $userEmail . ': ' . $newPassword);
            
            Mail::to($userEmail)->send(new NewPasswordMail($user, $newPassword));
            
            Log::info('New password email sent successfully to: ' . $userEmail);

            $request->session()->forget('login_email');

            return back()->with('status', __('A new password has been sent to :email. Please check your inbox.', ['email' => $userEmail]));
        } catch (\Exception $e) {
            Log::error('Failed to send new password email to ' . $user->email . ': ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            
            return back()->withErrors(['email' => __('Failed to send email to :email. Error: :error. Please check the logs or try again later.', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ])]);
        }
    }

    private function generateSecurePassword(): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $all = $lowercase . $uppercase . $numbers;
        
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        
        for ($i = 5; $i < 12; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        return str_shuffle($password);
    }
}

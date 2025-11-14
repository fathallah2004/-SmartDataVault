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
        // Get email from session (from failed login attempt)
        $email = $request->session()->get('login_email');
        
        if (!$email) {
            return back()->withErrors(['email' => __('Please try logging in first, then click on forgot password.')]);
        }

        // Find user by email - use the email from the database, not from session
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Clear invalid email from session
            $request->session()->forget('login_email');
            return back()->withErrors(['email' => __('We could not find a user with that email address.')]);
        }

        // Generate a secure random password (12 characters: letters, numbers, and special chars)
        $newPassword = $this->generateSecurePassword();

        // Update user's password
        $user->forceFill([
            'password' => Hash::make($newPassword),
        ])->save();

        // Send email with the new password to the user's email address from database
        try {
            $userEmail = $user->email; // Use the email from the database, not from session
            
            Log::info('Attempting to send new password email to: ' . $userEmail);
            Log::info('Generated password for ' . $userEmail . ': ' . $newPassword); // Log the password for debugging
            
            // Use Mailable class for better email handling
            Mail::to($userEmail)->send(new NewPasswordMail($user, $newPassword));
            
            Log::info('New password email sent successfully to: ' . $userEmail);

            // Clear email from session after successful send
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

    /**
     * Generate a secure random password.
     * Uses only alphanumeric characters to avoid copy/paste issues.
     */
    private function generateSecurePassword(): string
    {
        // Use only alphanumeric characters to avoid issues with special characters
        // when copying from email
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        
        // Remove special characters that can cause copy/paste issues
        $all = $lowercase . $uppercase . $numbers;
        
        $password = '';
        // Ensure at least one of each type
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        
        // Fill the rest (12 characters total)
        for ($i = 5; $i < 12; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        return str_shuffle($password);
    }
}

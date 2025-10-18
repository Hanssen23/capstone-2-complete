<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Member;

class MemberAuthController extends Controller
{
    public function showRegister()
    {
        return view('members.register');
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'middle_name' => 'nullable|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'last_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'age' => 'required|integer|min:1|max:120',
                'gender' => 'required|in:Male,Female,Other,Prefer not to say',
                'email' => 'required|email|unique:members,email,NULL,id,deleted_at,NULL|unique:users,email',
                'mobile_number' => 'required|string|regex:/^9\d{2}\s\d{3}\s\d{4}$/',
                'password' => 'required|min:6|confirmed',
                'accept_terms' => 'required|accepted',
            ], [
                'first_name.required' => 'First name is required for member registration',
                'first_name.regex' => 'First name must start with a capital letter and can only contain letters and spaces',
                'middle_name.regex' => 'Middle name must start with a capital letter and can only contain letters and spaces',
                'last_name.required' => 'Last name is required for member registration',
                'last_name.regex' => 'Last name must start with a capital letter and can only contain letters and spaces',
                'age.integer' => 'Age must be a valid number',
                'age.min' => 'Age must be at least 1',
                'age.max' => 'Age must not exceed 120',
                'gender.in' => 'Please select a valid gender option',
                'email.required' => 'Email is required for member registration',
                'email.email' => 'Please enter a valid email address',
                'email.unique' => 'This email is already registered',
                'mobile_number.required' => 'Mobile number is required for member registration',
                'mobile_number.regex' => 'Please enter a valid 10-digit Philippine mobile number (e.g., 912 345 6789)',
                'password.required' => 'Password is required for member registration',
                'password.min' => 'Password must be at least 6 characters for member registration',
                'password.confirmed' => 'Password confirmation does not match',
                'accept_terms.required' => 'You must accept the Terms and Conditions to register',
                'accept_terms.accepted' => 'You must accept the Terms and Conditions to register',
            ]);

            // Clean mobile number (remove spaces and add +63 prefix)
            $mobileNumber = '+63' . preg_replace('/\D/', '', $validated['mobile_number']);

            // Check UID pool status and generate more if needed
            $availableCount = \App\Models\UidPool::where('status', 'available')->count();
            if ($availableCount < 10) {
                \Log::warning('UID pool running low', ['available_count' => $availableCount]);
                try {
                    \App\Models\UidPool::generateNewUids(100); // Generate more UIDs
                    \Log::info('Generated new UIDs due to low pool', ['new_count' => 100]);
                } catch (\Exception $e) {
                    \Log::error('Failed to generate new UIDs', ['error' => $e->getMessage()]);
                }
            }

            // Get an available UID from the pool
            $availableUid = Member::getAvailableUid();

            if (!$availableUid) {
                \Log::error('No UIDs available in pool during registration', [
                    'email' => $validated['email'],
                    'available_count' => \App\Models\UidPool::where('status', 'available')->count()
                ]);
                return redirect()->back()->with('error', 'Registration system temporarily unavailable. Please try again in a few minutes or contact support.')->withInput();
            }

            try {
                $payload = [
                    'uid' => $availableUid,
                    'member_number' => Member::generateMemberNumber(),
                    'membership' => null, // No plan assigned initially
                    'subscription_status' => 'not_subscribed',
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'age' => $validated['age'],
                    'gender' => $validated['gender'],
                    'mobile_number' => $mobileNumber,
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'status' => 'inactive', // Set to inactive until email is verified
                    'role' => 'member', // Explicitly assign member role
                    'accepted_terms_at' => now(), // Record terms acceptance timestamp
                    'email_verified_at' => null, // Email not verified yet
                ];

                $member = Member::create($payload);

                \Log::info('Member created successfully', [
                    'member_id' => $member->id,
                    'email' => $member->email,
                    'uid' => $member->uid,
                    'member_number' => $member->member_number
                ]);

                // Send email verification notification
                try {
                    $member->sendEmailVerificationNotification();
                    \Log::info('Email verification sent successfully', ['member_id' => $member->id]);
                } catch (\Exception $e) {
                    \Log::warning('Email verification failed to send', [
                        'member_id' => $member->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail registration if email sending fails
                    return redirect()->route('member.verification.notice')->with('success', 'Registration successful! However, the verification email failed to send. Please contact support to verify your account.');
                }

            } catch (\Exception $e) {
                // If member creation fails, return the UID to the pool
                Member::returnUidToPool($availableUid);
                \Log::error('Member creation failed, UID returned to pool', [
                    'uid' => $availableUid,
                    'error' => $e->getMessage()
                ]);
                throw $e; // Re-throw the exception to be caught by outer catch block
            }

            // Do NOT auto-login after registration for security
            // Redirect back to registration page with success message to trigger modal
            return redirect()->route('member.register.show')->with('success', 'Registration successful! Please check your email to verify your account and complete the process.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return back with validation errors
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Log the error with detailed information for debugging
            \Log::error('Registration error: ' . $e->getMessage(), [
                'email' => $validated['email'] ?? 'unknown',
                'first_name' => $validated['first_name'] ?? 'unknown',
                'last_name' => $validated['last_name'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Provide more specific error messages based on the error type
            $errorMessage = 'Registration failed due to a technical issue. Please contact support if this continues.';

            // Check for specific database constraint violations first
            if (str_contains($e->getMessage(), 'duplicate') || str_contains($e->getMessage(), 'unique') || str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'email')) {
                    $errorMessage = 'This email address is already registered. Please use a different email or try logging in.';
                } elseif (str_contains($e->getMessage(), 'mobile')) {
                    $errorMessage = 'This mobile number is already registered. Please use a different mobile number.';
                } else {
                    $errorMessage = 'This information is already registered. Please use different information or contact support.';
                }
            } elseif (str_contains($e->getMessage(), 'uid') || str_contains($e->getMessage(), 'UID')) {
                $errorMessage = 'Registration system temporarily unavailable. Please try again in a few minutes.';
            } elseif (str_contains($e->getMessage(), 'database') || str_contains($e->getMessage(), 'connection')) {
                $errorMessage = 'Database connection issue. Please try again in a moment.';
            } elseif (str_contains($e->getMessage(), 'verification') || str_contains($e->getMessage(), 'notification')) {
                $errorMessage = 'Registration successful, but email verification failed to send. Please contact support to verify your account.';
            } elseif (str_contains($e->getMessage(), 'mobile') || str_contains($e->getMessage(), 'phone')) {
                $errorMessage = 'Invalid mobile number format. Please use the format: 9XX XXX XXXX (e.g., 912 345 6789).';
            } elseif (str_contains($e->getMessage(), 'sendEmailVerificationNotification')) {
                $errorMessage = 'Registration successful, but email verification failed to send. Please contact support to verify your account.';
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }
}



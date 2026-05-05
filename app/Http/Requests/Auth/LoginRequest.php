<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        try {
            \Log::info('LoginRequest: Starting authentication', ['email' => $this->email]);
            
            $this->ensureIsNotRateLimited();
            
            \Log::info('LoginRequest: Rate limit check passed');
            
            if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
                \Log::info('LoginRequest: Authentication failed');
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
            
            \Log::info('LoginRequest: Authentication successful');
            RateLimiter::clear($this->throttleKey());
        } catch (\Exception $e) {
            \Log::error('LoginRequest: Exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        try {
            \Log::info('LoginRequest: Checking rate limit', ['key' => $this->throttleKey()]);
            
            if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
                \Log::info('LoginRequest: Rate limit OK');
                return;
            }

            \Log::info('LoginRequest: Rate limit exceeded', ['attempts' => RateLimiter::attempts($this->throttleKey())]);
            
            event(new Lockout($this));

            $seconds = RateLimiter::availableIn($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        } catch (\Exception $e) {
            \Log::error('LoginRequest: Rate limit exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}

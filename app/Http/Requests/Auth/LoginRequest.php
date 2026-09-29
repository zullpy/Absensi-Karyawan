<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Custom error messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username atau nomor HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Differentiate between wrong password, wrong username/no_hp, or both wrong.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $username = $this->string('username')->value();
        $password = $this->string('password')->value();

        // 1. Cari user berdasarkan username atau no_hp
        $user = User::where('username', $username)
                    ->orWhere('no_hp', $username)
                    ->first();

        if ($user) {
            // User ditemukan, cek kecocokan password
            if (Hash::check($password, $user->password)) {
                Auth::login($user, $this->boolean('remember'));
                RateLimiter::clear($this->throttleKey());
                return;
            }

            // User benar, tetapi password salah
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'login_failed' => 'Kata sandi salah.',
                'password' => 'Kata sandi yang Anda masukkan salah.',
            ]);
        }

        // 2. User tidak ditemukan di database
        RateLimiter::hit($this->throttleKey());

        // Cek apakah password cocok dengan salah satu akun yang ada di sistem
        $passwordMatchesAny = User::all()->contains(function ($u) use ($password) {
            return Hash::check($password, $u->password);
        });

        if ($passwordMatchesAny) {
            // Password cocok dengan akun lain, berarti hanya username/no_hp yang salah
            throw ValidationException::withMessages([
                'login_failed' => 'Username atau nomor HP salah.',
                'username' => 'Username atau nomor HP tidak terdaftar.',
            ]);
        }

        // 3. Keduanya salah (Username/no HP tidak terdaftar dan password juga tidak cocok dengan akun manapun)
        throw ValidationException::withMessages([
            'login_failed' => 'Username/nomor HP dan kata sandi salah.',
            'username' => 'Username atau nomor HP salah.',
            'password' => 'Kata sandi salah.',
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')) . '|' . $this->ip());
    }
}

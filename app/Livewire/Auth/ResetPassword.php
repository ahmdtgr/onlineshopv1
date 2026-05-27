<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;

class ResetPassword extends Component
{
    public $token;
    public $email;
    public $password = '';
    public $password_confirmation = '';
    public $showPassword = false;
    public $showPasswordConfirmation = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    protected $messages = [
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'password.required' => 'Password baru wajib diisi',
        'password.min' => 'Password minimal 8 karakter',
        'password.confirmed' => 'Konfirmasi password tidak sama',
        'password_confirmation.required' => 'Konfirmasi password wajib diisi',
    ];

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function togglePasswordConfirmation()
    {
        $this->showPasswordConfirmation = !$this->showPasswordConfirmation;
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->dispatch('showAlert', [
                'message' => 'Password berhasil direset. Silakan login dengan password baru Anda.',
                'type' => 'success'
            ]);

            return redirect()->route('login');
        }

        $this->addError('email', __($status));
    }

    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('components.layouts.app', [
                'hideBottomNav' => true,
                'seoTitle' => 'Reset Password',
                'seoDescription' => 'Buat password baru untuk akun Anda',
                'seoRobots' => 'noindex, nofollow',
            ]);
    }
}

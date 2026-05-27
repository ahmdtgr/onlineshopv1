<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;

class ForgotPassword extends Component
{
    public $email = '';
    public $emailSent = false;

    protected $rules = [
        'email' => 'required|email|exists:users,email',
    ];

    protected $messages = [
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.exists' => 'Email tidak terdaftar dalam sistem',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function sendResetLink()
    {
        $this->validate();

        $status = Password::sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->emailSent = true;
            $this->dispatch('showAlert', [
                'message' => 'Link reset password telah dikirim ke email Anda',
                'type' => 'success'
            ]);
        } else {
            $this->addError('email', 'Gagal mengirim link reset password. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.app', [
                'hideBottomNav' => true,
                'seoTitle' => 'Lupa Password',
                'seoDescription' => 'Reset password akun Anda',
                'seoRobots' => 'noindex, nofollow',
            ]);
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Store;

class Profile extends Component
{
    public $name;
    public $email;
    public $whatsapp;

    public function render()
    {
        return view('livewire.profile')
            ->layout('components.layouts.app', [
                'seoTitle' => 'Profil Saya',
                'seoDescription' => 'Pengaturan profil akun Anda',
                'seoRobots' => 'noindex, nofollow',
            ]);
    }

    public $isAdmin;

    public function mount()
    {
        $user = auth()->user();
        $this->whatsapp = Store::first()->whatsapp;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->isAdmin = (bool) $user->is_admin;
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('home');
    }
}

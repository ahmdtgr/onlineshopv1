<?php

namespace App\Livewire;

use App\Models\Store;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class ChangelogPage extends Component
{
    public $store;

    public array $changelog = [];

    public function mount(): void
    {
        $this->store = Store::first() ?? new Store([
            'name' => 'Toko Online',
            'description' => 'Belanja online mudah dan aman',
            'primary_color' => '#ff6666',
            'secondary_color' => '#818CF8',
        ]);

        $this->changelog = $this->loadChangelogFromJson();
    }

    private function loadChangelogFromJson(): array
    {
        $path = resource_path('data/changelog.json');

        if (!File::exists($path)) {
            return [];
        }

        $content = File::get($path);
        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function badgeClass(string $type): string
    {
        return match ($type) {
            'new' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'improved' => 'bg-blue-50 text-blue-700 border-blue-200',
            'fixed' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    public function badgeLabel(string $type): string
    {
        return match ($type) {
            'new' => 'New',
            'improved' => 'Improved',
            'fixed' => 'Fixed',
            default => ucfirst($type),
        };
    }

    public function render()
    {
        return view('livewire.changelog-page')
            ->layout('components.layouts.app', [
                'seoTitle' => 'Changelog',
                'seoDescription' => 'Riwayat pembaruan fitur, perbaikan, dan peningkatan terbaru.',
                'seoKeywords' => 'changelog, update, release notes, pembaruan aplikasi',
                'hideBottomNavMobile' => true,
            ]);
    }
}

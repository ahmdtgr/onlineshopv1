<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;

class StoreShow extends Component
{
    public $store;
    public $categories;
    public $selectedCategory = 'all';
    public $products;
    public $search = '';

    public function render()
    {
        if (!$this->store) {
            return view('livewire.coming-soon')
                ->layout('components.layouts.app', ['hideBottomNav' => true]);;
        }

        $seoJsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Store',
            'name' => $this->store->name,
            'description' => $this->store->description ?? '',
            'url' => url('/'),
            'image' => $this->store->imageUrl ?? asset('image/store.png'),
            'address' => $this->store->address ?? '',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return view('livewire.store-show')
            ->layout('components.layouts.app', [
                'hideBottomNavMobile' => true,
                'seoTitle' => null,
                'seoDescription' => $this->store->description ?? 'Belanja online mudah, aman, dan terpercaya',
                'seoKeywords' => ($this->store->name ?? 'toko online') . ', belanja online, fashion, pakaian, toko online terpercaya',
                'seoImage' => $this->store->imageUrl ?? asset('image/store.png'),
                'seoJsonLd' => $seoJsonLd,
            ]);
    }

    public function mount()
    {
        $this->store = Store::first();
        
        if ($this->store) {
            $this->categories = Category::get();
            $this->loadProducts();
        }
    }

    public function loadProducts()
    {
        $query = Product::query();

        if ($this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        $this->products = $query->get();
    }

    public function setCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->loadProducts();
    }

    public function updatedSearch()
    {
        $this->loadProducts();
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Livewire\WithFileUploads;

class PaymentConfirmationPage extends Component
{
    use WithFileUploads;
    public $order;

    public $payment_proof;
    public $uploadedImagePath;

    protected $rules = [
        'payment_proof' => 'required|image|max:2048',
    ];

    protected $messages = [
        'payment_proof.required' => 'Upload bukti transfer',
        'payment_proof.image' => 'File harus berupa gambar',
        'payment_proof.max' => 'Ukuran file maksimal 2MB',
    ];

    public function mount($orderNumber)
    {
        $this->order = Order::where('order_number', $orderNumber)->firstOrFail();
    }

    public function updatedPaymentProof()
    {
        $this->validate([
            'payment_proof' => 'image|max:2048'
        ]);

        // Upload immediately when file selected
        if ($this->payment_proof) {
            $this->uploadedImagePath = $this->payment_proof->store('payment-proofs', 'public');
        }
    }

    public function submit()
    {
        if (!$this->uploadedImagePath) {
            session()->flash('error', 'Silakan upload bukti pembayaran terlebih dahulu');
            return;
        }

        try {
            $this->order->update([
                'payment_proof' => $this->uploadedImagePath,
                'payment_status' => 'paid',
                'status' => 'awaiting_confirmation',
            ]);

            session()->flash('message', 'Bukti pembayaran berhasil diunggah');
            return redirect()->route('orders');

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.payment-confirmation')
            ->layout('components.layouts.app', [
                'hideBottomNav' => true,
                'seoTitle' => 'Konfirmasi Pembayaran',
                'seoDescription' => 'Konfirmasi pembayaran pesanan #' . $this->order->order_number,
                'seoRobots' => 'noindex, nofollow',
            ]);
    }
}

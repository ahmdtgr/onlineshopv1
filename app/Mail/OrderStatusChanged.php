<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Services\OrderStatusService;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $status;
    public $statusLabel;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
        $this->statusLabel = OrderStatusService::getStatusLabel($status);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update Status Pesanan #' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-changed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

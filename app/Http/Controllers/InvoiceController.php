<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        $order->load(['items.product', 'user']);
        $store = $order->store;
        
        $pdf = PDF::loadView('invoices.template', [
            'order' => $order,
            'store' => $store,
            'date' => Carbon::now()->format('d M Y'),
        ]);
        
        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    public function preview(Order $order)
    {
        $order->load(['items.product', 'user']);
        $store = $order->store;
        
        $pdf = PDF::loadView('invoices.template', [
            'order' => $order,
            'store' => $store,
            'date' => Carbon::now()->format('d M Y'),
        ]);
        
        return $pdf->stream("invoice-{$order->order_number}.pdf");
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::query()
            ->with('customer')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('admin.orders.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.orders.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::query()
            ->with(['customer', 'items.product'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::query()->with('customer')->findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'delivery_type' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $originalStatus = $order->status;
        $order->update($data);

        if ($originalStatus !== $order->status && $order->customer) {
            app(WhatsAppService::class)->sendText(
                $order->customer->phone,
                "Atualização do Pedido #{$order->id}: {$order->status}"
            );
        }

        return redirect()->route('admin.orders.show', $order->id)
            ->with('status', 'Pedido atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('admin.orders.index');
    }
}

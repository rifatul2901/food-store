<?php

namespace App\Livewire\Web\Cart;

use App\Models\Cart;
use Livewire\Component;

class Index extends Component
{
    /**
     * Fungsi untuk menambah jumlah quantity
     */
    public function increment($cart_id)
    {
        $cart = Cart::find($cart_id);

        if ($cart) {
            $cart->update([
                'qty' => $cart->qty + 1
            ]);
            
            // Memberikan feedback (opsional, bisa pakai toast/alert)
            session()->flash('message', 'Jumlah berhasil ditambah.');
        }
    }

    /**
     * Fungsi untuk mengurangi jumlah quantity
     */
    public function decrement($cart_id)
    {
        $cart = Cart::find($cart_id);

        if ($cart) {
            if ($cart->qty > 1) {
                $cart->update([
                    'qty' => $cart->qty - 1
                ]);
            } else {
                // Jika qty sudah 1 dan dikurangi lagi, hapus dari keranjang
                $cart->delete();
            }
            
            session()->flash('message', 'Jumlah berhasil dikurangi.');
        }
    }

    /**
     * Fungsi untuk menghapus item dari keranjang
     */
    public function destroy($cart_id)
    {
        $cart = Cart::find($cart_id);
        if ($cart) {
            $cart->delete();
        }
    }

    public function render()
    {
        // 1. Get carts yang memiliki produk (menghindari error property of non-object)
        $carts = Cart::query()
            ->with('product')
            ->whereHas('product') 
            ->where('customer_id', auth()->guard('customer')->user()->id)
            ->latest()
            ->get();

        // 2. Menghitung total berat (Quantity * Berat Produk)
        $totalWeight = $carts->sum(function ($cart) {
            return $cart->product->weight * $cart->qty;
        });

        // 3. Menghitung total harga (Quantity * Harga Produk)
        $totalPrice = $carts->sum(function ($cart) {
            return $cart->product->price * $cart->qty;
        });

        return view('livewire.web.cart.index', compact('carts', 'totalWeight', 'totalPrice'));
    }
}
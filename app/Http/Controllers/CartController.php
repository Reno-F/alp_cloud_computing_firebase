<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CheckoutMail;
use App\Services\FirebaseService;

class CartController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function add(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Get user ID from session
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to add items to cart');
        }

        // Get product from Firebase
        $product = $this->firebaseService->getProductById($id);
        if (!$product) {
            return redirect()->route('catalog')->with('error', 'Product not found');
        }

        // Update session cart
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $validated['quantity'];
        } else {
            $cart[$id] = [
                'product_id' => $id,
                'quantity' => $validated['quantity'],
                'name' => $product['name'],
                'price' => $product['price'],
            ];
        }
        session()->put('cart', $cart);

        // Update Firebase cart data
        $database = $this->firebaseService->getDatabase();
        $cartRef = $database->getReference('shopping_carts/' . $userId . '/' . $id);
        $existingCart = $cartRef->getSnapshot()->getValue();

        if ($existingCart) {
            $newQuantity = $existingCart['quantity'] + $validated['quantity'];
            $cartRef->update([
                'quantity' => $newQuantity
            ]);
        } else {
            $cartRef->set([
                'product_id' => $id,
                'quantity' => $validated['quantity'],
                'name' => $product['name'],
                'price' => $product['price'],
            ]);
        }

        return redirect()->route('catalog')->with('success', 'Product successfully added to your cart!');
    }

    public function decrement(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to update your cart');
        }

        if (isset($cart[$id])) {
            // Update session
            if ($cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity'] -= 1;
                session()->put('cart', $cart);

                // Update Firebase
                $cartRef = $this->firebaseService->getDatabase()->getReference('shopping_carts/' . $userId . '/' . $id);
                $cartRef->update([
                    'quantity' => $cart[$id]['quantity']
                ]);
            } else {
                unset($cart[$id]);
                session()->put('cart', $cart);

                // Remove from Firebase
                $this->firebaseService->getDatabase()->getReference('shopping_carts/' . $userId . '/' . $id)->remove();
            }

            return redirect()->back()->with('success', 'Cart updated successfully!');
        }

        return redirect()->back()->with('error', 'Product not found in cart!');
    }

    public function increment(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to update your cart');
        }

        if (isset($cart[$id])) {
            // Update session
            $cart[$id]['quantity'] += 1;
            session()->put('cart', $cart);

            // Update Firebase
            $cartRef = $this->firebaseService->getDatabase()->getReference('shopping_carts/' . $userId . '/' . $id);
            $cartRef->update([
                'quantity' => $cart[$id]['quantity']
            ]);

            return redirect()->back()->with('success', 'Cart updated successfully!');
        }

        return redirect()->back()->with('error', 'Product not found in cart!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to remove items from cart');
        }

        if (isset($cart[$id])) {
            // Update session
            unset($cart[$id]);
            session()->put('cart', $cart);

            // Update Firebase
            $this->firebaseService->getDatabase()->getReference('shopping_carts/' . $userId . '/' . $id)->remove();

            return redirect()->back()->with('success', 'Product removed from cart!');
        }

        return redirect()->back()->with('error', 'Product not found in cart!');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (!$cart) {
            return redirect()->route('catalog')->with('error', 'Your cart is empty!');
        }

        $total = 0;
        foreach ($cart as $details) {
            $total += $details['price'] * $details['quantity'];
        }

        // Send email notification
        try {
            Mail::to('renovansetio02@gmail.com')->send(new CheckoutMail($cart, $total));
        } catch (\Exception $e) {
            \Log::error('Failed to send checkout email: ' . $e->getMessage());
            // Continue even if email fails
        }

        return view('checkout.checkout', compact('cart', 'total'));
    }

    // Add this method to process the checkout
    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        $userId = session('user_id');

        if (!$cart) {
            return redirect()->route('catalog')->with('error', 'Your cart is empty!');
        }

        // Calculate total
        $total = 0;
        foreach ($cart as $details) {
            $total += $details['price'] * $details['quantity'];
        }

        // Save order to Firebase
        $orderData = [
            'user_id' => $userId,
            'items' => $cart,
            'total' => $total,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $newOrderRef = $this->firebaseService->getDatabase()->getReference('orders')->push();
        $newOrderRef->set($orderData);

        // Clear cart after order
        session()->forget('cart');
        $this->firebaseService->getDatabase()->getReference('shopping_carts/' . $userId)->remove();

        return redirect()->route('catalog')->with('success', 'Your order has been placed successfully!');
    }
}

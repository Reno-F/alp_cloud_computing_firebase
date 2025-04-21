<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\File;
use Kreait\Firebase\Factory;

class ProductController extends Controller
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(public_path('firebase/cloud-computing-alp-firebase-adminsdk-fbsvc-38ebc4e74e.json'))
            ->withDatabaseUri('https://cloud-computing-alp-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
    }

    public function create(Request $request)
{
    // Pastikan user sudah login
    if (!session()->has('user_id')) {
        return redirect()->route('login');
    }

    if ($request->isMethod('get')) {
        $product = null;
        return view('product.form', compact('product'));
    }

    // Validasi input
    $data = $request->validate([
        'name' => ['required'],
        'description' => [],
        'price' => ['required', 'numeric', 'min:0'],
        'images' => ['required'],
        'images.*' => [File::image()->max(5 * 1024)]
    ]);

    // Menyimpan produk dengan menambahkan user_id
    $productRef = $this->database->getReference('products')->push([
        'name' => $data['name'],
        'description' => $data['description'] ?? '',
        'price' => $data['price'],
        'user_id' => session('user_id'), // Menambahkan user_id yang membuat produk
    ]);

    $productId = $productRef->getKey();

    // Menyimpan gambar produk
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;

            $file->move(public_path('storage/product'), $filename);

            // Menyimpan gambar produk di Firebase
            $this->database->getReference("product_images/{$productId}")->push([
                'name' => $filename
            ]);
        }
    }

    return redirect()->route('catalog')->with('success', 'Product created successfully');
}


public function edit(string $id, Request $request)
{
    $product = $this->database->getReference("products/{$id}")->getValue();
    if (!$product) {
        return redirect()->route('catalog')->with('error', 'Product not found.');
    }

    // Mendapatkan gambar produk yang sudah ada
    $productImages = $this->database->getReference("product_images/{$id}")->getValue();

    // Menambahkan gambar produk ke dalam data produk
    $product['images'] = $productImages ?? [];

    if ($request->isMethod('post')) {
        $data = $request->validate([
            'name' => ['required'],
            'description' => [],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        // Update produk di Firebase
        $this->database->getReference("products/{$id}")->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'],
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Simpan gambar baru (jika ada)
        if ($request->has('images')) {
            foreach ($request->file('images') as $file) {
                $extension = $file->getClientOriginalExtension();
                $filename = uniqid() . '.' . $extension;

                $file->move('storage/product', $filename);

                // Store image name in Firebase
                $this->database->getReference("product_images/{$id}")->push([
                    'name' => $filename
                ]);
            }
        }

        return redirect()->route('catalog-detail', ['id' => $id])->with('success', 'Product updated successfully.');
    }

    return view('product.form', [
        'product' => (object) $product
    ]);
}



    public function delete(string $id)
    {
        $product = $this->database->getReference("products/{$id}")->getValue();

        // Pastikan user yang login adalah pemilik produk
        if (session('user_id') != $product['user_id']) {
            return redirect()->route('catalog')->withErrors('You do not have permission to delete this product.');
        }

        // Hapus gambar produk
        $imagesRef = $this->database->getReference("product_images/{$id}");
        $images = $imagesRef->getValue();

        if ($images) {
            foreach ($images as $key => $image) {
                @unlink(public_path('storage/product/' . $image['name']));
            }
            $imagesRef->remove();
        }

        // Hapus produk dari Firebase
        $this->database->getReference("products/{$id}")->remove();

        return redirect('/')->with('success', 'Product deleted successfully.');
    }

}

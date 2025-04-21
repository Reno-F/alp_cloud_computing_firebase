<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    protected $firebaseService;

    // Construct with FirebaseService injection
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }


public function list(Request $request)
{
    $data['search'] = $request->search;
    $data['sort'] = $request->sort ?? 'most recent';

    // Mendapatkan data produk dari Firebase
    $productsData = $this->firebaseService->getProducts();

    // Pastikan $productsData adalah array yang valid
    $products = is_array($productsData) ? $productsData : [];

    if (empty($products)) {
        $data['products'] = []; // Pastikan selalu ada key 'products'
        return view('home', $data);
    }

    // Filtering berdasarkan keyword pencarian
    if ($data['search']) {
        $products = array_filter($products, function ($product) use ($data) {
            return stripos($product['name'], $data['search']) !== false ||
                   stripos($product['description'] ?? '', $data['search']) !== false;
        });
    }

   // Sorting produk sesuai dengan parameter sort
switch ($data['sort']) {
    case 'most recent':
        usort($products, function ($a, $b) {
            return strtotime(($b['created_at'] ?? '') ?: '0') - strtotime(($a['created_at'] ?? '') ?: '0');
        });
        break;
    case 'lowest price':
        usort($products, function ($a, $b) {
            return ($a['price'] ?? 0) - ($b['price'] ?? 0);
        });
        break;
    case 'highest price':
        usort($products, function ($a, $b) {
            return ($b['price'] ?? 0) - ($a['price'] ?? 0);
        });
        break;
    case 'name a-z':
        usort($products, function ($a, $b) {
            return strcmp($a['name'] ?? '', $b['name'] ?? '');
        });
        break;
    case 'name z-a':
        usort($products, function ($a, $b) {
            return strcmp($b['name'] ?? '', $a['name'] ?? '');
        });
        break;
}

// Jangan kirim array asosiatif, kirim array numerik
$data['products'] = array_values($products);

    return view('home', $data);
}


public function detail(string $id)
{
    $product = $this->firebaseService->getProductById($id);
    if (!$product) {
        return redirect()->route('catalog')->withErrors('Product not found.');
    }

    $reviewsRef = $this->firebaseService->getReviews();

    $productReviews = array_filter($reviewsRef, function ($review) use ($id) {
        return $review['product_id'] === $id;
    });

    $averageRating = 0;
    $totalRating = 0;
    $totalReviews = count($productReviews);

    if ($totalReviews > 0) {
        foreach ($productReviews as $review) {
            $totalRating += $review['rating'];
        }
        $averageRating = $totalRating / $totalReviews;
    }

    return view('detail', [
        'productId' => $id,
        'product' => $product,
        'reviews' => $productReviews,
        'averageRating' => $averageRating
    ]);
}


}

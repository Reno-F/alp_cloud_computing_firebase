<?php
namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;
class FirebaseService
{
    protected $auth;
    protected $database;

    public function __construct()
    {
        try {
            // Coba beberapa path yang mungkin
            $paths = [
                public_path('firebase/cloud-computing-alp-firebase-adminsdk-fbsvc-38ebc4e74e.json'),
                base_path('public/firebase/cloud-computing-alp-firebase-adminsdk-fbsvc-38ebc4e74e.json'),
                storage_path('app/firebase/cloud-computing-alp-firebase-adminsdk-fbsvc-b86050d660.json')
            ];

            $credentialsPath = null;
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $credentialsPath = $path;
                    break;
                }
            }

            if (!$credentialsPath) {
                throw new \Exception("Firebase credentials file not found in any of the checked paths");
            }

            // Debug informasi
            \Log::info('Firebase Credentials Path: ' . $credentialsPath);
            \Log::info('File Exists: ' . (file_exists($credentialsPath) ? 'Yes' : 'No'));
            \Log::info('File Readable: ' . (is_readable($credentialsPath) ? 'Yes' : 'No'));

            // Baca isi file untuk memastikan
            $fileContents = file_get_contents($credentialsPath);
            if (empty($fileContents)) {
                throw new \Exception("Firebase credentials file is empty");
            }

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath)
                ->withDatabaseUri('https://cloud-computing-alp-default-rtdb.firebaseio.com');

            $this->auth = $factory->createAuth();
            $this->database = $factory->createDatabase();
        } catch (\Exception $e) {
            // Log error secara detail
            \Log::error('Firebase initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function signup($email, $password, $name)
    {
        try {
            $userProperties = [
                'email' => $email,
                'emailVerified' => false,
                'password' => $password,
                'displayName' => $name,
                'disabled' => false,
            ];

            return $this->auth->createUser($userProperties);
        } catch (\Exception $e) {
            \Log::error('Firebase signup error: ' . $e->getMessage());
            throw $e;
        }
    }

// Menambahkan getter untuk database
public function getDatabase()
{
    return $this->database;
}
public function getProducts()
    {
        try {
            $productsReference = $this->database->getReference('products');
            $snapshot = $productsReference->getSnapshot();
            $products = $snapshot->getValue() ?: []; // Convert null to empty array

            // Log products as array
            \Log::debug('Data Produk Firebase:', is_array($products) ? $products : []);

            if (is_array($products) && !empty($products)) {
                // Loop setiap produk, tambahkan gambar ke dalamnya
                foreach ($products as $productId => &$product) {
                    $imagesReference = $this->database->getReference('product_images/' . $productId);
                    $imagesSnapshot = $imagesReference->getSnapshot();
                    $images = $imagesSnapshot->getValue();

                    // Kalau ada, simpan array gambarnya
                    if ($images) {
                        $product['images'] = array_values($images); // biar urut index angka
                    } else {
                        $product['images'] = []; // kosongin kalau ga ada
                    }

                    // Tambahkan ID ke dalam data produk juga
                    $product['id'] = $productId;
                }
            }

            return is_array($products) ? $products : [];
        } catch (\Exception $e) {
            \Log::error('Failed to get products: ' . $e->getMessage());
            return [];
        }
    }

    public function getProductById($id)
{
    try {
        $productReference = $this->database->getReference('products/' . $id);
        $snapshot = $productReference->getSnapshot();
        $product = $snapshot->getValue();

        if ($product === null) {
            return null;
        }

        // Tambahkan ID ke dalam data produk
        $product['id'] = $id;

        // Ambil gambar produk dari Firebase
        $imagesReference = $this->database->getReference('product_images/' . $id);
        $imagesSnapshot = $imagesReference->getSnapshot();
        $images = $imagesSnapshot->getValue();

        if ($images) {
            $product['images'] = array_values($images);
        }

        return $product;
    } catch (\Exception $e) {
        \Log::error('Failed to get product by ID: ' . $e->getMessage());
        return null;
    }
}

public function getReviews()
{
    try {
        $reviewsReference = $this->database->getReference('reviews');
        $snapshot = $reviewsReference->getSnapshot();
        $reviews = $snapshot->getValue();

        if (is_array($reviews)) {
            // Masukkan ID review
            foreach ($reviews as $reviewId => &$review) {
                $review['id'] = $reviewId;
            }
            return $reviews;
        }
        return [];
    } catch (\Exception $e) {
        \Log::error('Failed to get reviews: ' . $e->getMessage());
        return [];
    }
}


}


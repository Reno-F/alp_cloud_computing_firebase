<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Services\FirebaseService;

class ReviewController extends Controller
{
    protected $database;
    protected $firebaseService;
    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(public_path('firebase/cloud-computing-alp-firebase-adminsdk-fbsvc-38ebc4e74e.json'))
            ->withDatabaseUri('https://cloud-computing-alp-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
    }

    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $newReviewRef = $this->database->getReference('reviews')->push([
            'user_id' => session('user_id'),
            'user_name' => session('user_name'), // simpan nama user
            'product_id' => $productId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'created_at' => now()->toDateTimeString(),
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }


    public function update(Request $request, $reviewId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $this->database->getReference('reviews/' . $reviewId)->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'updated_at' => now()->toDateTimeString(),
        ]);

        return redirect()->back()->with('success', 'Review updated successfully!');
    }

    public function destroy($reviewId)
    {
        $this->database->getReference('reviews/' . $reviewId)->remove();
        return redirect()->back()->with('success', 'Review deleted successfully!');
    }
    public function edit(string $reviewId)
    {
        $review = $this->database->getReference("reviews/{$reviewId}")->getValue();
        if (!$review) {
            return response()->json(['error' => 'Review not found.'], 404);
        }

        return response()->json($review);
    }
}

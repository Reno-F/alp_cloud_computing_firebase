<x-template>
    <div class="mb-3">
        <a href="{{ route('catalog') }}" class="btn btn-secondary">Back</a>
        @if(session('user_id') && isset($product['user_id']) && session('user_id') == $product['user_id'])
        <div class="product-actions">
            <a href="{{ route('product-edit', ['id' => $productId]) }}" class="btn btn-warning">Edit Product</a>
            <form action="{{ route('product-delete', ['id' => $productId]) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete Product</button>
            </form>
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-5">
            <section>
                <div id="carouselImage" class="carousel slide">
                    <div class="carousel-inner">
                        @php $images = $product['images'] ?? []; @endphp
                        @forelse($images as $idx => $image)
                        <div class="carousel-item {{ $idx == 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/product/'.$image['name']) }}" class="d-block" style="max-height:500px">
                        </div>
                        @empty
                        <div class="carousel-item active">
                            <div class="bg-light d-block" style="max-height:500px"></div>
                        </div>
                        @endforelse
                    </div>
                    @if(count($images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselImage" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselImage" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    @endif
                </div>
            </section>
        </div>

        <div class="col-lg-7">
            <div class="mt-3">
                <section>
                    <h4>Average Rating:
                        @if($averageRating > 0)
                            {{ number_format($averageRating, 1) }} / 5
                        @else
                            No rating yet
                        @endif
                    </h4>
                </section>

                <form class="my-4" method="post" action="{{ route('cart-add', ['id' => $productId]) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add to cart</button>
                </form>

                <section>
                    <div class="fw-semibold mb-2">Description</div>
                    <p>{{ $product['description'] ?? 'No description available' }}</p>
                </section>

                <section>
                    <h4>Reviews</h4>

                    @php
                        $userReviews = collect($reviews)->where('user_id', session('user_id'))->where('product_id', $productId);
                    @endphp

                    @forelse($reviews as $review)
                    <div class="review mb-3 border-bottom pb-2">
                        <strong>{{ $review['user_name'] ?? 'Anonymous' }}</strong>
                        <p>{{ $review['comment'] }}</p>
                        <div>Rating:
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star {{ $i <= $review['rating'] ? 'text-warning' : 'text-secondary' }}">★</span>
                            @endfor
                        </div>

                        @if(session()->has('user_id') && $review['user_id'] === session('user_id'))
                        <div class="review-actions mt-2">
                            <button class="btn btn-sm btn-warning" onclick="editReview('{{ $review['id'] }}')">Edit</button>
                            <form action="{{ route('reviews.destroy', $review['id']) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @empty
                    <p>No reviews for this product yet.</p>
                    @endforelse

                    @if(session()->has('user_id'))
                    <!-- Form Tambah Review -->
                    <form id="addReviewForm" action="{{ route('reviews.store', $productId) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="form-group mb-2">
                            <label>Rating</label>
                            <select name="rating" class="form-control">
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} Star</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Comment</label>
                            <textarea name="comment" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>

                    <!-- Form Edit Review (Hidden) -->
                    <form id="editReviewForm" method="POST" style="display:none" class="mt-3">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-2">
                            <label>Rating</label>
                            <select name="rating" id="editRating" class="form-control">
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} Star</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Comment</label>
                            <textarea name="comment" id="editComment" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Update Review</button>
                        <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Cancel</button>
                    </form>
                    @else
                    <p class="text-muted">Login untuk menulis review.</p>
                    @endif
                </section>
            </div>
        </div>
    </div>

    <script>
        function editReview(reviewId) {
            fetch(`/reviews/${reviewId}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editReviewForm').style.display = 'block';
                    document.getElementById('addReviewForm').style.display = 'none';
                    document.getElementById('editRating').value = data.rating;
                    document.getElementById('editComment').value = data.comment;
                    document.getElementById('editReviewForm').action = `/reviews/${reviewId}`;
                });
        }

        function cancelEdit() {
            document.getElementById('editReviewForm').style.display = 'none';
            document.getElementById('addReviewForm').style.display = 'block';
        }
    </script>
</x-template>

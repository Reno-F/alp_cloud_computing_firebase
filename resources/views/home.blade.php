<x-template>
    <div class="mb-3 d-md-flex align-items-center">
        <div class="flex-grow-1 py-2">
            @if($search)
                Found {{ count($products) }} products that match keyword <b>{{ $search }}</b>
            @else
                We have {{ count($products) }} products in our catalog
            @endif
        </div>
        <div class="">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Sort by: {{ $sort }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('catalog', ['search' => $search, 'sort' => 'most recent']) }}">Most recent</a></li>
                    <li><a class="dropdown-item" href="{{ route('catalog', ['search' => $search, 'sort' => 'lowest price']) }}">Lowest price</a></li>
                    <li><a class="dropdown-item" href="{{ route('catalog', ['search' => $search, 'sort' => 'highest price']) }}">Highest price</a></li>
                    <li><a class="dropdown-item" href="{{ route('catalog', ['search' => $search, 'sort' => 'name a-z']) }}">Name A-Z</a></li>
                    <li><a class="dropdown-item" href="{{ route('catalog', ['search' => $search, 'sort' => 'name z-a']) }}">Name Z-A</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        @forelse($products as $productId => $product)
            <div class="col-md-6 col-lg-3">
                <div class="card mb-3" style="height: 400px">
                    <a href="{{ route('catalog-detail', ['id' => $product['id']]) }}" class="btn btn-light p-0 border-0 text-start">
                        @if (!empty($product['images']))
                            <img src="{{ asset('storage/product/'.$product['images'][0]['name']) }}" class="card-img-top" style="height:250px" alt="...">
                        @else
                            <div class="card-img-top bg-light" style="height:250px"></div>
                        @endif
                        <div class="card-body">
                            <div class="card-title">{{ $product['name'] }}</div>
                            <div class="text-danger fw-bold">Rp {{ number_format($product['price'], 0, ',', '.') }}</div>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No products found.
                    @if($search)
                        Try a different search keyword.
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    @if(session()->has('user_id'))
    <div class="position-fixed end-0 bottom-0 pe-3 pb-3">
        <a href="{{ route('product-create') }}" class="btn btn-success">
            <i class="fa fa-plus"></i>
            Add product
        </a>
    </div>
    @endif
</x-template>

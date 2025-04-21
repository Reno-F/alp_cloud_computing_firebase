<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>e-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('catalog') }}">SHOPEDIA</a>
            <a class="nav-link" href="{{ route('about') }}" style="margin-right: 15px;">About</a>
            <a class="nav-link" href="{{ route('contact') }}">Contact</a>
            <div class="flex-grow-1 px-5 d-none d-lg-block">
                <form class="input-group" role="search" action="{{ route('catalog') }}">
                    <input class="form-control bg-light" type="search" placeholder="Type to search..." aria-label="Search" name="search">
                    <button class="btn btn-light border" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="d-flex">
                @if (session()->has('user_id'))
                    <div class="dropdown">
                        <a href="#" class="btn position-relative" id="cartDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0 }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cartDropdown" style="min-width: 300px;">
                            <li><h6>Your Cart</h6></li>
                            @if(session('cart'))
                                @foreach(session('cart') as $id => $details)
                                    <li class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-bold">{{ $details['name'] }} ({{ $details['quantity'] }} pcs)</span><br>
                                                <form action="{{ route('cart-decrement', $id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning mt-1">-</button>
                                                </form>
                                                <form action="{{ route('cart-increment', $id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success mt-1">+</button>
                                                </form>
                                                <form action="{{ route('cart-remove', $id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger mt-1">Remove</button>
                                                </form>
                                            </div>
                                            <div>
                                                <span>Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a href="{{ route('checkout') }}" class="btn btn-primary btn-sm w-100">Checkout</a></li>
                            @else
                                <li><p class="text-center">Your cart is empty</p></li>
                            @endif
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Sign out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                @endif
            </div>
        </div>
        <div class="container-fluid py-2 d-block d-lg-none">
            <form class="input-group" role="search" action="{{ route('catalog') }}">
                <input class="form-control bg-light" type="search" placeholder="Type to search..." aria-label="Search" name="search">
                <button class="btn btn-light border" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
    </nav>

    <div class="container py-3">

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    {{ session('info') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
        {{ $slot }}
    </div>
</body>
</html>

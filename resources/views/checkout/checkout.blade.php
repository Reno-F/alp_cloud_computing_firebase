<x-template>
    <div class="container mt-5">
        <h2>Checkout</h2>
        @if(session('cart'))
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @foreach(session('cart') as $id => $details)
                            @php
                                $total += $details['price'] * $details['quantity'];
                            @endphp
                            <tr>
                                <td>{{ $details['name'] }}</td>
                                <td>{{ $details['quantity'] }}</td>
                                <td>Rp {{ number_format($details['price'], 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                            <td><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('catalog') }}" class="btn btn-secondary me-2">Continue Shopping</a>
                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                </form>
            </div>
        @else
            <p>Your cart is empty.</p>
            <a href="{{ route('catalog') }}" class="btn btn-primary">Back to Catalog</a>
        @endif
    </div>
    </x-template>

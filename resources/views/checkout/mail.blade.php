<x-template>
    <div>
        <h2>Order Confirmation</h2>
        <p>Thank you for your order. Here are the details:</p>
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
                    @foreach($cart as $id => $details)
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
    </div>
    </x-template>

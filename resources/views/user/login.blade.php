<x-template>
    <div class="d-flex justify-content-center">
        <div class="card" style="width:500px">
            <div class="card-header">Log In</div>
            <div class="card-body">

                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="post" class="was-validated">
                    @csrf
                    <div class="mb-3">
                        <label>Email address</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100">Log in</button>
                    </div>
                    <a href="{{ route('signup') }}" class="btn btn-link w-100">Sign up</a>
                </form>
            </div>
        </div>
    </div>
</x-template>

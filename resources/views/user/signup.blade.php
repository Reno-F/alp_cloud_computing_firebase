<x-template>
    <div class="d-flex justify-content-center">
        <div class="card" style="width:500px">
            <div class="card-header">Sign Up</div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="post" class="was-validated">
                    @csrf
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Email address</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100">Sign up</button>
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-link w-100">Log in</a>
                </form>
            </div>
        </div>
    </div>
</x-template>

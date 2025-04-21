<?php
// app/Http/Middleware/CheckLogin.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLogin
{
    public function handle(Request $request, Closure $next)
    {
        // Debug logging
        \Log::info('CheckLogin Middleware', [
            'path' => $request->path(),
            'user_id' => session('user_id'),
            'full_session' => session()->all()
        ]);

        if (!session()->has('user_id')) {
            \Log::warning('Redirecting to login', [
                'intended_url' => $request->fullUrl()
            ]);
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }
        return $next($request);
    }
}

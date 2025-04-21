<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use App\Services\FirebaseService;

class UserController extends Controller
{
    protected $auth;
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(public_path('firebase/cloud-computing-alp-firebase-adminsdk-fbsvc-38ebc4e74e.json'))
            ->withDatabaseUri('https://cloud-computing-alp-default-rtdb.firebaseio.com');

        $this->auth = $factory->createAuth();
        $this->database = $factory->createDatabase();
    }

    public function signup(Request $request)
{
    if ($request->isMethod('get')) {
        return view('user.signup');
    }

    $data = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'confirmed', 'min:8'],
        'name' => ['required'],
    ]);

    try {
        // Langsung simpan ke Realtime Database
        $newUserRef = $this->database->getReference('users')->push([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']), // Enkripsi password
            'created_at' => now()->toDateTimeString()
        ]);

        return redirect()->route('login')->with('success', 'User created successfully.');
    } catch (\Exception $e) {
        return back()->withInput()->withErrors(['email' => 'Failed to create new user: ' . $e->getMessage()]);
    }
}

public function login(Request $request)
{
    if ($request->isMethod('post')) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            // Cari user di Realtime Database
            $usersReference = $this->database->getReference('users');
            $users = $usersReference->getValue();

            // Cari user yang sesuai
            $matchedUser = null;
            foreach ($users as $userId => $user) {
                if ($user['email'] === $credentials['email']) {
                    $matchedUser = $user;
                    $matchedUser['id'] = $userId;
                    break;
                }
            }

            // Verifikasi password
            if ($matchedUser && password_verify($credentials['password'], $matchedUser['password'])) {
                // Simpan user ke session
                session([
                    'user_id' => $matchedUser['id'],
                    'user_name' => $matchedUser['name'],
                    'email' => $matchedUser['email']
                ]);

                return redirect()->route('catalog');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Login failed: ' . $e->getMessage(),
            ])->onlyInput('email');
        }
    }

    return view('user.login');
}

    // Logout User
    public function logout(Request $request)
    {
        // Logout user by clearing session
        $request->session()->flush();
        return redirect()->route('login');
    }
}

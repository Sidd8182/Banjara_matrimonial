<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AdminController extends Controller
{
    private const ALLOWED_ADMIN_ROLES = ['admin', 'super_admin'];

    public function showLogin()
    {
        return Inertia::render('Admin/Login', [
            'loginAction' => url(trim(config('app.super_admin_path'), '/') . '/login'),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid admin credentials.'],
            ]);
        }

        if (!in_array($user->role, self::ALLOWED_ADMIN_ROLES, true)) {
            throw ValidationException::withMessages([
                'email' => ['This account does not have admin access.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $profileCompletedUsers = User::where('profile_completion_step', '>=', 5)->count();

        return Inertia::render('Admin/Dashboard', [
            'logoutAction' => url(trim(config('app.super_admin_path'), '/') . '/logout'),
            'stats' => [
                'totalUsers' => $totalUsers,
                'verifiedUsers' => $verifiedUsers,
                'profileCompletedUsers' => $profileCompletedUsers,
                'maleUsers' => User::where('gender', 'Male')->count(),
                'femaleUsers' => User::where('gender', 'Female')->count(),
                'adminUsers' => User::whereIn('role', self::ALLOWED_ADMIN_ROLES)->count(),
            ],
            'recentUsers' => User::select('id', 'name', 'email', 'gender', 'role', 'created_at')
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

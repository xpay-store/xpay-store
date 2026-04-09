<?php

namespace App\Http\Controllers\AdminUi;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('admin_authed') === true) {
            return redirect('/admin/dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:500'],
        ]);

        $expected = config('app.admin_api_token');
        if (! is_string($expected) || $expected === '') {
            return back()->withErrors(['token' => 'Admin token is not configured on server.'])->withInput();
        }

        $provided = (string) $data['token'];
        if (! hash_equals($expected, $provided)) {
            return back()->withErrors(['token' => 'Invalid token.'])->withInput();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('admin_authed', true);

        return redirect('/admin/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authed');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin');
    }
}


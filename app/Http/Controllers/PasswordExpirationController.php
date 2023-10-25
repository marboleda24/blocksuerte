<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordExpiredRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class PasswordExpirationController extends Controller
{
    /**
     * @return Response
     */
    public function password_expired(): Response
    {
        return Inertia::render('Auth/PasswordExpired', [
            'username' => Session::get('user_reset_password'),
        ]);
    }

    /**
     * @param  PasswordExpiredRequest  $request
     * @return RedirectResponse
     */
    public function store(PasswordExpiredRequest $request): RedirectResponse
    {
        // Checking current password
        if (! Hash::check($request->current_password, $request->user()->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is not correct']);
        }

        $request->user()->update([
            'password' => bcrypt($request->password),
            'password_changed_at' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->back()->with(['status' => 'Password changed successfully']);
    }
}

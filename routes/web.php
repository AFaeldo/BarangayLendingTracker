<?php

use App\Http\Controllers\ResidentController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ItemController;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Root redirect
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ==========================
// REGISTER (FIRST USER ONLY)
// ==========================

// GET register page
Route::get('/register', function () {
    if (User::count() > 0) {
        return redirect()->route('login');
    }
    return view('Account.Register');
})->name('register')->middleware('guest');

// POST register (create first user)
Route::post('/register', function (Request $request) {
    if (User::count() > 0) {
        abort(403, 'Registration is closed.');
    }

    $data = $request->validate([
        'name'     => ['required', 'string'],
        'username' => ['required', 'string', 'unique:users,username'],
        'email'    => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'min:6', 'confirmed'],
    ]);

    $user = User::create([
        'name'     => $data['name'],
        'username' => $data['username'],
        'email'    => $data['email'],
        'password' => Hash::make($data['password']), // Ensure password is hashed
    ]);

    Auth::login($user);

    return redirect()->route('dashboard');
})->name('register.store')->middleware('guest');


// ==========================
// LOGIN
// ==========================

// GET login page
Route::get('/login', function () {
    // Force first user creation if none exist
    if (User::count() === 0) {
        return redirect()->route('register');
    }

    return view('Account.Login');
})->name('login')->middleware('guest');

// POST login
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'login' => 'Invalid username or password.',
    ])->onlyInput('username');
})->name('login.attempt')->middleware('guest');

// ==========================
// PASSWORD RESET
// ==========================

// GET forgot password
Route::get('/forgot-password', function () {
    return view('Account.ForgotPassword');
})->name('forgot.password')->middleware('guest');

// POST forgot password (send code)
Route::post('/forgot-password', function (Request $request) {
    $data = $request->validate(['email' => ['required','email']]);

    $user = User::where('email', $data['email'])->first();

    if ($user) {
        $code = (string) random_int(100000, 999999);
        $user->reset_code = $code;
        $user->reset_code_expires_at = now()->addMinutes(15);
        $user->save();

        Mail::raw(
            "Hello {$user->name},\n\nYour password reset code is: {$code}\n\nThis code will expire in 15 minutes.",
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password reset code - Brgy. San Antonio Lending Tracker');
            }
        );
    }

    return redirect()->route('password.reset.code.form')
        ->with('status', 'A reset code has been sent to your email if it exists in our system.');
})->name('forgot.password.send')->middleware('guest');

// GET reset password form
Route::get('/reset-password', function () {
    return view('Account.ResetPasswordCode');
})->name('password.reset.code.form')->middleware('guest');

// POST reset password (verify code & update)
Route::post('/reset-password', function (Request $request) {
    $data = $request->validate([
        'email' => ['required','email'],
        'code' => ['required','string'],
        'password' => ['required','min:6','confirmed'],
    ]);

    $user = User::where('email', $data['email'])->first();

    if (!$user || !$user->reset_code) {
        return back()->withErrors(['code' => 'Invalid reset code or email.'])->withInput();
    }

    if ($user->reset_code !== $data['code'] ||
        ($user->reset_code_expires_at && $user->reset_code_expires_at->isPast())
    ) {
        return back()->withErrors(['code' => 'Reset code is invalid or has expired.'])->withInput();
    }

    $user->password = Hash::make($data['password']);
    $user->reset_code = null;
    $user->reset_code_expires_at = null;
    $user->save();

    return redirect()->route('login')->with('status', 'Password has been reset. You can now log in.');
})->name('password.reset.code.update')->middleware('guest');

// ==========================
// LOGOUT
// ==========================
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout')->middleware('auth');

// ==========================
// PROTECTED ROUTES
// ==========================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('LendingTracker.Dashboard');
    })->name('dashboard');

    // Borrower Records
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::post('/borrowing/{borrowing}/return', [BorrowingController::class, 'markReturned'])
    ->name('borrowing.return');

    // Return Tracking
    Route::get('/return-tracking', function () {
        return view('LendingTracker.ReturnTracking');
    })->name('return-tracking.index');

    // Residents
    Route::get('/residents', [ResidentController::class, 'index'])->name('residents.index');
    Route::post('/residents', [ResidentController::class, 'store'])->name('residents.store');
    Route::put('/residents/{resident}', [ResidentController::class, 'update'])->name('residents.update');
    Route::delete('/residents/{resident}', [ResidentController::class, 'destroy'])->name('residents.destroy');

    // Items
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    // Reports
    Route::get('/reports', function () {
        return view('LendingTracker.Reports');
    })->name('reports.index');
});

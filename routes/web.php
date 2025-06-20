<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Hash;



// Temporary route to generate a bcrypt hash (Remove after use!)
Route::get('/generate-hash/{password?}', function ($password = '123456') {
    return [
        'password' => $password,
        'hash' => Hash::make($password),
    ];
});


// Public routes
// Route::get('/', function () {
//     return view('welcome');
// });


// Redirect root URL to /home
Route::redirect('/', '/home');

// Show welcome view at /home
Route::get('/home', function () {
    return view('welcome');
})->name('home');

// Public complaint routes
Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
Route::get('/complaints/history', [ComplaintController::class, 'history'])->name('complaints.history');
Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Complaint routes
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{complaint}/edit', [ComplaintController::class, 'edit'])->name('complaints.edit');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('complaints.update');
    Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');
    Route::post('/complaints/{complaint}/assign', [ComplaintController::class, 'assign'])->name('complaints.assign');
    Route::post('/complaints/{complaint}/resolve', [ComplaintController::class, 'resolve'])->name('complaints.resolve');
    Route::post('/complaints/{complaint}/revert', [ComplaintController::class, 'revert'])->name('complaints.revert');
    Route::post('/complaints/{complaint}/comment', [ComplaintController::class, 'comment'])->name('complaints.comment');

    // API routes for dynamic content
    Route::get('/api/assignable-users', [ComplaintController::class, 'getAssignableUsers'])->name('api.assignable-users');
});

    // Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::resource('users', UserController::class);
    Route::get('/create-user', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/create-user', [AuthController::class, 'register']);


require __DIR__ . '/auth.php';

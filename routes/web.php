<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;




// Redirect root URL to /home
Route::redirect('/', '/home');

// Show welcome view at /home
Route::get('/home', function () {
    // Get public IP (check for proxy headers first)
    $publicIp = request()->header('X-Forwarded-For') ?? request()->ip();
    // If X-Forwarded-For has multiple IPs, take the first one
    if (strpos($publicIp, ',') !== false) {
        $publicIp = trim(explode(',', $publicIp)[0]);
    }
    return view('welcome', ['user_ip' => $publicIp]);
})->name('home');

// Public ticket routes
Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');

// History route is public but protected by IP
Route::get('/complaints/history', [ComplaintController::class, 'history'])
    ->name('complaints.history')
    ->middleware(\App\Http\Middleware\CheckIPAccess::class);

Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');

// Authentication routes
Route::middleware('guest')->group(function () {
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
    Route::get('/complaints/data', [ComplaintController::class, 'data'])->name('complaints.data');
    Route::get('/complaints/{complaint}/edit', [ComplaintController::class, 'edit'])->name('complaints.edit');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('complaints.update');
    Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');
    Route::post('/complaints/{complaint}/assign', [ComplaintController::class, 'assign'])->name('complaints.assign');
    Route::post('/complaints/{complaint}/resolve', [ComplaintController::class, 'resolve'])->name('complaints.resolve');
    Route::post('/complaints/{complaint}/revert', [ComplaintController::class, 'revert'])->name('complaints.revert');
    Route::post('/complaints/{complaint}/comment', [ComplaintController::class, 'comment'])->name('complaints.comment');

    // API routes for dynamic content
    Route::get('/api/assignable-users', [ComplaintController::class, 'getAssignableUsers'])->name('api.assignable-users');

    Route::resource('users', UserController::class);
});

Route::get('/api/complaints/lookup', [App\Http\Controllers\ComplaintController::class, 'lookup'])->name('api.complaints.lookup');
Route::get('/complaints/track', [App\Http\Controllers\ComplaintController::class, 'track'])->name('complaints.track');

// Redirect any GET /login access to /home
Route::get('/login', function () {
    return redirect('/home');
});
// Redirect any GET or POST /register access to /home
Route::match(['get', 'post'], '/register', function () {
    return redirect('/home');
});


Route::get('/myip', function() {
    return request()->ip();
});



require __DIR__ . '/auth.php';

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SlideShowController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    if (Auth::user()->role == 'admin') {
        return redirect()->route('dashboard');
    } else {
        return Redirect::to('http://localhost:8080/');
    }
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'verified', config('jetstream.auth_session'), 'verified', 'isAdmin'])->group(function () {
    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('admin')->group(function () {
        //Region
        Route::resource('city', CityController::class);

        //User Control
        Route::resource('user', UserController::class);
        Route::post('user/role_change', [UserController::class, 'role_change'])->name('user.role_change');

        //Slideshows
        Route::resource('slideshow', SlideShowController::class);

        //Category
        Route::resource('category', CategoryController::class);

        //Report
        Route::resource('report', ReportController::class)->only(['index', 'show', 'destroy']);
    });
    Route::get('/clear',function(){
        Artisan::call("cache:clear");
        Artisan::call("config:cache");
        Artisan::call("route:clear");
        Artisan::call("view:clear");
        echo "<h3> Cache Cleared </h3>";
    });
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

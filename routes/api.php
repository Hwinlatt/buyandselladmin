<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DefaultApiController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LikeAndCommentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('register', [AuthController::class, 'register']); //name,email,password,confirmed_password,region,city
Route::post('login', [AuthController::class, 'login']);

Route::get('default/api', [DefaultApiController::class, 'defaultApi']);
Route::post('user/reset_password', [UserController::class, 'resetPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('home/view', [HomeController::class, 'index']);
    Route::post('home/view/for_you', [HomeController::class, 'for_you']);

    Route::get('email/verification-notification', [AuthController::class, 'sentVerification']);
    Route::post('email/verification-notification', [AuthController::class, 'makeVerification']);
    Route::post('logout', [AuthController::class, 'logout']);
    //User
    Route::prefix('user')->group(function () {
        Route::get('', function (Request $request) {
            if ($request->user()->role == 'suspend') {
                abort(403,'Unauthorized');
            }
            return response()->json($request->user(), 200);
        })->name('user.info');
        Route::post('update', [UserController::class, 'update']);
        Route::post('update/profileImage', [UserController::class, 'updateImage']);
        Route::post('update/coverImage', [UserController::class, 'updateCoverImg']);
        Route::post('update/password', [UserController::class, 'updatePassword']);

        // Make Login Session on Messenger
        Route::post('messenger', [UserController::class, 'messenger']);
    });
    //View Profile
    Route::prefix('profile')->group(function () {
        Route::get('view/{id}', [ProfileController::class, 'index']);
        Route::get('view/review/{id}', [ProfileController::class, 'show_review']);
        Route::post('add/review', [ProfileController::class, 'add_review']); //rate_user_id,rating,description
        Route::get('remove/review/{id}', [ProfileController::class, 'remove_review']); //rate_user_id
        Route::get('follow/{id}', [ProfileController::class, 'follow']); //make follow and un_follow
    });
    //Posts Controls
    Route::prefix('posts')->group(function () {
        Route::get('info/{id}', [PostController::class, 'show']);
        Route::get('category/{id}', [PostController::class, 'post_by_category']);
        Route::get('search/{key}', [PostController::class, 'search']);
        Route::get('user/{id}/{limit}', [PostController::class, 'post_by_user']);
    });
    //Like And Comment
    Route::prefix('like')->group(function () {
        Route::get('like_unlike/{id}', [LikeAndCommentController::class, 'like_unlike']); //post_id
        Route::get('count', [LikeAndCommentController::class, 'like_count']);
        Route::get('byUser/get', [LikeAndCommentController::class, 'like_byUser_get']);
        Route::get('who_like/{id}', [LikeAndCommentController::class, 'who_like']); //post_id
    });
    Route::prefix('comment')->group(function () {
        Route::get('get/{id}', [LikeAndCommentController::class, 'get_comment']); //post_id
        Route::post('add', [LikeAndCommentController::class, 'add_comment']); //post_id,user_id,description
        Route::get('delete/{id}', [LikeAndCommentController::class, 'delete_comment']); //id
    });
    //Report to Admin
    Route::prefix('report')->group(function () {
        Route::post('store', [ReportController::class, 'store']);
    });
    //Notification
    Route::prefix('notification')->group(function () {
        Route::get('', [NotificationController::class, 'index']);
        Route::get('count', [NotificationController::class, 'count']);
        Route::get('all_read', [NotificationController::class, 'all_read']);
        Route::post('destroy', [NotificationController::class, 'destroy']);
    });

});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::prefix('post')->group(function () {
        Route::post('add', [PostController::class, 'store']);
        Route::get('my_posts', [PostController::class, 'index']);
        Route::get('edit/{id}', [PostController::class, 'edit']);
        Route::post('update/{id}', [PostController::class, 'update']);
        Route::get('delete/{id}', [PostController::class, 'destroy']);
        Route::get('soldout/{id}', [PostController::class, 'soldout']);
        Route::get('resold/{id}', [PostController::class, 'resold']);
    });
});

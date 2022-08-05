<?php

use App\Models\User;
use App\Http\Livewire\Issues;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider within a group which | contains the "web" middleware group. Now create something great! | */

Route::get('/', function () {
    return view('welcome');
});
Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::get('/issues/{repo}', Issues::class)->name('issues');
    // Route::get('/issues/{repo}',  [\App\Http\Controllers\HomeController::class, 'getIssues'])->name('issues');
});



Route::get('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
});

Route::get('/auth/callback', function () {
    $githubUser = Socialite::driver('github')->user();

    // dd($githubUser);
    $user = User::updateOrCreate([
        'github_id' => $githubUser->id,
    ], [
        'name' => empty($githubUser->name) ? $githubUser->nickname : $githubUser->name,
        'github_nickname' => $githubUser->nickname,
        'email' => $githubUser->email,
        'github_token' => $githubUser->token,
        'github_refresh_token' => $githubUser->refreshToken,
    ]);
    Auth::login($user);
    return redirect('/dashboard');
});

Route::githubWebhooks('github-webhooks');


require __DIR__ . '/auth.php';

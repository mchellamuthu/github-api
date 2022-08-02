<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider within a group which | contains the "web" middleware group. Now create something great! | */

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // $githubUser = Socialite::driver('github')->user();
    $client = new \GuzzleHttp\Client();
    $options = [
        'Authorization'=>'token '.auth()->user()->github_token,
        'Accept'=>'application/vnd.github+json"'
    ];
    $response = $client->request('GET', 'https://api.github.com/repos/mchellamuthu/dexterapi/issues',$options);
    $response = json_decode($response->getBody()->getContents());

    dd($response);
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');



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
        'email' => $githubUser->email,
        'github_token' => $githubUser->token,
        'github_refresh_token' => $githubUser->refreshToken,
    ]);
    Auth::login($user);
    return redirect('/dashboard');
// $user->token
});
require __DIR__ . '/auth.php';

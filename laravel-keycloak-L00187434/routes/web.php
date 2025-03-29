<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
Route::get('/auth/redirect', [AuthController::class, 'redirectToKeycloak']);
Route::get('/auth/callback', [AuthController::class, 'handleKeycloakCallback']);
// routes/web.php
Route::get('/auth/redirect', function() {
    return Socialite::driver('keycloak')
        ->stateless()
        ->redirect();
});

Route::get('/auth/callback', function(\Illuminate\Http\Request $request) {
    if (!$request->has('code')) {
        // Debug what Keycloak actually returned
        logger()->error('Missing code in callback', [
            'full_url' => $request->fullUrl(),
            'params' => $request->all()
        ]);
        return redirect('/login')->withErrors('Authorization failed: no code received');
    }

    try {
        $user = Socialite::driver('keycloak')
            ->stateless()
            ->user();
        // Handle user authentication...
    } catch (\Exception $e) {
        logger()->error('Keycloak error', ['error' => $e->getMessage()]);
        return redirect('/login')->withErrors('Login failed');
    }
});
// routes/web.php
Route::get('/debug-auth', function() {
    $url = Socialite::driver('keycloak')
        ->stateless()
        ->redirect()
        ->getTargetUrl();
    
    return response()->json([
        'auth_url' => $url,
        'expected_params' => ['code', 'session_state'],
        'check' => [
            'has_redirect_uri' => str_contains($url, 'redirect_uri='.urlencode('http://localhost:8000/auth/callback')),
            'has_code_challenge' => str_contains($url, 'code_challenge=')
        ]
    ]);
});
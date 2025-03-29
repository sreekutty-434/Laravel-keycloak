<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;


class AuthController extends Controller
{
    public function redirectToKeycloak()
    {
        return Socialite::driver('keycloak')->redirect();
    }


public function handleKeycloakCallback(Request $request)
{
    \Log::debug('Full callback request:', [
        'url' => $request->fullUrl(),
        'headers' => $request->headers->all(),
        'server' => $request->server->all()
    ]);

    if ($request->missing('code')) {
        return redirect('/login')->withErrors([
            'auth' => 'Authentication failed: '.$request->input('error', 'No error code').' - '.
                     $request->input('error_description', 'No description provided')
        ]);
    }
   

    if (!$request->has('code')) {
        return redirect('/login')->withErrors([
            'auth' => 'Authorization code missing. Keycloak returned: ' . 
                     $request->input('error', 'No error parameter') . 
                     ' - ' . 
                     $request->input('error_description', 'No description')
        ]);
    }

    try {
        $user = Socialite::driver('keycloak')
            ->stateless()
            ->user();
            
        // Handle user creation/login...
        
    } catch (\Exception $e) {
        \Log::error('Keycloak authentication failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect('/login')->withErrors('Authentication failed');
    }
}
 
}

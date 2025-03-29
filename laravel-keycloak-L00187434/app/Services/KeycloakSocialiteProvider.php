<?php
namespace App\Services;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class KeycloakSocialiteProvider extends AbstractProvider
{
    protected $scopes = ['openid'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            config('services.keycloak.auth_url'),
            $state
        );
    }

    protected function getTokenUrl()
    {
        return config('services.keycloak.token_url');
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            config('services.keycloak.user_url'),
            ['headers' => ['Authorization' => 'Bearer ' . $token]]
        );
        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['sub'],
            'name' => $user['name'] ?? $user['preferred_username'],
            'email' => $user['email'],
        ]);
    }
}
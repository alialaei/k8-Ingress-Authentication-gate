<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginUser
{
    public function handle(string $email, string $password): Response
    {
        return app()->handle(
            Request::create('/oauth/token', 'POST', [
                'grant_type' => 'password',
                'client_id' => config('passport.password_client_id'),
                'client_secret' => config('passport.password_client_secret'),
                'username' => $email,
                'password' => $password,
                'scope' => '*',
            ])
        );
    }
}
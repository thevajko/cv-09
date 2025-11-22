<?php

namespace App\Auth;

use App\Models\User;
use Framework\Auth\DummyAuthenticator;
use Framework\Core\IIdentity;

class SimpleAuthenticator extends DummyAuthenticator
{
    protected function authenticate(string $username, string $password): ?IIdentity
    {
        // User is authenticated when username equals password (demo logic)
        if ($username !== '' && $username === $password) {
            return new User($username);
        }
        return null;
    }
}
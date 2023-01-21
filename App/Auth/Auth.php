<?php

namespace App\Auth;

class Auth
{
    protected function gen_token()
    {
        return bin2hex(random_bytes(32));
    }
}
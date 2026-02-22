<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Http\Request;

class RedirectToCentralLogin extends FilamentAuthenticate
{
    protected function redirectTo(Request $request): ?string
    {
        return route('login');
    }
}

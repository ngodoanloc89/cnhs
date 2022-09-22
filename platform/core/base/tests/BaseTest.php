<?php

namespace Botble\Base\Tests;

use Botble\ACL\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class BaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRoutes()
    {
        $this->withoutEvents();

        $auth = User::first();

        if ($auth) {
            $this->be($auth);
        }

        $routeCollection = Route::getRoutes();

        foreach ($routeCollection as $value) {
            if ($value->getActionMethod() !== 'GET') {
                continue;
            }

            if (Str::contains($value->uri(), '_debugbar')) {
                continue;
            }

            $response = $this->call($value->getActionMethod(), $value->uri());
            $this->assertNotEquals(500, $response->status(), $value->getActionMethod() . ' ' . $value->uri());
        }
    }
}

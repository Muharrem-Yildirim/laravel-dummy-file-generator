<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HorizonTest extends TestCase
{
    public function test_can_not_view_users_display_horizon_page(): void
    {
        $response = $this->get('/horizon');

        $response->assertStatus(401);
    }

    public function test_if_env_not_set_dont_display_horizon_page(): void
    {
        Config::set('horizon.user', null);
        Config::set('horizon.password', null);

        $response = $this->get('/horizon');

        $response->assertStatus(401);
    }

    public function test_show_horizon_page_if_authenticated(): void
    {
        Config::set('horizon.user', "test");
        Config::set('horizon.password', "test");

        $response = $this->withHeaders([
            'PHP_AUTH_USER' => "test",
            'PHP_AUTH_PW' => "test",
        ])->get('/horizon');

        $this->assertTrue($response->getStatusCode() !== 401, "User should be authenticated");
    }
}

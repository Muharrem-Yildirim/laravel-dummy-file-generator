<?php

namespace Tests\Feature;

use App\Jobs\CreateFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FileGeneratorControllerTest extends TestCase
{
    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_file_can_be_generated(): void
    {
        $response = $this->postJson(route('generator.store'), [
            'file_size' => 1024
        ]);

        $response->assertStatus(200);
    }

    public function test_can_not_generate_file_if_file_size_is_bigger_than_2gb(): void
    {
        $response = $this->postJson(route('generator.store'), [
            'file_size' => 2147483649
        ]);

        $response->assertStatus(422);
    }

    public function test_file_should_not_be_generated_if_file_size_is_not_multiple_of_1024(): void
    {
        $response = $this->postJson(route('generator.store'), [
            'file_size' => 1025
        ]);

        $response->assertStatus(422);
    }

    public function test_generate_response_must_contains_pending_output(): void
    {
        $response = $this->postJson(route('generator.store'), [
            'file_size' => 1024
        ]);

        $response->assertJsonStructure([
            'data' => [
                'success',
                'job'
            ]
        ]);
    }

    public function test_can_show_generate_response(): void
    {
        $response = $this->postJson(route('generator.store'), [
            'file_size' => 1024
        ]);

        Artisan::call('queue:work --once');

        $response = $this->getJson(route('generator.show', $response->json('data.job')));

        $response->assertStatus(200);
    }
}

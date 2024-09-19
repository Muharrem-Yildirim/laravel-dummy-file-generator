<?php

namespace Tests\Feature;

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
        $response = $this->postJson('/api/file/generate', [
            'file_size' => 1024
        ]);

        $response->assertStatus(200);
    }

    public function test_file_should_not_be_generated_if_file_size_is_not_multiple_of_1024(): void
    {
        $response = $this->postJson('/api/file/generate', [
            'file_size' => 1025
        ]);

        $response->assertStatus(422);
    }

    public function test_generate_response_must_contains_pending_output(): void
    {
        $response = $this->postJson('/api/file/generate', [
            'file_size' => 1024
        ]);

        dd($response->json());
        $response->assertJsonStructure(['output' => ['pending']]);
    }
}

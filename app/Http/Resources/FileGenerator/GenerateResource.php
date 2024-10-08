<?php

namespace App\Http\Resources\FileGenerator;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenerateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'job' => $this['jobId'],
            'session' => $this['sessionId']
        ];
    }
}

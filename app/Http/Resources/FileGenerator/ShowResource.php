<?php

namespace App\Http\Resources\FileGenerator;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
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
            'status' => $this->status,
            'outputs' => $this->output
        ];
    }
}

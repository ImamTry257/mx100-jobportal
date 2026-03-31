<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'applicationId' => $this->id,
            'cvFile'        => $this->cv_file,
            'appliedAt'     => $this->created_at->format('Y-m-d H:i:s') ?? null,

            'vacancy' => [
                'id'            => $this->vacancy->id,
                'title'         => $this->vacancy->title,
                'description'   => $this->vacancy->description,
                'status'        => strtoupper($this->vacancy->status),
            ]
        ];
    }
}

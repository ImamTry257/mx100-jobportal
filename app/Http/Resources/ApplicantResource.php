<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'applicantName'     => $this->name,
            'applicantEmail'    => $this->email,
            'role'              => strtoupper($this->role),
            'applyDate'         => $this->created_at->format('Y-m-d H:i:s') ?? null,
            'vacancyDetails'    => $this->pivot,
        ];
    }
}

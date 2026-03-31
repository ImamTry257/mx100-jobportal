<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacancyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'status'        => strtoupper($this->status),
            'salaryMin'     => $this->salary_min,
            'salaryMax'     => $this->salary_max,
            'type'          => strtoupper($this->type),
            'expiredAt'     => $this->expired_at,
            'companyId'     => $this->company_id,
            'createdAt'    => $this->created_at,
        ];
    }
}

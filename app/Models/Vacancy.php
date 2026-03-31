<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    const draft_status = 'DRAFT';
    const published_status = 'PUBLISHED';

    protected $table = 'vacancy';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function applicants()
    {
        return $this->belongsToMany(User::class, 'vacancy_apply', 'vacancy_id', 'freelancer_id')
            ->withPivot('cv_file')
            ->withTimestamps();
    }
}

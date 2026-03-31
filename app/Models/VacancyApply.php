<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacancyApply extends Model
{
    protected $table = 'vacancy_apply';

    protected $fillable = [
        'vacancy_id',
        'freelancer_id',
        'cv_file',
        'created_at',
        'updated_at'
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}

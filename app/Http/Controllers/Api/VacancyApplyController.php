<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\VacancyApply;
use Illuminate\Http\Request;

class VacancyApplyController extends Controller
{
    public function apply(Request $request, $id)
    {
        try {
            $vacancy = Vacancy::findOrFail($id);

            // handle for vacancy not published
            if (strtoupper($vacancy->status) !== Vacancy::published_status) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Vacancy not available',
                    'data'      => null
                ], 400);
            }

            // handle for duplicate apply
            $exists = VacancyApply::where('vacancy_id', $id)
                ->where('freelancer_id', $request->user()->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'This vacancy has already been applied',
                    'data'      => null
                ], 400);
            }

            $createdVacancyApply = VacancyApply::create([
                'vacancy_id' => intval($id),
                'freelancer_id' => $request->user()->id,
                'cv_file' => $request->cv_file ?? ""
            ]);

            return response()->json([
                'status'    => true,
                'message'   => 'Applied successfully',
                'data'      => $createdVacancyApply
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
                'data'      => null
            ], 500);
        }

    }
}

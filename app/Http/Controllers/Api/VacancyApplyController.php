<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MetaResource;
use App\Http\Resources\MyApplicationResource;
use App\Models\Application;
use App\Models\Vacancy;
use App\Models\VacancyApply;
use Illuminate\Http\Request;

class VacancyApplyController extends Controller
{
    public function apply(Request $request, $id)
    {
        try {
            $request->validate([
                'cvFile' => 'required|file|mimes:pdf,doc,docx|max:2048'
            ]);

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

            // upload cv file
            $filePath = $request->file('cvFile')->store('cv_files', 'public');

            $createdVacancyApply = VacancyApply::create([
                'vacancy_id' => intval($id),
                'freelancer_id' => $request->user()->id,
                'cv_file' => $filePath ?? ""
            ]);

            return response()->json([
                'status'    => true,
                'message'   => 'Applied successfully',
                'data'      => null
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
                'data'      => null
            ], 500);
        }

    }

    public function myApplications(Request $request)
    {
        $applications = Application::with('vacancy')
            ->where('freelancer_id', $request->user()->id)
            ->latest()
            ->paginate($request->size ?? 10);

        return response()->json([
            'status' => true,
            'message' => 'My applications retrieved successfully',
            'data' => MyApplicationResource::collection($applications->items()),
            'meta' => MetaResource::make($applications)
        ]);
    }
}

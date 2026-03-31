<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    // Public (freelancer lihat job)
    public function index()
    {
        $vacancy = Vacancy::where('status', Vacancy::published_status)->latest()->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Vacancy retrieved successfully',
            'data'      => $vacancy
        ], 200);
    }

    // Company create job
    public function store(Request $request)
    {
        try {
            if (strtolower($request->user()->role) !== 'company') {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Forbidden Access',
                    'data'      => null
                ], 403);
            }

            $request->validate([
                'title' => 'required|unique:vacancy,title',
                'description' => 'required',
                'status' => 'in:DRAFT,PUBLISHED'
            ]);

            $vacancy = Vacancy::create([
                'company_id'    => $request->user()->id,
                'title'         => $request->title,
                'description'   => $request->description,
                'status'        => $request->status ?? Vacancy::draft_status
            ]);

            return response()->json([
                'status'    => true,
                'message'   => 'Vacancy created successfully',
                'data'      => $vacancy
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
                'data'      => null
            ], 500);
        }
    }

    // Update job (only owner)
    public function update(Request $request, $id)
    {
        try {
            $vacancy = Vacancy::findOrFail($id);

            if ($vacancy->company_id !== $request->user()->id) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Forbidden Access',
                    'data'      => null
                ], 403);
            }

            $request->validate([
                'title' => 'required|unique:vacancy,title,' . $vacancy->id,
                'description' => 'required',
                'status' => 'in:DRAFT,PUBLISHED'
            ]);

            $vacancy->update($request->only('title', 'description', 'status'));

            return response()->json([
                'status'    => true,
                'message'   => 'Job updated successfully',
                'data'      => $vacancy
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
                'data'      => null
            ], 500);
        }

    }

    // Company get the vacancy
    public function myJobs(Request $request)
    {
        try {
            $myVacancies = Vacancy::where('company_id', $request->user()->id)->latest()->get();

            return response()->json([
                'status'    => true,
                'message'   => 'My jobs retrieved successfully',
                'data'      => $myVacancies
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicantResource;
use App\Http\Resources\MetaResource;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    // Public (freelancer lihat job)
    public function index(Request $request)
    {
        $query = Vacancy::where('created_at', '!=', null);

        // filter title
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // filter status
        if ($request->status) {
            $query->where('status', strtoupper($request->status));
        }

        $vacancy = $query->latest()->paginate($request->size ?? 10);

        return response()->json([
            'status'    => true,
            'message'   => 'Vacancy retrieved successfully',
            'data'      => VacancyResource::collection($vacancy),
            'meta'      => MetaResource::make($vacancy),
            'links' => [
                'first' => $vacancy->url(1),
                'last'  => $vacancy->url($vacancy->lastPage()),
                'prev'  => $vacancy->previousPageUrl(),
                'next'  => $vacancy->nextPageUrl(),
            ]
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

    // Company get the vacancy applicants
    public function applicants(Request $request, $id)
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

           $query = $vacancy->applicants()
                ->when($request->name, function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->name . '%');
                })
                ->when($request->status, function ($q) use ($request) {
                    $q->wherePivot('status', strtoupper($request->status));
                });

            $perPage = $request->input('size', 10);

            $applicants = $query->paginate($perPage);

            return response()->json([
                'status'    => true,
                'message'   => 'Applicants retrieved successfully',
                'data'      => ApplicantResource::collection($applicants),
                'meta'      => MetaResource::make($applicants),
                'links' => [
                    'first' => $applicants->url(1),
                    'last'  => $applicants->url($applicants->lastPage()),
                    'prev'  => $applicants->previousPageUrl(),
                    'next'  => $applicants->nextPageUrl(),
                ]
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

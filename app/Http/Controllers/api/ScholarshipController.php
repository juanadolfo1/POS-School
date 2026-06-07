<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Models\Scholarship;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        try {
            $scholarYearId = $request->query('scholar_year_id');

            $query = Scholarship::join('students', 'scholarships.student_id', '=', 'students.id')
                ->join('people', 'students.person_id', '=', 'people.id')
                ->join('scholar_years', 'scholarships.scholar_year_id', '=', 'scholar_years.id')
                ->select(
                    'scholarships.id',
                    'scholarships.name',
                    'scholarships.amount',
                    'scholarships.status',
                    'scholarships.student_id',
                    'scholarships.scholar_year_id',
                    'scholar_years.year as scholar_year',
                    DB::raw("CONCAT_WS(' ', people.name, people.first_lastname, people.second_lastname) as student_name")
                );

            if ($scholarYearId) {
                $query->where('scholarships.scholar_year_id', '=', $scholarYearId);
            }

            $scholarships = $query->get();

            if (count($scholarships) == 0) {
                return response()
                    ->json(ApiResponse::notFound('Scholarships not found', []))
                    ->setStatusCode(404);
            }

            return response()
                ->json(ApiResponse::success('Scholarships retrieved successfully', $scholarships))
                ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching scholarships: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve scholarships', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0|max:100',
            'student_id' => 'required|exists:students,id',
            'scholar_year_id' => 'required|exists:scholar_years,id',
        ]);

        try {
            $scholarship = new Scholarship();
            $scholarship->name = $request->name;
            $scholarship->amount = $request->amount;
            $scholarship->student_id = $request->student_id;
            $scholarship->scholar_year_id = $request->scholar_year_id;
            $scholarship->status = 1;
            $scholarship->save();

            return response()
                ->json(ApiResponse::success('Scholarship created successfully', $scholarship))
                ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error creating scholarship: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to create scholarship', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:scholarships,id',
            'name' => 'nullable|string|max:100',
            'amount' => 'nullable|numeric|min:0|max:100',
            'student_id' => 'nullable|exists:students,id',
            'scholar_year_id' => 'nullable|exists:scholar_years,id',
            'status' => 'nullable|integer|in:0,1',
        ]);

        try {
            $scholarship = Scholarship::find($request->id);
            if (!$scholarship) {
                return response()
                    ->json(ApiResponse::notFound('Scholarship not found', []))
                    ->setStatusCode(404);
            }

            $scholarship->name = $request->name ?? $scholarship->name;
            $scholarship->amount = $request->amount ?? $scholarship->amount;
            $scholarship->student_id = $request->student_id ?? $scholarship->student_id;
            $scholarship->scholar_year_id = $request->scholar_year_id ?? $scholarship->scholar_year_id;
            $scholarship->status = $request->status ?? $scholarship->status;
            $scholarship->save();

            return response()
                ->json(ApiResponse::success('Scholarship updated successfully', $scholarship))
                ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error updating scholarship: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to update scholarship', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function delete(int $id)
    {
        try {
            $scholarship = Scholarship::find($id);
            if (!$scholarship) {
                return response()
                    ->json(ApiResponse::notFound('Scholarship not found', []))
                    ->setStatusCode(404);
            }

            $scholarship->delete();

            return response()
                ->json(ApiResponse::success('Scholarship deleted successfully', []))
                ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error deleting scholarship: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to delete scholarship', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }
}

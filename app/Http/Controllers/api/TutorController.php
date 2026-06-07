<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Interfaces\TutorRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    protected TutorRepositoryInterface $tutorRepository;

    public function __construct(TutorRepositoryInterface $tutorRepository) {
        $this->tutorRepository = $tutorRepository;
    }

    public function index()
    {
        try {
            $tutors = $this->tutorRepository->get_all_tutors();

            if(count($tutors) == 0){
                return response()
                        ->json(ApiResponse::notFound('Tutors not found', $tutors))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Tutors retrieved successfully', $tutors))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve tutors', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function get_all_tutors_for_student(int $studentId)
    {
        try {
            $tutors = $this->tutorRepository->get_all_tutors_for_student($studentId);

            if(count($tutors) == 0){
                return response()
                        ->json(ApiResponse::notFound('Tutors not found', $tutors))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Tutors retrieved successfully', $tutors))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve tutors', [$e->getMessage()]))
                    ->setStatusCode(500);

        }
    }

    public function get_tutor_by_id(int $id){
        try {
            $tutor = $this->tutorRepository->get_tutor_by_id($id);

            if(!$tutor){
                return response()
                        ->json(ApiResponse::notFound('Tutor not found', $tutor))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Tutor retrieved successfully', $tutor))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve tutor', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function store_tutor(Request $request){
        $request->validate([
            'name' => 'required|string|max:128',
            'first_lastname' => 'required|string|max:64',
            'second_lastname' => 'nullable|string|max:64',
            'email' => 'nullable|email|max:128',
            'relation' => 'required|string|max:32',
            'tutor_type' => 'required|in:P,M,O',
            'student_id' => 'required|exists:students,id',
        ]);

        $tutorData = $request;
        try {
            $tutor = $this->tutorRepository->store_tutor($tutorData);

            return response()
                    ->json(ApiResponse::success('Tutor created successfully', $tutor))
                    ->setStatusCode(201);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to create tutor', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function update_tutor(Request $request){
        $request->validate([
            'id' => 'required|exists:student_tutors,id',
            'relation' => 'required|string|max:32',
            'tutor_type' => 'required|in:P,M,O',
            'student_id' => 'required|exists:students,id',
            'status' => 'required|integer',
        ]);

        $tutorData = $request;
        try {
            $tutor = $this->tutorRepository->update_tutor($tutorData);
            if(!$tutor){
                return response()
                        ->json(ApiResponse::notFound('Tutor not found', $tutor))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Tutor updated successfully', $tutor))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to update tutor', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function delete_tutor(int $id){
        try {
            $tutor = $this->tutorRepository->delete_tutor($id);
            if(!$tutor){
                return response()
                        ->json(ApiResponse::notFound('Tutor not found', $tutor))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Tutor deleted successfully', $tutor))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to delete tutor', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\StudentRepositoryInterface;
use App\Models\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    protected $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository){
        $this->studentRepository = $studentRepository;
    }

    public function index(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $offset = ($page - 1) * $limit;
        $search_text = $request->query('search');

        try {
            $students = $this->studentRepository->get_all_students($limit, $offset, $search_text);
            if (count($students) == 0){
                return response()
                        ->json(ApiResponse::notFound('Students not found', $students))
                        ->setStatusCode(404);
            }
            return response()
                    ->json(ApiResponse::success('Students retrieved successfully', $students))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching students: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve students', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function store(Request $request){
        $student = $request;

        try{
            $result = $this->studentRepository->store_student($student);
            if($result){
                return response()
                        ->json(ApiResponse::success('Student created', $result))
                        ->setStatusCode(201);
            }
        } catch (Exception $e){
            Log::error('Error creating student: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to create student', [$e->getMessage()]))
                    ->setStatusCode(400);
        }

    }

    public function update(Request $request){
        $student = $request;

        try{
            $result = $this->studentRepository->update_student($student);
            if($result){
                return response()
                        ->json(ApiResponse::success('Student updated', $result))
                        ->setStatusCode(200);
            }
        } catch (Exception $e) {
            Log::error('Error updating student: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to update student', [$e->getMessage()]))
                    ->setStatusCode(400);

        }
    }

    public function delete(Request $request, string $id){
        try{
            $result = $this->studentRepository->delete_student($id);
            if($result){
                return response()
                        ->json(ApiResponse::success('Student deleted', $result))
                        ->setStatusCode(200);
            }
        } catch (Exception $e) {
            Log::error('Error deleting student: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to delete student', [$e->getMessage()]))
                    ->setStatusCode(400);
        }

    }

    public function assign_group(Request $request){
        $studentId = (int) $request->student_id;
        $groupId = (int) $request->groupId;
        try{
            $result = $this->studentRepository->assing_group($studentId, $groupId);
            if($result){
                return response()
                        ->json(ApiResponse::success('Group assigned', $result))
                        ->setStatusCode(200);
            }
        } catch (Exception $e) {
            Log::error('Error assigning group: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to assign group', [$e->getMessage()]))
                    ->setStatusCode(400);
        }
    }

    public function get_students_by_group(Request $request, string $groupId){
        try{
            $result = $this->studentRepository->get_students_by_group($groupId);
            if($result){
                return response()
                        ->json(ApiResponse::success('Students retrieved', $result))
                        ->setStatusCode(200);
            }
        } catch (Exception $e) {
            Log::error('Error fetching students by group: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve students by group', [$e->getMessage()]))
                    ->setStatusCode(400);
        }
    }

    public function get_student_by_uuid(string $uuid){
        try{
            $student = $this->studentRepository->get_student_by_uuid($uuid);
            if($student){
                return response()
                        ->json(ApiResponse::success('Student retrieved', $student))
                        ->setStatusCode(200);
            }
            return response()
                    ->json(ApiResponse::notFound('Student not found', $student))
                    ->setStatusCode(404);
        } catch (Exception $e) {
            Log::error('Error fetching student by uuid: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve student by uuid', [$e->getMessage()]))
                    ->setStatusCode(400);
        }
    }

}

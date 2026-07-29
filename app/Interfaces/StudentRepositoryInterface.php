<?php

namespace App\Interfaces;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface StudentRepositoryInterface{
    /** @return Student[] */
    function get_all_students(int $limit = 10, int $offset = 0, string $search_text = null): array;
    function get_student_by_id(int $id): Student;
    function get_student_by_uuid(string $uuid): Student;
    function store_student($studentData): Student;
    function update_student($studentData): Student;
    function delete_student(int $id): Student;
    function assing_group(int $studentId, int $groupId): Student;
    function get_student_groups(int $studentId): array;
    function get_students_by_group(int $groupId): Collection;
}

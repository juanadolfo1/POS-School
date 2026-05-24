<?php

namespace App\Interfaces;

use App\Models\StudentTutor;

interface TutorRepositoryInterface{
    function get_all_tutors(): array;
    /**
     * @return StudentTutor[]
     */
    function get_all_tutors_for_student(int $studentId): array;
    function get_tutor_by_id(int $id): StudentTutor;
    function store_tutor($tutorData): StudentTutor;
    function update_tutor($tutorData): StudentTutor;
    function delete_tutor(int $id): StudentTutor;
}

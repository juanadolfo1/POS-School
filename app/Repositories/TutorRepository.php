<?php

namespace App\Repositories;

use App\Interfaces\TutorRepositoryInterface;
use App\Models\Person;
use App\Models\StudentTutor;

class TutorRepository implements TutorRepositoryInterface{

    public function get_all_tutors_for_student(int $studentId): array{
        $tutors = StudentTutor::join(
                    'people',
                    'student_tutors.person_id',
                    '=',
                    'people.id'
                )
                    ->where('student_tutors.student_id', '=', $studentId)
                    ->select(
                        'student_tutors.id',
                        'student_tutors.status',
                        'people.email',
                        'people.name',
                        'people.first_lastname',
                        'people.second_lastname'
                    )
                    ->get()
                    ->toArray();

        return $tutors;
    }

    public function get_tutor_by_id(int $id): StudentTutor{
        return StudentTutor::join(
                    'people',
                    'student_tutors.person_id',
                    '=',
                    'people.id'
                )
                    ->select(
                        'student_tutors.id',
                        'student_tutors.status',
                        'student_tutors.relation',
                        'people.email',
                        'people.name',
                        'people.first_lastname',
                        'people.second_lastname'
                    )
                    ->where('student_tutors.id', '=', $id)
                    ->first();
    }

    public function store_tutor($tutorData): StudentTutor{
        $newPerson = new Person();
        $newPerson->email = $tutorData->email;
        $newPerson->name = $tutorData->name;
        $newPerson->first_lastname = $tutorData->first_lastname;
        $newPerson->second_lastname = $tutorData->second_lastname;
        $newPerson->status = 1;
        $newPerson->save();

        $newTutor = new StudentTutor();
        $newTutor->person_id = $newPerson->id;
        $newTutor->relation = $tutorData->relation;
        $newTutor->student_id = $tutorData->student_id;
        $newTutor->tutor_type = $tutorData->tutor_type;
        $newTutor->status = 1;
        $newTutor->save();

        return $newTutor;
    }

    public function update_tutor($tutorData): StudentTutor{
        $tutor = StudentTutor::find($tutorData->id);
        if($tutor){
            $tutor->relation = $tutorData->relation;
            $tutor->student_id = $tutorData->student_id;
            $tutor->tutor_type = $tutorData->tutor_type;
            $tutor->status = $tutorData->status;
            $tutor->save();
        }

        return $tutor;
    }

    public function delete_tutor(int $id): StudentTutor{
        $tutor = StudentTutor::find($id);
        if($tutor){
            $tutor->delete();
        }

        return $tutor;
    }
}

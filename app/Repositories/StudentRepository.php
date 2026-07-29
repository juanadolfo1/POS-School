<?php

namespace App\Repositories;

use App\Interfaces\StudentRepositoryInterface;
use App\Models\Group;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentGroup;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StudentRepository implements StudentRepositoryInterface{
    /** @return Student[] */
    public function get_all_students($limit = 10, $offset = 0, string $search_text = null): array
    {
        $tota_rows = Student::count();
        $query = Student::join('people', 'students.person_id', '=', 'people.id')
                ->orderBy('students.id', 'desc')
                ->select(
                    'students.id',
                    'students.status',
                    'students.gender',
                    'students.birthday',
                    'students.curp',
                    'students.uuid',
                    'people.email',
                    'people.name',
                    'people.first_lastname',
                    'people.second_lastname',
                    DB::raw($tota_rows . ' as total')
                );

        if($search_text != null){
            $query->search($search_text);
        }

                return $query->skip($offset)->take($limit)->get()->toArray();
    }

    public function get_student_by_id($id): Student
    {
        return Student::find($id);
    }

    private function generateRandomUUID() {
        $uuid = '';

        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

        return $uuid;
    }

    public function store_student($studentData): Student
    {
        $person = new Person();
        $person->email = $studentData->email ?: null;
        $person->name = $studentData->name;
        $person->first_lastname = $studentData->first_lastname;
        $person->second_lastname = $studentData->second_lastname;
        $person->status = $studentData->status;
        $person->save();

        $student = new Student();
        $student->status = $studentData->status;
        $student->gender = $studentData->gender;
        $student->birthday = $studentData->birthday;
        $student->uuid = $this->generateRandomUUID();
        $student->curp = $studentData->curp;
        $student->person_id = $person->id;
        $student->save();

        return $student;
    }

    public function update_student($studentData): Student
    {
        $student = Student::find($studentData->id);
        if($student){
            $student->status = $studentData->status;
            $student->gender = $studentData->gender;
            $student->birthday = $studentData->birthday;
            $student->curp = $studentData->curp;
            $student->save();

            $person = Person::find($student->person_id);
            if($person){
                $person->email = $studentData->email;
                $person->name = $studentData->name;
                $person->first_lastname = $studentData->first_lastname;
                $person->second_lastname = $studentData->second_lastname;
                $person->save();
            }

        }
        return $student;
    }

    public function delete_student($id): Student
    {
        $student = Student::find($id);
        if($student){
            $student->delete();
        }

        return $student;
    }

    public function assing_group(int $studentId, int $groupId): Student
    {
        $student = Student::find($studentId);
        if (!$student) throw new Exception('Student not found');

        $group = Group::find($groupId);
        if (!$group) throw new Exception('Group not found');

        // Upsert: si ya tiene grupo en el mismo ciclo escolar, lo reemplaza
        $existing = StudentGroup::join('groups', 'student_groups.group_id', '=', 'groups.id')
            ->where('student_groups.student_id', $studentId)
            ->where('groups.scholar_year_id', $group->scholar_year_id)
            ->select('student_groups.id')
            ->first();

        if ($existing) {
            StudentGroup::where('id', $existing->id)->update(['group_id' => $groupId]);
        } else {
            $studentGroup = new StudentGroup();
            $studentGroup->student_id = $student->id;
            $studentGroup->group_id = $group->id;
            $studentGroup->status = 1;
            $studentGroup->save();
        }

        return $student;
    }

    public function get_student_groups(int $studentId): array
    {
        return StudentGroup::join('groups', 'student_groups.group_id', '=', 'groups.id')
            ->join('scholar_years', 'groups.scholar_year_id', '=', 'scholar_years.id')
            ->join('cat_academic_levels', 'groups.academic_level_id', '=', 'cat_academic_levels.id')
            ->where('student_groups.student_id', $studentId)
            ->select(
                'student_groups.id',
                'groups.id as group_id',
                'groups.label as group_label',
                'groups.scholar_year_id',
                'scholar_years.year as scholar_year',
                'groups.academic_level_id',
                'cat_academic_levels.label as academic_level'
            )
            ->orderBy('scholar_years.id', 'desc')
            ->get()->toArray();
    }

    public function get_students_by_group($groupId): Collection
    {
        $students = Student::join('student_groups', 'students.id', '=', 'student_groups.student_id')
            ->join('people', 'students.person_id', '=', 'people.id')
            ->where('student_groups.group_id', $groupId)
            ->select(
                'students.id',
                'student_groups.id as student_group_id',
                'students.status',
                'students.gender',
                'students.birthday',
                'students.curp',
                'people.email',
                'people.name',
                'people.first_lastname',
                'people.second_lastname'
            )
            ->get();
        return $students;
    }

    public function get_student_by_uuid(string $uuid): Student
    {
        $today = date('Y-m-d');
        $student = Student::join('people', 'students.person_id', '=', 'people.id')
            ->select(
                'students.id',
                'people.name',
                'people.first_lastname',
                'people.second_lastname'
            )
            ->where('uuid', '=', $uuid)->first();

        if (!$student) {
            throw new Exception('Student not found');
        }

        $academicLevel = StudentGroup::join('groups', 'student_groups.group_id', '=', 'groups.id')
            ->join('cat_academic_levels', 'groups.academic_level_id', '=', 'cat_academic_levels.id')
            ->join('scholar_years', 'groups.scholar_year_id', '=', 'scholar_years.id')
            ->where([
                ['student_groups.student_id', '=', $student->id],
                ['scholar_years.starts_at', '<=', $today],
                ['scholar_years.ends_at', '>=', $today],
            ])
            ->select('groups.academic_level_id', 'scholar_years.id as scholar_year_id')->first();

        $student->academic_level_id = $academicLevel?->academic_level_id ?? 0;
        $student->scholar_year_id = $academicLevel?->scholar_year_id ?? 0;
        return $student;
    }
}

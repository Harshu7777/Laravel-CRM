<?php

namespace App\Repositories;

use App\Models\Student;
use App\Interfaces\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface
{
    public function getAll()
    {
        return Student::select(
            'student_id', 'name', 'college',
            'city', 'state', 'gpa'            
        )
        ->orderBy('name')
        ->paginate(10);                        
    }

    public function findById($id)
    {
        return Student::where('student_id', $id)
                      ->firstOrFail();
    }

    public function create(array $data)
    {
        return Student::create($data);
    }

    // ─── Update ────────────────────────────────────
    public function update($id, array $data)
    {
        $student = $this->findById($id);
        $student->update($data);
        return $student;
    }

    // ─── Delete ────────────────────────────────────
    public function delete($id)
    {
        $student = $this->findById($id);
        return $student->delete();
    }

    // ─── Filter by State ───────────────────────────
    public function filterByState($state)
    {
        return Student::where('state', $state)
                      ->select('student_id', 'name', 'college', 'city', 'state', 'gpa')
                      ->get();
    }
}
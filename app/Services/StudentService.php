<?php

namespace App\Services;

use App\Interfaces\StudentRepositoryInterface;

class StudentService
{
    private $repository;

    public function __construct(StudentRepositoryInterface $repository)
    {
        $this->repository = $repository;       
    }

    public function getAllStudents()
    {
        return $this->repository->getAll();
    }

    public function findStudent($id)
    {
        return $this->repository->findById($id);
    }

    public function createStudent(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateStudent($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteStudent($id)
    {
        return $this->repository->delete($id);
    }

    public function filterByState($state)
    {
        return $this->repository->filterByState($state);
    }
}
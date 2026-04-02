<?php

namespace App\Http\Controllers;

use App\Services\StudentService;
use App\Http\Requests\StudentRequest;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getAllStudents();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function show($id)
    {
        $student = $this->service->findStudent($id);
        return response()->json(['status' => 'success', 'data' => $student]);
    }

    public function store(StudentRequest $request)
    {
        $student = $this->service->createStudent($request->validated());
        return response()->json(['status' => 'success', 'data' => $student], 201);
    }

    public function update(Request $request, $id)
    {
        $student = $this->service->updateStudent($id, $request->all());
        return response()->json(['status' => 'success', 'data' => $student]);
    }

    public function destroy($id)
    {
        $this->service->deleteStudent($id);
        return response()->json(['status' => 'success', 'message' => 'Student deleted!']);
    }

    public function filterByState($state)
    {
        $students = $this->service->filterByState($state);
        return response()->json(['status' => 'success', 'data' => $students]);
    }
}
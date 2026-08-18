<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\EmployeeService;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $employees = $this->employeeService->getAllEmployees();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $specialties = $this->employeeService->getTrainerSpecialties();
        return view('admin.employees.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $this->employeeService->createEmployee($request->all());
        return redirect()->route('employees.index')->with('success', 'Empleado creado con éxito.');
    }

    public function edit(string $id)
    {
        $employee = $this->employeeService->getEmployeeById($id);
        $specialties = $this->employeeService->getTrainerSpecialties();
        return view('admin.employees.edit', compact('employee', 'specialties'));
    }

    public function update(Request $request, string $id)
    {
        $this->employeeService->updateEmployee($id, $request->all());
        return redirect()->route('employees.index')->with('success', 'Empleado actualizado con éxito.');
    }

    public function activate(string $id)
    {
        $this->employeeService->activateEmployee($id);
        return redirect()->route('employees.index')->with('success', 'Empleado activado con éxito.');
    }

    public function destroy(string $id)
    {
        $this->employeeService->deleteEmployee($id);
        return redirect()->route('employees.index')->with('success', 'Empleado eliminado con éxito.');
    }
}

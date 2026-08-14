<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('department')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('employee_code', 'like', "%{$request->search}%")
                    ->orWhere('position', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->paginate(10)->withQueryString();
        $departments = Department::all();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('employees.form', ['employee' => new Employee, 'departments' => $departments]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:100',
            'joining_date' => 'nullable|date',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'create_login' => 'boolean',
        ]);

        $data['employee_code'] = 'EMP-' . str_pad((string) (Employee::max('id') + 1), 3, '0', STR_PAD_LEFT);

        if (!empty($data['create_login']) && !empty($data['email'])) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => 'password',
                'role' => 'employee',
            ]);
            $data['user_id'] = $user->id;
        }

        Employee::create($data);
        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load('department', 'attendance', 'payslips');
        $recentAttendance = $employee->attendance()->latest()->limit(10)->get();
        $recentPayslips = $employee->payslips()->latest()->limit(6)->get();
        return view('employees.show', compact('employee', 'recentAttendance', 'recentPayslips'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        return view('employees.form', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:100',
            'joining_date' => 'nullable|date',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $employee->update($data);
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
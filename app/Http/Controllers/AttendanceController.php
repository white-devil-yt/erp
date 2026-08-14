<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();

        $employees = Employee::with(['attendance' => function ($q) use ($date) {
            $q->where('date', $date->format('Y-m-d'));
        }])->where('status', 'active')->get();

        $summary = [
            'present' => $employees->filter(fn ($e) => optional($e->attendance->first())->status === 'present')->count(),
            'absent' => $employees->filter(fn ($e) => optional($e->attendance->first())->status === 'absent')->count(),
            'half_day' => $employees->filter(fn ($e) => optional($e->attendance->first())->status === 'half_day')->count(),
            'leave' => $employees->filter(fn ($e) => optional($e->attendance->first())->status === 'leave')->count(),
            'not_marked' => $employees->filter(fn ($e) => $e->attendance->isEmpty())->count(),
        ];

        return view('attendance.index', compact('employees', 'date', 'summary'));
    }

    public function mark(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
            'status' => 'required|in:present,absent,half_day,leave',
        ]);

        Attendance::updateOrCreate(
            ['employee_id' => $data['employee_id'], 'date' => $data['date']],
            [
                'status' => $data['status'],
                'check_in' => $data['status'] === 'present' || $data['status'] === 'half_day' ? Carbon::now()->format('H:i:s') : null,
                'check_out' => null,
            ]
        );

        return back()->with('success', 'Attendance marked successfully.');
    }

    public function destroy(Request $request, Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Attendance entry removed.');
    }
}
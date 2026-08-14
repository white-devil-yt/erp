<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payslip;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $months = Payslip::select('month')->distinct()->orderByDesc('month')->pluck('month');
        $month = $request->filled('month') ? $request->month : Carbon::now()->format('Y-m');

        $query = Payslip::with('employee')->where('month', $month);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payslips = $query->latest()->paginate(10)->withQueryString();
        $totalPayroll = $query->get()->sum('net_salary');
        $totalPaid = Payslip::where('month', $month)->where('status', 'paid')->sum('net_salary');

        return view('payslips.index', compact('payslips', 'months', 'month', 'totalPayroll', 'totalPaid'));
    }

    public function generate(Request $request)
    {
        $month = $request->filled('month') ? $request->month : Carbon::now()->format('Y-m');
        $monthDate = Carbon::createFromFormat('Y-m', $month);
        $daysInMonth = $monthDate->daysInMonth;

        $employees = Employee::where('status', 'active')->get();
        $generated = 0;

        foreach ($employees as $employee) {
            $attendance = $employee->attendance()
                ->whereBetween('date', [$monthDate->startOfMonth()->format('Y-m-d'), $monthDate->endOfMonth()->format('Y-m-d')])
                ->get();

            $presentDays = $attendance
                ->whereIn('status', ['present', 'half_day'])
                ->count();

            Payslip::updateOrCreate(
                ['employee_id' => $employee->id, 'month' => $month],
                [
                    'present_days' => $presentDays,
                    'total_working_days' => $daysInMonth,
                    'basic_salary' => $employee->basic_salary,
                    'allowances' => $employee->allowances,
                    'deductions' => $employee->deductions,
                    'overtime_amount' => 0,
                    'bonus' => 0,
                    'net_salary' => $employee->basic_salary + $employee->allowances - $employee->deductions,
                    'status' => 'generated',
                ]
            );
            $generated++;
        }

        return back()->with('success', "Payslips generated for {$generated} employees for {$month}.");
    }

    public function print(Payslip $payslip)
    {
        $payslip->load('employee.department');

        return view('payslips.print', compact('payslip'));
    }

    public function markPaid(Payslip $payslip)
    {
        $payslip->update([
            'status' => 'paid',
            'paid_date' => Carbon::today(),
        ]);

        app(AccountingService::class)->postPayroll($payslip);

        return back()->with('success', 'Payslip marked as paid.');
    }
}

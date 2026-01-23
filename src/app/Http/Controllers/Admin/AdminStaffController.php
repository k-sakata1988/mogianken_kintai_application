<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staffs = User::orderBy('name')->get();

        return view('admin.staff.list', compact('staffs'));
    }

    public function attendance(User $user)
    {
        $month = request('month', now()->format('Y-m'));

        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

        $dates = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(fn ($attendance) => $attendance->date->format('Y-m-d'));

            return view('admin.staff.attendance', compact(
                'user',
                'dates',
                'attendances',
                'month'
        ));
    }
}

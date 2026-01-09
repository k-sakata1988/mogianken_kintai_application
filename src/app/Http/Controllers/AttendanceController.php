<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(){
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->first();
        return view('user.attendance.index', compact('attendance'));
    }

    public function clockIn(){
        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'date' => today(),
            ]
        );

        if ($attendance->clock_in_time) {
            return redirect()->route('user.attendance.index');
        }

        $attendance->update([
            'clock_in_time' => now(),
        ]);

        return redirect()->route('user.attendance.index');
    }
    //実働時間の計算
    public function clockOut()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->firstOrFail();

        DB::transaction(function () use ($attendance) {
            $attendance->update([
                'clock_out_time' => now(),
                'total_working_time' => $attendance->calculateWorkingMinutes(),
            ]);
        });

        return redirect()->route('user.attendance.index');
    }

    public function breakStart()
    {
        $attendance = Attendance::where('user_id', auth()->id())->whereDate('date', today())->firstOrFail();

        $attendance->load('breaks');
        $latestBreak = $attendance->breaks->last();
        if ($latestBreak && !$latestBreak->break_end) {
            return redirect()->route('user.attendance.index');
        }

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
        ]);
        return redirect()->route('user.attendance.index');
    }
    // 休憩時間の計算
    public function breakEnd()
    {
        $attendance = Attendance::where('user_id', auth()->id())->whereDate('date', today())->firstOrFail();

        $attendance->load('breaks');
        $latestBreak = $attendance->breaks->last();

        if (!$latestBreak || $latestBreak->break_end) {
            return redirect()->route('user.attendance.index');
        }
        $latestBreak->update([
            'break_end' => now(),
        ]);

        $attendance->update([
            'total_break_time' => $attendance->calculateTotalBreakMinutes(),
        ]);

        return redirect()->route('user.attendance.index');
    }

    public function show(Attendance $attendance){
        abort_if($attendance->user_id !== auth()->id(), 403);
        $attendance->load('breaks', 'requests');
        return view('user.attendance.detail', compact('attendance'));
    }

    // 一覧表示
    public function monthly(Request $request)
    {
        $month = $request->query('month')? Carbon::createFromFormat('Y-m', $request->query('month')): now();

        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $attendances = Attendance::with('breaks')->where('user_id', auth()->id())
            ->whereBetween('date', [$start, $end])
            ->get()->keyBy(fn ($a) => $a->date->format('Y-m-d'));

        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->copy();
        }

        return view('user.attendance.list', compact(
            'dates',
            'attendances',
            'month'
        ));
    }
}

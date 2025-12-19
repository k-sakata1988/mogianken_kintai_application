<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;

class AttendanceController extends Controller
{
    public function index(){
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->first();
        $status = $attendance->status ?? 'before_work';
        return view('user.attendance.index', compact('attendance', 'status'));
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
    public function clockOut()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->firstOrFail();

        $attendance->update([
            'clock_out_time' => now(),
        ]);

        return redirect()->route('user.attendance.index');
    }

    public function breakStart()
    {
        $attendance = Attendance::where('user_id', auth()->id())->whereDate('date', today())->firstOrFail();

        $latestBreak = $attendance->breaks()->latest()->first();
        if ($latestBreak && !$latestBreak->break_end) {
            return redirect()->route('user.attendance.index');
        }

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
        ]);
        return redirect()->route('user.attendance.index');
    }

    public function breakEnd()
    {
        $attendance = Attendance::where('user_id', auth()->id())->whereDate('date', today())->firstOrFail();

        $latestBreak = $attendance->breaks()->latest()->first();

        if (!$latestBreak || $latestBreak->break_end) {
            return redirect()->route('user.attendance.index');
        }
        $latestBreak->update([
            'break_end' => now(),
        ]);
        return redirect()->route('user.attendance.index');
    }
}

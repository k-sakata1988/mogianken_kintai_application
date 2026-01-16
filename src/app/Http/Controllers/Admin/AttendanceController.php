<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function show(Attendance $attendance){

        $attendance->load('user');

        $latestBreak = $attendance->breaks()->latest()->first();

        return view('admin.attendance.detail', compact('attendance','latestBreak'));
    }

    public function update(Request $request, Attendance $attendance){
        $attendance->update([
            'clock_in_time' => $request->clock_in_time,
            'clock_out_time' => $request->clock_out_time,
            'remark' => $request->remark,
            'is_modified' => true,
        ]);

        if ($request->filled('break_start') && $request->filled('break_end')) {

            $break = $attendance->breaks()->latest()->first();

            if ($break) {
                $break->update([
                    'break_start' => Carbon::parse(
                        $attendance->date->format('Y-m-d').' '.$request->break_start
                    ),
                    'break_end' => Carbon::parse(
                        $attendance->date->format('Y-m-d').' '.$request->break_end
                    ),
                ]);
            } else {
                $attendance->breaks()->create([
                    'break_start' => Carbon::parse(
                        $attendance->date->format('Y-m-d').' '.$request->break_start
                    ),
                    'break_end' => Carbon::parse(
                        $attendance->date->format('Y-m-d').' '.$request->break_end
                    ),
                ]);
            }

            return redirect()
                ->route('admin.attendance.show', $attendance->id)
                ->with('success', '勤怠情報を更新しました');
        }
    }
}
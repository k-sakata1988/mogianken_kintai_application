<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;
use App\Http\Requests\AdminAttendanceUpdateRequest;

class AttendanceController extends Controller
{
    public function show(Attendance $attendance){

        $attendance->load('user');

        $latestBreak = $attendance->breaks()->latest()->first();

        return view('admin.attendance.detail', compact('attendance','latestBreak'));
    }

    public function update(AdminAttendanceUpdateRequest $request, Attendance $attendance)
    {

        $validated = $request->validated();

        $attendance->update([
            'clock_in_time' => $validated['clock_in_time'],
            'clock_out_time' => $validated['clock_out_time'],
            'remark' => $validated['remark'],
            'is_modified' => true,
        ]);

        if ($request->filled('break_start') && $request->filled('break_end')) {
            $break = $attendance->breaks()->latest()->first();

            $breakData = [
                'break_start' => Carbon::parse($attendance->date->format('Y-m-d').' '.$validated['break_start']),
                'break_end' => Carbon::parse($attendance->date->format('Y-m-d').' '.$validated['break_end']),
            ];

            if ($break) {
                $break->update($breakData);
            } else {
                $attendance->breaks()->create($breakData);
            }
        }

        return redirect()
            ->route('admin.attendance.show', $attendance->id)
            ->with('success', '勤怠情報を更新しました');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\DB;
use App\Models\AttendanceBreak;
use App\Http\Requests\AttendanceUpdateRequest;

class AttendanceRequestController extends Controller
{
    /**
     * 修正申請を保存
     */
    public function store(AttendanceUpdateRequest $request, Attendance $attendance){
        abort_if($attendance->user_id !== auth()->id(), 403);

        if ($attendance->hasPendingRequest()) {
            return back()->with('error', '既に修正申請中です');
        }

        DB::transaction(function () use ($request, $attendance) {
            $attendanceRequest = AttendanceRequest::create([
                'attendance_id'   => $attendance->id,
                'request_user_id' => auth()->id(),
                'before_data'     => [
                    'clock_in_time'  => optional($attendance->clock_in_time)->format('H:i'),
                    'clock_out_time' => optional($attendance->clock_out_time)->format('H:i'),
                ],
                'after_data'      => [
                    'clock_in_time'  => $request->clock_in_time,
                    'clock_out_time' => $request->clock_out_time,
                ],
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            foreach ($request->breaks ?? [] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    AttendanceBreak::create([
                        'attendance_id' => $attendance->id,
                        'break_start'   => $attendance->date->format('Y-m-d') . ' ' . $break['start'],
                        'break_end'     => $attendance->date->format('Y-m-d') . ' ' . $break['end'],
                    ]);
                }
            }

            $attendance->update([
                'is_modified' => true,
            ]);
        });

        return redirect()->route('user.attendance.show', $attendance)->with('success', '修正申請を送信しました');
    }

    public function index(Request $request){
        $status = $request->query('status', 'pending');

        $requests = AttendanceRequest::with('attendance')
            ->where('request_user_id', auth()->id())
            ->where('status', $status)
            ->latest()
            ->get();

            return view('user.request.user_list', compact('requests', 'status'));
    }

}

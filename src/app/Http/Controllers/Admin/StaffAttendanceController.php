<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffAttendanceController extends Controller
{
    public function exportCsv(User $user)
    {
        $month = request('month', now()->format('Y-m'));

        $start = Carbon::parse($month)->startOfMonth();
        $end   = Carbon::parse($month)->endOfMonth();

        $dates = CarbonPeriod::create($start, $end);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn ($a) => $a->date->format('Y-m-d'));

            $fileName = "{$user->name}_{$month}_attendance.csv";

            $headers = [
                "Content-Type" => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename={$fileName}",
            ];

            $callback = function () use ($dates, $attendances) {
                $file = fopen('php://output', 'w');

                fwrite($file, "\xEF\xBB\xBF");

                fputcsv($file, ['日付', '出勤', '退勤', '休憩', '合計']);

                foreach ($dates as $date) {
                    $attendance = $attendances[$date->format('Y-m-d')] ?? null;

                    fputcsv($file, [
                        $date->format('Y-m-d'),
                        $attendance?->clock_in_time?->format('H:i') ?? '',
                        $attendance?->clock_out_time?->format('H:i') ?? '',
                        $attendance?->total_break_time
                            ? gmdate('H:i', $attendance->total_break_time * 60)
                            : '',
                        $attendance?->total_working_time
                            ? gmdate('H:i', $attendance->total_working_time * 60)
                            : '',
                    ]);
                }

                fclose($file);
            };

        return new StreamedResponse($callback, 200, $headers);
    }
}

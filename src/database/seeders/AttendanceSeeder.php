<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {

            for ($i = 0; $i < 5; $i++) {

                $date = Carbon::today()->subDays($i);

                $attendance = Attendance::create([
                    'user_id'            => $user->id,
                    'date'               => $date,
                    'clock_in_time'      => '09:00',
                    'clock_out_time'     => '18:00',
                    'total_working_time' => 8 * 60, // 分
                    'total_break_time'   => 60,
                    'is_modified'        => false,
                ]);

                AttendanceBreak::create([
                    'attendance_id'   => $attendance->id,
                    'break_start'=> '12:00',
                    'break_end'  => '13:00',
                ]);
            }
        }
    }
}



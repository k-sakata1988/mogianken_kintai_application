<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttendanceBreak;
use App\Models\AttendanceRequest;
use Carbon\Carbon;


class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'total_working_time',
        'total_break_time',
        'is_modified',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
    ];

    /**
     * ユーザーを取得
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * 休憩時間の記録を取得
     */
    public function breaks(){
        return $this->hasMany(AttendanceBreak::class);
    }

    /**
     * 修正申請を取得
     */
    public function requests(){
        return $this->hasMany(AttendanceRequest::class);
    }

    public function hasPendingRequest(): bool{
        return $this->requests()->pending()->exists();
    }

    public function getStatusAttribute(){
        if (!$this->clock_in_time) {
            return 'before_work';
        }

        if ($this->clock_in_time && !$this->clock_out_time) {
            $latestBreak = $this->breaks->last();
            if ($latestBreak && !$latestBreak->break_end) {
                return 'breaking';
            }
            return 'working';
        }
        return 'finished';
    }

    public function getTotalBreakTimeAttribute(): int{
        return $this->calculateTotalBreakMinutes();
    }

    public function calculateTotalBreakMinutes(): int
    {
    return $this->breaks
        ->whereNotNull('break_end')
        ->sum(function ($break) {
            return Carbon::parse($break->break_start)
                ->diffInMinutes(Carbon::parse($break->break_end));
        });
    }

    public function calculateWorkingMinutes(): int{
        if (! $this->clock_in_time || ! $this->clock_out_time) {
            return 0;
        }

        $workMinutes = Carbon::parse($this->clock_in_time)->diffInMinutes(Carbon::parse($this->clock_out_time));

        return max(
            $workMinutes - ($this->total_break_time ?? 0),
            0
        );
    }
}

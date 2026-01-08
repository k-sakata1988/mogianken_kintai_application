<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttendanceBreak;
use App\Models\AttendanceRequest;

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
        return $this->hasMany(AttendanceRequests::class);
    }
    public function getStatusAttribute(){
        if (!$this->clock_in_time) {
            return 'before_work';
        }

        if ($this->clock_in_time && !$this->clock_out_time) {
            $latestBreak = $this->breaks()->latest()->first();
            if ($latestBreak && !$latestBreak->break_end) {
                return 'breaking';
            }
            return 'working';
        }
        return 'finished';
    }
}

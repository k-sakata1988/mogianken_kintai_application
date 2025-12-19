<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;

class AttendanceBreak extends Model
{
    use HasFactory;
    protected $table = 'breaks';

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];

    /**
     * 勤怠記録の取得
     */
    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttendanceBreak;
use App\Models\AttendanceRequest;

class Attendance extends Model
{
    use HasFactory;

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
}

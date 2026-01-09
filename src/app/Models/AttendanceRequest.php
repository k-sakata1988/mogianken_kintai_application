<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;
use App\Models\User;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'request_user_id',
        'approver_user_id',
        'before_data',
        'after_data',
        'reason',
        'status',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data'  => 'array',
    ];
    /**
     * 勤怠記録の取得
     */
    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    /**
     * ユーザーの取得
     */
    public function requester(){
        return $this->belongsTo(User::class, 'request_user_id');
    }

    /**
     * 承認/否認した管理者の取得
     */
    public function approver(){
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function isPending(): bool{
        return $this->status === 'pending';
    }

    public function isApproved(): bool{
        return $this->status === 'approved';
    }

    public function isRejected(): bool{
        return $this->status === 'rejected';
    }

    public function scopePending($query){
        return $query->where('status', 'pending');
    }
}

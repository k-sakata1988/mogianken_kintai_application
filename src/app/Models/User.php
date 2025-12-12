<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Attendance;
use App\Models\AttendanceRequest;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 勤怠記録の取得
     */
    public function attendances(){
        return $this->hasMany(Attendance::class);
    }

    /**
     * 修正申請の取得
     */
    public function submittedRequests(){
        return $this->hasMany(AttendanceRequest::class, 'request_user_id');
    }

    /**
     * 承認/否認した修正申請の取得
     */
    public function approveRequests(){
        return $this->hasMany(AttendanceRequest::class, 'approver_user_id');
    }
}

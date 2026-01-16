<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function show(Attendance $attendance){
        return view('admin.attendance.show', compact('attendance'));
    }
}

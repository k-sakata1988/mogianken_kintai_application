<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        $attendances = Attendance::with('user')
            ->whereDate('date', $date)
            ->orderBy('user_id')
            ->get();

        return view('admin.dashboard', [
            'date' => $date,
            'attendances' => $attendances,
        ]);
    }
}

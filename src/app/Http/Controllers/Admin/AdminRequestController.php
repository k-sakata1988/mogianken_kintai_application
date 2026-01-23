<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = AttendanceRequest::with('requester')
            ->when($status === 'pending', function ($q) {
                $q->where('status', 'pending');
            })
            ->when($status === 'approved', function ($q) {
                $q->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.request.list', compact('requests', 'status'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;

class NotificationWebController extends Controller
{
    public function index()
    {
        $notifications = NotificationLog::with('customer')->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }
}
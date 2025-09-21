<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MembershipCode;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalMembers = Member::count();
        $usedCodes = MembershipCode::where('used', true)->count();

        return view('dashboard.admin', compact('totalMembers', 'usedCodes'));
    }
}

<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipCode;

class MembershipCodeController extends Controller
{
    public function index()
    {
        $codes = MembershipCode::with('user')->latest()->paginate(15);
        return view('admin.codes.index', compact('codes'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
        ]);

        for ($i = 0; $i < $request->count; $i++) {
            MembershipCode::create([
                'code' => strtoupper(\Str::random(8)),
                'used' => false,
            ]);
        }

        return redirect()->route('admin.codes.index')->with('success', 'Codes generated successfully!');
    }
}
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class GenealogyController extends Controller
{
    public function index()
{
    $user = Auth::user();

    if (!$user || !$user->member) {
        return redirect()->back()->with('error', 'No member profile linked to this user.');
    }

    $member = $user->member;

    // Recursively build downline tree
    $chartData = [];
    $this->buildGenealogy($member, null, $chartData);

    return view('genealogy.index', compact('chartData'));
}



   private function buildGenealogy($member, $sponsorName, &$chartData)
{
   $photoUrl = $member->photo 
        ? asset('storage/photos/' . $member->photo)
        : asset('images/default-profile.png');  // ← this replaces the placeholder URL

    
    $fullName = $member->first_name . ' ' . $member->last_name;

    $html = '<div style="text-align:center;">
                <img src="' . $photoUrl . '" style="width:50px;height:50px;border-radius:50%;"><br>
                <strong>' . e($fullName) . '</strong>
            </div>';

    $chartData[] = [['v' => $fullName, 'f' => $html], $sponsorName];

    foreach ($member->sponsoredMembers as $downline) {
        $this->buildGenealogy($downline, $fullName, $chartData);
    }
}
}

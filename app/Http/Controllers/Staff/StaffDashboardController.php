<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class StaffDashboardController extends Controller
{
    /**
     * Display the staff dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get staff statistics
        $myProductsCount = Product::where('created_by', $user->id)->count();
        $activeProductsCount = Product::where('created_by', $user->id)
            ->where('active', true)
            ->count();
        $totalStock = Product::where('created_by', $user->id)
            ->sum('stock_quantity');
        
        // Recent products created by this staff
        $recentProducts = Product::where('created_by', $user->id)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboard.staff', compact(
            'myProductsCount',
            'activeProductsCount', 
            'totalStock',
            'recentProducts'
        ));
    }
}
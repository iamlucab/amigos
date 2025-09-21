<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReferralConfigurationController extends Controller
{
    /**
     * Display a listing of the referral configurations.
     */
    public function index()
    {
        $configurations = ReferralConfiguration::latest()->get();
        return view('admin.referral_configurations.index', compact('configurations'));
    }

    /**
     * Show the form for creating a new configuration.
     */
    public function create()
    {
        return view('admin.referral_configurations.create');
    }

    /**
     * Store a newly created configuration in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_allocation' => 'required|numeric|min:0',
            'max_level' => 'required|integer|min:1|max:11',
            'level_bonuses' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        // Convert level_bonuses to proper format if provided
        if ($request->has('level_bonuses')) {
            $levelBonuses = [];
            foreach ($request->level_bonuses as $level => $amount) {
                if (!empty($amount)) {
                    $levelBonuses[$level] = (float) $amount;
                }
            }
            $validated['level_bonuses'] = $levelBonuses;
        }

        DB::transaction(function () use ($validated) {
            // Deactivate all existing configurations
            ReferralConfiguration::where('is_active', true)
                ->update(['is_active' => false]);
                
            // Create new active configuration
            ReferralConfiguration::create([
                'name' => $validated['name'],
                'total_allocation' => $validated['total_allocation'],
                'max_level' => $validated['max_level'],
                'level_bonuses' => $validated['level_bonuses'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => true
            ]);
        });
        
        return redirect()->route('admin.referral-configurations.index')
            ->with('success', 'Referral configuration created successfully');
    }

    /**
     * Show the form for editing the specified configuration.
     */
    public function edit(ReferralConfiguration $referralConfiguration)
    {
        return view('admin.referral_configurations.edit', compact('referralConfiguration'));
    }

    /**
     * Update the specified configuration in storage.
     */
    public function update(Request $request, ReferralConfiguration $referralConfiguration)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_allocation' => 'required|numeric|min:0',
            'max_level' => 'required|integer|min:1|max:11',
            'level_bonuses' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        // Convert level_bonuses to proper format if provided
        if ($request->has('level_bonuses')) {
            $levelBonuses = [];
            foreach ($request->level_bonuses as $level => $amount) {
                if (!empty($amount)) {
                    $levelBonuses[$level] = (float) $amount;
                }
            }
            $validated['level_bonuses'] = $levelBonuses;
        }

        $referralConfiguration->update($validated);
        
        return redirect()->route('admin.referral-configurations.index')
            ->with('success', 'Referral configuration updated successfully');
    }

    /**
     * Activate the specified configuration.
     */
    public function activate(ReferralConfiguration $referralConfiguration)
    {
        DB::transaction(function () use ($referralConfiguration) {
            // Deactivate all configurations
            ReferralConfiguration::where('is_active', true)
                ->update(['is_active' => false]);
                
            // Activate the selected configuration
            $referralConfiguration->update(['is_active' => true]);
        });
        
        return redirect()->route('admin.referral-configurations.index')
            ->with('success', 'Referral configuration activated successfully');
    }

    /**
     * Remove the specified configuration from storage.
     */
    public function destroy(ReferralConfiguration $referralConfiguration)
    {
        // Don't allow deleting the active configuration
        if ($referralConfiguration->is_active) {
            return redirect()->route('admin.referral-configurations.index')
                ->with('error', 'Cannot delete the active configuration');
        }
        
        $referralConfiguration->delete();
        
        return redirect()->route('admin.referral-configurations.index')
            ->with('success', 'Referral configuration deleted successfully');
    }
    
    /**
     * Preview bonus distribution based on provided parameters.
     */
    public function preview(Request $request)
    {
        $totalAllocation = $request->input('total_allocation', 0);
        $maxLevel = $request->input('max_level', 1);
        $levelBonuses = $request->input('level_bonuses', []);
        
        // Create a temporary configuration for preview
        $config = new ReferralConfiguration([
            'total_allocation' => $totalAllocation,
            'max_level' => $maxLevel,
            'level_bonuses' => $levelBonuses,
        ]);
        
        $bonuses = $config->getAllBonuses();
        
        return response()->json([
            'bonuses' => $bonuses,
            'total' => array_sum($bonuses),
        ]);
    }
}

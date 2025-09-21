<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashInRequest;
use App\Models\Member;
use Carbon\Carbon;

class TestCashInSeeder extends Seeder
{
    public function run()
    {
        // Get the admin member
        $adminMember = Member::where('mobile_number', '09177260180')->first();
        
        if (!$adminMember) {
            $this->command->error('Admin member not found. Please run DeploymentSeeder first.');
            return;
        }

        // Create test cash-in requests
        $requests = [
            [
                'member_id' => $adminMember->id,
                'amount' => 1000.00,
                'note' => 'Test cash-in request with proof',
                'payment_method' => 'GCash',
                'proof_path' => 'proofs/test-proof-1.jpg',
                'status' => 'Pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'member_id' => $adminMember->id,
                'amount' => 500.00,
                'note' => 'Another test request',
                'payment_method' => 'PayMaya',
                'proof_path' => 'proofs/test-proof-2.png',
                'status' => 'Reviewed',
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(1),
            ],
        ];

        foreach ($requests as $request) {
            CashInRequest::create($request);
        }

        $this->command->info('✅ Test cash-in requests created successfully.');
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\User;
use App\Models\MembershipCode;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, let's generate enough membership codes for existing members without codes
        $this->generateMembershipCodesForExistingMembers();
        
        // Then assign codes to existing members who don't have them
        $this->assignCodesToExistingMembers();
        
        // Update member statuses from old system to new system
        $this->updateMemberStatuses();
    }

    /**
     * Generate membership codes for existing members who don't have codes assigned
     */
    private function generateMembershipCodesForExistingMembers()
    {
        // Get count of members without membership codes
        $membersWithoutCodes = DB::table('members')
            ->leftJoin('users', 'members.id', '=', 'users.member_id')
            ->leftJoin('membership_codes', 'users.id', '=', 'membership_codes.used_by')
            ->whereNull('membership_codes.id')
            ->count();

        if ($membersWithoutCodes > 0) {
            echo "Generating {$membersWithoutCodes} membership codes for existing members...\n";
            
            for ($i = 0; $i < $membersWithoutCodes; $i++) {
                do {
                    $code = strtoupper(\Str::random(8));
                } while (MembershipCode::where('code', $code)->exists());

                MembershipCode::create([
                    'code' => $code,
                    'used' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            echo "Generated {$membersWithoutCodes} new membership codes.\n";
        }
    }

    /**
     * Assign membership codes to existing members who don't have them
     */
    private function assignCodesToExistingMembers()
    {
        // Get members without assigned membership codes
        $membersWithoutCodes = DB::table('members')
            ->leftJoin('users', 'members.id', '=', 'users.member_id')
            ->leftJoin('membership_codes', 'users.id', '=', 'membership_codes.used_by')
            ->whereNull('membership_codes.id')
            ->select('members.*', 'users.id as user_id')
            ->get();

        if ($membersWithoutCodes->count() > 0) {
            echo "Assigning membership codes to {$membersWithoutCodes->count()} existing members...\n";
            
            foreach ($membersWithoutCodes as $member) {
                // Get an unused membership code
                $availableCode = MembershipCode::where('used', false)->first();
                
                if ($availableCode && $member->user_id) {
                    // Assign the code to this member
                    $availableCode->update([
                        'used' => true,
                        'used_by' => $member->user_id,
                        'used_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    echo "Assigned code {$availableCode->code} to member {$member->first_name} {$member->last_name} (ID: {$member->id})\n";
                }
            }
            
            echo "Completed assigning membership codes to existing members.\n";
        }
    }

    /**
     * Update member statuses to match new system requirements
     */
    private function updateMemberStatuses()
    {
        // Update any members with status 'Active' to 'Approved' (new system)
        $updatedMembers = DB::table('members')
            ->where('status', 'Active')
            ->update(['status' => 'Approved', 'updated_at' => now()]);
            
        if ($updatedMembers > 0) {
            echo "Updated {$updatedMembers} members from 'Active' to 'Approved' status.\n";
        }

        // Update corresponding users
        $updatedUsers = DB::table('users')
            ->where('status', 'Active')
            ->update(['status' => 'Approved', 'updated_at' => now()]);
            
        if ($updatedUsers > 0) {
            echo "Updated {$updatedUsers} users from 'Active' to 'Approved' status.\n";
        }

        // Ensure all existing approved members have proper status
        DB::table('members')
            ->whereIn('status', ['active', 'ACTIVE'])
            ->update(['status' => 'Approved', 'updated_at' => now()]);
            
        DB::table('users')
            ->whereIn('status', ['active', 'ACTIVE'])
            ->update(['status' => 'Approved', 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Note: This is a data migration, reversal would be complex and potentially destructive
        // We'll leave the data as-is for safety
        echo "Warning: This migration cannot be safely reversed as it involves data migration.\n";
    }
};
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipCodeRequest;
use App\Models\MembershipCode;
use App\Models\MembershipCodeRequestItem;
use App\Models\Notification;

class MembershipCodeRequestController extends Controller
{
    public function index()
    {
        $requests = MembershipCodeRequest::with('member.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Mark membership code request notifications as read
        Notification::where('user_id', auth()->id())
            ->where('type', 'membership_code_request')
            ->where('read', false)
            ->update(['read' => true]);

        return view('admin.membership-code-requests.index', compact('requests'));
    }

    public function update(Request $request, MembershipCodeRequest $membershipCodeRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,cancelled'
        ]);

        $oldStatus = $membershipCodeRequest->status;
        $newStatus = $request->status;
        
        $membershipCodeRequest->update([
            'status' => $newStatus
        ]);

        // If approved, generate the codes and reserve them for the member
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            $this->generateAndReserveCodes($membershipCodeRequest);
        }

        return redirect()->back()->with('success', 'Membership code request updated successfully.');
    }

    public function getAvailableCodes(Request $request)
    {
        $quantity = $request->query('quantity', 20);
        $search = $request->query('search', '');
        
        $query = MembershipCode::available();
        
        if ($search) {
            $query->where('code', 'like', '%' . $search . '%');
        }
        
        $codes = $query->take($quantity)->get();
        
        // Add status attribute to each code for the frontend
        $codes->each(function ($code) {
            $code->status = $code->status;
        });
        
        return response()->json([
            'success' => true,
            'data' => $codes
        ]);
    }

    public function assignCodes(Request $request, MembershipCodeRequest $membershipCodeRequest)
    {
        $request->validate([
            'code_ids' => 'required|array',
            'code_ids.*' => 'exists:membership_codes,id'
        ]);
        
        $codeIds = $request->code_ids;
        $userId = $membershipCodeRequest->member->user->id;
        
        // Check if we have the correct number of codes
        if (count($codeIds) != $membershipCodeRequest->quantity) {
            return redirect()->back()->with('error', 'Please select exactly ' . $membershipCodeRequest->quantity . ' codes.');
        }
        
        // Check if any of the selected codes are already reserved or used
        $codes = MembershipCode::whereIn('id', $codeIds)->get();
        foreach ($codes as $code) {
            if ($code->reserved || $code->used) {
                return redirect()->back()->with('error', 'One or more selected codes are already reserved or used.');
            }
        }
        
        // Reserve the codes for the member and link them to the request
        foreach ($codes as $code) {
            $code->markAsReserved($userId);
            
            // Create request item to link the code with the request
            MembershipCodeRequestItem::create([
                'membership_code_request_id' => $membershipCodeRequest->id,
                'membership_code_id' => $code->id
            ]);
        }
        
        // Update the request with the reserved codes count
        $membershipCodeRequest->update([
            'reserved_codes_count' => count($codeIds),
            'status' => 'approved'
        ]);
        
        return redirect()->back()->with('success', 'Codes assigned successfully.');
    }

    private function generateAndReserveCodes(MembershipCodeRequest $membershipCodeRequest)
    {
        $quantity = $membershipCodeRequest->quantity;
        $userId = $membershipCodeRequest->member->user->id;

        // Check if codes are already assigned to this request
        if ($membershipCodeRequest->requestItems->count() > 0) {
            // Codes already assigned, make sure they are reserved for the correct user
            $reservedCount = 0;
            foreach ($membershipCodeRequest->requestItems as $item) {
                // Always mark as reserved for the correct user, regardless of previous reservation status
                $item->membershipCode->markAsReserved($userId);
                $reservedCount++;
            }
            // Update the request with the reserved codes count
            $membershipCodeRequest->update([
                'reserved_codes_count' => $reservedCount
            ]);
            return;
        }
        
        // Get available codes first
        $availableCodes = MembershipCode::available()->take($quantity)->get();

        // If we don't have enough available codes, generate more
        $neededCodes = $quantity - $availableCodes->count();
        if ($neededCodes > 0) {
            for ($i = 0; $i < $neededCodes; $i++) {
                $code = MembershipCode::generateCode();
                $availableCodes->push($code);
            }
        }

        // Reserve the codes for the member and link them to the request
        $reservedCodes = [];
        foreach ($availableCodes->take($quantity) as $code) {
            // Only reserve if not already reserved
            if (!$code->reserved) {
                $code->markAsReserved($userId);
            }
            $reservedCodes[] = $code;
        }
        
        // Create request items to link the codes with the request
        foreach ($reservedCodes as $code) {
            // Check if the relationship already exists to avoid duplicates
            $exists = MembershipCodeRequestItem::where('membership_code_request_id', $membershipCodeRequest->id)
                ->where('membership_code_id', $code->id)
                ->exists();
                
            if (!$exists) {
                MembershipCodeRequestItem::create([
                    'membership_code_request_id' => $membershipCodeRequest->id,
                    'membership_code_id' => $code->id
                ]);
            }
        }
        
        // Update the request with the reserved codes count
        $membershipCodeRequest->update([
            'reserved_codes_count' => count($reservedCodes)
        ]);
    }
}

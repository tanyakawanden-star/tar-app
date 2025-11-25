<?php

namespace App\Http\Controllers;

use App\Mail\TravelRequestApproved;
use App\Mail\TravelRequestRejected;
use App\Models\Approval;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Pending approvals for current user (manager/finance/director)
    public function index()
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['manager', 'finance', 'director', 'admin'])) {
            abort(403);
        }

        $query = TravelRequest::query();

        if ($user->isManager()) {
            $query->where('status', TravelRequest::STATUS_SUBMITTED);
        } elseif ($user->isFinance()) {
            $query->where('status', TravelRequest::STATUS_MANAGER_APPROVED);
        } elseif ($user->isDirector()) {
            $query->where('status', TravelRequest::STATUS_FINANCE_APPROVED)
                ->where('is_overseas', true);
        }

        $requests = $query->latest()->paginate(15);

        return view('approvals.index', compact('requests'));
    }

    public function show(TravelRequest $travelRequest)
    {
        $user = Auth::user();

        if (!$travelRequest->canBeApprovedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $travelRequest->load(['user', 'approvals']);

        return view('approvals.show', compact('travelRequest'));
    }

    public function approve(Request $request, TravelRequest $travelRequest)
    {
        $user = Auth::user();

        if (!$travelRequest->canBeApprovedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $note = $request->input('note');

        $fromStatus = $travelRequest->status;
        $nextStatus = $travelRequest->nextStatusAfterApproval();

        if (!$nextStatus) {
            return back()->with('error', 'Cannot approve this request in its current status.');
        }

        if ($user->isManager()) {
            $travelRequest->approved_by_manager_at = now();
        } elseif ($user->isFinance()) {
            $travelRequest->approved_by_finance_at = now();
        } elseif ($user->isDirector()) {
            $travelRequest->approved_by_director_at = now();
        }

        $travelRequest->status = $nextStatus;
        $travelRequest->rejected_at = null;
        $travelRequest->rejected_reason = null;
        $travelRequest->save();

        Approval::create([
            'travel_request_id' => $travelRequest->id,
            'user_id' => $user->id,
            'role' => $user->role,
            'action' => 'APPROVED',
            'from_status' => $fromStatus,
            'to_status' => $nextStatus,
            'note' => $note,
        ]);

        // Email notifications
        Mail::to($travelRequest->user->email)->queue(
            new TravelRequestApproved($travelRequest, $user)
        );

        // If manager approved -> notify finance
        if ($user->isManager()) {
            $finances = User::where('role', 'finance')->get();
            foreach ($finances as $finance) {
                Mail::to($finance->email)->queue(
                    new TravelRequestApproved($travelRequest, $user)
                );
            }
        }

        // If finance approved and overseas -> notify director
        if ($user->isFinance() && $travelRequest->isOverseas()) {
            $directors = User::where('role', 'director')->get();
            foreach ($directors as $director) {
                Mail::to($director->email)->queue(
                    new TravelRequestApproved($travelRequest, $user)
                );
            }
        }

        return back()->with('success', 'Travel request approved and moved to status ' . $nextStatus);
    }

    public function reject(Request $request, TravelRequest $travelRequest)
    {
        $user = Auth::user();

        if (!$travelRequest->canBeApprovedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string',
        ]);

        $reason = $request->input('reason');

        $fromStatus = $travelRequest->status;
        $travelRequest->status = TravelRequest::STATUS_REJECTED;
        $travelRequest->rejected_at = now();
        $travelRequest->rejected_reason = $reason;
        $travelRequest->save();

        Approval::create([
            'travel_request_id' => $travelRequest->id,
            'user_id' => $user->id,
            'role' => $user->role,
            'action' => 'REJECTED',
            'from_status' => $fromStatus,
            'to_status' => TravelRequest::STATUS_REJECTED,
            'note' => $reason,
        ]);

        Mail::to($travelRequest->user->email)->queue(
            new TravelRequestRejected($travelRequest, $user, $reason)
        );

        return back()->with('success', 'Travel request rejected.');
    }
}

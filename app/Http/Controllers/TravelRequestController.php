<?php

namespace App\Http\Controllers;

use App\Mail\TravelRequestCreated;
use App\Models\Approval;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TravelRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Employee: list own requests
    public function index()
    {
        $user = Auth::user();

        $queries = TravelRequest::query();

        if (!$user->isAdmin() && !$user->hasAnyRole(['manager', 'finance', 'director'])) {
            $queries->where('user_id', $user->id);
        }

        $requests = $queries->latest()->paginate(15);

        return view('dashboard', [
            'requests' => $requests,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        return view('travel_requests.create', [
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'destination_city' => 'required|string|max:255',
            'destination_country' => 'nullable|string|max:255',
            'is_overseas' => 'nullable|boolean',
            'purpose' => 'nullable|string',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'estimated_transport_cost' => 'nullable|numeric|min:0',
            'estimated_hotel_cost' => 'nullable|numeric|min:0',
            'estimated_meals_cost' => 'nullable|numeric|min:0',
            'estimated_other_cost' => 'nullable|numeric|min:0',
        ]);

        $validated['is_overseas'] = $request->boolean('is_overseas');
        $validated['user_id'] = $user->id;
        $validated['tar_number'] = TravelRequest::generateTarNumber();
        $validated['status'] = TravelRequest::STATUS_SUBMITTED;

        $travelRequest = TravelRequest::create($validated);

        // Log initial submission
        Approval::create([
            'travel_request_id' => $travelRequest->id,
            'user_id' => $user->id,
            'role' => $user->role,
            'action' => 'SUBMITTED',
            'from_status' => null,
            'to_status' => $travelRequest->status,
            'note' => null,
        ]);

        // Notify managers
        $managers = User::where('role', 'manager')->get();
        foreach ($managers as $manager) {
            Mail::to($manager->email)->queue(new TravelRequestCreated($travelRequest, $manager));
        }

        return redirect()
            ->route('travel-requests.show', $travelRequest)
            ->with('success', 'Travel request submitted with TAR number ' . $travelRequest->tar_number);
    }

    public function show(TravelRequest $travelRequest)
    {
        $this->authorizeView($travelRequest);

        $travelRequest->load(['user', 'approvals', 'expenses']);

        return view('travel_requests.show', compact('travelRequest'));
    }

    public function edit(TravelRequest $travelRequest)
    {
        $this->authorizeOwner($travelRequest);

        if (!in_array($travelRequest->status, [TravelRequest::STATUS_DRAFT, TravelRequest::STATUS_SUBMITTED], true)) {
            abort(403, 'Cannot edit travel request after approvals started.');
        }

        return view('travel_requests.edit', compact('travelRequest'));
    }

    public function update(Request $request, TravelRequest $travelRequest)
    {
        $this->authorizeOwner($travelRequest);

        if (!in_array($travelRequest->status, [TravelRequest::STATUS_DRAFT, TravelRequest::STATUS_SUBMITTED], true)) {
            abort(403, 'Cannot edit travel request after approvals started.');
        }

        $validated = $request->validate([
            'destination_city' => 'required|string|max:255',
            'destination_country' => 'nullable|string|max:255',
            'is_overseas' => 'nullable|boolean',
            'purpose' => 'nullable|string',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'estimated_transport_cost' => 'nullable|numeric|min:0',
            'estimated_hotel_cost' => 'nullable|numeric|min:0',
            'estimated_meals_cost' => 'nullable|numeric|min:0',
            'estimated_other_cost' => 'nullable|numeric|min:0',
        ]);

        $validated['is_overseas'] = $request->boolean('is_overseas');

        $travelRequest->update($validated);

        return redirect()
            ->route('travel-requests.show', $travelRequest)
            ->with('success', 'Travel request updated.');
    }

    public function destroy(TravelRequest $travelRequest)
    {
        $this->authorizeOwner($travelRequest);

        if ($travelRequest->status !== TravelRequest::STATUS_DRAFT) {
            abort(403, 'Cannot delete travel request after submission.');
        }

        $travelRequest->delete();

        return redirect()
            ->route('travel-requests.index')
            ->with('success', 'Travel request deleted.');
    }

    // --- PDF download ---

    public function pdf(TravelRequest $travelRequest)
    {
        $this->authorizeView($travelRequest);

        // You can plug dompdf/snappy here. For now just return view.
        return view('pdf.tar', [
            'travelRequest' => $travelRequest->load('user', 'approvals'),
        ]);
    }

    // --- Internal helpers ---

    protected function authorizeOwner(TravelRequest $travelRequest): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ($travelRequest->user_id !== $user->id) {
            abort(403);
        }
    }

    protected function authorizeView(TravelRequest $travelRequest): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ($travelRequest->user_id === $user->id) {
            return;
        }

        if ($user->hasAnyRole(['manager', 'finance', 'director'])) {
            return;
        }

        abort(403);
    }
}

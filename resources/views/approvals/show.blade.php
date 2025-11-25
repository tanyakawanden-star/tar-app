@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Review Travel Request</h1>

    <p>
        <strong>TAR No:</strong> {{ $travelRequest->tar_number }}<br>
        <strong>Requester:</strong> {{ $travelRequest->user->name }} ({{ $travelRequest->user->department }})<br>
        <strong>Status:</strong> {{ $travelRequest->status }}
    </p>

    <div class="mb-3">
        <a href="{{ route('approvals.index') }}" class="btn btn-secondary btn-sm">Back to list</a>
        <a href="{{ route('travel-requests.pdf', $travelRequest) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Print / PDF</a>
        <a href="{{ route('travel-requests.show', $travelRequest) }}" class="btn btn-outline-secondary btn-sm">View Full</a>
    </div>

    {{-- Travel details card --}}
    <div class="card mb-3">
        <div class="card-header">Travel Details</div>
        <div class="card-body">
            <p><strong>Destination:</strong> {{ $travelRequest->destination_city }}
                @if($travelRequest->destination_country), {{ $travelRequest->destination_country }}@endif</p>
            <p><strong>Overseas:</strong> {{ $travelRequest->is_overseas ? 'Yes' : 'No' }}</p>
            <p><strong>Dates:</strong> {{ $travelRequest->departure_date->format('d M Y') }} — {{ $travelRequest->return_date->format('d M Y') }}</p>
            <p><strong>Purpose:</strong><br>{{ $travelRequest->purpose }}</p>
        </div>
    </div>

    {{-- Estimated costs --}}
    <div class="card mb-3">
        <div class="card-header">Estimated Costs</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Transport</strong><div>{{ number_format($travelRequest->estimated_transport_cost, 2) }}</div></div>
                <div class="col-md-3"><strong>Hotel</strong><div>{{ number_format($travelRequest->estimated_hotel_cost, 2) }}</div></div>
                <div class="col-md-3"><strong>Meals</strong><div>{{ number_format($travelRequest->estimated_meals_cost, 2) }}</div></div>
                <div class="col-md-3"><strong>Other</strong><div>{{ number_format($travelRequest->estimated_other_cost, 2) }}</div></div>
            </div>
        </div>
    </div>

    {{-- Approval actions --}}
    <div class="card mb-3">
        <div class="card-header">Approval</div>
        <div class="card-body">
            @if(auth()->user()->can(function () use ($travelRequest) { return $travelRequest->canBeApprovedBy(auth()->user()); }))
                <form method="POST" action="{{ route('approvals.approve', $travelRequest) }}" class="mb-3">
                    @csrf
                    <div class="mb-2">
                        <label for="note" class="form-label">Note (optional)</label>
                        <textarea name="note" id="note" rows="2" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>

                <form method="POST" action="{{ route('approvals.reject', $travelRequest) }}">
                    @csrf
                    <div class="mb-2">
                        <label for="reason" class="form-label">Reject reason (required)</label>
                        <textarea name="reason" id="reason" rows="2" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            @else
                <div class="alert alert-info">You are not authorized to approve or reject this request.</div>
            @endif
        </div>
    </div>

    {{-- Approval history --}}
    <div class="card mb-3">
        <div class="card-header">Approval History</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>By</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($travelRequest->approvals as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $log->user->name }}</td>
                            <td>{{ $log->role }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->from_status }}</td>
                            <td>{{ $log->to_status }}</td>
                            <td>{{ $log->note }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No approval activity yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

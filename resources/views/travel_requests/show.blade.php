@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Travel Authorization Detail</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <p class="mb-1"><strong>TAR No:</strong> {{ $travelRequest->tar_number }}</p>
            <p class="mb-1"><strong>Status:</strong> {{ $travelRequest->status }}</p>
        </div>
        <div>
            <a href="{{ route('travel-requests.pdf', $travelRequest) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                Print / PDF
            </a>
            @if(auth()->id() === $travelRequest->user_id && in_array($travelRequest->status, [\App\Models\TravelRequest::STATUS_DRAFT, \App\Models\TravelRequest::STATUS_SUBMITTED]))
                <a href="{{ route('travel-requests.edit', $travelRequest) }}" class="btn btn-primary btn-sm">
                    Edit
                </a>
            @endif
            <a href="{{ route('travel-requests.index') }}" class="btn btn-secondary btn-sm">
                Back to List
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Requester Information
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Name:</strong> {{ $travelRequest->user->name }}
                </div>
                <div class="col-md-4">
                    <strong>NIK:</strong> {{ $travelRequest->user->nik }}
                </div>
                <div class="col-md-4">
                    <strong>Department:</strong> {{ $travelRequest->user->department }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Email:</strong> {{ $travelRequest->user->email }}
                </div>
                <div class="col-md-4">
                    <strong>Created At:</strong> {{ $travelRequest->created_at->format('d M Y H:i') }}
                </div>
                <div class="col-md-4">
                    <strong>Last Update:</strong> {{ $travelRequest->updated_at->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Travel Details
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Destination:</strong>
                    {{ $travelRequest->destination_city }}
                    @if($travelRequest->destination_country)
                        , {{ $travelRequest->destination_country }}
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>Overseas:</strong> {{ $travelRequest->is_overseas ? 'Yes' : 'No' }}
                </div>
                <div class="col-md-4">
                    <strong>Travel Dates:</strong>
                    {{ $travelRequest->departure_date->format('d M Y') }} –
                    {{ $travelRequest->return_date->format('d M Y') }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <strong>Purpose:</strong>
                    <div>{{ $travelRequest->purpose }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Estimated Costs
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3">
                    <strong>Transport:</strong> {{ number_format($travelRequest->estimated_transport_cost, 2) }}
                </div>
                <div class="col-md-3">
                    <strong>Hotel:</strong> {{ number_format($travelRequest->estimated_hotel_cost, 2) }}
                </div>
                <div class="col-md-3">
                    <strong>Meals:</strong> {{ number_format($travelRequest->estimated_meals_cost, 2) }}
                </div>
                <div class="col-md-3">
                    <strong>Other:</strong> {{ number_format($travelRequest->estimated_other_cost, 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Approval Status
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Manager:</strong>
                    @if($travelRequest->approved_by_manager_at)
                        Approved at {{ $travelRequest->approved_by_manager_at->format('d M Y H:i') }}
                    @else
                        Pending
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>Finance:</strong>
                    @if($travelRequest->approved_by_finance_at)
                        Approved at {{ $travelRequest->approved_by_finance_at->format('d M Y H:i') }}
                    @else
                        Pending
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>Director:</strong>
                    @if($travelRequest->approved_by_director_at)
                        Approved at {{ $travelRequest->approved_by_director_at->format('d M Y H:i') }}
                    @else
                        {{ $travelRequest->is_overseas ? 'Pending' : 'Not Required' }}
                    @endif
                </div>
            </div>

            @if($travelRequest->rejected_at)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <strong>Rejected:</strong>
                        {{ $travelRequest->rejected_at->format('d M Y H:i') }}<br>
                        <strong>Reason:</strong> {{ $travelRequest->rejected_reason }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Approval History
        </div>
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
                    <tr>
                        <td colspan="7" class="text-center">No approval history yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

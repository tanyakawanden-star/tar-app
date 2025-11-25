@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <p>Welcome, {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>

    @if(auth()->user()->isEmployee())
        <div class="mb-3">
            <a href="{{ route('travel-requests.create') }}" class="btn btn-primary">
                New Travel Authorization
            </a>
        </div>
    @endif

    <h3>My Travel Requests</h3>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>TAR No</th>
                <th>Destination</th>
                <th>Dates</th>
                <th>Status</th>
                <th>Last Update</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($requests as $request)
            <tr>
                <td>{{ $request->tar_number }}</td>
                <td>
                    {{ $request->destination_city }}
                    @if($request->destination_country)
                        , {{ $request->destination_country }}
                    @endif
                </td>
                <td>{{ $request->departure_date->format('d M Y') }} – {{ $request->return_date->format('d M Y') }}</td>
                <td>{{ $request->status }}</td>
                <td>{{ $request->updated_at->format('d M Y H:i') }}</td>
                <td>
                    <a href="{{ route('travel-requests.show', $request) }}" class="btn btn-sm btn-outline-secondary">View</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No travel requests.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    {{ $requests->links() }}

    @if(auth()->user()->hasAnyRole(['manager','finance','director','admin']))
        <hr>
        <h3>Pending Approvals</h3>
        <a href="{{ route('approvals.index') }}" class="btn btn-outline-primary btn-sm">
            Go to Approval List
        </a>
    @endif
</div>
@endsection

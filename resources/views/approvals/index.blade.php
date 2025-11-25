@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pending Approvals</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <p class="mb-3">Hello, {{ auth()->user()->name }} — role: <strong>{{ auth()->user()->role }}</strong></p>

    @if($requests->isEmpty())
        <div class="alert alert-info">No pending approvals.</div>
    @else
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>TAR No</th>
                    <th>Requester</th>
                    <th>Destination</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                    <tr>
                        <td>{{ $req->tar_number }}</td>
                        <td>{{ $req->user->name }}<br><small>{{ $req->user->department }}</small></td>
                        <td>
                            {{ $req->destination_city }}
                            @if($req->destination_country), {{ $req->destination_country }}@endif
                        </td>
                        <td>{{ $req->departure_date->format('d M Y') }} — {{ $req->return_date->format('d M Y') }}</td>
                        <td>{{ $req->status }}</td>
                        <td>{{ $req->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('approvals.show', $req) }}" class="btn btn-sm btn-primary">Review</a>
                            <a href="{{ route('travel-requests.show', $req) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $requests->links() }}
    @endif
</div>
@endsection

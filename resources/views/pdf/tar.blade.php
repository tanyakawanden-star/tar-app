<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Travel Authorization {{ $travelRequest->tar_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
    </style>
</head>
<body>
    <h2>Travel Authorization</h2>
    <p><strong>TAR No:</strong> {{ $travelRequest->tar_number }}</p>

    <h3>Requester Information</h3>
    <table>
        <tr>
            <th>Name</th>
            <td>{{ $travelRequest->user->name }}</td>
        </tr>
        <tr>
            <th>NIK</th>
            <td>{{ $travelRequest->user->nik }}</td>
        </tr>
        <tr>
            <th>Department</th>
            <td>{{ $travelRequest->user->department }}</td>
        </tr>
    </table>

    <h3>Travel Details</h3>
    <table>
        <tr>
            <th>Destination</th>
            <td>
                {{ $travelRequest->destination_city }}
                @if($travelRequest->destination_country)
                    , {{ $travelRequest->destination_country }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Overseas</th>
            <td>{{ $travelRequest->is_overseas ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <th>Departure Date</th>
            <td>{{ $travelRequest->departure_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Return Date</th>
            <td>{{ $travelRequest->return_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Purpose</th>
            <td>{{ $travelRequest->purpose }}</td>
        </tr>
    </table>

    <h3>Estimated Costs</h3>
    <table>
        <tr>
            <th>Transport</th>
            <td>{{ number_format($travelRequest->estimated_transport_cost, 2) }}</td>
        </tr>
        <tr>
            <th>Hotel</th>
            <td>{{ number_format($travelRequest->estimated_hotel_cost, 2) }}</td>
        </tr>
        <tr>
            <th>Meals</th>
            <td>{{ number_format($travelRequest->estimated_meals_cost, 2) }}</td>
        </tr>
        <tr>
            <th>Other</th>
            <td>{{ number_format($travelRequest->estimated_other_cost, 2) }}</td>
        </tr>
    </table>

    <h3>Approval Status</h3>
    <table>
        <tr>
            <th>Manager Approval</th>
            <td>
                @if($travelRequest->approved_by_manager_at)
                    Approved on {{ $travelRequest->approved_by_manager_at->format('d M Y H:i') }}
                @else
                    Pending
                @endif
            </td>
        </tr>
        <tr>
            <th>Finance Approval</th>
            <td>
                @if($travelRequest->approved_by_finance_at)
                    Approved on {{ $travelRequest->approved_by_finance_at->format('d M Y H:i') }}
                @else
                    Pending
                @endif
            </td>
        </tr>
        <tr>
            <th>Director Approval</th>
            <td>
                @if($travelRequest->approved_by_director_at)
                    Approved on {{ $travelRequest->approved_by_director_at->format('d M Y H:i') }}
                @else
                    {{ $travelRequest->is_overseas ? 'Pending' : 'Not Required' }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Overall Status</th>
            <td>{{ $travelRequest->status }}</td>
        </tr>
        @if($travelRequest->rejected_at)
            <tr>
                <th>Rejected</th>
                <td>
                    On {{ $travelRequest->rejected_at->format('d M Y H:i') }}<br>
                    Reason: {{ $travelRequest->rejected_reason }}
                </td>
            </tr>
        @endif
    </table>

    <h3>Approval History</h3>
    <table>
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
        @foreach($travelRequest->approvals as $log)
            <tr>
                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                <td>{{ $log->user->name }}</td>
                <td>{{ $log->role }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->from_status }}</td>
                <td>{{ $log->to_status }}</td>
                <td>{{ $log->note }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

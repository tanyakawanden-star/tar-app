<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Travel Request</title>
</head>
<body>
    <p>Dear {{ optional($recipient)->name ?? 'Manager' }},</p>

    <p>A new travel authorization request has been submitted.</p>

    <p>
        <strong>TAR No:</strong> {{ $travelRequest->tar_number }}<br>
        <strong>Requester:</strong> {{ $travelRequest->user->name }} ({{ $travelRequest->user->department }})<br>
        <strong>Destination:</strong> {{ $travelRequest->destination_city }}
        @if($travelRequest->destination_country)
            , {{ $travelRequest->destination_country }}
        @endif
        <br>
        <strong>Dates:</strong> {{ $travelRequest->departure_date->format('d M Y') }}
        – {{ $travelRequest->return_date->format('d M Y') }}<br>
        <strong>Overseas:</strong> {{ $travelRequest->is_overseas ? 'Yes' : 'No' }}<br>
    </p>

    <p>
        Please log in to the system to review and approve this request.
    </p>
</body>
</html>

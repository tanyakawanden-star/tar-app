<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Travel Request Updated</title>
</head>
<body>
    <p>Dear {{ $travelRequest->user->name }},</p>

    <p>
        Your travel authorization request has been updated.
    </p>

    <p>
        <strong>TAR No:</strong> {{ $travelRequest->tar_number }}<br>
        <strong>Current Status:</strong> {{ $travelRequest->status }}<br>
        <strong>Updated By:</strong> {{ $approver->name }} ({{ $approver->role }})<br>
    </p>

    <p>Please log in to the system to see the full details.</p>
</body>
</html>

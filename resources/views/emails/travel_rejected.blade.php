<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Travel Request Rejected</title>
</head>
<body>
    <p>Dear {{ $travelRequest->user->name }},</p>

    <p>Your travel authorization request has been <strong>rejected</strong>.</p>

    <p>
        <strong>TAR No:</strong> {{ $travelRequest->tar_number }}<br>
        <strong>Rejected By:</strong> {{ $approver->name }} ({{ $approver->role }})<br>
        <strong>Reason:</strong> {{ $reason }}<br>
    </p>

    <p>If you have questions, please contact your approver.</p>
</body>
</html>

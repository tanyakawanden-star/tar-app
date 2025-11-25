<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'travel_request_id',
        'user_id',
        'role',
        'action', // APPROVED / REJECTED / SUBMITTED etc.
        'from_status',
        'to_status',
        'note',
    ];

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

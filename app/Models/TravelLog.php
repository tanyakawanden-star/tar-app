<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelLog extends Model
{
    protected $fillable = [
        'travel_request_id',
        'user_id',
        'action',
        'description',
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

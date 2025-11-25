<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = ['travel_request_id','accommodation','meals','transport','airfare','others','total'];

    public function travelRequest(){ return $this->belongsTo(TravelRequest::class); }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nik',
        'department',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function travelRequests()
    {
        return $this->hasMany(TravelRequest::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    // --- Role helpers ---

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isFinance(): bool
    {
        return $this->role === 'finance';
    }

    public function isDirector(): bool
    {
        return $this->role === 'director';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}

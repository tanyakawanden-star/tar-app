<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TravelRequest extends Model
{
    protected $fillable = [
        'user_id',
        'tar_number',
        'destination_city',
        'destination_country',
        'is_overseas',
        'purpose',
        'departure_date',
        'return_date',
        'estimated_transport_cost',
        'estimated_hotel_cost',
        'estimated_meals_cost',
        'estimated_other_cost',
        'status',
        'approved_by_manager_at',
        'approved_by_finance_at',
        'approved_by_director_at',
        'rejected_at',
        'rejected_reason',
    ];

    protected $casts = [
        'is_overseas' => 'boolean',
        'departure_date' => 'date',
        'return_date' => 'date',
        'approved_by_manager_at' => 'datetime',
        'approved_by_finance_at' => 'datetime',
        'approved_by_director_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // --- Status constants ---

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SUBMITTED = 'SUBMITTED';
    public const STATUS_MANAGER_APPROVED = 'MANAGER_APPROVED';
    public const STATUS_FINANCE_APPROVED = 'FINANCE_APPROVED';
    public const STATUS_DIRECTOR_APPROVED = 'DIRECTOR_APPROVED';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_REJECTED = 'REJECTED';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_MANAGER_APPROVED,
            self::STATUS_FINANCE_APPROVED,
            self::STATUS_DIRECTOR_APPROVED,
            self::STATUS_COMPLETED,
            self::STATUS_REJECTED,
        ];
    }

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // --- TAR number generation ---

    public static function generateTarNumber(): string
    {
        $datePrefix = now()->format('Y-m-d');
        $prefix = 'TAR-' . $datePrefix;

        $last = static::whereDate('created_at', now()->toDateString())
            ->where('tar_number', 'like', $prefix . '-%')
            ->orderBy('tar_number', 'desc')
            ->first();

        $sequence = 1;
        if ($last && preg_match('/(\d{3})$/', $last->tar_number, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function isDomestic(): bool
    {
        return !$this->is_overseas;
    }

    public function isOverseas(): bool
    {
        return (bool) $this->is_overseas;
    }

    public function canBeApprovedBy(User $user): bool
    {
        if ($this->status === self::STATUS_SUBMITTED && $user->isManager()) {
            return true;
        }

        if ($this->status === self::STATUS_MANAGER_APPROVED && $user->isFinance()) {
            return true;
        }

        if ($this->status === self::STATUS_FINANCE_APPROVED && $this->isOverseas() && $user->isDirector()) {
            return true;
        }

        return false;
    }

    public function nextStatusAfterApproval(): ?string
    {
        switch ($this->status) {
            case self::STATUS_SUBMITTED:
                return self::STATUS_MANAGER_APPROVED;
            case self::STATUS_MANAGER_APPROVED:
                return $this->isOverseas()
                    ? self::STATUS_FINANCE_APPROVED
                    : self::STATUS_FINANCE_APPROVED;
            case self::STATUS_FINANCE_APPROVED:
                return $this->isOverseas()
                    ? self::STATUS_DIRECTOR_APPROVED
                    : self::STATUS_COMPLETED;
            case self::STATUS_DIRECTOR_APPROVED:
                return self::STATUS_COMPLETED;
            default:
                return null;
        }
    }
}

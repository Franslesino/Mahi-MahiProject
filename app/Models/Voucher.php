<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas voucher.
 */
class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'max_usage',
        'used_count',
        'min_purchase',
        'max_discount',
        'start_date',
        'end_date',
        'is_active',
        'allowed_courses',
        'allowed_users',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'allowed_courses' => 'array',
        'allowed_users' => 'array',
    ];

    // Relationships
    /**
     * Relasi hasMany ke VoucherUsage.
     */
    public function usages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Relasi hasManyThrough ke Transaction.
     */
    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, VoucherUsage::class);
    }

    // Scopes
    /**
     * Scope query untuk active.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    // Helper Methods
    /**
     * Memeriksa valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check start date
        if ($this->start_date && now()->lt($this->start_date)) {
            return false;
        }

        // Check end date
        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }

        // Check usage limit
        if ($this->max_usage && $this->used_count >= $this->max_usage) {
            return false;
        }

        return true;
    }

    /**
     * Memeriksa be used by.
     */
    public function canBeUsedBy($userId, $courseId = null): bool
    {
        // Check if voucher is valid
        if (!$this->isValid()) {
            return false;
        }

        // Check if course is allowed
        if ($this->allowed_courses && $courseId) {
            if (!in_array($courseId, $this->allowed_courses)) {
                return false;
            }
        }

        // Check if user is allowed
        if ($this->allowed_users && $userId) {
            if (!in_array($userId, $this->allowed_users)) {
                return false;
            }
        }

        // Check if user has already used this voucher
        $hasUsed = VoucherUsage::where('voucher_id', $this->id)
                               ->where('user_id', $userId)
                               ->exists();

        if ($hasUsed) {
            return false;
        }

        return true;
    }

    /**
     * Menghitung discount.
     */
    public function calculateDiscount($price): float
    {
        if ($this->type === 'percentage') {
            $discount = $price * ($this->value / 100);
            
            // Apply max discount if set
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
            
            return $discount;
        }

        // Fixed discount
        return min($this->value, $price); // Discount tidak boleh lebih dari harga
    }

    /**
     * Menangani logika model.
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Accessor untuk atribut discount text.
     */
    public function getDiscountTextAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }
}
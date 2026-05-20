<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'address',
        'city',
        'district',
        'phone',
        'description',
        'image',
        'open_time',
        'close_time',
        'price_range',
        'status',
    ];

    protected $casts = [
        'price_range' => 'decimal:2',
        'status' => 'boolean',
        // open_time và close_time có thể cast thành datetime để dễ xử lý với Carbon
    ];

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    // Accessors
    public function getAverageRatingAttribute(): float
    {
        // Sử dụng relationship reviews() để tính trung bình, trả về 0 nếu chưa có review
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
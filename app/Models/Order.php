<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property-read User $user
 * @property-read Collection<int, OrderItem> $items
 * @method static OrderFactory factory($count = null, $state = [])
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'shipping_address',
        'shipping_method',
        'payment_method',
        'subtotal_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'voucher_code',
        'idempotency_key',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'shipping_method_label',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'shipping_method' => 'string',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    public const STATUS_TRANSITIONS = [
        'pending' => ['processing', 'canceled'],
        'processing' => ['shipped', 'canceled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'canceled' => [],
    ];

    /**
     * @var array<string, string>
     */
    public const SHIPPING_METHOD_LABELS = [
        'standard' => 'Standard Courier (3-5 days)',
        'express' => 'Express Courier (1-2 days)',
        'same_day' => 'Same Day Delivery',
    ];

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<OrderItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getShippingMethodLabelAttribute(): string
    {
        if (!$this->shipping_method) {
            return 'N/A';
        }

        return self::SHIPPING_METHOD_LABELS[$this->shipping_method] ?? ucwords(str_replace('_', ' ', (string) $this->shipping_method));
    }

    /**
     * Scope orders that contain items from a specific seller.
     */
    public function scopeForSeller(Builder $query, int $sellerId): Builder
    {
        return $query->whereHas('items.product', function (Builder $builder) use ($sellerId): void {
            $builder->where('user_id', $sellerId);
        });
    }

    public function sellerHasAccess(int $sellerId): bool
    {
        return $this->items()
            ->whereHas('product', function (Builder $builder) use ($sellerId): void {
                $builder->where('user_id', $sellerId);
            })
            ->exists();
    }

    /**
     * @return array<int, string>
     */
    public function allowedStatusTransitions(): array
    {
        return self::STATUS_TRANSITIONS[$this->status] ?? [];
    }
}
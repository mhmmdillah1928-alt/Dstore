<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'store_id',
        'total_amount',
        'commission_amount',
        'shipping_address',
        'payment_status',
        'order_status',
        'payment_proof_path',
        'payment_review_status',
        'payment_rejection_reason',
    ];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function store() { return $this->belongsTo(Store::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function shipment() { return $this->hasOne(Shipment::class); }
    public function dispute() { return $this->hasOne(Dispute::class); }

    public const STATUS_LABELS = [
        'pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim',
        'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->order_status] ?? $this->order_status;
    }
}

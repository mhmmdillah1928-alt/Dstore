<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['user_id', 'store_name', 'logo', 'address', 'is_verified', 'balance'];
    protected $casts = ['is_verified' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function products() { return $this->hasMany(Product::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function withdrawals() { return $this->hasMany(Withdrawal::class); }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset('storage/' . $this->logo) : 'https://placehold.co/80x80?text=' . urlencode(substr($this->store_name, 0, 1));
    }
}

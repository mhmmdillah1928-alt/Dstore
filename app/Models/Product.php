<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ['store_id', 'brand_id', 'name', 'slug', 'condition', 'price', 'stock', 'weight', 'ram', 'storage', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function store() { return $this->belongsTo(Store::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function images() { return $this->hasMany(ProductImage::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function orderItemsExist(): bool { return OrderItem::where('product_id', $this->id)->exists(); }

    public function getThumbnailAttribute(): string
    {
        $img = $this->images->first();
        if ($img) {
            return asset('storage/' . $img->image_path);
        }

        $filename = Str::slug($this->name) . '.jpg';
        if (is_file(public_path('images/products/' . $filename))) {
            return asset('images/products/' . $filename);
        }

        return asset('images/products/phone-default.svg');
    }

    public function getConditionLabelAttribute(): string
    {
        return $this->condition === 'new' ? 'Baru' : 'Bekas';
    }
}

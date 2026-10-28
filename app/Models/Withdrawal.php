<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = ['store_id', 'amount', 'status'];

    public function store() { return $this->belongsTo(Store::class); }
}

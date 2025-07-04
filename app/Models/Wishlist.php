<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{

    public $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function Items(){
        return $this->hasMany(OrderItem::class);
    }
}

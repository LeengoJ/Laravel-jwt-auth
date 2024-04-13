<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class before_order_details extends Model
{
    use HasFactory;
    protected $table = 'before_order_details';

    protected $fillable = [

        'size',
        'before_orderId',
        'productId',
        'price',
        'number'
    ];

    public function order()
    {
       return $this->belongsTo(Order::class, 'before_orderId');
    }

    public function product()
    {
       return $this->belongsTo(Product::class, 'productId');
    }
}

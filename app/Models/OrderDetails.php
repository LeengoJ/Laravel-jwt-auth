<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [

        'size',
        'orderId',
        'productId',
        'price',
        'number'
    ];

    public function order()
    {
       return $this->belongsTo(Order::class, 'orderId');
    }

    public function product()
    {
       return $this->belongsTo(Product::class, 'productId');
    }

}

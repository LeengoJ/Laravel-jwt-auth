<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'orderId';

    protected $fillable = [
        'userId',
        'time',
        'sdt',
        'note',
        'numberProduct',
        'totalBill',
        'status',
        'discountPayment',
        'numberTable',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}

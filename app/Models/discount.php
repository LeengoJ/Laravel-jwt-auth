<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';

    protected $fillable = [
        'startTime',
        'endTime',
        'name',
        'code',
        'discountPercent',
        'productId',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'productId');
    }
}

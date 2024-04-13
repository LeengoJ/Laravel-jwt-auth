<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class before_order extends Model
{
    use HasFactory;
    protected $fillable = [
        'userId', 'time', 'status', 'tableNumber', 'isTakeAway', 'note', 'discountCode'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}

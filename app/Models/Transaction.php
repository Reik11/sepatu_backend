<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'shipping_address',
        'shipping_courier',
        'latitude',
        'longitude',
        'total_price',
        'status',
        'payment_proof',
        'user_id',
    ];

    /**
     * Get the items in this transaction.
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get the user who made the transaction (if registered).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'shoe_id',
        'shoe_size',
        'quantity',
        'price',
    ];

    /**
     * Get the transaction this item belongs to.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the shoe reference.
     */
    public function shoe()
    {
        return $this->belongsTo(Shoe::class);
    }
}

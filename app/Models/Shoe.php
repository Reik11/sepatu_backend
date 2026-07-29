<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shoe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'category',
        'price',
        'sizes',
        'image_url',
        'description',
        'stock',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sizes' => 'array',
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    /**
     * Get the items referencing this shoe.
     */
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}

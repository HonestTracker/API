<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory;

    protected $table = 'product_prices';
    protected $fillable = [
        'price',
        'date',
        'product_id',
    ];
    public function product(): BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }
}

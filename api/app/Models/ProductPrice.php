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
        'product_id',
        'price',
        'date',
        'change_percentage'
    ];
    public function product(): BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }
    public function site(): BelongsTo
    {
        return $this->BelongsTo(CategorySite::class, 'site_id', 'id');
    }
}

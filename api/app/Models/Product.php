<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'url',
        'current_price',
        'change_percentage',
        'currency',
    ];
    public function site(): BelongsTo
    {
        return $this->BelongsTo(CategorySite::class);
    }
    public function prices(): HasMany
    {
        return $this->HasMany(ProductPrice::class);
    }
}

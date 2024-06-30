<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'url',
        'current_price',
        'current_price_id',
        'change_percentage',
        'currency',
        'picture_url',
    ];
    public function site(): BelongsTo
    {
        return $this->belongsTo(CategorySite::class, 'site_id');
    }
    public function prices(): HasMany
    {
        return $this->HasMany(ProductPrice::class);
    }
    public function current_price(): HasOne
    {
        return $this->hasOne(ProductPrice::class);
    }
    public function comments(): HasMany
    {
        return $this->HasMany(Comment::class);
    }
}

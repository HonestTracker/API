<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'url',
        'site_name',
        'category_id',
    ];
    public function prices(): HasMany
    {
        return $this->HasMany(ProductPrice::class);
    }
}

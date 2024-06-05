<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorySite extends Model
{
    use HasFactory;
    protected $table = 'category_sites';
    protected $fillable = [
        'category_id',
        'site_name',
        'url',
    ];
    public function category(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }
    public function products(): HasMany
    {
        return $this->HasMany(Product::class, "site_id");
    }
}

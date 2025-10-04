<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndirectCosts extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'price',
        'categories_indirect_costs_id',
    ];

    //relacion de muchos a uno con la tabla category_indirect_costs
    public function categories_indirect_costs(){
        return $this->belongsTo(CategoriesIndirectCosts::class);
    }
}

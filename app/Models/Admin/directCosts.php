<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class directCosts extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'amount',
        'price',
        'category_direct_costs_id',
    ];

    //relacion de muchos a uno con la tabla category_direct_costs
    public function category_direct_costs(){
        return $this->belongsTo(categoriesDirectCosts::class);
    }
}

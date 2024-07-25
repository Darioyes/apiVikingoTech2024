<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoriesDirectCosts extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    //relacion de uno a muchos con la tabla direct_costs
    public function direct_costs(){
        return $this->hasMany(directCosts::class);
    }
}

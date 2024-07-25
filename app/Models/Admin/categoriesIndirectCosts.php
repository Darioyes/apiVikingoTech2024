<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoriesIndirectCosts extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    //relacion de uno a muchos con la tabla indirect_costs
    public function indirect_costs(){
        return $this->hasMany(indirectCosts::class);
    }
}

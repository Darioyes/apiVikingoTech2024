<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBold extends Model
{
    use HasFactory;

        protected $fillable = [
        'order_id',
        'amount',
        'currency',
        'status',
        'reference',
        'bold_response',
        'signature_valid'
    ];

    protected $casts = [
        'bold_response' => 'array',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    //relacion de uno a muchos con la tabla sales
    public function sales(){
        return $this->hasMany(Sales::class, 'bold_order_id', 'order_id');
    }
}

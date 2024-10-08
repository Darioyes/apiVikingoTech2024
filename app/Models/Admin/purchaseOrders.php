<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchaseOrders extends Model
{
    use HasFactory;

    protected $fillable = [
        'purcharse',
        'amount',
        'description',
        'purcharse_order',
        'products_id',
        'suppliers_id',
    ];

    //relacion de muchos a uno con la tabla products
    public function products()
    {
        return $this->belongsTo(products::class);
    }

    //relacion de muchos a uno con la tabla suppliers
    public function suppliers()
    {
        return $this->belongsTo(suppliers::class);
    }

    //relacion de uno a muchos con la tabla transactions
    public function transactions()
    {
        return $this->hasMany(transactions::class);
    }
}

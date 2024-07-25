<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class suppliers extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'nit',
        'phone1',
        'phone2',
        'address',
        'email',
        'description',
        'cities_id',
    ];

    //relacion de uno a muchos con la tabla purcharse_orders
    public function purcharseOrders()
    {
        return $this->hasMany(purchaseOrders::class);
    }

    //relacion de muchos a uno con la tabla cities
    public function cities()
    {
        return $this->belongsTo(cities::class);
    }
}

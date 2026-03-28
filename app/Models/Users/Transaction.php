<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

        protected $fillable = [
        'sales_id',
        'indirect_costs_id',
        'direct_costs_id',
        'purcharse_orders_id',
        'maintenances_id'
    ];

    //relacion de muchos a uno con la tabla sales
    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

//relacion de muchos a uno con la tabla maintenances
    public function maintenances()
    {
        return $this->belongsTo(Maintenance::class);
    }
}

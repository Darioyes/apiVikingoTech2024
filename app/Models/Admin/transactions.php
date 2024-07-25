<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transactions extends Model
{
    use HasFactory;
    protected $fillable = [
        'sales_id',
        'indirect_costs_id',
        'direct_costs_id',
        'purcharse_orders_id',
    ];

    //relacion de muchos a uno con la tabla sales
    public function sales()
    {
        return $this->belongsTo(sales::class);
    }

    //relacion de muchos a uno con la tabla indirect_costs
    public function indirect_costs()
    {
        return $this->belongsTo(indirectCosts::class);
    }

    //relacion de muchos a uno con la tabla direct_costs
    public function direct_costs()
    {
        return $this->belongsTo(directCosts::class);
    }

    //relacion de muchos a uno con la tabla purcharse_orders
    public function purcharse_orders()
    {
        return $this->belongsTo(purchaseOrders::class);
    }
}

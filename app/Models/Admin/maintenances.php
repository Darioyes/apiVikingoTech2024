<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class maintenances extends Model
{
    use HasFactory;

    protected $fillable = [
        'product',
        'description',
        'reference',
        'price',
        'delivery_date',
        'image1',
        'image2',
        'image3',
        'image4',
        'advance',
        'repaired',
        'warranty',
        'users_id',
    ];

    //relacion de muchos a uno con la tabla users
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    //relacion de uno a muchos con la tabla transactions
    public function transactions()
    {
        return $this->hasMany(transactions::class);
    }
}

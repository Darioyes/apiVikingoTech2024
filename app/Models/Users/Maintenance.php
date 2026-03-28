<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'product',
        'description',
        'reference',
        'price',
        'delivery_date',
        'created_at',
        'image1',
        'image2',
        'image3',
        'image4',
        'advance',
        'users_id',
    ];

    //relacion de muchos a uno con la tabla users
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    //relacion de uno a muchos con la tabla transactions
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}

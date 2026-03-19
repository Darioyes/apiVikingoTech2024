<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
    ];

    //relacion muchos a uno con la tabla cities
     public function cities()
     {
         return $this->belongsTo(Cities::class);
     }

    //relacion uno a muchos con la tabla users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

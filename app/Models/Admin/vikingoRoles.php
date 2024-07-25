<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vikingoRoles extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_admin',
    ];

    //relacion uno a muchos con la tabla users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

<?php

namespace App\Models\Admin;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'name',
        'lastname',
        'email',
        'gender',
        'birthday',
        'phone1',
        'phone2',
        'address',
        'password',
        'cities_id',
        'vikingo_roles_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     //relacion muchos a uno con la tabla cities
     public function cities()
     {
         return $this->belongsTo(cities::class);
     }

     //relacion de uno a muchos con la tabla maintenances
     public function maintenances()
     {
         return $this->hasMany(maintenances::class);
     }

     //relacion de muchos a uno con la tabla vikingo_roles
     public function vikingo_roles()
     {
         return $this->belongsTo(vikingoRoles::class);
     }

     //relacion de uno a muchos con la tabla sales
     public function sales()
     {
         return $this->hasMany(sales::class);
     }
}

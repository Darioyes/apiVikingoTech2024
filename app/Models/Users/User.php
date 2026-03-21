<?php

namespace App\Models\Users;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements MustVerifyEmail,CanResetPassword
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

    //relacion de uno a muchos con la tabla maintenances
     public function maintenances()
     {
         return $this->hasMany(Maintenance::class);
     }

     public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmail {
            public function toMail($notifiable)
            {
                $verificationUrl = $this->verificationUrl($notifiable);

                return (new MailMessage)
                    ->subject('VikingoTech - Confirma tu correo electrónico') 
                    ->greeting('¡Hola, ' . $notifiable->name . '!')
                    ->line('Gracias por registrarte en VikingoTech. Para activar tu cuenta, por favor haz clic en el botón de abajo.')
                    ->action('Verificar mi cuenta', $verificationUrl)
                    ->line('Si no creaste esta cuenta, no es necesario realizar ninguna acción.')
                    ->salutation('Saludos, el equipo de VikingoTech');
            }
        });
    }   
    
}

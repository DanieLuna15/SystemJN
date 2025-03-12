<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Constants\Status;
use App\Http\Traits\GlobalStatus;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, GlobalStatus;

    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'ministerio_user')->withTimestamps();
    }

    public function ministeriosLiderados()
    {
        return $this->belongsToMany(Ministerio::class, 'ministerio_lider')->withTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_name',    // Nuevo campo
        'address',      // Nuevo campo
        'ci',           // Nuevo campo
        'profile_image',// Nuevo campo        
        'phone',        // Nuevo campo
        'estado'        // Nuevo campo
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


    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->estado == Status::ACTIVE
                    ? '<span class="badge badge-success d-flex align-items-center justify-content-center w-100 h-100">Activo</span>'
                    : '<span class="badge badge-danger d-flex align-items-center justify-content-center w-100 h-100">Inactivo</span>';
            }
        );
    }
}

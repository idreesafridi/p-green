<?php

namespace App\Models;

use App\Notifications\PasswordReset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'password',
        'orignalpass',
        'birthplace',
        'birth_country',
        'dob',
        'residence_city',
        'residence_province',
        'residence',
        'fiscal_code',
        'professional_college',
        'common_college',
        'registration_number',
        'status',
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
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }

    /**
     * hasOne Relationship with techincian
     */
    public function techincian()
    {
        return $this->hasOne(TechincianDetail::class);
    }

    /**
     * hasOne Relationship with business
     */
    public function business()
    {
        return $this->hasOne(BusinessDetail::class);
    }
    /**
     * relation with StatusTechnician
     */
    public function status_technician()
    {
        return $this->hasOne(statusTechnicianDetail::class);
    }
    /**
     * relation with StatusTechnician
     */
    public function construction_notes()
    {
        return $this->hasOne(ConstructionNotes::class);
    }

    public function StatusPreAnalysis()
    {
        return $this->hasMany(StatusPreAnalysis::class, 'updated_by');
    }

    public function StatusTechnician()
    {
        return $this->hasMany(StatusTechnician::class, 'updated_by');
    }

    public function StatusRelief()
    {
        return $this->hasMany(StatusRelief::class, 'updated_by');
    }
}

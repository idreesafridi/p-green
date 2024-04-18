<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusTechnician extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',
        'tecnician_id',
        'state',
        'updated_on',
        'updated_by',
        'reminders_emails',
        'reminders_days'
    ];
    /**
     * relation with User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'tecnician_id');
    }
    /**
     * relation with ConstructionSite
     */
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

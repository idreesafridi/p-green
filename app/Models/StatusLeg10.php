<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLeg10 extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',
        'state',
        'updated_on',
        'updated_by',
        'reminders_emails',
        'reminders_days',
        'status',
    ];
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
    // relation with ReliefDocumentFile
    public function Legge10DocumentFile()
    {
        return $this->hasMany(Leg10File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPrNoti extends Model
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
    /**
     * relation with PrNotDoc
     */
    public function PrNotDoc()
    {
        return $this->hasMany(PrNotDoc::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

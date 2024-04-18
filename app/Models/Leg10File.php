<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leg10File extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status_leg10_id',
        'construction_site_id',
        'allow',
        'file_name',
        'description',
        'file_path',
        'bydefault',
        'state',
        'updated_on',
        'updated_by',
        'file_upload',
        'status',
    ];
    // relation with StatusLegge10
    public function StatusLegge10()
    {
        return $this->belongsTo(StatusLeg10::class, 'status_leg10_id');
    }
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
}

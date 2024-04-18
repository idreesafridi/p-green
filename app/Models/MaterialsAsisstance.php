<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialsAsisstance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',
        'construction_material_id',
        'machine_model',
        'freshman',
        'start_date',
        'expiry_date',
        'invoice',
        'report',
        'notes',
        'state',
        'updated_on',
        'updated_by',
        'status',
    ];

    /**
     * relation with ConstructionSite
     */
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }

    /**
     * relation with ConstructionMaterial
     */
    public function ConstructionMaterial()
    {
        return $this->belongsTo(ConstructionMaterial::class, 'construction_material_id');
    }
}

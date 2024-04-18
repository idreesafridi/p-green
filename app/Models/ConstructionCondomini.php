<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionCondomini extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',
        'construction_assigned_id'
    ];

    public function ConstructionCondomini()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_assigned_id');
    }

    public function ConstructionCondominiP()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }

   
    public function PropertyDataRevers()
    {
        return $this->belongsTo(PropertyData::class, 'construction_assigned_id', 'construction_site_id');
    }

    public function ConstructionSiteSettingforChild(){

        return $this->belongsTo(ConstructionSiteSetting::class, 'construction_assigned_id', 'construction_site_id');
    }

    public function ConstructionSiteSettingforParent(){

        return $this->belongsTo(ConstructionSiteSetting::class, 'construction_site_id', 'construction_site_id');
    }
    
}

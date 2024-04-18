<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionShipping extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id'
    ];

    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }

    public function ConstructionShippingList()
    {
        return $this->hasMany(ConstructionShippingList::class);
    }

    public function ConstructionShippingListGet($id)
    {
        return $this->hasOne(ConstructionShippingList::class)->where('construction_material_id', $id)->first();
    }
}

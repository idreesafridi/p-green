<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionShippingList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_shipping_id',
        'construction_material_id',
        'qty',
        'total_qty',
        'ship_change',
        'rem_qty',
        'shipping_truck',
    ];

    public function ConstructionShipping()
    {
        return $this->belongsTo(ConstructionShipping::class, 'construction_shipping_id');
    }

    public function ConstructionShippingGet($id)
    {
        return $this->where('construction_material_id', $id)->first();
    }

    public function ConstructionShippingMaterials()
    {
        return $this->belongsTo(ConstructionMaterial::class, 'construction_material_id');
    }
}

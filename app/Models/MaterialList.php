<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'material_type_id',
        'name',
        'unit',
        'user_id',
        'status',
    ];

    public function MaterialTypeBelongs()
    {
        return $this->belongsTo(MaterialType::class, 'material_type_id');
    }
    public function materialPriceRel(){
        
        return $this->hasMany(MaterialPrice::class, 'material_lists_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatarialHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   protected $guarded = [];

   public function ConstructionMaterialHistory(){

       return $this->belongsTo(ConstructionMaterial::class, 'material_id');
   }
   public function User(){

       return $this->belongsTo(User::class, 'changeBy');
   }
}

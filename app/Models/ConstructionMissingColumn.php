<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionMissingColumn extends Model
{
    use HasFactory;

 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['construction_site_id','documento', 'user_id'];

    public function ConstructionMissingAttr()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
  
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

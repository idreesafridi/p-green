<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionMaterial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',
        'material_list_id',
        'quantity',
        'state',
        'consegnato',
        'avvio',
        'note',
        'montato',
        'updated_by',
        'updated_at',
        'delete_status'
    ];

    /**
     * MaterialList belongsto user relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * MaterialList belongsto user relationship
     */
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }

    /**
     * MaterialList belongsto relationship
     */
    public function MaterialList()
    {
        return $this->belongsTo(MaterialList::class, 'material_list_id');
    }

    // relation with MaterialsAsisstance construction_material_id
    public function MaterialsAsisstance()
    {
        return $this->hasOne(MaterialsAsisstance::class);
    }


    // relation with MaterialsAsisstance construction_material_id
    public function MaterialsAsisstancelist()
    {
        return $this->hasMany(MaterialsAsisstance::class);
    }

    public function MatHistory(){

        return $this->hasMany(MatarialHistory::class, 'material_id');
    }

    public function matoption(){
        $this->load('MaterialList.MaterialTypeBelongs.MaterialOptionBelongs');

        if ($this->MaterialList && $this->MaterialList->MaterialTypeBelongs) {
            // Access the MaterialOption
            if($this->MaterialList->MaterialTypeBelongs != null){
                return $this->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs();
            }
            
           
        }
    
        // If any step of the relationship is null, return null or handle the error as needed
        return null;
    }
}

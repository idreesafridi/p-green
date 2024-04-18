<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'material_option_id',
        'name',
        'status'
    ];

    /**
     * Relationship with MaterialLists
     */
    public function MaterialOptionBelongs()
    {
        return $this->belongsTo(MaterialOption::class, 'material_option_id');
    }

    /**
     * Relationship with MaterialLists
     */
    public function MaterialList()
    {
        return $this->hasMany(MaterialList::class);
    }
}

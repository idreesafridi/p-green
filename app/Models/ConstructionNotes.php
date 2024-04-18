<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionNotes extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',
        'admin_id',
        'notes',
        'priority',
        'status'
    ];
    /**
     * relation with ConstructionSite
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    /**
     * relation with ConstructionSite
     */
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
}
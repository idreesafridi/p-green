<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReliefDoc extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status_relief_id',
        'construction_site_id',
        'folder_name',
        'description',
        'allow',
        'state',
        'updated_on',
        'updated_by',
    ];
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
    // relation with StatusRelief
    public function StatusRelief()
    {
        return $this->belongsTo(StatusRelief::class);
    }
    // relation with ReliefDocumentFile
    public function ReliefDocumentFile()
    {
        return $this->hasMany(RelDocFile::class);
    }

    public function ReliefLatestUpdated()
    {
        $RelDocFile = optional($this->ReliefDocumentFile())->where('state', 1)->latest('updated_on')->first(['updated_by', 'updated_on']);

        return  $RelDocFile;
    }
}

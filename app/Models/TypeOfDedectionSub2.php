<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeOfDedectionSub2 extends Model
{
    use HasFactory, Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_of_dedection_sub1_id',
        'construction_site_id',
        'allow',
        'folder_name',
        'file_name',
        'file_path',
        'updated_on',
        'description',
        'updated_by',
        'reminders_emails',
        'reminders_days',
        'bydefault',
        'state',
    ];


    protected $encryptable = [
        'folder_name',
        'file_name',
        'file_path'
    ];
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
    // relation with TypeOfDedectionSub1
    public function TypeOfDedectionSub1()
    {
        return $this->belongsTo(TypeOfDedectionSub1::class, 'type_of_dedection_sub1_id');
    }
    // relation with TypeOfDedectionFiles
    public function TypeOfDedectionFiles()
    {
        return $this->hasMany(TypeOfDedectionFiles::class);
    }


    public function DedectionSub2LatestUpdate()
    {
        $Dec2File = optional($this->TypeOfDedectionFiles())->where('state', 1)->latest('updated_on')->first(['updated_by', 'updated_on']);
      
        return  $Dec2File;
    }
}

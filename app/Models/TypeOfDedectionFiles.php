<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeOfDedectionFiles extends Model
{
    use HasFactory, Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        'type_of_dedection_sub2_id',
        'construction_site_id',
        'allow',
        'folder_name',
        'file_name',
        'description',
        'file_path',
        'updated_on',
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
    // relation with TypeOfDedectionSub2
    public function TypeOfDedectionSub2()
    {
        return $this->belongsTo(TypeOfDedectionSub2::class, 'type_of_dedection_sub2_id');
    }
    // fublic function TypeOfDedectionFiles2
    public function TypeOfDedectionFiles2()
    {
        return $this->hasMany(TypeOfDedectionFiles2::class, 'type_of_dedection_file_id');
    }
}

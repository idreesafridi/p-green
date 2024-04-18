<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RelDocFile extends Model
{
    use HasFactory, Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'relief_doc_id',
        'construction_site_id',
        'ref_folder_name',
        'folder_name',
        'allow',
        'file_name',
        'description',
        'file_path',
        'bydefault',
        'state',
        'updated_on',
        'updated_by',
        'reminders_emails',
        'reminders_days',
        'status',
    ];


    protected $encryptable = [
        'folder_name',
        'file_name',
        'file_path'
    ];
    // relation with RelifDocFileSub1
    public function RelifDocFileSub1()
    {
        return $this->hasMany(RelifDocFileSub1::class);
    }
    // relation with ReliefDocument
    public function ReliefDocument()
    {
        return $this->belongsTo(ReliefDoc::class, 'relief_doc_id');
    }
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
}

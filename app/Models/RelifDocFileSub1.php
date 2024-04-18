<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RelifDocFileSub1 extends Model
{
    use HasFactory, Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rel_doc_file_id',
        'construction_site_id',
        'rel_doc_file_folder_name',
        'folder_name',
        'file_name',
        'allow',
        'file_path',
        'description',
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
    // relation with RelDocFile
    public function RelDocFile()
    {
        return $this->belongsTo(RelDocFile::class, 'rel_doc_file_id');
    }
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
}

<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrNotDocFile extends Model
{
    use HasFactory, Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pr_not_doc_id',
        'construction_site_id',
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
    // relation with PrNotDoc
    public function PrNotDoc()
    {
        return $this->belongsTo(PrNotDoc::class, 'pr_not_doc_id');
    }
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
}

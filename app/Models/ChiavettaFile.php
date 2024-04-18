<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiavettaFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chiavetta_doc_id',
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

    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
    // relation with PrNotDoc
    public function ChiavettaDoc()
    {
        return $this->belongsTo(ChiavettaDoc::class, 'chiavetta_doc_id');
    }
}

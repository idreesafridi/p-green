<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeOfDedectionSub1 extends Model
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
        'allow',
        'folder_name',
        'file_name',
        'description',
        'type',
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
    // relation with PrNotDoc
    public function PrNotDoc()
    {
        return $this->belongsTo(PrNotDoc::class, 'pr_not_doc_id');
    }
    // relation with TypeOfDedectionSub2
    public function TypeOfDedectionSub2()
    {
        return $this->hasMany(TypeOfDedectionSub2::class);
    }


    public function  FilesCounting(){

        $count = 0;
          
        foreach ($this->TypeOfDedectionSub2->where('state', 1) as $file) {
            if ( !empty($file['updated_by']) &&  !empty($file['file_name']) && !empty( $file['updated_on'])) {
                $count++; 
            }

            foreach ($file->TypeOfDedectionFiles->where('state', 1) as $TypeOfDedectionFiles) {
                if ( !empty($TypeOfDedectionFiles['updated_by']) &&  !empty($TypeOfDedectionFiles['file_name']) && !empty( $TypeOfDedectionFiles['updated_on'])) {
                    $count++;
                }

                foreach ($TypeOfDedectionFiles->TypeOfDedectionFiles2->where('state', 1) as $TypeOfDedectionFiles2) {
                    if (!empty($TypeOfDedectionFiles2['updated_by']) &&  !empty($TypeOfDedectionFiles2['file_name']) && !empty( $TypeOfDedectionFiles2['updated_on'])) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }
}

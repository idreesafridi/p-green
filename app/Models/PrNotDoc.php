<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrNotDoc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status_pr_noti_id',
        'construction_site_id',
        'folder_name',
        'allow',
        'state',
        'description',
        'updated_on',
        'updated_by',
        'reminders_emails',
        'reminders_days'
    ];
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
    // relation with StatusPrNoti
    public function StatusPriorNotification()
    {
        return $this->belongsTo(StatusPrNoti::class, 'status_pr_noti_id');
    }
    // relation with PrNotDocFile
    public function PrNotDocFile()
    {
        return $this->hasMany(PrNotDocFile::class);
    }
    // relation with TypeOfDedectionSub1
    public function TypeOfDedectionSub1()
    {
        return $this->hasMany(TypeOfDedectionSub1::class);
    }


    // public function countAllfileofprNotDoc($PrNotDocd)
    // {
   
    //     $count = 0;

    //         foreach ($PrNotDocd->PrNotDocFile->where('updated_by', '!=',  null)->where('state', 1) as $prNotDocFile) {
            
    //                 $count++;
    //             }   
    //             // dd($PrNotDocd->TypeOfDedectionSub1);
    //             foreach ($PrNotDocd->TypeOfDedectionSub1->where('updated_by', '!=',  null)->where('state', 1) as $typeOfDedectionSub1) {
    //                     $count++;

    //                 foreach ($typeOfDedectionSub1->TypeOfDedectionSub2->where('updated_by', '!=',  null)->where('state', 1) as $typeOfDedectionSub2) {
                
    //                         $count++;    
                      
    //                     foreach ($typeOfDedectionSub2->TypeOfDedectionFiles->where('updated_by', '!=',  null)->where('state', 1) as $typeOfDedectionFile) {
                        
    //                             $count++;                        
                      
    //                         foreach ($typeOfDedectionFile->TypeOfDedectionFiles2->where('updated_by', '!=',  null)->where('state', 1) as $TypeOfDedectionFiles2) {
                            
    //                                 $count++;
                            
    //                         }
    //                     }
    //                 }
    //             }
        
    // return $count;
    // }


    public function getLatestUpdate()
    {
        $PrNotDocFile = optional($this->PrNotDocFile())->where('state', 1)->latest('updated_on')->first(['updated_by', 'updated_on']);
        $TypeOfDedectionSub1 =  optional($this->TypeOfDedectionSub1())->where('state', 1)->latest('updated_on')->first(['updated_by', 'updated_on']);
        return  $PrNotDocFile ? $PrNotDocFile : $TypeOfDedectionSub1;
    }

    // public function FilesCounting()
    // {
    
    //   $cacheKey = 'files_count_' . $this->id; // Add a dynamic part to the key
    //   $cacheDuration = 60; // Adjust the cache duration as needed

    //     // Attempt to retrieve the result from the cache
    //     $count = Cache::remember($cacheKey, $cacheDuration, function () {
   
    //         return $this->calculateFilesCount();
    //     });
        
    //     return $count;
    // }
    

    public function  FilesCounting(){


        $count = 0;

        if ($this->folder_name == 'Documenti 110' || $this->folder_name == 'Documenti 90' || $this->folder_name == 'Documenti 65' || $this->folder_name == 'Documenti 50' ||  $this->folder_name == 'Documenti Sicurezza') {
            foreach ($this->PrNotDocFile as $PrNotDocFile) {
                if ( !empty($PrNotDocFile['updated_on']) && !empty($PrNotDocFile['updated_by'])  && $PrNotDocFile['state'] == 1 && !empty($PrNotDocFile['file_name']) ) {
                    $count++;
                }
            }

            foreach ($this->TypeOfDedectionSub1 as $file) {
                if (!empty($file['updated_on']) && !empty($file['updated_by'])  && $file['state'] == 1 && !empty($file['file_name'])) {
                    $count++;
                }

                foreach ($file->TypeOfDedectionSub2 as $TypeOfDedectionSub2) {
                    if (!empty($TypeOfDedectionSub2['updated_on'] && $TypeOfDedectionSub2['updated_by']  && $TypeOfDedectionSub2['file_name']) && $TypeOfDedectionSub2['state'] == 1) {
                        $count++;
                    }
                    foreach ($TypeOfDedectionSub2->TypeOfDedectionFiles as $TypeOfDedectionFiles) {
                        if (!empty($TypeOfDedectionFiles['updated_on'] && $TypeOfDedectionFiles['updated_by']  && $TypeOfDedectionFiles['file_name']) && $TypeOfDedectionFiles['state'] == 1) {
                            $count++;
                        }
                        foreach ($TypeOfDedectionFiles->TypeOfDedectionFiles2 as $TypeOfDedectionFiles2) {
                            if (!empty($TypeOfDedectionFiles2['updated_on'] && $TypeOfDedectionFiles2['updated_by']  && $TypeOfDedectionFiles2['file_name']) && $TypeOfDedectionFiles2['state'] == 1) {

                                $count++;
                            }
                        }

                    }
                }
            }
        }
        else {
            foreach ($this->PrNotDocFile as $file) {
                if (!empty($file['updated_on'] && $file['updated_by']  && $file['file_name']) && $file['state'] == 1) {
                    $count++;
                }
            }
        }
        return $count;
    }


 

//     public function calculateFilesCount()
// {
//     $count = 0;

//     if (
//         in_array($this->folder_name, ['Documenti 110', 'Documenti 90', 'Documenti 65', 'Documenti 50', 'Documenti Sicurezza'])
//     ) {
//         $count += $this->PrNotDocFile
//             ->where('state', 1)
//             ->filter(function ($file) {
//                 return !empty($file['updated_on']) && !empty($file['updated_by']) && !empty($file['file_name']);
//             })
//             ->count();

//         $count += $this->TypeOfDedectionSub1
//             ->where('state', 1)
//             ->filter(function ($file) {
//                 return !empty($file['updated_on']) && !empty($file['updated_by']) && !empty($file['file_name']);
//             })
//             ->count();

//         $count += $this->TypeOfDedectionSub1
//             ->flatMap(function ($file) {
//                 return $file->TypeOfDedectionSub2;
//             })
//             ->where('state', 1)
//             ->filter(function ($file) {
//                 return !empty($file['updated_on']) && !empty($file['updated_by']) && !empty($file['file_name']);
//             })
//             ->count();

//         $count += $this->TypeOfDedectionSub1
//             ->flatMap(function ($file) {
//                 return $file->TypeOfDedectionSub2->flatMap(function ($file) {
//                     return $file->TypeOfDedectionFiles;
//                 });
//             })
//             ->where('state', 1)
//             ->filter(function ($file) {
//                 return !empty($file['updated_on']) && !empty($file['updated_by']) && !empty($file['file_name']);
//             })
//             ->count();

//         $count += $this->TypeOfDedectionSub1
//             ->flatMap(function ($file) {
//                 return $file->TypeOfDedectionSub2->flatMap(function ($file) {
//                     return $file->TypeOfDedectionFiles->flatMap(function ($file) {
//                         return $file->TypeOfDedectionFiles2;
//                     });
//                 });
//             })
//             ->where('state', 1)
//             ->filter(function ($file) {
//                 return !empty($file['updated_on']) && !empty($file['updated_by']) && !empty($file['file_name']);
//             })
//             ->count();
//     } else {
//         $count += $this->PrNotDocFile
//             ->where('state', 1)
//             ->filter(function ($file) {
//                 return !empty($file['updated_on']) && !empty($file['updated_by']) && !empty($file['file_name']);
//             })
//             ->count();
//     }

//     return $count;
// }

}

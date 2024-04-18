<?php

namespace App\Models;

use Carbon\Carbon;

use App\Traits\Encryptable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;




class ConstructionSite extends Model
{
    use HasFactory,  Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'oldid',
        'name',
        'surename',
        'date_of_birth',
        'town_of_birth',
        'province',
        'residence_street',
        'residence_house_number',
        'residence_postal_code',
        'residence_common',
        'residence_province',
        'latest_status',
        'pin_location',
        'page_status',
        'status',
        'archive'
    ];

    protected $encryptable = [
        'name',
        'surename',
        'date_of_birth',
        'town_of_birth',
        'province',
        'residence_street',
        'residence_house_number',
        'residence_postal_code',
        'residence_common',
        'residence_province',
        'pin_location',
    ];


    




      // Mutator for encryption when setting attributes
//       public function setAttribute($key, $value)
//       {
//          // Ensure that the $this->attributes array is initialized
//           $this->attributes = $this->attributes ?? [];

//           if (in_array($key, $this->fillable) && !in_array($key, ['latest_status', 'page_status', 'status', 'archive']) && !is_null($value)) {
//               // Encrypt the value before setting
//               $this->attributes[$key] = encrypt($value);
//           } else {
//               parent::setAttribute($key, $value);
//           }
//       }

//       // Accessor for decryption when retrieving attributes
//       public function getAttribute($key)
//       {
//          // Retrieve the value
//           $value = parent::getAttribute($key);

//           // Check if the value is encrypted
//           if (Str::startsWith($value, ['eyJpdiI6', 'eyJuYW1lIjoi']) && !in_array($key, ['latest_status', 'page_status', 'status','archive']) ) {
//               try {
//                   // Decrypt the value
//                   return decrypt($value);
//               } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
//                   // Handle decryption failure, you may want to log the error or return a default value
//               // dd($e);
//               }
//       }

//   // Return the value as is
//   return $value;
//       }

    /**
     * relation with document and contracts
     */
    public function DocumentAndContact()
    {
        return $this->hasOne(DocumentAndContact::class);
    }

    /**
     * relation with property data
     */
    public function PropertyData()
    {
        return $this->hasOne(PropertyData::class);
    }

    /**
     * relation with ConstructionSiteSetting
     */
    public function ConstructionSiteSetting()
    {
        return $this->hasOne(ConstructionSiteSetting::class);
    }

    /**
     * relation with StatusPreAnalysis
     */
    public function StatusPreAnalysis()
    {
        return $this->hasOne(StatusPreAnalysis::class);
    }

    /**
     * relation with StatusTechnician
     */
    public function StatusTechnician()
    {
        return $this->hasOne(StatusTechnician::class);
    }

    /**
     * relation with StatusRelief
     */
    public function StatusRelief()
    {
        return $this->hasOne(StatusRelief::class);
    }

    public function ReliefDocument()
    {
        return $this->hasMany(ReliefDoc::class);
    }

    public function RelDocFile()
    {
        return $this->hasMany(RelDocFile::class);
    }

    public function relDocFilesget()
    {
        return $this->hasManyThrough(RelDocFile::class, ReliefDoc::class);
    }

    // relation with RelifDocFileSub1
    public function RelifDocFileSub1()
    {
        return $this->hasMany(RelifDocFileSub1::class);
    }

    /**
     * relation with StatusLegge10
     */
    public function StatusLegge10()
    {
        return $this->hasOne(StatusLeg10::class);
    }

    // relation with Legge10DocumentFile
    public function Legge10DocumentFile()
    {
        return $this->hasMany(Leg10File::class);
    }
    // relation with Legge10filecount


    // relation with RegPracDocFile
    public function RegPracDocFile()
    {
        return $this->hasMany(RegPracDoc::class);
    }

    // relation with TypeOfDedectionSub1
    public function TypeOfDedectionSub1()
    {
        return $this->hasMany(TypeOfDedectionSub1::class);
    }

    // relation with TypeOfDedectionSub2
    public function TypeOfDedectionSub2()
    {
        return $this->hasMany(TypeOfDedectionSub2::class);
    }

    // relation with TypeOfDedectionFiles
    public function TypeOfDedectionFiles()
    {
        return $this->hasMany(TypeOfDedectionFiles::class);
    }

    // relation with TypeOfDedectionFiles2
    public function TypeOfDedectionFiles2()
    {
        return $this->hasMany(TypeOfDedectionFiles2::class);
    }

    /**
     * relation with RegPracDoc
     */
    public function RegPracDoc()
    {
        return $this->hasMany(RegPracDoc::class);
    }

    /**
     * relation with StatusComputation
     */
    public function StatusComputation()
    {
        return $this->hasOne(StatusComputation::class);
    }

    /**
     * relation with StatusPrNoti
     */
    public function StatusPrNoti()
    {
        return $this->hasOne(StatusPrNoti::class);
    }

    public function PrNotDoc()
    {
        return $this->hasMany(PrNotDoc::class);
    }

    /**
     * relation with statusRegPrac
     */
    public function statusRegPrac()
    {
        return $this->hasOne(statusRegPrac::class);
    }

    /**
     * relation with StatusWorkStarted
     */
    public function StatusWorkStarted()
    {
        return $this->hasOne(StatusWorkStarted::class);
    }

    /**
     * relation with StatusSAL
     */
    public function StatusSAL()
    {
        return $this->hasOne(StatusSAL::class);
    }

    /**
     * relation with StatusEneaBalance
     */
    public function StatusEneaBalance()
    {
        return $this->hasOne(StatusEneaBalance::class);
    }

    /**
     * relation with StatusWorkClose
     */
    public function StatusWorkClose()
    {
        return $this->hasOne(StatusWorkClose::class);
    }

    /**
     * relation with StatusTechnician
     */
    public function ConstructionNotes()
    {
        return $this->hasMany(ConstructionNotes::class);
    }

    /**
     * relation with StatusTechnician
     */
    public function ConstructionNotesFirst()
    {
        return $this->hasOne(ConstructionNotes::class)->where('status', 0)->latest();
    }

    public function ConstructionShipping()
    {
        return $this->hasMany(ConstructionShipping::class);
    }

    public function ConstructionShippingSingle()
    {
        return $this->hasOne(ConstructionShipping::class);
    }

    public function ConstructionShippingListThrough()
    {
        return $this->hasManyThrough(ConstructionShippingList::class, ConstructionShipping::class);
    }

    /**
     * relation with ConstructionJobDetail
     */
    public function ConstructionJobDetail()
    {
        return $this->hasOne(ConstructionJobDetail::class);
    }

    /**
     * relation with ConstructionJobDetail
     */
    public function ConstructionImagesFolder()
    {
        return $this->hasMany(ConstructionSiteImage::class)->where('folder', request()->route()->image);
    }
    // for Apis
    public function ConstructionImagesFolderApi()
    {
        return $this->hasMany(ConstructionSiteImage::class);
    }
    // GET countfiles
    public function countfiles($id)
    {
        $count = 0;
        $totalfiles = 9;
        $constructionSite = ConstructionSite::find($id);

        // here we check if type of deduction  is more then 1
        if ($constructionSite->ConstructionSiteSetting != null) {
            $check_deduction = explode(",", $constructionSite->ConstructionSiteSetting->type_of_deduction);
        } else {
            $check_deduction = [];
        }

        if (count($check_deduction) != 0) {


            // dd($constructionSite->RegPracDoc);
            foreach ($constructionSite->RegPracDoc as $RegPracDocfiles) {

                if ($RegPracDocfiles->file_name == "Cilas Protocollata 110") {
                    if ($RegPracDocfiles->updated_on != null) {
                        $count += 1;
                    }
                    $totalfiles += 1;
                }
                if ($RegPracDocfiles->file_name == "Protocollo Cilas 110") {

                    if ($RegPracDocfiles->updated_on != null) {
                        $count += 1;
                    }

                    $totalfiles += 1;
                }
            }

            if (in_array("50", $check_deduction) || in_array("65", $check_deduction) || in_array("90", $check_deduction)) {


                foreach ($constructionSite->RegPracDoc as $RegPracDocfiles) {

                    if ($RegPracDocfiles->file_name == "Cila Protocollata 50-65-90") {

                        if ($RegPracDocfiles->updated_on != null) {
                            $count += 1;
                        }
                        $totalfiles += 1;
                    }
                    if ($RegPracDocfiles->file_name == "Protocollo Cila 50-65-90") {
                        if ($RegPracDocfiles->updated_on != null) {
                            $count += 1;
                        }
                        $totalfiles += 1;
                    }
                }
            }
        }

        // common file
        $constructionSite->RelDocFile;
        foreach ($constructionSite->RelDocFile as $relDocFiles) {
            if ($relDocFiles->updated_on && $relDocFiles->file_name == 'Atto Di Provenienza') {
                if ($relDocFiles->updated_on != null) {
                    $count += 1;
                }
            }
            if ($relDocFiles->file_name == 'Sopralluogo Fine Lavori') {
                if ($relDocFiles->updated_on != null) {
                    $count += 1;
                }
            }
            if ($relDocFiles->file_name == 'Visura Catastale') {
                if ($relDocFiles->updated_on != null) {
                    $count += 1;
                }
            }
            if ($relDocFiles->file_name == 'Bolletta Luce') {
                if ($relDocFiles->updated_on != null) {
                    $count += 1;
                }
            }
            if ($relDocFiles->file_name == 'Codice Fiscale') {
                if ($relDocFiles->updated_on != null) {
                    $count += 1;
                }
            }
            if ($relDocFiles->file_name == 'Carta D identità co-intestatario') {
                if ($relDocFiles->updated_on != null) {
                    $count += 1;
                }
            }
            if ($relDocFiles->file_name == 'Carta D Identità Intestatario Bollette') {
                if ($relDocFiles->updated_on != null) {
                    $count += 1;
                }
            }
            if ($relDocFiles->file_name == 'ANTONACCI IMMACOLATA Relazione Fotovoltaico') {
                if ($relDocFiles->updated_on != null) {
                    $count += 1;
                }
            }
        }
        // legg 10
        // $legg10 = $constructionSite->ReliefDocumentFile;
        // foreach ($legg10 as $legg10files) {

        //     if ($legg10files->file_name == 'Legge 10') {
        //         if ($legg10files->updated_on != null) {
        //             $count += 1;
        //         }
        //     }
        // }
        // Reg Prac Doc File count
        $RegFilecount = $constructionSite->RegPracDoc;
        foreach ($RegFilecount as $RegFilecount) {
            if ($RegFilecount->file_name == 'Notifica Preliminare') {
                if ($RegFilecount->updated_on != null) {
                    $count += 1;
                }
            }
        }
        return array($count, $totalfiles);
    }



    public function documentiCount($id)
    {
        $totalfiles = 9;
        $count = 0;
        $constructionSite = ConstructionSite::find($id);

        $ReliefDoc =  $constructionSite->ReliefDocument->where('folder_name', 'Diagnosi Energetica')->first();

        $count2 =  $ReliefDoc ?  $ReliefDoc->ReliefDocumentFile->where('file_name', 'Legge 10')->where('updated_by', '!=', null)->where('state', 1)->count() : 0;


        $DocumentiClienti =  $constructionSite->ReliefDocument->where('folder_name', 'Documenti Clienti')->first();
        $files2 = ["Carta D'identità", 'Codice Fiscale', 'Atto Di Provenienza', 'Visura Catastale'];
        $count46 =  $DocumentiClienti ?  $DocumentiClienti->ReliefDocumentFile->whereIn('file_name', $files2)->where('updated_by', '!=', null)->where('state', 1)->count() : 0;

        $DocumentiFotovoltaico =  $constructionSite->ReliefDocument->where('folder_name', 'Documenti Fotovoltaico')->first();
        $files3 = ['Bolletta Luce', 'Carta D Identità Intestatario Bollette'];
        $count50 =  $DocumentiFotovoltaico ?  $DocumentiFotovoltaico->ReliefDocumentFile->whereIn('file_name', $files3)->where('updated_by', '!=', null)->where('state', 1)->count() : 0;

        $PraticheComunali =  $constructionSite->ReliefDocument->where('folder_name', 'Pratiche Comunali')->first();
        $count90 =  null;
        $count91 =  null;
        $count92 =  null;
        if ($PraticheComunali) {
            $files4 = ['Cilas Protocollata 110', 'Protocollo cilas 110'];
            $count90 =   $PraticheComunali->ReliefDocumentFile->whereIn('file_name', $files4)->where('updated_by', '!=', null)->where('state', 1)->count();

            $files5 = ['Cila protocollata 50-65-90', 'Protocollo Cila 50-65-90'];
            $count91 =   $PraticheComunali->ReliefDocumentFile->whereIn('file_name', $files5)->where('updated_by', '!=', null)->where('state', 1)->count();

            $files6 = ['Estratto Di Mappa', 'Notifica Preliminare'];
            $count92 =   $PraticheComunali->ReliefDocumentFile->whereIn('file_name', $files6)->where('updated_by', '!=', null)->where('state', 1)->count();
        }


        $count1 = RelDocFile::where('file_name', "Carta D'identità")->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        // $count2 = RelDocFile::where('file_name', 'Carta D Identità Intestatario Bollette')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count3 = RelDocFile::where('file_name', 'Codice Fiscale')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count4 = RelDocFile::where('file_name', 'Bolletta Luce')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count5 = RelDocFile::where('file_name', 'Cilas Protocollata 110')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count6 = RelDocFile::where('file_name', 'Atto Di Provenienza')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count7 = RelDocFile::where('file_name', 'Visura Catastale')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count8 = RelDocFile::where('file_name', 'Estratto Di Mappa')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count9 = RelDocFile::where('file_name', 'Legge 10')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count10 = RelDocFile::where('file_name', 'Notifica Preliminare')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count11 = RelDocFile::where('file_name', 'Protocollo cilas 110')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count12 = RelDocFile::where('file_name', 'Cila protocollata 50-65-90')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();
        $count13 = RelDocFile::where('file_name', 'Protocollo Cila 50-65-90')->where('state', 1)->where('updated_by', '!=', null)->where('construction_site_id', $id)->count();













        if ($constructionSite->ConstructionSiteSetting != null && $constructionSite->ConstructionSiteSetting->type_of_deduction) {
            $check_deduction = explode(",", $constructionSite->ConstructionSiteSetting->type_of_deduction);


            if (count($check_deduction) != 0) {
                if (in_array("110", $check_deduction) && (in_array("50", $check_deduction) || in_array("65", $check_deduction) || in_array("90", $check_deduction))) {
                    $count = $count2 + $count46 + $count50 + $count90 + $count91 + $count92;
                    $totalfiles = 13;
                } elseif (in_array("110", $check_deduction) || in_array("50", $check_deduction) || in_array("65", $check_deduction) || in_array("90", $check_deduction)) {
                    // dd($count2, $count46, $count50 ,$count91, $count92);
                    $count = $count2 + $count46 + $count50 + $count91 + $count92;
                    $totalfiles = 11;
                } else {
                    $count = $count2 + $count46 + $count50 + $count90 + $count91 + $count92;
                    $totalfiles = 9;
                }
            }


            return array($count, $totalfiles);
        }
    }

    /**
     * Construction Material relationship
     */
    public function ConstructionMaterial()
    {
        return $this->hasMany(ConstructionMaterial::class);
    }

    /**
     * Construction material has many through relationship
     */
    public function ConstructionMaterialAssitance()
    {
        return $this->hasManyThrough(MaterialsAsisstance::class, ConstructionMaterial::class);
    }

    // relation with MaterialsAsisstance construction_material_id
    public function MaterialsAsisstance()
    {
        return $this->hasMany(MaterialsAsisstance::class);
    }

    // relation with MaterialsAsisstance construction_material_id
    public function MaterialsAsisstanceDate()
    {
        if (request()->date) {
            $date = request()->date;
        } else {
            $date = Carbon::now();
        }
        $month = Carbon::parse($date)->month;
        $year = Carbon::parse($date)->year;

        return $this->hasMany(MaterialsAsisstance::class)->whereMonth('start_date', $month)->whereYear('start_date', $year);
    }

    public function ConstructionCondominiMain($id)
    {
        return $this->hasOne(ConstructionCondomini::class, 'construction_assigned_id')->where('construction_assigned_id', $id)->first();
    }

    public function ConstructionCondominiParent($id)
    {
        return $this->hasOne(ConstructionCondomini::class, 'construction_site_id')->where('construction_site_id', $id)->first();
    }

    public function GetConstructionSiteCondomini()
    {
        return $this->hasOne(ConstructionCondomini::class, 'construction_site_id');
    }
    
       public function GetConstructionSiteCondominies()
    {
        return $this->hasMany(ConstructionCondomini::class, 'construction_site_id');
    }


    public function GetConstructionCondomini()
    {
        return $this->hasMany(ConstructionCondomini::class, 'construction_assigned_id');
    }

    public function GetConstructionCondominiOne()
    {
        return $this->hasOne(ConstructionCondomini::class, 'construction_assigned_id');
    }


    public function missingColumns()
    {
        return $this->hasOne(ConstructionMissingColumn::class);

    }

    public function consMatHistory(){
        
        return $this->hasMany(MatarialHistory::class, 'construction_site_id');
    }
    
}

<?php

namespace App\Http\Controllers;

use Google_Client;
// import Que Jobs
use App\Models\User;
use App\Jobs\UserFolders;
use App\Models\StatusSAL;
// -------
use Google_Service_Sheets;
use App\Models\StatusLeg10;
use Illuminate\Support\Str;
use App\Models\MaterialList;
use App\Models\PropertyData;
use App\Models\StatusPrNoti;
use App\Models\StatusRelief;
use App\Models\statusRegPrac;
use App\Models\StatusWorkClose;
use App\Models\ConstructionSite;
use App\Models\StatusTechnician;
use App\Models\ConstructionNotes;
use App\Models\StatusComputation;
use App\Models\StatusEneaBalance;
use App\Models\StatusPreAnalysis;
use App\Models\StatusWorkStarted;
use App\Models\DocumentAndContact;
use App\Models\MaterialsAsisstance;
use App\Http\Controllers\Controller;
use App\Models\ConstructionMaterial;
use Illuminate\Support\Facades\Hash;
use App\Models\ConstructionCondomini;
use App\Models\ConstructionJobDetail;
use App\Models\ConstructionSiteImage;
use App\Jobs\UploadConstructionImages;
use App\Jobs\ConstructionFileStructure;
use App\Models\ConstructionSiteSetting;

class UploadCSVController extends Controller
{



    private function get_google_sheet_api($range)
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);
        $service = new Google_Service_Sheets($client);

        $spreadsheetId = '1CWx_AERIB5eEdz9V2cnCLLJbNpAxjVYfMtq2fXOfxW0';

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        return $response->getValues();
    }

    /**
     * Add new user
     */
    public function upload()
    {
        $values = $this->get_google_sheet_api('Users!B3:T289');
        $i =  0;
        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $i++;
            $id = ['id' => $row[0]];

            $userArr = [
                'id' => $row[0],
                'name' => $row[1],
                'email' => $row[2],
                'phone' => $row[3],
                'birthplace' => $row[4],
                'password' => Hash::make($row[5]),
                'orignalpass' => $row[5],
                'birth_country' => $row[6],
                'dob' => $row[7],
                'residence_city' => $row[8],
                'residence_province' => $row[9],
                'residence' => $row[10],
                'fiscal_code' => $row[11],
                'professional_college' => $row[12],
                'common_college' => $row[13],
                'registration_number' => $row[14],
                'status' => isset($row[16]) ? (int)$row[16] : 0,
            ];

            $check_user = User::where('email', $row[2])->first();

            $userrole = explode(' ', $row[15]);
            $role = Str::lower($userrole[0]);

            if ($check_user == null) {

                $user = User::updateOrCreate($id, $userArr);
                $user->assignRole($role);

                if ($role == 'business') {
                    $businessArr = [
                        'company_name' => isset($row[18]) ? $row[18] : null,
                        'company_type' => isset($row[17]) ? $row[17] : null,
                        'status' => isset($row[16]) ? (int)$row[16] : 0
                    ];

                    $user->business()->updateOrCreate($businessArr);
                }
            } else {
                if ($role == 'business') {
                    $businessArr = [
                        'company_name' => isset($row[18]) ? $row[18] : null,
                        'company_type' => isset($row[17]) ? $row[17] : null,
                        'status' => isset($row[16]) ? (int)$row[16] : 0
                    ];

                    $check_user->business()->updateOrCreate($businessArr);
                }
            }
        }

        return $i . "users have been uploaded successfully";
    }


    public function deleteimges()
    {
        // Retrieve all records from the ConstructionSiteImage table
        $constructionSiteImages = ConstructionSiteImage::all();

        // Specify the base directory where your images are stored
        $baseDirectory = public_path(); // You might need to adjust this path

        // Loop through each record and remove the associated image
        foreach ($constructionSiteImages as $image) {
            // dd($image);
            $path = $image->path;
            $imagePath = $baseDirectory . '/' . 'construction-assets' . '/' . $image->construction_site_id . '/' . $path;
            $thunmbnilImages = $baseDirectory . '/' . 'construction-assets' . '/' . $image->construction_site_id . '/' . 'thumbnail' . '/' . $image->folder . '/'. $image->name;
            // $thunmbnil =  asset('construction-assets/' . $image->construction_site_id . '/thumbnail/' . $image->folder . '/'. $image->name);

            // dd($imagePath);

            // Use PHP's file system functions to check and delete the image file
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            if (file_exists($thunmbnilImages)) {
                unlink($thunmbnilImages);
            }





            // Delete the record from the table
            $image->delete();
        }
        return true;
    }

    /**
     * add new material list
     */
    public function addMaterialList()
    {
        $values = $this->get_google_sheet_api('material_lists!C3:F224');
        // dd($values);
        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {

            $materialArr = [
                'material_type_id' => $row[0],
                'name' => $row[1],
                'unit' => $row[2],
                'user_id' => $row[3],
            ];

            $material_list = MaterialList::where('name', $row[1])->first();

            if ($material_list == null) {
                MaterialList::create($materialArr);
            }
        }

        return true;
    }

    private function add_construction($oldid, $constructionArr)
    {
        return ConstructionSite::updateOrCreate($oldid, $constructionArr);
    }

    /**
     * add new construction site
     */
    public function addConstructionSite()
    {
        // $values = $this->get_google_sheet_api('construction_site!B3:O');
        $values = $this->get_google_sheet_api('construction_site!B3:O925');
        // $values = $this->get_google_sheet_api('construction_site!B3:O');

        // Iterate through the rows and insert the data into the database

        foreach ($values as $row) {
            $oldId = [
                'oldid' => isset($row[0]) ? $row[0] : null,
            ];
            // $ConstructionSite  = ConstructionSite::where('oldid', $row[0])->first() ;
        
            // $constructionArr = [
            //     'id' => $row[0],
            //     'oldid' => $row[0],
            //     'name' => isset($row[1]) ? $row[1] : null,
            //     'surename' => isset($row[2]) ? $row[2] : null,
            //     'date_of_birth' => isset($row[3]) ? $row[3] : null,
            //     'town_of_birth' => isset($row[4]) ? $row[4] : null,
            //     'province' => isset($row[5]) ? $row[5] : null,
            //     'residence_street' => isset($row[6]) ? $row[6] : null,
            //     'residence_house_number' => isset($row[7]) ? $row[7] : null,
            //     'residence_postal_code' => isset($row[8]) ? $row[8] : null,
            //     'residence_common' => isset($row[9]) ? $row[9] : null,
            //     'residence_province' => isset($row[10]) ? $row[10] : null,
            //     'latest_status' => isset($row[11]) ? $row[11] : null,
            //     'archive' => isset($row[12]) ? (int)$row[12] : null,
            //     'page_status' => isset($row[13]) ? (int)$row[13] : null,
            // ];

            // ConstructionSite::create($constructionArr);
                // if($ConstructionSite == null){

        
            $cons = new ConstructionSite();
            // $cons->id = $row[0];
            $cons->oldid = $row[0];
            $cons->name = isset($row[1]) ? $row[1] : null;
            $cons->surename = isset($row[2]) ? $row[2] : null;
            $cons->date_of_birth = isset($row[3]) ? $row[3] : null;
            $cons->town_of_birth = isset($row[4]) ? $row[4] : null;
            $cons->province = isset($row[5]) ? $row[5] : null;
            $cons->residence_street = isset($row[6]) ? $row[6] : null;
            $cons->residence_house_number = isset($row[7]) ? $row[7] : null;
            $cons->residence_postal_code = isset($row[8]) ? $row[8] : null;
            $cons->residence_common = isset($row[9]) ? $row[9] : null;
            $cons->residence_province = isset($row[10]) ? $row[10] : null;
            $cons->latest_status = isset($row[11]) ? $row[11] : null;
            $cons->archive = isset($row[12]) ? (int)$row[12] : null;
            $cons->page_status = isset($row[13]) ? (int)$row[13] : null;
            $cons->save();
                // }
            // $this->add_construction($oldId, $constructionArr);
        }

        return true;
    }

    /**
     * add new document_and_contacts
     */
    public function document_and_contacts()
    {
        // $values = $this->get_google_sheet_api('document_and_contacts!B3:L687');
        // $values = $this->get_google_sheet_api('document_and_contacts!B688:L925');
        $values = $this->get_google_sheet_api('document_and_contacts!B3:L925');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);

            if ($id != null) {
                $conId = [
                    'construction_site_id' => $id->id,
                ];

                $constructionArr = array(
                    'document_number' => isset($row[1]) ? $row[1] : null,
                    'issued_by' => isset($row[2]) ? $row[2] : null,
                    'release_date' => isset($row[3]) ? $row[3] : null,
                    'expiration_date' => isset($row[4]) ? $row[4] : null,
                    'fiscal_document_number' => isset($row[5]) ? $row[5] : null,
                    'vat_number' => isset($row[6]) ? $row[6] : null,
                    'contact_email' => isset($row[7]) ? $row[7] : null,
                    'contact_number' => isset($row[8]) ? $row[8] : null,
                    'alt_refrence_name' => isset($row[9]) ? $row[9] : null,
                    'alt_contact_number' => isset($row[10]) ? $row[10] : null
                );

                DocumentAndContact::updateOrCreate($conId, $constructionArr);
            }

            
        }

        return true;
    }
    /**
     * add new property_data
     */
    public function property_data()
    {
        // $values = $this->get_google_sheet_api('property_data!B3:M852');
        $values = $this->get_google_sheet_api('property_data!B3:M925');
        // $values = $this->get_google_sheet_api('property_data!B3:M');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);

            if ($id != null) {
                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                $constructionArr = array(
                    'property_street' => isset($row[1]) ? $row[1] : null,
                    'property_house_number' => isset($row[2]) ? $row[2] : null,
                    'property_common' => isset($row[3]) ? $row[3] : null,
                    'property_postal_code' => isset($row[4]) ? $row[4] : null,
                    'property_province' => isset($row[5]) ? $row[5] : null,
                    'cadastral_dati' => isset($row[6]) ? $row[6] : null,
                    'cadastral_section' => isset($row[7]) ? $row[7] : null,
                    'cadastral_particle' => isset($row[8]) ? $row[8] : null,
                    'sub_ordinate' => isset($row[9]) ? $row[9] : null,
                    'pod_code' => isset($row[10]) ? $row[10] : null,
                    'cadastral_category' => isset($row[11]) ? $row[11] : null
                );

                PropertyData::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new construction_site_settings
     */
    public function construction_site_settings()
    {
        $values = $this->get_google_sheet_api('construction_site_settings!B3:I925');
        // $values = $this->get_google_sheet_api('construction_site_settings!B851:I925');
        
        // $values = $this->get_google_sheet_api('construction_site_settings!B3:I');
        // dd($values);
        $fileStructureArr = [];
     
        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $typeDeductionArr = [];

            $id  = ConstructionSite::where('oldid', $row[0])->first();

            if ($id == null) {
                echo "construction site not found for: " . $row[0] . "<br/>";
              
            } else {
                if (isset($row[3]) && $row[3] != null && $row[3] != 'NULL') {
                    array_push($typeDeductionArr, $row[3]);
                }
                if (isset($row[4]) && $row[4] != null && $row[4] != 'NULL') {
                    array_push($typeDeductionArr, $row[4]);
                }
                if (isset($row[5]) && $row[5] != null && $row[5] != 'NULL') {
                    array_push($typeDeductionArr, $row[5]);
                }
                if (isset($row[6]) && $row[6] != null && $row[6] != 'NULL') {
                    array_push($typeDeductionArr, $row[6]);
                }
                if (isset($row[7]) && $row[7] != null && $row[7] != 'NULL') {
                    array_push($typeDeductionArr, $row[7]);
                }

                $type_of_deduction = implode(",", $typeDeductionArr);

                if ($id != null) {
                    $conId = [
                        'construction_site_id' => $id == null ? null : $id->id,
                    ];

                    $constructionArr = array(
                        'type_of_property' => isset($row[1]) ? $row[1] : null,
                        'type_of_construction' => isset($row[2]) ? $row[2] : null,
                        'type_of_deduction' => $type_of_deduction
                    );

                    ConstructionSiteSetting::updateOrCreate($conId, $constructionArr);
                }

              


                // dispatch(new ConstructionFileStructure($id, $typeDeductionArr));

                array_push($fileStructureArr, ['id' => $id, 'typeDeductionArr' => $typeDeductionArr]);
            }
        }

        $this->session_store('fileStructureArr', $fileStructureArr);

        return true;
    }

    /**
     * Create file structure
     */
    public function filestructure()
    {
        $fileStructureArr = $this->session_get('fileStructureArr');
        // dd($fileStructureArr);
        

        foreach ($fileStructureArr as $value) {
          
            dispatch(new ConstructionFileStructure($value['id'], $value['typeDeductionArr']));
        }

        $this->session_remove('fileStructureArr');

        return true;
    }

    /**
     * add new construction_materials
     */
    public function construction_materials()
    {
        $values = $this->get_google_sheet_api('construction_materials!B3:K4615');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[1])->first();

            if ($id != null) {
                $matId = null;

                if (isset($row[2])) {
                    if ($row[2] == 0) {
                        $matId = null;
                    } else {
                        $matId = (int)$row[2];
                    }
                } else {
                    $matId = null;
                }

                $matData = MaterialList::where('id', $matId)->first();

                if (isset($row[9])) {
                    $updatedlastby =  User::find($row[9]);

                    if ($updatedlastby) {
                        $updatedlastbyid = $updatedlastby->id;
                    } else {
                        $updatedlastbyid =  null;
                    }
                } else {
                    $updatedlastbyid =  null;
                }


                // $constructionArr = array(
                //     'id' => $row[0],
                //     'construction_site_id' => $id == null ? null : $id->id,
                //     'material_list_id' => $matData != null ? $matData->id : null,
                //     'quantity' => isset($row[3]) ? $row[3] : null,
                //     'state' => isset($row[4]) ? $row[4] : null,
                //     'consegnato' => isset($row[5]) ? $row[5] : null,
                //     'avvio' => isset($row[6]) ? $row[6] : null,
                //     'montato' => isset($row[7]) ? $row[7] : null,
                //     'note' => isset($row[8]) ? $row[8] : null,
                //     'updated_by' => isset($row[9]) ? $row[9] : null,
                // );

                // ConstructionMaterial::create($constructionArr);

                $new = new ConstructionMaterial();

                $new->id = $row[0];

                $existingRecord = ConstructionMaterial::where('id', $row[0])->first();

                if ($existingRecord == null) {
                    $new->construction_site_id = $id == null ? null : $id->id;
                    $new->material_list_id = $matData != null ? $matData->id : null;
                    $new->quantity = isset($row[3]) ? $row[3] : null;
                    $new->state = isset($row[4]) ? $row[4] : null;
                    $new->consegnato = isset($row[5]) ? $row[5] : 0;
                    $new->avvio = isset($row[6]) ? $row[6] : null;
                    $new->montato = isset($row[7]) ? $row[7] : 0;
                    $new->note = isset($row[8]) ? $row[8] : null;
                    $new->updated_by = $updatedlastbyid;
                    $new->save();
                }
            }
        }

        return true;
    }

    /**
     * add new material_assistance
     */
    public function material_assistance()
    {
        $values = $this->get_google_sheet_api('material_assistance!B3:I224');
        // dd($values);
        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[1])->first();

            // $this->checkConstruction($id, $row[0]);

            if ($id != null) {
                $asmat = new MaterialsAsisstance();

                $asmat->id = $row[0];
                $asmat->construction_site_id = $id == null ? null : $id->id;
                $asmat->machine_model = isset($row[2]) ? $row[2] : null;
                $asmat->freshman = isset($row[3]) ? ($row[3] == 'NULL' ? null : $row[3]) : null;
                $asmat->expiry_date = isset($row[4]) ? $row[4] : null;
                $asmat->notes = isset($row[5]) ? $row[5] : null;
                $asmat->state = isset($row[6]) ? $row[6] : null;
                $asmat->status = isset($row[7]) ? $row[7] : 0;

                $asmat->save();
            }
        }

        return true;
    }

    /**
     * add new construction_notes
     */
    public function construction_notes()
    {
        $values = $this->get_google_sheet_api('construction_notes!B3:E711');
        // $values = $this->get_google_sheet_api('construction_notes!B3:E');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            $user_id = null;
            if (isset($row[3])) {
                if ($row[3] != "NULL") {
                    $user_id  = User::where('id', $row[3])->pluck('id')->first();
                }
            }

            // $this->checkConstruction($id, $row[0]);

            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'notes' => isset($row[1]) ? $row[1] : null,
                    'priority' => isset($row[2]) ? $row[2] : null,
                    'admin_id' => $user_id,
                );

                ConstructionNotes::create($constructionArr);
            }
        }

        return true;
    }

    /**
     * add new construction_job
     */
    public function construction_job()
    {
        $values = $this->get_google_sheet_api('construction_job!B3:E711');
        // $values = $this->get_google_sheet_api('construction_job!B3:E');

        // Iterate through the rows and insert the data into the database

        foreach ($values as $row) {
            if (isset($row[0])) {
                $id  = ConstructionSite::where('oldid', $row[0])->first();

                $fixtures = null;
                if (isset($row[1])) {
                    if ($row[1] != "NULL") {
                        $fixtures  = User::where('id', $row[1])->pluck('id')->first();
                    }
                }

                $plumbing = null;
                if (isset($row[3])) {
                    if ($row[3] != "NULL") {
                        $plumbing  = User::where('id', $row[3])->pluck('id')->first();
                    }
                }

                $electrical = null;
                if (isset($row[5])) {
                    if ($row[5] != "NULL") {
                        $electrical  = User::where('id', $row[5])->pluck('id')->first();
                    }
                }

                $construction = null;
                if (isset($row[7])) {
                    if ($row[7] != "NULL") {
                        $construction  = User::where('id', $row[7])->pluck('id')->first();
                    }
                }

                $construction2 = null;
                if (isset($row[9])) {
                    if ($row[9] != "NULL") {
                        $construction2  = User::where('id', $row[9])->pluck('id')->first();
                    }
                }

                $photovoltaic = null;
                if (isset($row[11])) {
                    if ($row[11] != "NULL") {
                        $photovoltaic  = User::where('id', $row[11])->pluck('id')->first();
                    }
                }

                $coordinatore = null;
                if (isset($row[19])) {
                    if ($row[19] != "NULL") {
                        $coordinatore  = User::where('id', $row[19])->pluck('id')->first();
                    }
                }

                $construction_manager = null;
                if (isset($row[20])) {
                    if ($row[20] != "NULL") {
                        $construction_manager  = User::where('id', $row[20])->pluck('id')->first();
                    }
                }

                // $this->checkConstruction($id, $row[0]);

                if ($id != null) {
                    $constructionArr = array(
                        'construction_site_id' => $id == null ? null : $id->id,
                        'fixtures' => $fixtures,
                        'fixtures_company_price' => isset($row[2]) ? $row[2] : null,
                        'plumbing' => $plumbing,
                        'plumbing_company_price' => isset($row[4]) ? $row[4] : null,
                        'electrical' => $electrical,
                        'electrical_installations_company_price' => isset($row[6]) ? $row[6] : null,
                        'construction' => $construction,
                        'construction_company1_price' => isset($row[8]) ? $row[8] : null,
                        'construction2' => $construction2,
                        'construction_company2_price' => isset($row[10]) ? $row[10] : null,
                        'photovoltaic' => $photovoltaic,
                        'photovoltaic_price' => isset($row[12]) ? $row[12] : null,

                        'coordinatore' => $coordinatore,
                        'construction_manager' => $construction_manager,
                    );

                    ConstructionJobDetail::create($constructionArr);
                }
            }
        }

        return true;
    }

    /**
     * add new status_pre_analyses
     */
    public function status_pre_analyses()
    {
        $values = $this->get_google_sheet_api('status_pre_analyses!B3:I689');
        // $values = $this->get_google_sheet_api('status_pre_analyses!B3:I');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'reminder_emails' => isset($row[2]) ? $row[2] : null,
                    'reminder_days' => isset($row[3]) ? $row[3] : null,
                    'turnover' => isset($row[4]) ? $row[4] : null,
                    'embedded' => isset($row[5]) ? $row[5] : null,
                    'updated_on' => isset($row[6]) ? $row[6] : null,
                    'updated_by' => isset($row[7]) ? $row[7] : null,
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusPreAnalysis::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_technicians
     */
    public function status_technicians()
    {
        $values = $this->get_google_sheet_api('status_technicians!B3:H689');
        // $values = $this->get_google_sheet_api('status_technicians!B3:H');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                if (isset($row[1]) && $row[1] != 0) {
                    $user = User::where('id', $row[1])->first();

                    if ($user != null) {
                        $tech_id = $user->id;
                    } else {
                        $tech_id = null;
                    }
                } else {
                    $tech_id = null;
                }

                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'tecnician_id' => $tech_id,
                    'state' => isset($row[2]) ? $row[2] : null,
                    'reminder_emails' => isset($row[3]) ? $row[3] : null,
                    'reminder_days' => isset($row[4]) ? $row[4] : null,
                    'updated_on' => isset($row[5]) ? $row[5] : null,
                    'updated_by' => isset($row[6]) ? $row[6] : null,
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusTechnician::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_reliefs
     */
    public function status_reliefs()
    {
        $values = $this->get_google_sheet_api('status_reliefs!B3:G689');
        // $values = $this->get_google_sheet_api('status_reliefs!B3:G');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'reminder_emails' => isset($row[2]) ? $row[2] : null,
                    'reminder_days' => isset($row[3]) ? $row[3] : null,
                    'updated_on' => isset($row[4]) ? $row[4] : null,
                    'updated_by' => isset($row[5]) ? $row[5] : null,
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusRelief::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_leg10s
     */
    public function status_leg10s()
    {
        $values = $this->get_google_sheet_api('status_leg10s!B3:G689');
        // $values = $this->get_google_sheet_api('status_leg10s!B3:G');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'reminder_emails' => isset($row[2]) ? $row[2] : null,
                    'reminder_days' => isset($row[3]) ? $row[3] : null,
                    'updated_on' => isset($row[4]) ? $row[4] : null,
                    'updated_by' => isset($row[5]) ? $row[5] : null,
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusLeg10::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_computations
     */
    public function status_computations()
    {
        $values = $this->get_google_sheet_api('status_computations!B3:E689');
        // $values = $this->get_google_sheet_api('status_computations!B3:E');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'updated_on' => isset($row[2]) ? $row[2] : null,
                    'updated_by' => isset($row[3]) ? $row[3] : null
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusComputation::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_pr_notis
     */
    public function status_pr_notis()
    {
        $values = $this->get_google_sheet_api('status_pr_notis!B3:G689');
        // $values = $this->get_google_sheet_api('status_pr_notis!B3:G');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'reminder_emails' => isset($row[2]) ? $row[2] : null,
                    'reminder_days' => isset($row[3]) ? $row[3] : null,
                    'updated_on' => isset($row[4]) ? $row[4] : null,
                    'updated_by' => isset($row[5]) ? $row[5] : null
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusPrNoti::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_reg_pracs
     */
    public function status_reg_pracs()
    {
        $values = $this->get_google_sheet_api('status_reg_pracs!B3:G689');
        // $values = $this->get_google_sheet_api('status_reg_pracs!B3:G');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'reminder_emails' => isset($row[2]) ? $row[2] : null,
                    'reminder_days' => isset($row[3]) ? $row[3] : null,
                    'updated_on' => isset($row[4]) ? $row[4] : null,
                    'updated_by' => isset($row[5]) ? $row[5] : null
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                statusRegPrac::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_work_starteds
     */
    public function status_work_starteds()
    {
        $values = $this->get_google_sheet_api('status_work_starteds!B3:F689');
        // $values = $this->get_google_sheet_api('status_work_starteds!B3:F');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'work_started_date' => isset($row[2]) ? $row[2] : null,
                    'updated_on' => isset($row[3]) ? $row[3] : null,
                    'updated_by' => isset($row[4]) ? $row[4] : null
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusWorkStarted::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_work_closes
     */
    public function status_work_closes()
    {
        $values = $this->get_google_sheet_api('status_work_closes!B3:E689');
        // $values = $this->get_google_sheet_api('status_work_closes!B3:E');


        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            /// $this->checkConstruction($id, $row[0]);

            if ($id != null) {
                if (isset($row[1])) {
                    if ($row[1] == 'Completed') {
                        $id->update(['status' => 0]);
                    }
                }

                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'updated_on' => isset($row[2]) ? $row[2] : null,
                    'updated_by' => isset($row[3]) ? $row[3] : null
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusWorkClose::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_s_a_l_s
     */
    public function status_s_a_l_s()
    {
        $values = $this->get_google_sheet_api('status_s_a_l_s!B3:H689');
        // $values = $this->get_google_sheet_api('status_s_a_l_s!B3:H');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'select_accountant' => isset($row[2]) ? ($row[2] == 0 ? null : $row[2]) : null,
                    'updated_on' => isset($row[3]) ? $row[3] : null,
                    'updated_by' => isset($row[4]) ? $row[4] : null,
                    'reminder_emails' => isset($row[5]) ? $row[5] : null,
                    'reminder_days' => isset($row[6]) ? $row[6] : null
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusSAL::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * add new status_enea_balances
     */
    public function status_enea_balances()
    {
        $values = $this->get_google_sheet_api('status_enea_balances!B3:F689');
        // $values = $this->get_google_sheet_api('status_enea_balances!B3:F');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'state' => isset($row[1]) ? $row[1] : null,
                    'select_accountant' => isset($row[2]) ? ($row[2] == 0 ? null : $row[2]) : null,
                    'updated_on' => isset($row[3]) ? $row[3] : null,
                    'updated_by' => isset($row[4]) ? $row[4] : null
                );

                $conId = [
                    'construction_site_id' => $id == null ? null : $id->id,
                ];

                StatusEneaBalance::updateOrCreate($conId, $constructionArr);
            }
        }

        return true;
    }

    /**
     * Upload construction images
     */
    public function  uploadImages()
    {
        // $values = $this->get_google_sheet_api('construction_site_images!B3:I32307');
        $values = $this->get_google_sheet_api('construction_site_images!B32308:I32771');
        // dd(        $values);
        // $values = $this->get_google_sheet_api('construction_site_images!B3:I');
        // $values = $this->get_google_sheet_api('construction_site_images!B3:I1');
        // dd($values);
        foreach ($values as $row) {
            // if($row[0] == '732'){
            // if($row[0] == '299'&& $row[4] == '1'  || $row[0] == '191' && $row[4] == '1' || $row[0] == '732' && $row[4] == '1' || $row[0] == '1030' && $row[4] == '1' || $row[0] == '423' && $row[4] == '1' ){
            dispatch(new UploadConstructionImages($row));
            // }

        }

        return true;
    }
    /**
     * Upload construction images
     */
    public function uploadFolder()
    {
        // $values = $this->get_google_sheet_api('folders!A2:N34751');
        // $values = $this->get_google_sheet_api('test-folders!A2:N35279');
        $values = $this->get_google_sheet_api('test-folders!A35280:N36030');
        // $values = $this->get_google_sheet_api('test-folders!A2:N');
        // $values = $this->get_google_sheet_api('folders!A2:N100');
        // dd($values);
        // $count = 0;
        // $missedRecords = [];
        foreach ($values as $innerArray) {
            // if ($innerArray[0] == "732") {
            // foreach ($values as $row) {
            dispatch(new UserFolders($innerArray));
            // }

            // }
        }
        // echo $missedRecords;
        // dd("Count: " . $count);


        return true;
    }

    /**
     * add new construction_condominis
     */
    public function construction_condominis()
    {
        $values = $this->get_google_sheet_api('construction_condominis!B3:C163');
        // $values = $this->get_google_sheet_api('construction_condominis!B3:C');

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {
            $id  = ConstructionSite::where('oldid', $row[0])->first();
            $assign_id  = ConstructionSite::where('oldid', $row[1])->first();

            // $this->checkConstruction($id, $row[0]);
            if ($id != null) {
                if ($assign_id == null) {
                    $add_construction = ['oldid' => $row[1]];

                    $assign_id =  $this->add_construction($add_construction, $add_construction);

                    // echo "construction construction condominis assign data failed for: " . $row[1] . "new id: " . $assign_id->id . "<br/>";
                } else {
                    // echo "construction construction condominis assign data inserted for: " . $row[1] . "<br/>";
                }

                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'construction_assigned_id' => $assign_id == null ? null : $assign_id->id
                );

                ConstructionCondomini::create($constructionArr);
            }
            echo  "<br/>";
        }

        return true;
    }

    private function checkConstruction($id, $oldId)
    {
        if ($id == null) {
            $add_construction = ['oldid' => $oldId];

            $id =  $this->add_construction($add_construction, $add_construction);
            // echo "construction construction condominis data failed for: " . $row[0] . "new id: " . $id->id . "<br/>";
        }
    }
}

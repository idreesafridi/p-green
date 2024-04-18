<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\PrNotDoc;
use App\Models\Leg10File;
use App\Models\ReliefDoc;
use App\Models\RegPracDoc;
use App\Models\RelDocFile;
// here i get files from models
use App\Models\PrNotDocFile;
use Illuminate\Http\Request;
use App\Models\ConstructionSite;
// chiavetta
use App\Models\TypeOfDedectionSub1;
use App\Models\TypeOfDedectionSub2;
use App\Http\Controllers\Controller;
use App\Models\TypeOfDedectionFiles;
use App\Models\ChiavettaDoc;
use Illuminate\Support\Facades\File;
use App\Helper\ConstuctionChiledStore;
use App\Models\BusinessDetail;
use App\Models\systemStatus;

use Illuminate\Support\Facades\Response;
use Exception;

// use Dompdf\Dompdf;
// use Dompdf\Options;

class ConstructionSiteController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $essentialId = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ConstructionSite $modal, $essential = null)
    {
        $this->_request = $request;
        $this->_modal = $modal;
        $this->essentialId = $essential;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $cliente = systemStatus::first();
        // return view('home');
          if ($cliente && $cliente->status == 0) {
            $errorMessage = 'Sorry! Your site is down. For further details, please contact the site development team.';
            abort(500, $errorMessage);
        } else {
            return view('home');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (isset($this->_request->id) && $this->_request->id != null) {
            $data = $this->get_by_id($this->_modal, $this->_request->id);
        } else {
            $data = null;
        }

        return view('construction.construction', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {


        // dd($this->_request->all());
        $data = $this->_request->only('name', 'surename', 'date_of_birth', 'town_of_birth', 'province', 'residence_street', 'residence_house_number', 'residence_postal_code', 'residence_common', 'residence_province');
        $DocumentAndContact = $this->_request->only('document_number', 'issued_by', 'release_date', 'expiration_date', 'fiscal_document_number', 'vat_number', 'contact_email', 'contact_number', 'alt_refrence_name', 'alt_contact_number');
        $PropertyData = $this->_request->only('cadastral_dati', 'property_house_number', 'property_street', 'property_postal_code', 'property_common', 'property_province', 'cadastral_section', 'cadastral_category', 'cadastral_particle', 'sub_ordinate','Piano', 'pod_code');
        if(isset($PropertyData['Piano'])){
            $PianoAsString = implode(',', $PropertyData['Piano']);

            $PropertyData['Piano'] = $PianoAsString;
        }
     

      
       
        $ConstructionSiteSetting = $this->_request->only('type_of_property', 'type_of_construction', 'type_of_deduction');

        if ($this->_request->type_of_deduction != null) {
            $type_of_deduction = implode(",", $this->_request->type_of_deduction);
            $ConstructionSiteSetting['type_of_deduction'] = $type_of_deduction;
        }

        $type_of_deduction_values = $this->_request->type_of_deduction;

        $var = $this->add($this->_modal, $data);

        $var->DocumentAndContact()->updateOrCreate($DocumentAndContact);
        $var->PropertyData()->updateOrCreate($PropertyData);
        $var->ConstructionSiteSetting()->updateOrCreate($ConstructionSiteSetting);

        $this->update_page_status(new ConstructionSite(), $var->id, 4);
        // constrions chiled
        $ConstuctionChiledStore = new ConstuctionChiledStore;
        $ConstuctionChiledStore->add_data_into_chiled($var, $type_of_deduction_values);

        if ($type_of_deduction_values != null) {

            if (in_array("Fotovoltaico",  $type_of_deduction_values) || in_array("fotovoltaico",  $type_of_deduction_values)) {

                $to = 'fotovoltaico.greengen@gmail.com';
                $subject = '!! fotovoltaico portale GREENGEN';
                $data = [
                    'name' => $this->_request->name,
                    'email' => $this->_request->contact_email
                ];
                $path = 'emails.cf2';
                $this->email_against_missing_files($to, $subject, $data, $path);
            }
        }

        if ($this->_request->contact_email != null && $this->_request->name != null) {
            // welcome email sent to construction owner
            $to = $this->_request->contact_email;
            $subject = '!! Accedi al nuovo portale GREENGEN';
            $data = [
                'name' => $this->_request->name,
                'email' => $this->_request->contact_email
            ];
            $path = 'emails.mail-assigned';
            $this->email_against_missing_files($to, $subject, $data, $path);
        }
        // dd( $this->_request->type_of_property);
        // store condominio
        // $this->_request->type_of_property == 'Condominio' ? $var->GetConstructionSiteCondomini()->create(['construction_site_id'=>$var->id]) : null;


        // if ($this->_request->fk_id != null) {
        //     $condominioArr = [
        //         'construction_site_id' => $this->_request->fk_id,
        //     ];

        //     $var->GetConstructionCondomini()->create($condominioArr);
        //     // $var->GetConstructionSiteCondomini()->create($condominioArr);
        // }else{
        //     $condominioArr = [
        //         'construction_site_id' => $var->id,
        //     ];
        //     $var->GetConstructionSiteCondomini()->create($condominioArr);
        // }

        // store construction id in session
        $this->session_store("construction_id", $var->id);

        return redirect()->route('home')->with('success', 'Dati di costruzione aggiunti con successo!');
    }

    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show($id, $pagename)
    {
        $construct_id  = $id;

        // store construction id in session
        $this->session_store("construction_id", $id);

        $data['data'] = $this->get_by_id($this->_modal, $id);
        $data['tech'] = $this->user_by_role(new User(), 'technician');
        $data['account'] = $this->user_by_role(new User(), 'businessconsultant');
        $data['photovoltaic'] = $this->user_by_role(new User(), 'photovoltaic');
        $data['plumbing'] = $this->business_user_by_role(new User(), 'Idraulico');
        $data['fixtures'] = $this->business_user_by_role(new User(), 'Infissi');
        $data['electrician'] = $this->business_user_by_role(new User(), 'Elettricista');
        $data['construction'] = $this->business_user_by_role(new User(), 'Edile');

        return view('construction.construction-detail.construction-detail', compact('data', 'construct_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        return view('construction.construction_edit_form1', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  {{ model }}  $modal
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {


        $data = $this->_request->except('_token', '_method');

        $construction = $this->get_by_id($this->_modal, $id);

        $construction->update($data);

        $consid = ['construction_site_id' => $id];

        // dd($this->_request->all());
        $DocumentAndContactData = $this->_request->only('document_number', 'issued_by', 'release_date', 'expiration_date', 'fiscal_document_number', 'vat_number', 'contact_email', 'contact_number', 'alt_refrence_name', 'alt_contact_number');

        $construction->DocumentAndContact()->updateOrCreate($consid, $DocumentAndContactData);

        return redirect()->route('construction_detail', ['id' => $id, 'pagename' => 'Cliente'])->with('success', 'Dati cliente aggiornato');
    }

    /**
     * Update building site data
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function update_building($id)
    {
        // dd($this->_request->all());

        $data = $this->_request->except('_token', '_method');

        $construction = $this->get_by_id($this->_modal, $id);

        $construction->update($data);

        $construction_site_id = ['construction_site_id' => $id];

        $PropertyDataData = $this->_request->only('cadastral_dati', 'property_house_number', 'property_street', 'property_postal_code', 'property_common', 'property_province', 'cadastral_section', 'cadastral_category', 'cadastral_particle', 'sub_ordinate', 'Piano', 'pod_code');
        if(isset($PropertyDataData['Piano'])){
        $PianoAsString = implode(',', $PropertyDataData['Piano']);

        $PropertyDataData['Piano'] = $PianoAsString;
        }else{
            $PropertyDataData['Piano'] = null;
        }
        
        $construction->PropertyData()->updateOrCreate($construction_site_id, $PropertyDataData);

        $ConstructionSiteSettingData = $this->_request->only('type_of_property', 'type_of_construction');

        if ($this->_request->type_of_deduction != null) {
            $type_of_deduction = implode(",", $this->_request->type_of_deduction);
            $ConstructionSiteSettingData['type_of_deduction'] = $type_of_deduction;
        } else {
            $ConstructionSiteSettingData['type_of_deduction'] = null;
        }

        $type_of_deduction_values = $this->_request->type_of_deduction;

        // constrions chiled
        $ConstuctionChiledStore = new ConstuctionChiledStore;

        $ConstuctionChiledStore->add_data_into_chiled($construction, $type_of_deduction_values);
        //_

        $construction->ConstructionSiteSetting()->updateOrCreate($construction_site_id, $ConstructionSiteSettingData);

        // if ($this->_request->type_of_property ==  'Condominio') {

        //         $construction->GetConstructionSiteCondomini()->updateOrCreate(['construction_site_id' => $construction->id]);
            
        //     // if ($construction->GetConstructionCondomini != null) {

        //     //     $construction->GetConstructionCondomini->each->delete();
        //     // }
        // } else {

        //     if ($construction->ConstructionCondominiMain($id) != null) {

        //         $construction->ConstructionCondominiMain($id)->delete();
        //     }
        // }

        $ConstructionJobDetailData = $this->_request->only(
            'construction_site_id',
            'fixtures',
            'fixtures_company_price',
            'plumbing',
            'plumbing_company_price',
            'electrical',
            'electrical_installations_company_price',
            'construction',
            'construction_company1_price',
            'construction2',
            'construction_company2_price',
            'photovoltaic',
            'photovoltaic_price',
            'coordinator',
            'construction_manager',
        );

        $fixturesConJob = $construction->ConstructionJobDetail()->where('fixtures', $ConstructionJobDetailData['fixtures'])->first();

        if ($fixturesConJob == null) {
            $fixturesMail = User::where('id', $ConstructionJobDetailData['fixtures'])->pluck('email')->first();

            try {
                if ($fixturesMail != null) {
                    $to = $fixturesMail;
                    $subject = 'Impresa Infissi' . ' ' . $construction->name . ' ' . $construction->surname;
                    $data = [
                        'name' => $construction->name,
                        'email' => $fixturesMail
                    ];
                    $path = 'emails.mail-ctr-subappalto';
                    $this->email_against_missing_files($to, $subject, $data, $path);

                    // Email sent successfully
                }
            } catch (Exception $e) {
                // Handle the email sending error here
                // You can log the error, send a notification, or take any other necessary action
                // For example:
                //\Log::error('Email sending error: ' . $e->getMessage());
            }
        }

        $plumbingConJob = $construction->ConstructionJobDetail()->where('plumbing', $ConstructionJobDetailData['plumbing'])->first();

        if ($plumbingConJob == null) {
            $plumbingMail = User::where('id', $ConstructionJobDetailData['plumbing'])->pluck('email')->first();

            try {
                if ($plumbingMail != null) {
                    $to = $plumbingMail;
                    $subject = 'Impresa Idraulico' . ' ' . $construction->name . ' ' . $construction->surename;
                    $data =
                        [
                            'name' => $construction->name,
                            'email' => $plumbingMail
                        ];
                    $path = 'emails.mail-ctr-subappalto';
                    $this->email_against_missing_files($to, $subject, $data, $path);
                }
            } catch (Exception $e) {
                // Handle the email sending error here
                // You can log the error, send a notification, or take any other necessary action
                // For example:
                //\Log::error('Email sending error: ' . $e->getMessage());
            }
        }

        $electricalConJob = $construction->ConstructionJobDetail()->where('electrical', $ConstructionJobDetailData['electrical'])->first();

        if ($electricalConJob == null) {
            $electricalMail = User::where('id', $ConstructionJobDetailData['electrical'])->pluck('email')->first();

            try {
                if ($electricalMail != null) {
                    $to = $electricalMail;
                    $subject = 'Impresa Elettricista' . ' ' . $construction->name . ' ' . $construction->surename;
                    $data =
                        [
                            'name' => $construction->name,
                            'email' => $electricalMail
                        ];
                    $path = 'emails.mail-ctr-subappalto';
                    $this->email_against_missing_files($to, $subject, $data, $path);
                }
            } catch (Exception $e) {
                // Handle the email sending error here
                // You can log the error, send a notification, or take any other necessary action
                // For example:
                //\Log::error('Email sending error: ' . $e->getMessage());
            }
        }

        $constructionConJob = $construction->ConstructionJobDetail()->where('construction', $ConstructionJobDetailData['construction'])->first();

        if ($constructionConJob == null) {
            $constructionMail = User::where('id', $ConstructionJobDetailData['construction'])->pluck('email')->first();

            try {
                if ($constructionMail != null) {
                    $to = $constructionMail;
                    $subject = 'Impresa Edile' . ' ' . $construction->name . ' ' . $construction->surename;
                    $data =
                        [
                            'name' => $construction->name,
                            'email' => $constructionMail
                        ];
                    $path = 'emails.mail-ctr-subappalto';
                    $this->email_against_missing_files($to, $subject, $data, $path);
                }
            } catch (Exception $e) {
                // Handle the email sending error here
                // You can log the error, send a notification, or take any other necessary action
                // For example:
                //\Log::error('Email sending error: ' . $e->getMessage());
            }
        }

        $construction2ConJob = $construction->ConstructionJobDetail()->where('construction2', $ConstructionJobDetailData['construction2'])->first();

        if ($construction2ConJob == null) {
            $construction2Mail = User::where('id', $ConstructionJobDetailData['construction2'])->pluck('email')->first();

            try {
                if ($construction2Mail != null) {
                    $to = $construction2Mail;
                    $subject = 'Impresa Edile' . ' ' . $construction->name . ' ' . $construction->surename;
                    $data =
                        [
                            'name' => $construction->name,
                            'email' => $construction2Mail
                        ];
                    $path = 'emails.mail-ctr-subappalto';
                    $this->email_against_missing_files($to, $subject, $data, $path);
                }
            } catch (Exception $e) {
                // Handle the email sending error here
                // You can log the error, send a notification, or take any other necessary action
                // For example:
                //\Log::error('Email sending error: ' . $e->getMessage());
            }
        }

        $photovoltaicConJob = $construction->ConstructionJobDetail()->where('photovoltaic', $ConstructionJobDetailData['photovoltaic'])->first();

        if ($photovoltaicConJob == null) {
            $photovoltaicMail = User::where('id', $ConstructionJobDetailData['photovoltaic'])->pluck('email')->first();

            try {
                if ($photovoltaicMail != null) {
                    $to = $photovoltaicMail;
                    $subject = 'Fotovoltaico' . ' ' . $construction->name . ' ' . $construction->surename;
                    $data =
                        [
                            'name' => $construction->name,
                            'email' => $photovoltaicMail
                        ];
                    $path = 'emails.mail-ctr-subappalto';
                    $this->email_against_missing_files($to, $subject, $data, $path);
                }
            } catch (Exception $e) {
                // Handle the email sending error here
                // You can log the error, send a notification, or take any other necessary action
                // For example:
                //\Log::error('Email sending error: ' . $e->getMessage());
            }
        }

        $coordinatorConJob = $construction->ConstructionJobDetail()->where('coordinator', $ConstructionJobDetailData['coordinator'])->first();

        if ($coordinatorConJob == null) {
            $coordinatorMail = User::where('id', $ConstructionJobDetailData['coordinator'])->pluck('email')->first();

            try {
                if ($coordinatorMail != null) {
                    $to = $coordinatorMail;
                    $subject = 'Coordinatore' . ' ' . $construction->name . ' ' . $construction->surename;
                    $data =
                        [
                            'name' => $construction->name,
                            'email' => $coordinatorMail
                        ];
                    $path = 'emails.mail-ctr-subappalto';
                    $this->email_against_missing_files($to, $subject, $data, $path);
                }
            } catch (Exception $e) {
                // Handle the email sending error here
                // You can log the error, send a notification, or take any other necessary action
                // For example:
                //\Log::error('Email sending error: ' . $e->getMessage());
            }
        }

        $construction->ConstructionJobDetail()->updateOrCreate($construction_site_id, $ConstructionJobDetailData);

        if ($type_of_deduction_values != null) {

            try {
                if (in_array("Fotovoltaico", $type_of_deduction_values)) {
                    // welcome email sent to construction owner
                    $to = 'fotovoltaico.greengen@gmail.com';
                    $subject = '!! fotovoltaico portale GREENGEN';
                    $data = [
                        'name' => $this->_request->name,
                        'email' => $this->_request->contact_email
                    ];
                    $path = 'emails.cf2';
                    $this->email_against_missing_files($to, $subject, $data, $path);
                }
            } catch (Exception $e) {
                // Handle the email sending error here
                // You can log the error, send a notification, or take any other necessary action
                // For example:
                //\Log::error('Email sending error: ' . $e->getMessage());
            }
        }

        $this->sendMail($construction);

        return redirect()->route('construction_detail', ['id' => $id, 'pagename' => 'Cantiere'])->with('success', 'Dati immobile aggiornato.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  {{ model }} $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->destroyById($this->_modal, $id);
        return redirect()->route('home');
    }

    /**
     * Set construction archive
     */
    public function set_archive()
    {
        $data = $this->get_by_id($this->_modal, $this->_request->id);
        $data->archive = $this->_request->archive;
        $data->update();
        return redirect()->route('construction_detail', ['id' => $this->_request->id, 'pagename' => 'Cantiere']);
    }

    private function sendMail($construction)
    {
        // here we check if plumber is not null then sent email ||  if elettrico is not null || construction_email
        // $plmberemail = 'pasquale.greengen@gmail.com'; //Note=> orignal email when we upload this on server we do uncomint this.
        // $electrican_email = 'ordini.greengen@gmail.com'; //Note=> orignal email when we upload this on server we do uncomint this.
        // $photovoltaic_email = 'fotovoltaico.greengen@gmail.com'; //Note=> orignal email when we upload this on server we do uncomint this.
        $plmberemail = "waleedhaq339@gmail.com";
        $electrican_email = "waleedhaq339@gmail.com";
        $photovoltaic_email = "waleedhaq339@gmail.com";

        if ($this->_request->plumbing != null || $this->_request->electrical != null || $this->_request->construction != null) {
            $to = $plmberemail;
            $subject = 'impresa idraulico' . ' ' . $construction->name . ' ' . $construction->surename;
            $data =
                [
                    'name' => $construction->name,
                    'email' => $plmberemail
                ];
            $path = 'emails.mail-ctr-subappalto';
            $this->email_against_missing_files($to, $subject, $data, $path);
        }
        //email send here to construction owner
        else if ($this->_request->fixtures != null) {
            $to = $construction->DocumentAndContact->contact_email;
            $subject = 'Sei stato assegnato | Cantiere' . ' ' . $construction->name . ' ' . $construction->surename;
            $data =
                [
                    'name' => $construction->name,
                    'email' => $electrican_email
                ];
            $path = 'emails.ta2-infissi';
            $this->email_against_missing_files($to, $subject, $data, $path);
        } else if ($this->_request->photovoltaic != null) {
            $to = $photovoltaic_email;
            $subject = $construction->name . ' ' . $construction->surename;
            $data =
                [
                    'name' => $construction->name,
                    'email' => $photovoltaic_email
                ];

            $path = 'emails.cf2';
            $this->email_against_missing_files($to, $subject, $data, $path);
        }
    }

    /**
     * get data from relation
     */
    public function get_assistance()
    {
        // store construction id in session
        $construct_id = $this->session_get("construction_id");
        $construction_data = $this->get_by_id($this->_modal, $construct_id);
        $data = $construction_data->MaterialsAsisstance;

        $all_assistance =  view('response.get_all_assistance', compact('data'))->render();
        return response()->json([
            'data' => $all_assistance,
        ]);
    }

    /**
     * print construction material
     */
    public function print_material($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        return view('print-documents.print-material', compact('data'));
    }

    /**
     * print construction assistance
     */
    public function assistance_print($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        return view('print-documents.print-assistance', compact('data'));
    }

    public function assistance_document($id, $folder_name)
    {
        $parent = "Documenti Assistenza";
        $construct_id = $id;
        $pr_not_doc = PrNotDoc::where('construction_site_id', $id)->where('folder_name', $parent)->first();
        if ($pr_not_doc) {
            $pr_not_doc_id = $pr_not_doc->TypeOfDedectionSub1->where('folder_name', $folder_name)->first();
            if ($pr_not_doc_id) {

                return  redirect()->route('type_of_deduc_sub1', ['prnotid' => $pr_not_doc->id, 'id' => $pr_not_doc_id->id, 'docname' => $parent]);
            } else {
                $this->createAssistanceFolder($folder_name, $id);


                // $pr_not_doc_id = $pr_not_doc->TypeOfDedectionSub1->where('folder_name', $folder_name)->first();


                $TypeOfDedectionSub1 = [
                    'pr_not_doc_id' => $pr_not_doc->id,
                    'construction_site_id' => $id,
                    'allow' => 'admin,user',
                    'folder_name' => $folder_name,
                    'state' => 1,
                ];
                $folder = $pr_not_doc->TypeOfDedectionSub1()->updateOrCreate($TypeOfDedectionSub1);

                $fattura = "Fattura" . ' ' . $folder_name;
                $rapportino = "Rapportino" . ' ' . $folder_name;

                $sub_folder_1 = [
                    'type_of_dedection_sub1_id' => $folder->id,
                    'construction_site_id' => $construct_id,
                    'allow' => 'admin,user',
                    'file_name' => $fattura,
                    'bydefault' => '1',
                    'state' => 1
                ];
                $sub_folder_2 = [
                    'type_of_dedection_sub1_id' => $folder->id,
                    'construction_site_id' => $construct_id,
                    'allow' => 'admin,user',
                    'file_name' => $rapportino,
                    'bydefault' => '1',
                    'state' => 1
                ];
                $folder->TypeOfDedectionSub2()->updateOrCreate($sub_folder_1);
                $folder->TypeOfDedectionSub2()->updateOrCreate($sub_folder_2);
                return redirect()->route('type_of_deduc_sub1', ['prnotid' => $pr_not_doc->id, 'id' => $folder->id, 'docname' => $parent]);
            }
        } else {
            return back();
        }
    }

    /**
     * stampa documents
     */
    public function stampa($id)
    {


        $path = public_path() . '/assets/stampa/' . $id;

        if (File::isDirectory($path)) {

            $data = File::files($path);
        } else {
            $data = [];
        }

        return view('construction.stampa', compact('data', 'id'));
    }

    /**
     * print_construction_stampa
     */
    public function print_construction_stampa($id, $page)
    {
        $data = $this->get_by_id($this->_modal, $id);
        return view('print-documents.construction-stampa', compact('data', 'page'));
    }

    public function print_construction_stampa_material()
    {
        $construction  = $this->get_by_id($this->_modal, $this->_request->contractionId);
         
        $constructionName  = $construction['name'] . ' ' . $construction['surename'];
     
        $unitPrice = implode(', ', $this->_request->SumOfpricePerUnit);
        $SumOfpricePerUnit =  explode(',', $unitPrice);
        $otherData =  $this->_request->only('Descrizionelavorazione', 'Prezzo_per_unita', 'quantity');
        $countOtherData = count($otherData['Descrizionelavorazione']);
        $countSumOfpricePerUnit = count($SumOfpricePerUnit);
        // dd($otherData, $SumOfpricePerUnit);
        $combinedData = [];
        for ($i = 0; $i < $countOtherData; $i++) {
            $combinedData[] = [
                'Descrizionelavorazione' => $otherData['Descrizionelavorazione'][$i],
                'Prezzo_per_unita' => $otherData['Prezzo_per_unita'][$i],
                'SumOfPrezzo_per_unita' => $SumOfpricePerUnit[$i],
                'quantity' => $otherData['quantity'][$i],

            ];
        }

        $id = $this->_request->business_id;
        $data =  BusinessDetail::findorfail($id);
        
        $data['constructionName'] = $constructionName;

        $totali =  $this->_request->totali;
        $total = number_format($totali, 2);
        $page = 'material_report';
     
    //     if($this->_request->uploading == 'true'){
    //        // Load the HTML content of the view
    // $html = view('print-documents.construction-stampa-material', compact('data', 'page', 'total', 'combinedData'))->render();

    // // Create PDF
    // $pdf = new Dompdf();
    // $options = new Options();
    // $options->set('isHtml5ParserEnabled', true);
    // $pdf->setOptions($options);

    // // Load HTML content with styles and script
    // $pdf->loadHtml($html);

    // // (Optional) Set paper size and orientation
    // $pdf->setPaper('A4', 'portrait');

    // // Render the PDF
    // $pdf->render();

    // // Output the generated PDF (download)
    // return $pdf->stream('document.pdf');
    //     }else{
    //         return view('print-documents.construction-stampa-material', compact('data', 'page', 'total', 'combinedData'));
    //     }
       

    return view('print-documents.construction-stampa-material', compact('data', 'page', 'total', 'combinedData'));
    }

    // view essential
    public function essential($id = null)
    {
        $construct_id =  isset($id) ? $id : $this->session_get("construction_id");

        $var = $this->get_by_id($this->_modal, $construct_id);

        $check_deduction = explode(",", $var->ConstructionSiteSetting->type_of_deduction);
        // dd($check_deduction);
        if (count($check_deduction) != 0) {
            $data110 = [];
            $common_for_50_65_90 = [];
            // dd($check_deduction == "110");
            if (in_array("110", $check_deduction)) {
                $data110 = [];
                $data110['RegPracDoc_for110'] = RegPracDoc::where('construction_site_id', $construct_id)->whereIn('file_name', ['Cilas Protocollata 110', 'Protocollo Cilas 110'])->get();
                // get PrNotDoc id
                $data110['pr_not_doc_id'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->first();

                $data110['deduction1_for110'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->where('folder_name', 'Documenti SALDO 110')->first();
            } else {
                $data110 = [];
            }

            // common for 50 65 and 90
            if (in_array("50", $check_deduction) || in_array("65", $check_deduction) || in_array("90", $check_deduction)) {

                $common_for_50_65_90['Common_RegPracDoc'] = RegPracDoc::where('construction_site_id', $construct_id)->whereIn('file_name', ['Cila Protocollata 50-65-90', 'Protocollo cila 50-65-90'])->get();
            } else {
                $common_for_50_65_90 = [];
            }
        }
        // store construction id in session
        $prenoti_doc_sub2 = $this->get_by_id(new ConstructionSite, $construct_id);
        // get by file
        $data['RelDocFile'] = RelDocFile::where('construction_site_id', $construct_id)->whereIn('file_name', [
            'Cilas Protocollata 110',
            'Protocollo cilas 110',
            'Cila protocollata 50-65-90',
            'Protocollo cila 50-65-90',
            'Atto di Provenienza',
            'Sopralluogo fine lavori',
            'Visura Catastale',
            "Carta D'identità",
            'Bolletta Luce',
            'Codice Fiscale',
            'Estratto di Mappa',
            // 'Carta D identità co-intestatario',
            'Carta d identità intestatario bollette',
            'ANTONACCI IMMACOLATA relazione fotovoltaico',
            'Notifica Preliminare'
        ])->get();

        // dd($data['RelDocFile']);
        $data['Legge10'] = RelDocFile::where('construction_site_id', $construct_id)->where('file_name', 'Legge 10')->first();


        // dd($data['Notifica']);

        //count for saldo

        $uniqueFileNames = [];

        $countPrNotDoc = PrNotDoc::where('construction_site_id', $construct_id)
            ->whereIn('folder_name', ['Dico', 'Contratto Di Subappalto Impresa'])
            ->get();

        $uniqueFileNames = array_merge(
            $uniqueFileNames,
            $countPrNotDoc->flatMap(function ($prNotDoc) {
                return $prNotDoc->PrNotDocFile;
            })->where('updated_by', '!=', null)->where('state', 1  || 'saldo')->pluck('file_name')->toArray()
        );

        $TypeOfDedectionSub2 = TypeOfDedectionSub2::where('construction_site_id', $construct_id)
            ->whereIn('folder_name', ['Computo SALDO 110', 'Notifica SALDO', 'Formulario rifiuti'])
            ->get();

        $uniqueFileNames = array_merge(
            $uniqueFileNames,
            $TypeOfDedectionSub2->flatMap(function ($prNotDoc) {
                return $prNotDoc->TypeOfDedectionFiles;
            })->where('updated_by', '!=', null)->where('state', 1 || 'saldo')->pluck('file_name')->toArray()
        );

        $uniqueFileNames = array_merge(
            $uniqueFileNames,
            RelDocFile::where('construction_site_id', $construct_id)
                ->whereIn('file_name', ['Legge 10 SALDO',])
                ->where('updated_by', '!=', null)
                ->where('state', 1 || 'saldo')
                ->pluck('file_name')->toArray()
        );

        $count = count(array_unique($uniqueFileNames));


        return view('essential', compact('data', 'data110', 'common_for_50_65_90', 'construct_id', 'count'));
    }

    // view essential
    public function essentialSaldo($consId =  null)
    {
        if ($consId != null) {
            $construct_id = $consId;
        } else {
            $construct_id = $this->session_get("construction_id");
        }
        //----------------------------------------------- document 110
        $var =  $this->get_by_id($this->_modal, $construct_id);

        // $var = $this->get_by_id($this->_modal, $construct_id);
        $data110 = [];
        $data110['PrNotDoc'] = PrNotDoc::where('construction_site_id', $construct_id)->whereIn('folder_name', ['Dico', 'Contratto Di Subappalto Impresa'])->get();
        // get PrNotDoc id
        // $data110['TypeOfDedectionSub2'] = TypeOfDedectionSub2::where('construction_site_id', $construct_id)->whereIn('folder_name', ['Computo SALDO 110'])->get();


        //    dd( $data110['TypeOfDedectionSub2'] = TypeOfDedectionSub2::where('construction_site_id', $construct_id)->where('folder_name', 'Computo SALDO 50')->first());

        $RelDocFile['RelDocFile'] = RelDocFile::where('construction_site_id', $construct_id)->whereIn(
            'file_name',
            [
                'Legge 10 SALDO',
                'Formulario rifiuti',
                'Notifica SALDO'
            ]
        )->get();


        $check_deduction = explode(",", $var->ConstructionSiteSetting->type_of_deduction);

        $data110['TypeOfDedectionSub2'] = [];

        if (count($check_deduction) != 0) {
            $folders = [];
            $data110['TypeOfDedectionSub2'] = [];

            if (in_array("110", $check_deduction)) {
                $folders[] = 'Computo SALDO 110';
            }

            if (in_array("50", $check_deduction)) {
                $folders[] = 'Computo SALDO 50';
            }

            if (in_array("65", $check_deduction)) {
                $folders[] = 'Computo SALDO 65';
            }


            if (!empty($folders)) {
                $data110['TypeOfDedectionSub2'] = TypeOfDedectionSub2::where('construction_site_id', $construct_id)
                    ->whereIn('folder_name', $folders)
                    ->get();
            }
        }
        
        return view('essentialSaldo', compact('RelDocFile', 'data110', 'construct_id'));
    }



    // chiavetta
    public function chiavetta($id = null)
    {

        if ($id == null) {
            $construct_id = $this->session_get("construction_id");
        } else {
            $construct_id = $id;
        }

        $data['RelDocFile'] = RelDocFile::where('construction_site_id', $construct_id)->whereIn(
            'file_name',
            [
                'Cilas Protocollata 110',
                'Protocollo cilas 110',
                'Cila protocollata 50-65-90',
                'Protocollo cila 50-65-90', 'Carta D identità co-intestatario', "Carta D'identità", 'Visura Catastale', 'Libretto impianti ante', 'Libretto impianti post', 'Codici accesso portale', 'Contratto GSE', 'Estensione garanzia FTV'
            ]
        )->get();

        $data['Legge10'] = RelDocFile::where('construction_site_id', $construct_id)->whereIn('file_name', ['Legge 10', 'Ape regionale', 'Ricevuta Ape Regione'])->get();

        $data['PrNotDocFile'] = PrNotDocFile::where('construction_site_id', $construct_id)->whereIn(
            'file_name',
            ['DICO Impianto elettrico', 'DICO Impianto idrico-fognante', 'DICO Impianto termico']
        )->get();
        // here we check if type of deduction is 110,90,65,50 or fotovoltaic
        //----------------------------------------------- document 110
        $var = $this->get_by_id($this->_modal, $construct_id);

        $check_deduction = explode(",", $var->ConstructionSiteSetting->type_of_deduction);

        if (count($check_deduction) != 0) {
            $data110 = [];
            $data90 = [];
            $data65 = [];
            $data50 = [];
            $common_for_50_65_90 = [];
            $common_schemi = [];
            $common_schemi_fotovoltaic = [];

            foreach ($check_deduction as $check_deduction) {
                // dd($check_deduction == "110");
                if ($check_deduction == "110") {
                    $data110 = [];
                    // get PrNotDoc id
                    $data110['pr_not_doc_id'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->first();

                    //
                    // $deduction1_for110_1 = TypeOfDedectionSub1::where('construction_site_id', $construct_id)
                    // ->whereIn('folder_name', [
                    //     'Ricevuta Di Invio Ade Sal 110',
                    //     'Ricevuta Di Invio Ade SALDO 110',
                    //     'Visto Di Conformita Firmato Sal 110',
                    //     'Visto Di Conformita Firmato SALDO 110'
                    // ])
                    // ->get();

                    $data110['deduction1_for110'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)
                        ->whereIn('file_name', [
                            'Contratto 110'
                        ])
                        ->get();



                    $data110['TypeOfDedectionFiles'] = TypeOfDedectionFiles::where('construction_site_id', $construct_id)
                        ->whereIn('folder_name', [
                            'Ricevuta Di Invio Ade Sal 110',
                            'Ricevuta Di Invio Ade SALDO 110',
                            'Visto Di Conformita Firmato Sal 110',
                            'Visto Di Conformita Firmato SALDO 110'
                        ])
                        ->get();
                    // dd($data110['TypeOfDedectionFiles']);

                    // $data110['deduction1_for110'] = $deduction1_for110_1->merge($deduction1_for110_2);



                    $data110['deduction2_for110'] =  TypeOfDedectionSub2::where('construction_site_id', $construct_id)->whereIn(
                        'folder_name',
                        [
                            'Asseverazione SAL 110',
                            'Asseverazione SALDO 110',
                            'Computo SALDO 110',
                            'Fattura SAL 110',
                            'Fattura SALDO 110',


                        ]

                    )->get();

                    $data110['RegPracDoc_for110'] = RegPracDoc::where('construction_site_id', $construct_id)->whereIn('file_name', ['Cilas Protocollata 110', 'Protocollo cilas 110'])->get();
                    // dd( $data['deduction1_for110']);
                }

                if ($check_deduction == "90") {
                    $data90 = [];
                    $data90['pr_not_doc_id'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->first();

                    $data90['deduction1_for90'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->where('folder_name', 'Fattura SAL 90')->first();

                    $data90['deduction2_for90'] =  TypeOfDedectionSub2::where('construction_site_id', $construct_id)->whereIn(
                        'folder_name',
                        [
                            'Asseverazione SAL 90',
                            'Asseverazione SALDO 90',
                            'Fattura SAL 90',
                            'Fattura SALDO 90'
                        ]
                    )->get();
                }
                if ($check_deduction == "65") {
                    $data65 = [];
                    // get PrNotDoc id
                    $data65['pr_not_doc_id'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->first();

                    $data65['deduction1_for65'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->where('folder_name', 'Fattura SAL 65')->first();

                    $data65['deduction2_for65'] =  TypeOfDedectionSub2::where('construction_site_id', $construct_id)->whereIn(
                        'folder_name',
                        [
                            'Asseverazione SAL 65',
                            'Asseverazione SALDO 65',
                            'Fattura SALDO 65',
                            'Fattura SAL 65'

                        ]
                    )->get();
                }
                if ($check_deduction == "50") {
                    $data50 = [];
                    // get PrNotDoc id
                    $data50['pr_not_doc_id'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->first();

                    $data50['deduction1_for50'] = TypeOfDedectionSub1::where('construction_site_id', $construct_id)->whereIn(
                        'folder_name',
                        [
                            'Fattura SAL 50',
                        ]
                    )
                        // ->orwhere('file_name', 'Contract 50')->get();
                        ->get();
                    // $data50['deduction2_for50'] =  TypeOfDedectionSub2::where('construction_site_id', $construct_id)->whereIn(
                    //     'folder_name',
                    //     [

                    //         'Asseverazione SAL 50',
                    //         'Asseverazione SALDO 50',
                    //         'Computo SALDO 50',
                    //         'Fattura SALDO 50',
                    //         'Contratto 50'
                    //     ]
                    // )->get();
                    $deduction2_for50_1 = TypeOfDedectionSub2::where('construction_site_id', $construct_id)
                        ->whereIn('folder_name', [
                            'Asseverazione SAL 50',
                            'Asseverazione SALDO 50',
                            'Computo SALDO 50',
                            'Fattura SALDO 50',
                        ])
                        ->get();

                    $deduction2_for50_2 = TypeOfDedectionSub2::where('construction_site_id', $construct_id)
                        ->whereIn('file_name', [
                            'Contratto 50'
                        ])
                        ->get();

                    $data50['deduction2_for50'] = $deduction2_for50_1->merge($deduction2_for50_2);
                }
                // common for 50 65 and 90
                if ($check_deduction == "50" || $check_deduction == "65" || $check_deduction == "90") {
                    $common_for_50_65_90 = [];
                    $common_for_50_65_90['Common_RegPracDoc'] = RegPracDoc::where('construction_site_id', $construct_id)->whereIn('file_name', ['Cila Protocollata 50-65-90', 'Protocollo Cila 50-65-90'])->get();
                }
                // common folder for 110 65 and 50

                if ($check_deduction == "Fotovoltaico" || $check_deduction == "fotovoltaico") {

                    $common_schemi_fotovoltaic = [];
                    $common_schemi_fotovoltaic['Schemi'] = ReliefDoc::where('construction_site_id', $construct_id)->where('folder_name', 'Schemi Impianti')->first();
                }
                if ($check_deduction != null) {
                    $common_schemi = [];
                    $common_schemi['Schemi'] = ReliefDoc::where('construction_site_id', $construct_id)->where('folder_name', 'Schemi Impianti')->first();
                }
            }
        } else {
            $data110 = [];
            $data90 = [];
            $data65 = [];
            $data50 = [];
            $common_for_50_65_90 = [];
            $common_schemi = [];
            $common_schemi_fotovoltaic = [];
        }

        return view('chiavetta', compact('data', 'data110', 'data90', 'data65', 'data50', 'common_for_50_65_90', 'common_schemi', 'common_schemi_fotovoltaic', 'construct_id'));
    }

    private function return_doc_Fotovoltaico($construct_id)
    {
        $data['Fotovoltaico'] = 'return_doc_Fotovoltaico';
        return $data;
    }

    public function zip_chiavetta_or_essential($slug)
    {
        // dd("download here");
        $folder_name = $slug;
        $path = $slug;

        // ---------------------------//
        return $this->download_zip_folder($folder_name, $path);
    }

    public function construction_pin_location()
    {
        $fk_const = $this->_request->fk_const;

        $pin = $this->get_by_id($this->_modal, $fk_const);
        $pin->update(['pin_location' => $this->_request->pin_location]);

        return redirect()->back()->with('success', 'Posizione del segnaposto inviata');
    }

    public function sendInviaEmail()
    {
        $data = $this->_request->except('_token', 'method');

       
        $emails = explode(',', $data['email']);
        $invalidEmails = [];
    
        foreach ($emails as $email) {
            // Trim whitespace and validate email
            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($invalidEmails, $email);
            }
        }
    
        if (!empty($invalidEmails)) {
            return redirect()->back()->with('error', 'Invalid emails: ' . implode(', ', $invalidEmails));
        }
    
        foreach ($emails as $email) {
            $data['email'] = $email;
            $this->InviaEmail($data['email'], $data['subject'], 'Invia Email', $data['msg']);
        }
    
        return redirect()->back()->with('success', 'Email inviata');
    }

    public function all_constructions()
    {
        return view('reports');
    }


    public function doc_fotovoltaico($id)
    {
        $construct_id =  isset($id) ? $id : $this->session_get("construction_id");
        $var = $this->get_by_id($this->_modal, $construct_id);

        $prenoti_doc['prenoti_doc'] = $this->get_by_id($this->_modal, $id);
        $prenoti_doc['prenoti_and_relif'] = $this->get_by_column(new ReliefDoc, 'construction_site_id', $construct_id);

        $data['Files'] = TypeOfDedectionSub2::where('construction_site_id', $construct_id)->whereIn('file_name', ['Dichiarazione Sostitutiva Atto Di Notorietà 110', 'Dichiarazione Sostitutiva Atto Di Notorietà 65', 'Dichiarazione Sostitutiva Atto Di Notorietà 90', 'Dichiarazione Sostitutiva Atto Di Notorietà 50'])->get();

        return view('doc_fotovoltaico', compact('construct_id', 'var', 'prenoti_doc', 'data'));
    }

    public function doc_chiavetta($id)
    {
        $construct_id =  isset($id) ? $id : $this->session_get("construction_id");
        $var = $this->get_by_id(new ConstructionSite, $construct_id);

        $prenoti_doc['prenoti_doc'] = $this->get_by_id($this->_modal, $id);
        $prenoti_doc['prenoti_and_relif'] = $this->get_by_column(new ReliefDoc, 'construction_site_id', $construct_id);

        $data['Folders'] = ChiavettaDoc::where('state', 1)->get();
        $data['Files'] = RelDocFile::where('construction_site_id', $construct_id)->where('file_name', 'Ape Regionale')->get();
        //dd($data['Files']);

        return view('doc_chiavetta', compact('construct_id', 'var', 'prenoti_doc', 'data'));
    }

    public function doc_commercialista($id)
    {
        $construct_id =  isset($id) ? $id : $this->session_get("construction_id");
        $var = $this->get_by_id($this->_modal, $construct_id);

        $prenoti_doc['prenoti_doc'] = $this->get_by_id($this->_modal, $id);
        $prenoti_doc['prenoti_and_relif'] = $this->get_by_column(new ReliefDoc, 'construction_site_id', $construct_id);

        return view('doc_commercialista', compact('construct_id', 'var', 'prenoti_doc'));
    }

    public function doc_tecnico($id)
    {
        $construct_id =  isset($id) ? $id : $this->session_get("construction_id");
        $var = $this->get_by_id($this->_modal, $construct_id);

        $prenoti_doc['prenoti_doc'] = $this->get_by_id($this->_modal, $id);
        $prenoti_doc['prenoti_and_relif'] = $this->get_by_column(new ReliefDoc, 'construction_site_id', $construct_id);

        return view('doc_tecnico', compact('construct_id', 'var', 'prenoti_doc'));
    }

    public function zip_doc_files($slug)
    {
        // dd("download here");
        $folder_name = $slug;
        $path = $slug;

        // ---------------------------//
        return $this->download_zip_folder($folder_name, $path);
    }
}

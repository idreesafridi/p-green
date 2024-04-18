<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Date;
use App\Models\Note;
use App\Models\User;
use App\Models\Timer;
use GuzzleHttp\Client;
use App\Models\Cliente;
use App\Models\Cantiere;
use App\Models\Checkbox;
use App\Models\PrNotDoc;
use App\Models\Condomini;
use App\Models\Materials;
use App\Models\ReliefDoc;
use App\Models\StatusSAL;
use App\Models\Assistenze;
use App\Models\RelDocFile;
use App\Models\StatusLeg10;
use App\Models\MaterialList;
use App\Models\MaterialType;
use App\Models\PropertyData;
use App\Models\StatusPrNoti;
use App\Models\StatusRelief;
use App\Models\systemStatus;
use Illuminate\Http\Request;
use App\Models\statusRegPrac;
use App\Models\MaterialOption;
use App\Models\StatoUpdatedby;
use App\Models\StatusWorkClose;
use App\Models\AssegnaMateriali;
use App\Models\ConstructionSite;
use App\Models\StatusTechnician;
use App\Models\ConstructionNotes;
use App\Models\StatusComputation;
use App\Models\StatusEneaBalance;
use App\Models\StatusPreAnalysis;
use App\Models\StatusWorkStarted;
use App\Models\DocumentAndContact;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\MaterialsAsisstance;
use App\Models\TypeOfDedectionSub1;
use App\Http\Controllers\Controller;
use App\Models\ConstructionLocation;
use App\Models\ConstructionMaterial;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;


use App\Models\ConstructionCondomini;
use App\Models\ConstructionJobDetail;
use Symfony\Component\Process\Process;
use App\Models\ConstructionSiteSetting;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Response;
use App\Models\ConstructionMissingColumn;
use Symfony\Component\Process\Exception\ProcessFailedException;



class ImportController extends Controller
{
    private $_request = null;

    private $_directory = '';

    /**
     * Create a new controller instance.
     *
     * @return $reauest,
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    public function noteScript()
    {
        // $values = $this->get_google_sheet_api('construction_notes!B3:E711');

        // $values = $this->get_google_sheet_api('construction_notes!B3:E');
        $values = Note::get();

        // Iterate through the rows and insert the data into the database
        foreach ($values as $row) {

            $id = ConstructionSite::where('oldid', $row->fk_clienteid)->first();

            $user_id = null;
            if (isset($row->publishedby)) {
                if ($row->publishedby != "NULL" || $row->publishedby != null) {
                    $user_id = User::where('id', $row->publishedby)->pluck('id')->first();
                }
            }

            // $this->checkConstruction($id, $row[0]);

            if ($id != null) {
                $constructionArr = array(
                    'construction_site_id' => $id == null ? null : $id->id,
                    'notes' => $row->note != null ? $row->note : null,
                    'priority' => $row->pin != null ? $row->pin : 0,
                    'admin_id' => $user_id,
                );

                ConstructionNotes::create($constructionArr);
            }
        }

        return true;
    }

    public function ConstructionMaterialScript()
    {
        $material = AssegnaMateriali::all();
        // dd($material);
        foreach ($material as $r) {

            $find = MaterialList::where('name', $r->Materiale)->first();

            $cliente = Cliente::where('clienteId', $r->fk_cantiere)->pluck('clienteId')->first();
            // dd($cliente);
            if ($cliente) {
                //     'id' => $r->id_assegnamento,
                //     'construction_site_id' => $cantiere == null ? null : $cantiere,
                //     'material_list_id' => $find->id,
                //     'quantity' => $r->quantita,
                //     'state' => $r->Stato,
                //     'consegnato' => $r->montato2,
                //     'avvio' => $r->avvio,
                //     'note' => $r->Nota,
                //     'montato' => $r->montato,
                //     'updated_by' => $r->updatedlastby
                // ];

                // ConstructionMaterial::create($matArr);
                $constructionSite = ConstructionSite::Where('oldid', $cliente)->first();
                if ($constructionSite) {
                    $updatedlastby = User::find($r->updatedlastby);
                    $updatedlastbyid = $updatedlastby ? $r->updatedlastby : null;
                    $new = new ConstructionMaterial();
                    //                $new->id = $r->id_assegnamento;
                    //                $new->construction_site_id = $cantiere == null ? null : $cantiere;
                    $new->construction_site_id = $constructionSite->id;
                    $new->material_list_id = $find == null ? null : $find->id;
                    $new->quantity = $r->quantita;
                    $new->state = $r->Stato;
                    $new->consegnato = $r->montato2;
                    $new->avvio = $r->avvio;
                    $new->note = $r->Nota;
                    $new->montato = $r->montato;
                    $new->updated_by = $updatedlastbyid;
                    $new->save();
                } else {
                }
            } else {
                //                dd('ni mila');
            }
        }

        return 'data completed';
    }

    public function assistenzeScript()
    {
        $assistenze = Assistenze::all();

        foreach ($assistenze as $r) {
            $cliente = Cliente::where('clienteId', $r->fk_cantiere)->pluck('clienteId')->first();
            // $cantiere = Cantiere::where('cantiereId', $r->fk_cantiere)->pluck('cantiereId')->first();

            if ($cliente != null) {
                // $constructionId = Cantiere::where('cantiereId', $r->fk_cantiere)->first();

                $constructionId = ConstructionSite::with('ConstructionMaterial.MaterialList')->where('oldid', $r->fk_cantiere)->first();

                if ($constructionId) {
                    $constructionIds = $constructionId->id;
                    $construction_material_id = $constructionId->ConstructionMaterial;
                    $cons_m_id = null;
                    foreach ($construction_material_id as $construction_material) {
                        if ($construction_material->MaterialList != null) {
                            if ($construction_material->MaterialList->name == $r->modello) {
                                $cons_m_id = $construction_material->id;
                            }
                        }
                    }
                    // dd($construction_material_id);
                } else {
                    $constructionIds = null;
                    // $cons_m_id = null;

                }


                $assis = new MaterialsAsisstance();

                $assis->construction_material_id = $cons_m_id;
                $assis->construction_site_id = $constructionIds;
                $assis->machine_model = $r->modello != null ? $r->modello : null;
                $assis->freshman = $r->matricola != null ? $r->matricola : null;
                $assis->expiry_date = $r->data != null ? ($r->data == "0000-00-00" ? null : $r->data) : null;
                $assis->notes = $r->nota != null ? $r->nota : null;
                $assis->state = $r->stato != null ? $r->stato : 0;
                $assis->save();
            }
        }

        return 'data completed';
    }


    public function MaterialList()
    {
        $materials = Materials::all();
        $id = 1;
        foreach ($materials as $m) {
            $name = $m->nome_materiale;
            $option = $m->tipo_materiale;
            $type = $m->sotto_tipo;
            $unit = $m->unita_misura;
            $user = $m->created_by;

            $findType = MaterialType::Where('name', $type)->first();
            if ($findType) {
                $typeId = $findType->id;
            } else {

                if ($option) {
                    $materialOption = MaterialOption::where('name', $option)->first();
                    $optionId = $materialOption->id;
                    $materialType = new MaterialType();
                    $materialType->name = $type;
                    $materialType->material_option_id = $optionId;
                    $materialType->save();
                } else {
                }
                //                echo $type.'------'.$option."<br />";
            }


            $materialList = new MaterialList();
            // $materialList->id = $id;
            $materialList->material_type_id = $typeId;
            $materialList->user_id = $user;
            $materialList->name = $name;
            $materialList->unit = $unit;
            $materialList->save();

            $id++;
        }

        return 'Data inserted';
    }


    public function constructionSites()
    {
        $clients = Cliente::all();

        if (count($clients) > 0) {

            $sr = 1;
            $doc = 1;
            foreach ($clients as $client) {
                $client_id = $client->clienteId;
                $primaryId = $sr++;

                $constructionSite = ConstructionSite::where('oldid', $client_id)->first();
                if ($constructionSite) {
                    $consId = $constructionSite->id;
                } else {
                    $consId = null;
                }

                $constructionSite = ConstructionSite::find($consId);

                if ($constructionSite) {


                    $consArr = [
                        // 'id' => $client_id,
                        'oldid' => $client_id,
                        'name' => $client->nome == null ? '' : $client->nome,
                        'surename' => $client->cognome == null ? '' : $client->cognome,
                        'date_of_birth' => $client->dataNascita == null ? '' : $client->dataNascita,
                        'town_of_birth' => $client->comuneNascita == null ? '' : $client->comuneNascita,
                        'province' => $client->provinciaNascita == null ? '' : $client->provinciaNascita,
                        'residence_street' => $client->viaResid == null ? '' : $client->viaResid,
                        'residence_house_number' => $client->numResid == null ? '' : $client->numResid,
                        'residence_postal_code' => $client->capResid == null ? '' : $client->capResid,
                        'residence_common' => $client->comuneResid == null ? '' : $client->comuneResid,
                        'residence_province' => $client->provinciaResid == null ? '' : $client->provinciaResid,
                    ];

                    $cantiere = Cantiere::where('FK_cliente', $client_id)->first();

                    if ($cantiere != null) {
                        // echo $primaryId . ' if: ' . $client_id . '</br>';

                        // if ($cantiere->archivia == null) {
                        //     $archive = 1;
                        // } else {
                        //     $archive = $cantiere->archivia;
                        // }

                        $consArr['archive'] = $cantiere->archivia;
                        $consArr['latest_status'] = $cantiere->stato == null ? '' : $cantiere->stato;
                        $consArr['page_status'] = 4;

                        // $consInsertElse = new ConstructionSite();
                        // $consInsertElse->create($consArr);

                        // document and contacts
                        $DocumentAndContactArr = [
                            // 'id' => $primaryId,
                            'construction_site_id' => $consId,
                            'document_number' => $client->numDoc == null ? '' : $client->numDoc,
                            'issued_by' => $client->rilascioDoc == null ? '' : $client->rilascioDoc,
                            'release_date' => $client->dataDoc == null ? '' : $client->dataDoc,
                            'expiration_date' => $client->scadenzaDoc == null ? '' : $client->scadenzaDoc,
                            'fiscal_document_number' => $client->cf == null ? '' : $client->cf,
                            'vat_number' => $client->iva == null ? '' : $client->iva,
                            'contact_email' => $client->email == null ? '' : $client->email,
                            'contact_number' => $client->telefono_mobile == null ? '' : $client->telefono_mobile,
                            'alt_refrence_name' => $cantiere->rif_contatto == null ? '' : $cantiere->rif_contatto,
                            'alt_contact_number' => $client->telefono_fisso == null ? '' : $client->telefono_fisso,
                        ];
                        // $DocumentAndContactInsertElse = new DocumentAndContact();
                        // $DocumentAndContactInsertElse->create($DocumentAndContactArr);

                        $DocumentAndContact = DocumentAndContact::firstOrCreate(['id' => $primaryId], $DocumentAndContactArr);

                        if ($DocumentAndContact->wasRecentlyCreated) {

                            echo 'Record added: ' . $DocumentAndContact->id; // Add other attributes you want to display
                        }

                        // property_data
                        $PropertyDataArr = [
                            // 'id' => $primaryId,
                            'construction_site_id' => $consId,
                            'property_street' => $cantiere->viaImm == null ? '' : $cantiere->viaImm,
                            'property_house_number' => $cantiere->numImm == null ? '' : $cantiere->numImm,
                            'property_common' => $cantiere->comuneImm == null ? '' : $cantiere->comuneImm,
                            'property_postal_code' => $cantiere->capImm == null ? '' : $cantiere->capImm,
                            'property_province' => $cantiere->provinciaImm == null ? '' : $cantiere->provinciaImm,
                            'cadastral_dati' => $cantiere->dati_catasto == null ? '' : $cantiere->dati_catasto,
                            'cadastral_section' => $cantiere->foglioImm == null ? '' : $cantiere->foglioImm,
                            'cadastral_particle' => $cantiere->partImm == null ? '' : $cantiere->partImm,
                            'sub_ordinate' => $cantiere->subImm == null ? '' : $cantiere->subImm,
                            'pod_code' => $cantiere->pod == null ? '' : $cantiere->pod,
                            'cadastral_category' => $cantiere->catc == null ? '' : $cantiere->catc,
                        ];

                        $PropertyData = PropertyData::firstOrCreate(['id' => $primaryId], $PropertyDataArr);

                        if ($PropertyData->wasRecentlyCreated) {

                            echo 'Record added: ' . $PropertyData->id; // Add other attributes you want to display
                        }

                        // $PropertyDataInsertElse = new PropertyData();
                        // $PropertyDataInsertElse->create($PropertyDataArr);


                        // construction_site_settings
                        $ConstructionSiteSettingArr = [
                            // 'id' => $primaryId,
                            'construction_site_id' => $consId,
                            'type_of_property' => $cantiere->tipologia == null ? '' : $cantiere->tipologia,
                        ];

                        $ConstructionSiteSettingArr['type_of_construction'] = $cantiere->esterni;

                        if ($cantiere['110'] == 1) {
                            $ConstructionSiteSettingArr['oneten'] = '110';
                        } else {
                            $ConstructionSiteSettingArr['oneten'] = '';
                        }

                        if ($cantiere['90'] == 1) {
                            $ConstructionSiteSettingArr['nineT'] = '90';
                        } else {
                            $ConstructionSiteSettingArr['nineT'] = '';
                        }

                        if ($cantiere['65'] == 1) {
                            $ConstructionSiteSettingArr['sixF'] = '65';
                        } else {
                            $ConstructionSiteSettingArr['sixF'] = '';
                        }

                        if ($cantiere['50'] == 1) {
                            $ConstructionSiteSettingArr['fiftyF'] = '50';
                        } else {
                            $ConstructionSiteSettingArr['fiftyF'] = '';
                        }

                        if ($cantiere['fotovoltaico'] == 1) {
                            $ConstructionSiteSettingArr['fotovoltaico'] = 'fotovoltaico';
                        } else {
                            $ConstructionSiteSettingArr['fotovoltaico'] = '';
                        }

                        // $ConstructionSiteSettingInsertElse = new ConstructionSiteSetting();
                        // $ConstructionSiteSettingInsertElse->create($ConstructionSiteSettingArr);

                        echo $consArr['archive'] . " and " . $consArr['latest_status'] . '</br>';
                    } else {
                        // echo $sr++ . ' else: ' . $client_id . '</br>';

                        // $consArr['page_status'] = 1;

                        // $consInsertElse = new ConstructionSite();
                        // $consInsertElse->create($consArr);

                        // $consId = ['id' => $primaryId, 'construction_site_id' => $consId];

                        // $DocumentAndContactInsertElse = new DocumentAndContact();
                        // $DocumentAndContactInsertElse->create($consId);

                        $DocumentAndContact = DocumentAndContact::firstOrCreate(['id' => $primaryId], [
                            'construction_site_id' => $consId
                        ]);

                        if ($DocumentAndContact->wasRecentlyCreated) {

                            echo 'Record added: ' . $DocumentAndContact->id; // Add other attributes you want to display
                        }


                        // $PropertyDataInsertElse = new PropertyData();
                        // $PropertyDataInsertElse->create($consId);


                        $PropertyData = PropertyData::firstOrCreate(['id' => $primaryId], [
                            'construction_site_id' => $consId
                        ]);

                        if ($PropertyData->wasRecentlyCreated) {

                            echo 'Record added: ' . $PropertyData->id; // Add other attributes you want to display
                        }

                        // $ConstructionSiteSettingInsertElse = new ConstructionSiteSetting();
                        // $ConstructionSiteSettingInsertElse->create($consId);
                    }
                } else {
                    throw new \Exception('Construction site with ID ' . $consId . ' does not exist.');
                }
            }
        }
    }


    public function addSaldoFile()
    {
        // Get unique construction_site_ids and relief_doc_ids for 'Diagnosi Energetica'
        // $uniqueConstructionSiteIds = RelDocFile::where('ref_folder_name', 'Diagnosi Energetica')
        //     ->pluck('construction_site_id')
        //     ->unique()
        //     ->toArray();

        $uniqueReliefDocIds = RelDocFile::where('ref_folder_name', 'Diagnosi Energetica')
            ->pluck('relief_doc_id')
            ->unique()
            ->toArray();


        $construction_site_id = 0;
        foreach ($uniqueReliefDocIds as $relief_doc_id) {
            $id = ++$construction_site_id;

            RelDocFile::updateOrCreate(
                [
                    'construction_site_id' => $id,
                    'relief_doc_id' => $relief_doc_id,
                    'ref_folder_name' => 'Diagnosi Energetica',
                    'file_name' => 'Notifica SALDO',
                ],
                [
                    'bydefault' => 1,
                    'state' => 'saldo',
                ]
            );

            RelDocFile::updateOrCreate(
                [
                    'construction_site_id' => $id,
                    'relief_doc_id' => $relief_doc_id,
                    'ref_folder_name' => 'Diagnosi Energetica',
                    'file_name' => 'Formulario rifiuti',
                ],
                [
                    'bydefault' => 1,
                    'state' => 'saldo',
                ]
            );
        }


        return true;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function importDataToCunstructionCondomeni()
    {

        $filePath = public_path('data/data.csv'); // Replace 'filename.csv' with the actual name of your CSV file

        if (file_exists($filePath)) {
            $file = fopen($filePath, "r");

            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);
                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);
            // dd($importData_arr);
            foreach ($importData_arr as $importData) {

                $Fk_cliente_first = $importData[0];
                // dd($Fk_cliente);
                if ($importData[0] != null) {
                    $child = $importData[5];
                    $childArray = explode('|', $child);
                }

                $dataCentritable = Cantiere::where('cantiereId', $Fk_cliente_first)->first();


                if ($dataCentritable != null) {
                    $Fk_cliente = $dataCentritable->FK_cliente;
                    $constructionSite = ConstructionSite::where('oldid', $Fk_cliente)->first();

                    if ($constructionSite != null) {
                        $storeParent = new ConstructionCondomini();
                        $storeParent->construction_site_id = $constructionSite->id;
                        $storeParent->save();

                        //store child
                        $counter = 0;  // Initialize the counter variable
                        foreach ($childArray as $child) {
                            $dataCentritable = Cantiere::where('cantiereId', $child)->first();
                            if ($dataCentritable != null) {
                                $dataCentritableFk = $dataCentritable->FK_cliente;

                                if ($dataCentritableFk != null) {
                                    $constuction = ConstructionSite::where('oldid', $dataCentritableFk)->first();
                                    if ($constuction != null) {

                                        $ConstructionCondomini = $storeParent;
                                        $counter++;
                                        if ($counter === 1) {
                                            $ConstructionCondomini->construction_assigned_id = $constuction->id;
                                            $ConstructionCondomini->update();
                                        } else {
                                            $ConstructionCondomini = new ConstructionCondomini();
                                            $ConstructionCondomini->construction_assigned_id = $constuction->id;
                                            $ConstructionCondomini->construction_site_id = $constructionSite->id;
                                            $ConstructionCondomini->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            dd('Dati sono stati caricati con successo!');
        } else {
            dd('Mi dispiace che i dati non vengano salvati');
        }
    }


    public function updatelateststatus()
    {
        $construnction = ConstructionSite::get();
        foreach ($construnction as $data) {
            if ($data->latest_status == 'preanalysis to be invoiced') {
                $latest_status = 'Preanalisi Fatturato';
            } elseif ($data->latest_status == 'preanalysis revenue') {
                $latest_status = 'Preanalisi Fatturato';
            } elseif ($data->latest_status == 'technician not assigned') {
                $latest_status = 'Tecnico Da Assegnare';
            } elseif ($data->latest_status == 'relief to assign') {
                $latest_status = 'agevolazione da assegnare';
            } elseif ($data->latest_status == 'legge 10 waiting') {
                $latest_status = 'legge 10 in attesa';
            } elseif ($data->latest_status == 'computation waiting') {
                $latest_status = 'Computo In Attesa';
            } elseif ($data->latest_status == 'preliminary notification waiting') {
                $latest_status = 'Notifica Preliminare In Attesa';
            } elseif ($data->latest_status == 'registered practice waiting') {
                $latest_status = 'Pratica Protocollata In Attesa';
            } elseif ($data->latest_status == 'waiting') {
                $latest_status = 'In Attesa';
            } elseif ($data->latest_status == 'work Waiting' || $data->latest_status == 'work Deliever' || $data->latest_status == 'work ') {
                $latest_status = 'Lavori Iniziati In Attesa';
            } elseif ($data->latest_status == 'SAL waiting') {
                $latest_status = 'SAL in attesa';
            } elseif ($data->latest_status == 'balance Aeneas waiting') {
                $latest_status = 'Saldo Enea In Attesa';
            } elseif ($data->latest_status == 'closed waiting') {
                $latest_status = 'Chiuso In Attesa';
            } elseif ($data->latest_status == 'closed') {
                $latest_status = 'Chiusa';
            }

            if (isset($latest_status)) {
                $data->update(['latest_status' => $latest_status]);
            }
        }

        return true;
    }

    public function ReliefDocumentImport()
    {
        $cunstruction = ConstructionSite::get();
        $counter = 0;  // Counter for edited records
        foreach ($cunstruction as $data) {

            $documents = $data->ReliefDocument->where('folder_name', 'Schemi Impianti')->where('st')->count();
            if ($documents) {


                if ($data->StatusRelief) {
                    $reliefId = $data->StatusRelief->id;
                    // Create a new ReliefDocument instance
                    $reliefDoc = new ReliefDoc();
                    $reliefDoc->status_relief_id = $reliefId;
                    $reliefDoc->construction_site_id = $data->id;
                    $reliefDoc->allow = 'admin,technician,businessconsultant,user,business';
                    $reliefDoc->folder_name = 'Schemi Impianti';
                    $reliefDoc->description = 'Pianta lastrico solare e pianta imp. Termico';

                    // Save the new ReliefDocument
                    $reliefDoc->save();

                    $counter++;  // Increment the counter for edited records

                }
            }
        }
        echo 'Total edited records: ' . $counter;
        // dd('Total edited records: ' . $counter);
    }

    public function ReliefDocumentPermission()
    {
        $cunstruction = ConstructionSite::get();
        $counter = 0;  // Counter for edited records
        foreach ($cunstruction as $data) {

            $ReliefDocument = $data->ReliefDocument->where('folder_name', 'Schemi Impianti')->first();

            if ($ReliefDocument) {
                foreach ($ReliefDocument->ReliefDocumentFile as $ReliefDocumentFile) {

                    $ReliefDocumentFile->update(['allow' => 'admin,technician,businessconsultant,user,business']);
                    $counter++;
                }
            }
            // Increment the counter for edited records
        }
        echo 'Total edited records: ' . $counter;
    }


    public function ConstructionStatus()
    {

        $cliente = Cantiere::get();

        foreach ($cliente as $data) {

            // $clienteId = $data->FK_cliente;
            $clienteIdget = ConstructionSite::where('oldid', $data->FK_cliente)->first();
            if ($clienteIdget) {
                $clienteId = $clienteIdget->id;
            } else {
                $clienteId = null;
            }
            //    $clienteId  = $clienteIdget != null ? $clienteIdget : null;
            $cantiereId = $data->cantiereId;

            if ($clienteId != null) {

                if ($data->tecnico_id == null || $data->tecnico_id == '' || $data->tecnico_id == 0) {
                    $tecnico_id = null;
                } else {
                    $tecnico_id = $data->tecnico_id;
                }

                // $data_checkbox =  Checkbox::where('fk_cantiere', $clienteId)->first();
                $data_checkbox = Checkbox::where('fk_cantiere', $cantiereId)->first();

                if ($data_checkbox != null) {

                    // $this->status_pre_analisi($data_checkbox->boxpdf, $clienteId, $cantiereId);
                    // $this->status_technician($data_checkbox->boxta, $clienteId,  $tecnico_id, $cantiereId);
                    // $this->status_reliefs($data_checkbox->boxsf, $clienteId, $cantiereId);
                    // $this->status_leg10s($data_checkbox->boxpo, $clienteId, $cantiereId);
                    // $this->status_computations($data_checkbox->boxce, $clienteId, $cantiereId);

                    // $this->status_pr_notis($data_checkbox->boxnp, $clienteId, $cantiereId);
                    // $this->status_reg_pracs($data_checkbox->boxpdiac, $clienteId, $cantiereId);
                    // $this->status_work_starteds($data_checkbox->boxli, $clienteId, $cantiereId);
                    // $this->status_work_closes($data_checkbox->boxc, $clienteId, $cantiereId);
                    // $this->status_s_a_l_s($data_checkbox->boxsal1, $clienteId, $cantiereId);
                    $this->status_enea_balances($data_checkbox->boxse, $clienteId, $cantiereId);
                } else {
                    // $this->status_pre_analisi(null, $clienteId, $cantiereId);
                    // $this->status_technician(null, $clienteId,  $tecnico_id, $cantiereId);
                    // $this->status_reliefs(null, $clienteId, $cantiereId);
                    // $this->status_leg10s(null, $clienteId, $cantiereId);
                    // $this->status_computations(null, $clienteId, $cantiereId, $cantiereId);

                    // $this->status_pr_notis(null, $clienteId, $cantiereId, $cantiereId);
                    // $this->status_reg_pracs(null, $clienteId, $cantiereId);
                    // $this->status_work_starteds(null, $clienteId, $cantiereId);
                    // $this->status_work_closes(null, $clienteId, $cantiereId);
                    // $this->status_s_a_l_s(null, $clienteId, $cantiereId);
                    $this->status_enea_balances(null, $clienteId, $cantiereId);

                    echo "<br/> Nessun record per " . $clienteId . " <hr/>";
                }
            } else {
                echo "<br/> Nessun record per " . $cantiereId . " <hr/>";
            }
        }
    }

    public function status_pre_analisi($boxpdf, $clienteId, $cantiereId)
    {
        // condition to get state
        if ($boxpdf === null) {
            $state = null;
        } else if ($boxpdf == 1) {
            $state = "Da fatturare";
        } else if ($boxpdf == 2) {
            $state = "Reddito";
        } else if ($boxpdf == 3) {
            $state = "Incassato";
        } else if ($boxpdf == 4) {
            $state = "Non dovuto";
        } else if ($boxpdf == -1) {
            $state = null;
        }


        // $pre_cantiere_data = Cantiere::where('FK_cliente', $cantiereId)->first();
        $pre_cantiere_data = Cantiere::where('cantiereId', $cantiereId)->first();
        if ($pre_cantiere_data != null) {

            $turnover = $pre_cantiere_data->pfdate;
            $embedded = $pre_cantiere_data->pidate;
        } else {
            $turnover = null;
            $embedded = null;
        }

        // get reminder emails and days
        $time = $this->select_time($cantiereId, 'pdf');

        $reminder_emails = $time['reminder_emails'];
        $reminder_days = $time['reminder_days'];

        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxpdf');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'pdf');
        $updated_on = $select_updated_on['updated_on'];

        $insert_status_pre = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'turnover' => $turnover,
            'embedded' => $embedded,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by,
            // 'reminders_emails' => $reminder_emails !=  null ? $reminder_emails : 5,
            // 'reminders_days' => $reminder_days != null ? $reminder_days : 10,
            'reminders_emails' => $reminder_emails,
            'reminders_days' => $reminder_days,
            'status' => 1,
        ];

        // insert into status pre analisi
        // $run_status_pre =  StatusPreAnalysis::updateOrCreate([$construction_site_id, $insert_status_pre]);

        // $run_status_pre = StatusPreAnalysis::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $insert_status_pre // Data to insert or update
        // );

        $run_status_pre = StatusPreAnalysis::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $insert_status_pre // Data to insert or update
        );

        if (!$run_status_pre->exists) {
            // Save the record only if it's new (doesn't exist)
            $run_status_pre->save();
        }


        if ($run_status_pre) {
            echo "Stato pre analisi inserito per la costruzione: " . $clienteId . "<hr>";
        } else {
            echo "Stato preanalisi non riuscita per la costruzione: " . $clienteId . "<hr>";
        }
    }

    public function status_technician($boxta, $clienteId, $tecnician_id, $cantiereId)
    {
        // Determine the state based on $boxta
        if ($boxta === null) {
            $state = null;
        } else if ($boxta == 1) {
            $state = 'Assigned';
        } else if ($boxta == 0) {
            $state = 'Not Assigned';
        } else if ($boxta == -1) {
            $state = null;
        }


        // get reminder emails and days
        $time = $this->select_time($cantiereId, 'ta');

        $reminder_emails = $time['reminder_emails'];
        $reminder_days = $time['reminder_days'];

        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxta');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'ta');
        $updated_on = $select_updated_on['updated_on'];


        $insert_status_technician = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'tecnician_id' => $tecnician_id,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by,
            // 'reminders_emails' => $reminder_emails != null ? $reminder_emails : 1,
            // 'reminders_days' => $reminder_days != null ? $reminder_days : 10,
            'reminders_emails' => $reminder_emails,
            'reminders_days' => $reminder_days,
            'status' => 1,
        ];
        // insert into status pre analisi
        // $insert =  StatusTechnician::create($insert_status_technician);
        // $insert = StatusTechnician::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $insert_status_technician // Data to insert or update
        // );
        $StatusTechnician = StatusTechnician::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $insert_status_technician // Data to insert or update
        );

        if (!$StatusTechnician->exists) {
            // Save the record only if it's new (doesn't exist)
            $StatusTechnician->save();
        }


        if ($StatusTechnician->save()) {
            echo "Stato tecnico inserito per costruzione: " . $clienteId . "<hr>";
        } else {
            echo "Stato tecnico fallito per la costruzione: " . $clienteId . "<hr>";
        }
    }

    public function status_reliefs($boxsf, $clienteId, $cantiereId)
    {
        // status reliefs
        if ($boxsf === null) {
            $state = null;
        } else if ($boxsf == 1) {
            $state = 'Ricevuto';
        } else if ($boxsf == 0) {
            $state = 'Assegnare';
        } else if ($boxsf == -1) {
            $state = null;
        }

        // get reminder emails and days
        $time = $this->select_time($cantiereId, 'sf');
        $reminder_emails = $time['reminder_emails'];
        $reminder_days = $time['reminder_days'];

        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxsf');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'sf');
        $updated_on = $select_updated_on['updated_on'];

        $Data = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by,
            // 'reminders_emails' => $reminder_emails != null ? $reminder_emails : 1,
            // 'reminders_days' => $reminder_days != null ? $reminder_days : 7,
            'reminders_emails' => $reminder_emails,
            'reminders_days' => $reminder_days,
            'status' => 1,
        ];
        // insert into status pre analisi
        // $insert =  StatusRelief::create($Data);


        // $insert = StatusRelief::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );


        $StatusRelief = StatusRelief::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$StatusRelief->exists) {
            // Save the record only if it's new (doesn't exist)
            $StatusRelief->save();
        }

        if ($StatusRelief->save()) {
            echo "Inserite agevolazioni sullo stato per la costruzione:" . $clienteId . "<hr>";
        } else {
            echo "Le agevolazioni sullo status non sono riuscite per la costruzione:" . $clienteId . "<hr>";
        }
    }


    public function status_leg10s($boxpo, $clienteId, $cantiereId)
    {
        // status legge 10
        if ($boxpo === null) {
            $state = null;
        } else if ($boxpo == 1) {
            $state = 'Completed';
        } else if ($boxpo == 0) {
            $state = 'Waiting';
        } else if ($boxpo == -1) {
            $state = null;
        }


        // get reminder emails and days
        $time = $this->select_time($cantiereId, 'po');
        $reminder_emails = $time['reminder_emails'];
        $reminder_days = $time['reminder_days'];

        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxpo');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'po');
        $updated_on = $select_updated_on['updated_on'];

        $Data = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by,
            // 'reminders_emails' => $reminder_emails != null ? $reminder_emails : 1,
            // 'reminders_days' => $reminder_days != null ? $reminder_days : 5
            'reminders_emails' => $reminder_emails,
            'reminders_days' => $reminder_days
        ];
        // insert into status pre analisi
        // $insert =  StatusLeg10::create($Data);


        // $insert = StatusLeg10::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );


        $insert = StatusLeg10::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$insert->exists) {
            // Save the record only if it's new (doesn't exist)
            $insert->save();
        }

        if ($insert->save()) {
            echo "Stato legge 10 inserita per l'edilizia: " . $clienteId . "<hr>";
        } else {
            echo "Stato legge 10 fallito per costruzione: " . $clienteId . "<hr>";
        }
    }

    public function status_computations($boxce, $clienteId, $cantiereId)
    {
        // status legge 10
        if ($boxce === null) {
            $state = null;
        } else if ($boxce == 1) {
            $state = 'Completato';
        } else if ($boxce == 0) {
            $state = 'Inatteso';
        } else if ($boxce == -1) {
            $state = null;
        }


        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxce');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'ce');
        $updated_on = $select_updated_on['updated_on'];


        $Data = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by,
            'status' => 1,
        ];
        // insert into status pre analisi
        // $insert =  StatusComputation::create($Data);
        // $insert = StatusComputation::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );

        $insert = StatusComputation::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$insert->exists) {
            // Save the record only if it's new (doesn't exist)
            $insert->save();
        }

        if ($insert->save()) {

            echo "Stato legge 10 inserita per l'edilizia: " . $clienteId . "<hr>";
        } else {
            echo "Stato legge 10 fallito per costruzione: " . $clienteId . "<hr>";
        }
    }


    public function status_pr_notis($boxnp, $clienteId, $cantiereId)
    {
        // status legge 10
        if ($boxnp === null) {
            $state = null;
        } else if ($boxnp == 1) {
            $state = 'Completato';
        } else if ($boxnp == 0) {
            $state = 'Inatteso';
        } else if ($boxnp == -1) {
            $state = null;
        }

        // get reminder emails and days
        $time = $this->select_time($cantiereId, 'po');
        $reminder_emails = $time['reminder_emails'];
        $reminder_days = $time['reminder_days'];

        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxpo');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'po');
        $updated_on = $select_updated_on['updated_on'];

        $Data = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by,
            'reminders_emails' => $reminder_emails,
            'reminders_days' => $reminder_days
        ];
        // insert into status pre analisi
        // $insert =  StatusPrNoti::create($Data);

        // $insert = StatusPrNoti::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );

        $insert = StatusPrNoti::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$insert->exists) {
            // Save the record only if it's new (doesn't exist)
            $insert->save();
        }

        if ($insert->save()) {
            echo "Stato pr noti inseriti per la costruzione: " . $clienteId . "<hr>";
        } else {
            echo "Stato pr noti non riuscita per la costruzione: " . $clienteId . "<hr>";
        }
    }

    public function status_reg_pracs($boxpdiac, $clienteId, $cantiereId)
    {
        // status legge 10
        if ($boxpdiac === null) {
            $state = null;
        } else if ($boxpdiac == 1) {
            $state = 'Completato';
        } else if ($boxpdiac == 0) {
            $state = 'Inatteso';
        } else if ($boxpdiac == -1) {
            $state = null;
        }

        $time = $this->select_time($cantiereId, 'pdi');
        $reminder_emails = $time['reminder_emails'];
        $reminder_days = $time['reminder_days'];

        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxpdiac');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'pdi');
        $updated_on = $select_updated_on['updated_on'];

        $Data = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by,
            'reminders_emails' => $reminder_emails,
            'reminders_days' => $reminder_days
        ];
        // insert into status pre analisi
        // $insert =  statusRegPrac::create($Data);

        // $insert = statusRegPrac::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );

        $insert = statusRegPrac::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$insert->exists) {
            // Save the record only if it's new (doesn't exist)
            $insert->save();
        }

        if ($insert->save()) {
            echo "Stato reg pracs inserito per la costruzione: " . $clienteId . "<hr>";
        } else {
            echo "Stato reg pracs non riuscito per la costruzione:" . $clienteId . "<hr>";
        }
    }


    public function status_work_starteds($boxli, $clienteId, $cantiereId)
    {
        // status legge 10
        if ($boxli === null) {
            $state = null;
        } else if ($boxli == 1) {
            $state = 'Completato';
        } else if ($boxli == 2) {
            $state = 'Consegnatore';
        } else if ($boxli == 0) {
            $state = 'Inatteso';
        } else if ($boxli == -1) {
            $state = null;
        }

        // Find the cantiere record by FK_cliente
        $cantiere = Cantiere::where('cantiereId', $cantiereId)->first();

        if ($cantiere) {
            $work_started_date = $cantiere->lidate;
        } else {
            $work_started_date = null;
        }

        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxli');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'li');
        $updated_on = $select_updated_on['updated_on'];

        $Data = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'work_started_date' => $work_started_date,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by
        ];
        // insert into status pre analisi
        // $insert =  StatusWorkStarted::create($Data);
        // $insert = StatusWorkStarted::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );

        $insert = StatusWorkStarted::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$insert->exists) {
            // Save the record only if it's new (doesn't exist)
            $insert->save();
        }

        if ($insert->save()) {
            echo "Stato inizio lavori inserito per la costruzione: " . $clienteId . "<hr>";
        } else {
            echo "Stato lavori iniziati non riusciti per la costruzione: " . $clienteId . "<hr>";
        }
    }


    public function status_work_closes($boxc, $clienteId, $cantiereId)
    {
        // status legge 10
        if ($boxc === null) {
            $state = null;
        } else if ($boxc == 1) {
            $ConstructionSite = ConstructionSite::find($clienteId);
            if ($ConstructionSite) {
                $ConstructionSite->status = 0;
                $ConstructionSite->update();
            }
            $state = 'Completato';
        } else if ($boxc == 0) {
            $state = 'Inatteso';
        } else if ($boxc == -1) {
            $state = null;
        }


        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxc');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'c');
        $updated_on = $select_updated_on['updated_on'];

        $Data = [
            'construction_site_id' => $clienteId,
            'state' => $state,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by
        ];
        // insert into status pre analisi
        // $insert =  StatusWorkClose::create($Data);
        // $insert = StatusWorkClose::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );

        $insert = StatusWorkClose::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$insert->exists) {
            // Save the record only if it's new (doesn't exist)
            $insert->save();
        }

        if ($insert->save()) {
            echo "Stato chiusura lavori inseriti per la costruzione: " . $clienteId . "<hr>";
        } else {
            echo "Stato chiusura lavori non riuscita per costruzione: " . $clienteId . "<hr>";
        }
    }

    public function status_s_a_l_s($boxsal1, $clienteId, $cantiereId)
    {
        // status legge 10
        if ($boxsal1 === null) {
            $state = null;
        } else if ($boxsal1 == 1) {
            $state = 'Completed';
        } else if ($boxsal1 == 0) {
            $state = 'Waiting';
        } else if ($boxsal1 == -1) {
            $state = null;
        }


        $cantiere = Cantiere::where('cantiereId', $cantiereId)->first();

        if ($cantiere) {
            $select_accountant = $cantiere->salcom;
        } else {
            $select_accountant = null;
        }


        $time = $this->select_time($cantiereId, 'sal');
        $reminder_emails = $time['reminder_emails'];
        $reminder_days = $time['reminder_days'];

        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxsal1');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'sal1');
        $updated_on = $select_updated_on['updated_on'];

        $Data = [
            'construction_site_id' => $clienteId,
            'select_accountant' => $select_accountant,
            'state' => $state,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by,
            'reminders_emails' => $reminder_emails,
            'reminders_days' => $reminder_days
        ];
        // insert into status pre analisi
        // $insert =  StatusSAL::create($Data);
        // $insert = StatusSAL::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );

        $insert = StatusSAL::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$insert->exists) {
            // Save the record only if it's new (doesn't exist)
            $insert->save();
        }

        if ($insert->save()) {
            echo "Stato sal inserito per la costruzione: " . $clienteId . "<hr>";
        } else {
            echo "Stato sal non riuscito per la costruzione: " . $clienteId . "<hr>";
        }
    }

    public function status_enea_balances($boxse, $clienteId, $cantiereId)
    {

        // status legge 10
        if ($boxse === null) {
            $state = null;
        } else if ($boxse == 1) {
            $state = 'Completato';
        } else if ($boxse == 0) {
            $state = 'Inatteso';
        } else if ($boxse == -1) {
            $state = null;
        } else {
            $state = null;
        }


        $cantiere = Cantiere::where('cantiereId', $cantiereId)->first();

        if ($cantiere) {
            $select_accountant = $cantiere->saldoeneacom;
        } else {
            $select_accountant = null;
        }


        // get updated by column
        $select_updated_by = $this->select_updated_by($cantiereId, 'boxse');
        $updated_by = $select_updated_by['updated_by'];

        // get updated on column
        $select_updated_on = $this->select_updated_on($cantiereId, 'saldoenea');
        $updated_on = $select_updated_on['updated_on'];

        $Data = [
            'construction_site_id' => $clienteId,
            'select_accountant' => $select_accountant,
            'state' => $state,
            'updated_on' => $updated_on,
            'updated_by' => $updated_by
        ];
        // insert into status pre analisi
        // $insert =  StatusEneaBalance::create($Data);
        // $insert = StatusEneaBalance::updateOrCreate(
        //     ['construction_site_id' => $clienteId], // Conditions to identify the record
        //     $Data // Data to insert or update
        // );


        $insert = StatusEneaBalance::firstOrNew(
            ['construction_site_id' => $clienteId], // Conditions to identify the record
            $Data // Data to insert or update
        );

        if (!$insert->exists) {
            // Save the record only if it's new (doesn't exist)
            $insert->save();
        }


        if ($insert->save()) {

            echo "Stato bilanci enea inserito per la costruzione: " . $clienteId . "<hr>";
        } else {
            echo "Stato bilanci enea falliti per costruzione:" . $clienteId . "<hr>";
        }
    }


    public function select_time($fk_cantiere, $col)
    {
        $coll1 = $col . '1r';
        $coll2 = $col . '2r';
        // $timer  =  Timer::where('Fk_Cantiere', $fk_cantiere)->first();
        $timer = Timer::where('Fk_Cantiere', $fk_cantiere)->first();

        if ($timer) {
            $reminder_emails = $timer->{$coll1};
            $reminder_days = $timer->{$coll2};
        } else {
            $reminder_emails = null;
            $reminder_days = null;
        }
        return [
            'reminder_emails' => $reminder_emails,
            'reminder_days' => $reminder_days,
        ];
    }


    public function select_updated_on($fk_cantiere, $col)
    {

        $updatedOn = Date::where('fk_cantiere', $fk_cantiere)
            ->value($col);
        return ['updated_on' => $updatedOn];
    }

    public function select_updated_by($fk_cantiere, $stato_col)
    {
        $updatedBy = StatoUpdatedby::where('fk_cantiere', $fk_cantiere)
            ->where('stato_col', $stato_col)
            ->value('updated_by');
        return ['updated_by' => $updatedBy];
    }


    public function MaterialsNewRecoards()
    {

        $materials = Materials::all();
    }


    public function import()
    {
        $clients = Cliente::all();


        if (count($clients) > 0) {

            $sr = 1;
            $doc = 1;
            foreach ($clients as $client) {
                $client_id = $client->clienteId;
                $primaryId = $sr++;
                $construction_ids = ConstructionSite::where('oldid', $client_id)->first();
                $construction_id = $construction_ids->id;
                $consArr = [
                    'id' => $primaryId,
                    'oldid' => $client_id,
                    'name' => $client->nome == null ? '' : $client->nome,
                    'surename' => $client->cognome == null ? '' : $client->cognome,
                    'date_of_birth' => $client->dataNascita == null ? '' : $client->dataNascita,
                    'town_of_birth' => $client->comuneNascita == null ? '' : $client->comuneNascita,
                    'province' => $client->provinciaNascita == null ? '' : $client->provinciaNascita,
                    'residence_street' => $client->viaResid == null ? '' : $client->viaResid,
                    'residence_house_number' => $client->numResid == null ? '' : $client->numResid,
                    'residence_postal_code' => $client->capResid == null ? '' : $client->capResid,
                    'residence_common' => $client->comuneResid == null ? '' : $client->comuneResid,
                    'residence_province' => $client->provinciaResid == null ? '' : $client->provinciaResid,
                ];

                $cantiere = Cantiere::where('FK_cliente', $client_id)->first();

                if ($cantiere != null) {
                    echo $primaryId . ' if: ' . $client_id . '</br>';

                    // if ($cantiere->archivia == null) {
                    //     $archive = 1;
                    // } else {
                    //     $archive = $cantiere->archivia;
                    // }

                    $consArr['archive'] = $cantiere->archivia;
                    $consArr['latest_status'] = $cantiere->stato == null ? '' : $cantiere->stato;
                    $consArr['page_status'] = 4;

                    // $consInsertElse = new ConstructionSite();
                    // $consInsertElse->create($consArr);

                    // document and contacts
                    $DocumentAndContactArr = [
                        'id' => $primaryId,
                        'construction_site_id' => $client_id,
                        'document_number' => $client->numDoc == null ? '' : $client->numDoc,
                        'issued_by' => $client->rilascioDoc == null ? '' : $client->rilascioDoc,
                        'release_date' => $client->dataDoc == null ? '' : $client->dataDoc,
                        'expiration_date' => $client->scadenzaDoc == null ? '' : $client->scadenzaDoc,
                        'fiscal_document_number' => $client->cf == null ? '' : $client->cf,
                        'vat_number' => $client->iva == null ? '' : $client->iva,
                        'contact_email' => $client->email == null ? '' : $client->email,
                        'contact_number' => $client->telefono_mobile == null ? '' : $client->telefono_mobile,
                        'alt_refrence_name' => $cantiere->rif_contatto == null ? '' : $cantiere->rif_contatto,
                        'alt_contact_number' => $client->telefono_fisso == null ? '' : $client->telefono_fisso,
                    ];
                    // $DocumentAndContactInsertElse = new DocumentAndContact();
                    // $DocumentAndContactInsertElse->create($DocumentAndContactArr);

                    // property_data
                    $PropertyDataArr = [
                        'id' => $primaryId,
                        'construction_site_id' => $client_id,
                        'property_street' => $cantiere->viaImm == null ? '' : $cantiere->viaImm,
                        'property_house_number' => $cantiere->numImm == null ? '' : $cantiere->numImm,
                        'property_common' => $cantiere->comuneImm == null ? '' : $cantiere->comuneImm,
                        'property_postal_code' => $cantiere->capImm == null ? '' : $cantiere->capImm,
                        'property_province' => $cantiere->provinciaImm == null ? '' : $cantiere->provinciaImm,
                        'cadastral_dati' => $cantiere->dati_catasto == null ? '' : $cantiere->dati_catasto,
                        'cadastral_section' => $cantiere->foglioImm == null ? '' : $cantiere->foglioImm,
                        'cadastral_particle' => $cantiere->partImm == null ? '' : $cantiere->partImm,
                        'sub_ordinate' => $cantiere->subImm == null ? '' : $cantiere->subImm,
                        'pod_code' => $cantiere->pod == null ? '' : $cantiere->pod,
                        'cadastral_category' => $cantiere->catc == null ? '' : $cantiere->catc,
                    ];
                    // $PropertyDataInsertElse = new PropertyData();
                    // $PropertyDataInsertElse->create($PropertyDataArr);

                    // construction_site_settings
                    $ConstructionSiteSettingArr = [
                        'id' => $primaryId,
                        'construction_site_id' => $construction_id,
                        'type_of_property' => $cantiere->tipologia == null ? '' : $cantiere->tipologia,
                    ];

                    $ConstructionSiteSettingArr['type_of_construction'] = $cantiere->esterni;

                    if ($cantiere['110'] == 1) {
                        $ConstructionSiteSettingArr['oneten'] = '110';
                    } else {
                        $ConstructionSiteSettingArr['oneten'] = '';
                    }

                    if ($cantiere['90'] == 1) {
                        $ConstructionSiteSettingArr['nineT'] = '90';
                    } else {
                        $ConstructionSiteSettingArr['nineT'] = '';
                    }

                    if ($cantiere['65'] == 1) {
                        $ConstructionSiteSettingArr['sixF'] = '65';
                    } else {
                        $ConstructionSiteSettingArr['sixF'] = '';
                    }

                    if ($cantiere['50'] == 1) {
                        $ConstructionSiteSettingArr['fiftyF'] = '50';
                    } else {
                        $ConstructionSiteSettingArr['fiftyF'] = '';
                    }

                    if ($cantiere['fotovoltaico'] == 1) {
                        $ConstructionSiteSettingArr['fotovoltaico'] = 'fotovoltaico';
                    } else {
                        $ConstructionSiteSettingArr['fotovoltaico'] = '';
                    }

                    $ConstructionSiteSettingInsertElse = new ConstructionSiteSetting();
                    $ConstructionSiteSettingInsertElse->create($ConstructionSiteSettingArr);

                    echo $consArr['archive'] . " and " . $consArr['latest_status'] . '</br>';
                } else {
                    echo $sr++ . ' else: ' . $client_id . '</br>';

                    $consArr['page_status'] = 1;

                    // $consInsertElse = new ConstructionSite();
                    // $consInsertElse->create($consArr);

                    $consId = ['id' => $primaryId, 'construction_site_id' => $construction_id];

                    // $DocumentAndContactInsertElse = new DocumentAndContact();
                    // $DocumentAndContactInsertElse->create($consId);

                    // $PropertyDataInsertElse = new PropertyData();
                    // $PropertyDataInsertElse->create($consId);

                    $ConstructionSiteSettingInsertElse = new ConstructionSiteSetting();
                    $ConstructionSiteSettingInsertElse->create($consId);
                }
            }
        }
    }


    public function DeductionSub2file()
    {
        $constructionSites = ConstructionSite::all();

        $fileCounter = 0; // Counter to track the number of files added
        foreach ($constructionSites as $constructionSite) {

            foreach ($constructionSite->TypeOfDedectionSub1->where('folder_name', 'Documenti Saldo 110') as $TypeOfDedectionSub1) {

                $find = $TypeOfDedectionSub1->TypeOfDedectionSub2->where('file_name', 'DURC di congruità');

                if (count($find) == 0) {

                    $sub_file_56 = [
                        'type_of_dedection_sub1_id' => $TypeOfDedectionSub1->id,
                        'construction_site_id' => $constructionSite->id,
                        'allow' => 'admin,businessconsultant,user',
                        'file_name' => 'DURC di congruità',
                        'bydefault' => 1,
                        'state' => 1
                    ];

                    $TypeOfDedectionSub1->TypeOfDedectionSub2()->updateOrCreate($sub_file_56);
                    $fileCounter++; //
                }
            }
        }

        echo "Number of files added: $fileCounter";
    }


    public function DeductionSub2filepermission()
    {
        $constructionSites = ConstructionSite::all();

        $fileCounter = 0; // Counter to track the number of files added
        foreach ($constructionSites as $constructionSite) {

            foreach ($constructionSite->TypeOfDedectionSub1->where('folder_name', 'Documenti Saldo 110') as $TypeOfDedectionSub1) {
                foreach ($TypeOfDedectionSub1->TypeOfDedectionSub2 as $TypeOfDedectionSub2) {
                    $TypeOfDedectionSub2->update(['allow' => 'admin,businessconsultant,user']);
                    $fileCounter++;
                }
            }
        }

        echo "Number of files added: $fileCounter";
    }

    public function DeductionSub2subfilepermission()
    {
        $constructionSites = ConstructionSite::all();

        $fileCounter = 0; // Counter to track the number of files added
        foreach ($constructionSites as $constructionSite) {

            foreach ($constructionSite->TypeOfDedectionSub1->where('folder_name', 'Documenti Saldo 110') as $TypeOfDedectionSub1) {
                foreach ($TypeOfDedectionSub1->TypeOfDedectionSub2 as $TypeOfDedectionSub2) {
                    foreach ($TypeOfDedectionSub2->TypeOfDedectionFiles as $TypeOfDedectionFiles) {
                        $TypeOfDedectionFiles->update(['allow' => 'admin,businessconsultant,user']);
                        $fileCounter++;
                    }
                }
            }
        }

        echo "Number of files updated: $fileCounter";
        return true;
    }

    public function extraCondominio()
    {
        $constructionSite = ConstructionCondomini::get();
        $count = 0;
        foreach ($constructionSite as $constructionSites) {

            if ($constructionSites->ConstructionCondomini !== null) {
                if ($constructionSites->ConstructionCondomini->ConstructionSiteSetting !== null) {
                    $constructionSites->ConstructionCondomini->ConstructionSiteSetting->type_of_property == 'Condominio' ? $count++ : '';

                    $constructionSites->delete();
                }
            }
        }
        echo 'total : ' . $count . ' deleted';
        //    dd($constructionSite->construction_condominis);
    }


    public function DocumentiRilevanti()
    {
        $constructionSites = ConstructionSite::get();
        $count = 0;
        foreach ($constructionSites as $constructionSite) {
            if ($constructionSite->PrNotDoc !== null) {
                foreach ($constructionSite->PrNotDoc->where('folder_name', 'Documenti Rilevanti') as $PrNotDoc) {
                    $PrNotDoc->update(['allow' => 'admin,user']);
                    $count++;
                }
            }
        }
        echo 'total : ' . $count . ' updated';
    }

    public function PrNotDocFile()
    {
        $constructionSites = ConstructionSite::get();
        $count = 0;
        foreach ($constructionSites as $constructionSite) {
            if ($constructionSite->PrNotDoc !== null) {
                foreach ($constructionSite->PrNotDoc->where('folder_name', 'Documentazione Varia') as $PrNotDoc) {


                    foreach ($PrNotDoc->PrNotDocFile->where('file_name', 'Delega Commercialista Guarino') as $PrNotDocFile) {

                        $PrNotDocFile->update(['allow' => 'admin,user']);
                        $count++;
                    }
                }
            }
        }
        echo 'total : ' . $count . ' updated';
    }

    public function permissionIssue()
    {
        $constructionSites = ConstructionSite::get();
        $count = 0;
        foreach ($constructionSites as $constructionSite) {
            // dd($constructionSite->PrNotDoc);
            if ($constructionSite->PrNotDoc !== null) {
                foreach ($constructionSite->PrNotDoc->where('folder_name', ['Documenti 110']) as $PrNotDoc) {
                    // dd($PrNotDoc);
                    $PrNotDoc->update(['allow' => 'admin,user']);
                    $count++;
                }
            }
        }
        echo 'total : ' . $count . ' updated';
    }


    public function constructionMissingDate()
    {
        $cliente = Cliente::all();
        $count = 0;
        foreach ($cliente as $client) {
            if ($client->dataNascita) {
                $clientId = $client->clienteId;

                $construction = ConstructionSite::where('oldid', $clientId)->first();
                if ($construction) {
                    $construction->date_of_birth = $client->dataNascita;
                    $construction->update();
                    $count++;

                    if ($client->dataDoc) {
                        $constructionId = $construction->id;

                        $documentAndContact = DocumentAndContact::where('construction_site_id', $constructionId)->first();
                        if ($documentAndContact) {
                            $documentAndContact->release_date = $client->dataDoc;
                            $documentAndContact->update();
                        }
                    }

                    if ($client->telefono_mobile) {
                        $constructionId1 = $construction->id;

                        $documentAndContact1 = DocumentAndContact::where('construction_site_id', $constructionId1)->first();
                        if ($documentAndContact1) {
                            $documentAndContact1->contact_number = $client->telefono_mobile;
                            $documentAndContact1->update();
                        }
                    }
                }
            }
        }
        echo 'total = ' . $count;
    }

    public function constructionMissingColumnData()
    {
        $cliente = Cliente::all();
        $count = 0;
        foreach ($cliente as $client) {
            if ($client->documento) {
                $clientId = $client->clienteId;

                $construction = ConstructionSite::where('oldid', $clientId)->first();
                if ($construction) {
                    $missingCol = new ConstructionMissingColumn();
                    $missingCol->construction_site_id = $construction->id;
                    $missingCol->documento = $client->documento;
                    $missingCol->save();
                    $count++;
                }
            }
        }
        echo 'total decumento = ' . $count;

        $count = 0;
        foreach ($cliente as $client) {
            $clientId = $client->clienteId;

            $cantiere = Cantiere::where('FK_cliente', $clientId)->first();

            if ($cantiere && $cantiere->tecnico) {
                $usersWithRole = User::whereHas('roles', function ($query) {
                    $query->where('name', 'technician');
                })->where('name', $cantiere->tecnico)->first();
                $construction = ConstructionSite::where('oldid', $clientId)->first();
                if ($construction && $usersWithRole) {
                    $constructionId = $construction->id;
                    $userId = $usersWithRole->id;

                    ConstructionMissingColumn::updateOrInsert(
                        ['construction_site_id' => $constructionId],
                        ['user_id' => $userId]
                    );
                    $count++;
                }
            }
        }
        echo 'total technico = ' . $count;
    }


    //script for dublication files
    public function DocumentiClienti()
    {

        $all = ReliefDoc::where('folder_name', 'Documenti Clienti')->get();
        $count = 0;
        foreach ($all as $value) {
            foreach ($value->ReliefDocumentFile->where('file_name', 'Carta D Identita')->where('updated_by', '!=', null) as $file) {
                $data = RelDocFile::find($file->id);
                $data->bydefault = 1;
                $data->update();
                // $file->updated(['bydefault', 1]);

                RelDocFile::where('relief_doc_id', $value->id)->where('file_name', "Carta D'identità")->delete();
                $count++;
            }
        }
    }

    public function PraticheComunali()
    {

        $all = ReliefDoc::where('folder_name', 'Pratiche Comunali')->get();

        $count = 0;

        foreach ($all as $value) {
            // $fileName =  ['Cilas Protocollata','Protocollo Cilas'];
            foreach ($value->ReliefDocumentFile->whereIn('file_name', 'Cilas Protocollata')->where('updated_by', '!=', null) as $file) {
                $data = RelDocFile::find($file->id);
                if ($data) {
                    // $file_name = $data->file_name =  "Protocollo Cilas" ? "Protocollo cilas 110" : "Cilas Protocollata 110";

                    RelDocFile::where('relief_doc_id', $value->id)->where('updated_on', null)->where('file_name', 'Cilas Protocollata 110')->delete();


                    // $description  =  $data->file_name =  "Protocollo Cilas" ? "No foto - solo doc ufficiale (PEC o ricevuta)" : "Unico file completo ufficiale";
                    $data->bydefault = 1;
                    $data->file_name = 'Cilas Protocollata 110';
                    $data->description = 'Unico file completo ufficiale';
                    $data->update();

                    $count++;
                }
            }
        }

        echo 'total updated recoards are' . $count;
        return true;
    }

    public function PraticheComunaliProtocollocilas110()
    {

        $all = ReliefDoc::where('folder_name', 'Pratiche Comunali')->get();

        $count = 0;

        foreach ($all as $value) {

            foreach ($value->ReliefDocumentFile->where('file_name', 'Protocollo Cilas')->where('updated_by', '!=', null) as $file) {
                $data = RelDocFile::find($file->id);
                if ($data) {

                    RelDocFile::where('relief_doc_id', $value->id)->where('updated_on', null)->where('file_name', 'Protocollo cilas 110')->delete();
                    $data->bydefault = 1;
                    $data->file_name = 'Protocollo cilas 110';
                    $data->description = 'No foto - solo doc ufficiale (PEC o ricevuta)';
                    $data->update();

                    $count++;
                }
            }
        }
        echo 'total updated recoards are' . $count;
        return true;
    }


    public function EstrattoDiMappa()
    {

        $all = ReliefDoc::where('folder_name', 'Pratiche Comunali')->get();

        $count = 0;

        foreach ($all as $value) {

            foreach ($value->ReliefDocumentFile->where('file_name', 'Estratto Di Mappa')->where('updated_by', '!=', null) as $file) {
                $data = RelDocFile::find($file->id);
                if ($data) {
                    $data->delete();
                    $count++;
                }
            }
        }
        echo 'total updated recoards are' . $count;
        return true;
    }

    public function NotificaPreliminare()
    {
        $construction = ConstructionSite::get();
        $count = 0;
        foreach ($construction as $constructionsingle) {

            $data = $constructionsingle->RegPracDocFile->where('file_name', 'Notifica Preliminare')->where('updated_by', '!=', null)->first();
            if ($data) {
                // dd($data);
                $RelDocFile = RelDocFile::where('construction_site_id', $data->construction_site_id)->where('file_name', 'Notifica Preliminare')->where('updated_by', null)->first();
                if ($RelDocFile) {

                    $RelDocFile->update(['file_path' => $data->file_path, 'updated_by' => $data->updated_by, 'updated_on' => $data->updated_on]);
                    $count++;
                }
                // dd($RegPracDocFile);

            }
        }
        echo 'total updated recoards are' . $count;
    }


    public function RemoveNotificaPreliminare()
    {
        $cons = ConstructionSite::get();
        $count = 0;
        $updatedRecords = [];

        foreach ($cons as $conssingle) {

            $data = $conssingle->TypeOfDedectionSub1->where('file_name', 'Notifica Preliminare')->where('updated_by', '!=', null)->first();
            if ($data) {
                if ($data->delete()) {
                    $count++;
                }


                $updatedRecords[] = $data;
            }
        }
        echo 'total updated recoards are' . $count;

        if ($count > 0) {
            echo 'Updated Records:';
            foreach ($updatedRecords as $record) {
                // Display relevant data from $record
                echo 'Construction Site ID: ' . $record->construction_site_id . '</br>';

                // Add more fields as needed
            }
        }
    }


    public function CantiereCondominio()
    {
        $count =  0;

        $Condomini = Condomini::get();
        foreach ($Condomini as $singleCondomini) {
            $data = $singleCondomini->cantiere;
            if ($data) {
                $construction = ConstructionSite::where('oldid', $data->FK_cliente)->first();
                if ($construction) {
                    // dd();
                    $consId =  $construction->id;

                    $allData = $singleCondomini->utenti;

                    $utentiArray = explode(',', $allData);

                    $filteredUtentiArray = array_filter($utentiArray, function ($userId) {
                        return !empty($userId);
                    });

                    foreach ($filteredUtentiArray as $userId) {

                        $Cantiere = Cantiere::where('cantiereId', $userId)->first();

                        if ($Cantiere) {

                            $ChildConstruction = ConstructionSite::where('oldid', $Cantiere->FK_cliente)->first();
                            if ($ChildConstruction) {

                                // $existingRelationship = ConstructionCondomini::where('construction_site_id', $ChildConstruction->id)
                                //     ->where('construction_assigned_id', $construction->id)
                                //     ->first();

                                // if (!$existingRelationship && $ChildConstruction->id !=  $construction->id) {
                                if ($ChildConstruction->id !=  $construction->id) {

                                    echo "Parent Name:" . $construction->surename  . ' ' . $construction->name . '</br>';
                                    // echo "Child ID: $ChildConstruction->id".'</br></br>';
                                    echo "Child Name:" . $ChildConstruction->surename  . ' ' . $ChildConstruction->name . '</br></br>';

                                    ConstructionCondomini::create(['construction_site_id' =>  $construction->id, 'construction_assigned_id' => $ChildConstruction->id]);
                                    // ConstructionCondomini::create(['construction_site_id' =>  $ChildConstruction->id, 'construction_assigned_id'=>$construction->id]);
                                    $count++;
                                }
                            }
                        }
                    }
                }
            }
        }
        echo 'total added recoards are' . $count;
    }
    public function RemoveCondomonioDublication()
    {
        $ConstructionCondomini = ConstructionCondomini::get();
        $count  = 0;

        foreach ($ConstructionCondomini as $ConstructionCondominisingle) {
            $data = ConstructionCondomini::where('construction_assigned_id', $ConstructionCondominisingle->construction_site_id)->first();
            // $data  = $ConstructionCondomini->where('construction_assigned_id', $ConstructionCondominisingle)->delete();
            if ($data) {
                echo $data->construction_site_id . '</br>';
                $data->delete();

                $count++;
            }
            // $data  ??
        }
        echo 'total deleted recoards are' . $count;
    }

    public function addNewRecords()
    {
        $construction_condominis = array(
            array('id' => '1', 'construction_site_id' => '627', 'construction_assigned_id' => '628', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '2', 'construction_site_id' => '520', 'construction_assigned_id' => '285', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '3', 'construction_site_id' => '520', 'construction_assigned_id' => '286', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '4', 'construction_site_id' => '520', 'construction_assigned_id' => '427', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '5', 'construction_site_id' => '351', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '6', 'construction_site_id' => '347', 'construction_assigned_id' => '609', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '7', 'construction_site_id' => '347', 'construction_assigned_id' => '692', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '8', 'construction_site_id' => '446', 'construction_assigned_id' => '444', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '9', 'construction_site_id' => '446', 'construction_assigned_id' => '445', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '10', 'construction_site_id' => '280', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '11', 'construction_site_id' => '431', 'construction_assigned_id' => '432', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '12', 'construction_site_id' => '431', 'construction_assigned_id' => '433', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '13', 'construction_site_id' => '588', 'construction_assigned_id' => '589', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '14', 'construction_site_id' => '588', 'construction_assigned_id' => '584', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '15', 'construction_site_id' => '631', 'construction_assigned_id' => '630', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '16', 'construction_site_id' => '619', 'construction_assigned_id' => '413', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '17', 'construction_site_id' => '619', 'construction_assigned_id' => '645', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '18', 'construction_site_id' => '651', 'construction_assigned_id' => '652', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '19', 'construction_site_id' => '651', 'construction_assigned_id' => '658', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '20', 'construction_site_id' => '451', 'construction_assigned_id' => '248', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '21', 'construction_site_id' => '451', 'construction_assigned_id' => '443', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '22', 'construction_site_id' => '693', 'construction_assigned_id' => '377', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '23', 'construction_site_id' => '693', 'construction_assigned_id' => '378', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '24', 'construction_site_id' => '533', 'construction_assigned_id' => '534', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '25', 'construction_site_id' => '533', 'construction_assigned_id' => '380', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '26', 'construction_site_id' => '495', 'construction_assigned_id' => '497', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '27', 'construction_site_id' => '495', 'construction_assigned_id' => '496', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '28', 'construction_site_id' => '458', 'construction_assigned_id' => '456', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '29', 'construction_site_id' => '458', 'construction_assigned_id' => '457', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '30', 'construction_site_id' => '458', 'construction_assigned_id' => '464', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '31', 'construction_site_id' => '458', 'construction_assigned_id' => '577', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '32', 'construction_site_id' => '682', 'construction_assigned_id' => '318', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '33', 'construction_site_id' => '682', 'construction_assigned_id' => '681', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '34', 'construction_site_id' => '325', 'construction_assigned_id' => '686', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '35', 'construction_site_id' => '325', 'construction_assigned_id' => '687', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '36', 'construction_site_id' => '613', 'construction_assigned_id' => '617', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '37', 'construction_site_id' => '613', 'construction_assigned_id' => '614', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '38', 'construction_site_id' => '613', 'construction_assigned_id' => '615', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '39', 'construction_site_id' => '621', 'construction_assigned_id' => '622', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '40', 'construction_site_id' => '621', 'construction_assigned_id' => '624', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '41', 'construction_site_id' => '625', 'construction_assigned_id' => '359', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '42', 'construction_site_id' => '657', 'construction_assigned_id' => '656', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '43', 'construction_site_id' => '162', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '44', 'construction_site_id' => '308', 'construction_assigned_id' => '688', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '45', 'construction_site_id' => '308', 'construction_assigned_id' => '689', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '46', 'construction_site_id' => '610', 'construction_assigned_id' => '611', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '47', 'construction_site_id' => '610', 'construction_assigned_id' => '612', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '48', 'construction_site_id' => '400', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '49', 'construction_site_id' => '350', 'construction_assigned_id' => '548', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '50', 'construction_site_id' => '350', 'construction_assigned_id' => '549', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '51', 'construction_site_id' => '521', 'construction_assigned_id' => '324', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '52', 'construction_site_id' => '521', 'construction_assigned_id' => '500', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '53', 'construction_site_id' => '665', 'construction_assigned_id' => '666', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '54', 'construction_site_id' => '665', 'construction_assigned_id' => '668', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '55', 'construction_site_id' => '665', 'construction_assigned_id' => '669', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '57', 'construction_site_id' => '88', 'construction_assigned_id' => '401', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '58', 'construction_site_id' => '88', 'construction_assigned_id' => '411', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '59', 'construction_site_id' => '518', 'construction_assigned_id' => '110', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '60', 'construction_site_id' => '518', 'construction_assigned_id' => '111', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '61', 'construction_site_id' => '636', 'construction_assigned_id' => '637', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '62', 'construction_site_id' => '636', 'construction_assigned_id' => '639', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '63', 'construction_site_id' => '662', 'construction_assigned_id' => '187', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '65', 'construction_site_id' => '233', 'construction_assigned_id' => '234', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '66', 'construction_site_id' => '233', 'construction_assigned_id' => '283', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '67', 'construction_site_id' => '233', 'construction_assigned_id' => '235', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '68', 'construction_site_id' => '233', 'construction_assigned_id' => '547', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '69', 'construction_site_id' => '189', 'construction_assigned_id' => '255', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '70', 'construction_site_id' => '189', 'construction_assigned_id' => '532', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '71', 'construction_site_id' => '519', 'construction_assigned_id' => '176', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '72', 'construction_site_id' => '519', 'construction_assigned_id' => '177', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '73', 'construction_site_id' => '519', 'construction_assigned_id' => '178', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '74', 'construction_site_id' => '519', 'construction_assigned_id' => '179', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '75', 'construction_site_id' => '522', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '76', 'construction_site_id' => '299', 'construction_assigned_id' => '542', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '77', 'construction_site_id' => '299', 'construction_assigned_id' => '543', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '78', 'construction_site_id' => '253', 'construction_assigned_id' => '524', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '79', 'construction_site_id' => '253', 'construction_assigned_id' => '530', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '80', 'construction_site_id' => '253', 'construction_assigned_id' => '641', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '81', 'construction_site_id' => '253', 'construction_assigned_id' => '529', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '82', 'construction_site_id' => '198', 'construction_assigned_id' => '194', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '83', 'construction_site_id' => '198', 'construction_assigned_id' => '197', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '84', 'construction_site_id' => '198', 'construction_assigned_id' => '196', 'created_at' => '2023-10-23 07:22:10', 'updated_at' => '2023-10-23 07:22:10'),
            array('id' => '85', 'construction_site_id' => '898', 'construction_assigned_id' => '896', 'created_at' => '2023-10-23 08:50:04', 'updated_at' => '2023-10-23 08:50:04'),
            array('id' => '86', 'construction_site_id' => '898', 'construction_assigned_id' => '897', 'created_at' => '2023-10-23 08:50:13', 'updated_at' => '2023-10-23 08:50:13'),
            array('id' => '87', 'construction_site_id' => '813', 'construction_assigned_id' => '812', 'created_at' => '2023-10-23 09:37:28', 'updated_at' => '2023-10-23 09:37:28'),
            array('id' => '89', 'construction_site_id' => '795', 'construction_assigned_id' => '796', 'created_at' => '2023-10-23 09:41:01', 'updated_at' => '2023-10-23 09:41:01'),
            array('id' => '90', 'construction_site_id' => '795', 'construction_assigned_id' => '798', 'created_at' => '2023-10-23 09:41:11', 'updated_at' => '2023-10-23 09:41:11'),
            array('id' => '91', 'construction_site_id' => '795', 'construction_assigned_id' => '807', 'created_at' => '2023-10-23 09:41:19', 'updated_at' => '2023-10-23 09:41:19'),
            array('id' => '92', 'construction_site_id' => '938', 'construction_assigned_id' => '936', 'created_at' => '2023-10-23 09:54:36', 'updated_at' => '2023-10-23 09:54:36'),
            array('id' => '93', 'construction_site_id' => '938', 'construction_assigned_id' => '937', 'created_at' => '2023-10-23 09:54:43', 'updated_at' => '2023-10-23 09:54:43'),
            array('id' => '100', 'construction_site_id' => '953', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-23 14:09:42', 'updated_at' => '2023-10-23 14:09:42'),
            array('id' => '101', 'construction_site_id' => '953', 'construction_assigned_id' => '868', 'created_at' => '2023-10-23 14:09:53', 'updated_at' => '2023-10-23 14:09:53'),
            array('id' => '102', 'construction_site_id' => '953', 'construction_assigned_id' => '869', 'created_at' => '2023-10-23 14:10:02', 'updated_at' => '2023-10-23 14:10:02'),
            array('id' => '103', 'construction_site_id' => '943', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-23 14:11:22', 'updated_at' => '2023-10-23 14:11:22'),
            array('id' => '105', 'construction_site_id' => '953', 'construction_assigned_id' => '874', 'created_at' => '2023-10-23 14:14:11', 'updated_at' => '2023-10-23 14:14:11'),
            array('id' => '106', 'construction_site_id' => '953', 'construction_assigned_id' => '870', 'created_at' => '2023-10-23 14:14:44', 'updated_at' => '2023-10-23 14:14:44'),
            array('id' => '107', 'construction_site_id' => '953', 'construction_assigned_id' => '866', 'created_at' => '2023-10-23 14:14:51', 'updated_at' => '2023-10-23 14:14:51'),
            array('id' => '108', 'construction_site_id' => '953', 'construction_assigned_id' => '871', 'created_at' => '2023-10-23 14:15:02', 'updated_at' => '2023-10-23 14:15:02'),
            array('id' => '109', 'construction_site_id' => '954', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-23 14:19:30', 'updated_at' => '2023-10-23 14:19:30'),
            array('id' => '110', 'construction_site_id' => '954', 'construction_assigned_id' => '876', 'created_at' => '2023-10-23 14:19:49', 'updated_at' => '2023-10-23 14:19:49'),
            array('id' => '111', 'construction_site_id' => '954', 'construction_assigned_id' => '877', 'created_at' => '2023-10-23 14:19:57', 'updated_at' => '2023-10-23 14:19:57'),
            array('id' => '112', 'construction_site_id' => '954', 'construction_assigned_id' => '878', 'created_at' => '2023-10-23 14:20:06', 'updated_at' => '2023-10-23 14:20:06'),
            array('id' => '115', 'construction_site_id' => '943', 'construction_assigned_id' => '900', 'created_at' => '2023-10-23 15:58:30', 'updated_at' => '2023-10-23 15:58:30'),
            array('id' => '116', 'construction_site_id' => '943', 'construction_assigned_id' => '895', 'created_at' => '2023-10-23 16:00:25', 'updated_at' => '2023-10-23 16:00:25'),
            array('id' => '117', 'construction_site_id' => '943', 'construction_assigned_id' => '893', 'created_at' => '2023-10-23 16:00:39', 'updated_at' => '2023-10-23 16:00:39'),
            array('id' => '118', 'construction_site_id' => '943', 'construction_assigned_id' => '891', 'created_at' => '2023-10-23 16:03:21', 'updated_at' => '2023-10-23 16:03:21'),
            array('id' => '119', 'construction_site_id' => '943', 'construction_assigned_id' => '889', 'created_at' => '2023-10-23 16:03:31', 'updated_at' => '2023-10-23 16:03:31'),
            array('id' => '120', 'construction_site_id' => '943', 'construction_assigned_id' => '888', 'created_at' => '2023-10-23 16:03:39', 'updated_at' => '2023-10-23 16:03:39'),
            array('id' => '121', 'construction_site_id' => '943', 'construction_assigned_id' => '887', 'created_at' => '2023-10-23 16:03:50', 'updated_at' => '2023-10-23 16:03:50'),
            array('id' => '122', 'construction_site_id' => '943', 'construction_assigned_id' => '886', 'created_at' => '2023-10-23 16:03:59', 'updated_at' => '2023-10-23 16:03:59'),
            array('id' => '123', 'construction_site_id' => '918', 'construction_assigned_id' => '914', 'created_at' => '2023-10-23 16:35:43', 'updated_at' => '2023-10-23 16:35:43'),
            array('id' => '124', 'construction_site_id' => '918', 'construction_assigned_id' => '916', 'created_at' => '2023-10-23 16:35:49', 'updated_at' => '2023-10-23 16:35:49'),
            array('id' => '125', 'construction_site_id' => '918', 'construction_assigned_id' => '917', 'created_at' => '2023-10-23 16:35:56', 'updated_at' => '2023-10-23 16:35:56'),
            array('id' => '126', 'construction_site_id' => '918', 'construction_assigned_id' => '915', 'created_at' => '2023-10-23 16:36:09', 'updated_at' => '2023-10-23 16:36:09'),
            array('id' => '127', 'construction_site_id' => '903', 'construction_assigned_id' => '902', 'created_at' => '2023-10-23 19:47:40', 'updated_at' => '2023-10-23 19:47:40'),
            array('id' => '128', 'construction_site_id' => '903', 'construction_assigned_id' => '905', 'created_at' => '2023-10-23 19:49:22', 'updated_at' => '2023-10-23 19:49:22'),
            array('id' => '129', 'construction_site_id' => '521', 'construction_assigned_id' => '702', 'created_at' => '2023-10-24 08:35:17', 'updated_at' => '2023-10-24 08:35:17'),
            array('id' => '130', 'construction_site_id' => '941', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-24 09:44:07', 'updated_at' => '2023-10-24 13:05:47'),
            array('id' => '131', 'construction_site_id' => '927', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-24 10:37:05', 'updated_at' => '2023-10-24 10:37:05'),
            array('id' => '132', 'construction_site_id' => '927', 'construction_assigned_id' => '926', 'created_at' => '2023-10-24 10:37:27', 'updated_at' => '2023-10-24 10:37:27'),
            array('id' => '134', 'construction_site_id' => '927', 'construction_assigned_id' => '955', 'created_at' => '2023-10-24 12:18:08', 'updated_at' => '2023-10-24 12:18:08'),
            array('id' => '135', 'construction_site_id' => '162', 'construction_assigned_id' => '758', 'created_at' => '2023-10-24 13:06:39', 'updated_at' => '2023-10-24 13:06:39'),
            array('id' => '136', 'construction_site_id' => '162', 'construction_assigned_id' => '755', 'created_at' => '2023-10-24 13:06:59', 'updated_at' => '2023-10-24 13:06:59'),
            array('id' => '137', 'construction_site_id' => '162', 'construction_assigned_id' => '757', 'created_at' => '2023-10-24 13:07:33', 'updated_at' => '2023-10-24 13:07:33'),
            array('id' => '138', 'construction_site_id' => '162', 'construction_assigned_id' => '756', 'created_at' => '2023-10-24 13:08:09', 'updated_at' => '2023-10-24 13:08:09'),
            array('id' => '139', 'construction_site_id' => '162', 'construction_assigned_id' => '754', 'created_at' => '2023-10-24 13:08:32', 'updated_at' => '2023-10-24 13:08:32'),
            array('id' => '140', 'construction_site_id' => '745', 'construction_assigned_id' => '744', 'created_at' => '2023-10-24 14:20:52', 'updated_at' => '2023-10-24 14:20:52'),
            array('id' => '141', 'construction_site_id' => '745', 'construction_assigned_id' => '751', 'created_at' => '2023-10-24 14:21:01', 'updated_at' => '2023-10-24 14:21:01'),
            array('id' => '142', 'construction_site_id' => '745', 'construction_assigned_id' => '749', 'created_at' => '2023-10-24 14:21:08', 'updated_at' => '2023-10-24 14:21:08'),
            array('id' => '143', 'construction_site_id' => '745', 'construction_assigned_id' => '750', 'created_at' => '2023-10-24 14:21:20', 'updated_at' => '2023-10-24 14:21:20'),
            array('id' => '144', 'construction_site_id' => '745', 'construction_assigned_id' => '747', 'created_at' => '2023-10-24 14:21:27', 'updated_at' => '2023-10-24 14:21:27'),
            array('id' => '145', 'construction_site_id' => '941', 'construction_assigned_id' => '939', 'created_at' => '2023-10-24 14:36:18', 'updated_at' => '2023-10-24 14:36:18'),
            array('id' => '146', 'construction_site_id' => '941', 'construction_assigned_id' => '940', 'created_at' => '2023-10-24 14:36:29', 'updated_at' => '2023-10-24 14:36:29'),
            array('id' => '147', 'construction_site_id' => '671', 'construction_assigned_id' => '679', 'created_at' => '2023-10-24 14:49:54', 'updated_at' => '2023-10-24 14:49:54'),
            array('id' => '148', 'construction_site_id' => '671', 'construction_assigned_id' => '677', 'created_at' => '2023-10-24 14:50:03', 'updated_at' => '2023-10-24 14:50:03'),
            array('id' => '149', 'construction_site_id' => '671', 'construction_assigned_id' => '678', 'created_at' => '2023-10-24 14:50:10', 'updated_at' => '2023-10-24 14:50:10'),
            array('id' => '150', 'construction_site_id' => '671', 'construction_assigned_id' => '672', 'created_at' => '2023-10-24 14:50:31', 'updated_at' => '2023-10-24 14:50:31'),
            array('id' => '151', 'construction_site_id' => '671', 'construction_assigned_id' => '676', 'created_at' => '2023-10-24 14:50:38', 'updated_at' => '2023-10-24 14:50:38'),
            array('id' => '152', 'construction_site_id' => '671', 'construction_assigned_id' => '675', 'created_at' => '2023-10-24 14:51:08', 'updated_at' => '2023-10-24 14:51:08'),
            array('id' => '153', 'construction_site_id' => '671', 'construction_assigned_id' => '674', 'created_at' => '2023-10-24 14:51:22', 'updated_at' => '2023-10-24 14:51:22'),
            array('id' => '154', 'construction_site_id' => '956', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-24 15:55:39', 'updated_at' => '2023-10-24 15:55:39'),
            array('id' => '155', 'construction_site_id' => '956', 'construction_assigned_id' => '957', 'created_at' => '2023-10-24 16:01:17', 'updated_at' => '2023-10-24 16:01:17'),
            array('id' => '158', 'construction_site_id' => '960', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-25 10:06:06', 'updated_at' => '2023-10-25 10:06:06'),
            array('id' => '161', 'construction_site_id' => '803', 'construction_assigned_id' => '804', 'created_at' => '2023-10-25 11:18:12', 'updated_at' => '2023-10-25 11:18:12'),
            array('id' => '162', 'construction_site_id' => '803', 'construction_assigned_id' => '806', 'created_at' => '2023-10-25 11:32:49', 'updated_at' => '2023-10-25 11:32:49'),
            array('id' => '163', 'construction_site_id' => '520', 'construction_assigned_id' => '792', 'created_at' => '2023-10-25 13:24:43', 'updated_at' => '2023-10-25 13:24:43'),
            array('id' => '164', 'construction_site_id' => '520', 'construction_assigned_id' => '791', 'created_at' => '2023-10-25 13:24:52', 'updated_at' => '2023-10-25 13:24:52'),
            array('id' => '165', 'construction_site_id' => '831', 'construction_assigned_id' => '785', 'created_at' => '2023-10-25 13:25:33', 'updated_at' => '2023-10-25 13:25:33'),
            array('id' => '166', 'construction_site_id' => '831', 'construction_assigned_id' => '776', 'created_at' => '2023-10-25 13:26:01', 'updated_at' => '2023-10-25 13:26:01'),
            array('id' => '167', 'construction_site_id' => '831', 'construction_assigned_id' => '786', 'created_at' => '2023-10-25 13:26:34', 'updated_at' => '2023-10-25 13:26:34'),
            array('id' => '168', 'construction_site_id' => '831', 'construction_assigned_id' => '782', 'created_at' => '2023-10-25 13:26:45', 'updated_at' => '2023-10-25 13:26:45'),
            array('id' => '169', 'construction_site_id' => '831', 'construction_assigned_id' => '784', 'created_at' => '2023-10-25 13:27:01', 'updated_at' => '2023-10-25 13:27:01'),
            array('id' => '170', 'construction_site_id' => '831', 'construction_assigned_id' => '779', 'created_at' => '2023-10-25 13:27:10', 'updated_at' => '2023-10-25 13:27:10'),
            array('id' => '171', 'construction_site_id' => '831', 'construction_assigned_id' => '780', 'created_at' => '2023-10-25 13:27:23', 'updated_at' => '2023-10-25 13:27:23'),
            array('id' => '172', 'construction_site_id' => '831', 'construction_assigned_id' => '781', 'created_at' => '2023-10-25 13:27:35', 'updated_at' => '2023-10-25 13:27:35'),
            array('id' => '173', 'construction_site_id' => '831', 'construction_assigned_id' => '783', 'created_at' => '2023-10-25 13:27:42', 'updated_at' => '2023-10-25 13:27:42'),
            array('id' => '174', 'construction_site_id' => '831', 'construction_assigned_id' => '778', 'created_at' => '2023-10-25 13:27:50', 'updated_at' => '2023-10-25 13:27:50'),
            array('id' => '175', 'construction_site_id' => '657', 'construction_assigned_id' => '794', 'created_at' => '2023-10-25 13:33:01', 'updated_at' => '2023-10-25 13:33:01'),
            array('id' => '176', 'construction_site_id' => '657', 'construction_assigned_id' => '833', 'created_at' => '2023-10-25 13:33:07', 'updated_at' => '2023-10-25 13:33:07'),
            array('id' => '177', 'construction_site_id' => '765', 'construction_assigned_id' => '766', 'created_at' => '2023-10-25 14:46:27', 'updated_at' => '2023-10-25 14:46:27'),
            array('id' => '178', 'construction_site_id' => '831', 'construction_assigned_id' => '787', 'created_at' => '2023-10-25 14:58:02', 'updated_at' => '2023-10-25 14:58:02'),
            array('id' => '179', 'construction_site_id' => '759', 'construction_assigned_id' => '760', 'created_at' => '2023-10-25 15:32:11', 'updated_at' => '2023-10-25 15:32:11'),
            array('id' => '180', 'construction_site_id' => '759', 'construction_assigned_id' => '762', 'created_at' => '2023-10-25 15:32:28', 'updated_at' => '2023-10-25 15:32:28'),
            array('id' => '181', 'construction_site_id' => '759', 'construction_assigned_id' => '763', 'created_at' => '2023-10-25 15:32:37', 'updated_at' => '2023-10-25 15:32:37'),
            array('id' => '182', 'construction_site_id' => '759', 'construction_assigned_id' => '764', 'created_at' => '2023-10-25 15:32:49', 'updated_at' => '2023-10-25 15:32:49'),
            array('id' => '183', 'construction_site_id' => '759', 'construction_assigned_id' => '768', 'created_at' => '2023-10-25 15:32:55', 'updated_at' => '2023-10-25 15:32:55'),
            array('id' => '184', 'construction_site_id' => '759', 'construction_assigned_id' => '769', 'created_at' => '2023-10-25 15:33:03', 'updated_at' => '2023-10-25 15:33:03'),
            array('id' => '185', 'construction_site_id' => '759', 'construction_assigned_id' => '770', 'created_at' => '2023-10-25 15:33:13', 'updated_at' => '2023-10-25 15:33:13'),
            array('id' => '187', 'construction_site_id' => '759', 'construction_assigned_id' => '771', 'created_at' => '2023-10-25 15:33:32', 'updated_at' => '2023-10-25 15:33:32'),
            array('id' => '188', 'construction_site_id' => '759', 'construction_assigned_id' => '772', 'created_at' => '2023-10-25 15:34:59', 'updated_at' => '2023-10-25 15:34:59'),
            array('id' => '189', 'construction_site_id' => '759', 'construction_assigned_id' => '773', 'created_at' => '2023-10-25 15:35:10', 'updated_at' => '2023-10-25 15:35:10'),
            array('id' => '190', 'construction_site_id' => '759', 'construction_assigned_id' => '774', 'created_at' => '2023-10-25 15:35:16', 'updated_at' => '2023-10-25 15:35:16'),
            array('id' => '191', 'construction_site_id' => '759', 'construction_assigned_id' => '775', 'created_at' => '2023-10-25 15:35:24', 'updated_at' => '2023-10-25 15:35:24'),
            array('id' => '193', 'construction_site_id' => '642', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-26 11:06:41', 'updated_at' => '2023-10-26 11:06:41'),
            array('id' => '194', 'construction_site_id' => '810', 'construction_assigned_id' => '808', 'created_at' => '2023-10-26 14:22:50', 'updated_at' => '2023-10-26 14:22:50'),
            array('id' => '195', 'construction_site_id' => '810', 'construction_assigned_id' => '809', 'created_at' => '2023-10-26 14:23:03', 'updated_at' => '2023-10-26 14:23:03'),
            array('id' => '196', 'construction_site_id' => '965', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-26 16:46:52', 'updated_at' => '2023-10-26 16:46:52'),
            array('id' => '197', 'construction_site_id' => '965', 'construction_assigned_id' => '963', 'created_at' => '2023-10-26 16:47:13', 'updated_at' => '2023-10-26 16:47:13'),
            array('id' => '198', 'construction_site_id' => '965', 'construction_assigned_id' => '964', 'created_at' => '2023-10-26 16:47:26', 'updated_at' => '2023-10-26 16:47:26'),
            array('id' => '199', 'construction_site_id' => '625', 'construction_assigned_id' => '790', 'created_at' => '2023-10-27 08:26:29', 'updated_at' => '2023-10-27 08:26:29'),
            array('id' => '200', 'construction_site_id' => '922', 'construction_assigned_id' => '920', 'created_at' => '2023-10-27 13:06:49', 'updated_at' => '2023-10-27 13:06:49'),
            array('id' => '201', 'construction_site_id' => '922', 'construction_assigned_id' => '921', 'created_at' => '2023-10-27 13:07:02', 'updated_at' => '2023-10-27 13:07:02'),
            array('id' => '202', 'construction_site_id' => '956', 'construction_assigned_id' => '967', 'created_at' => '2023-10-27 13:57:18', 'updated_at' => '2023-10-27 13:57:18'),
            array('id' => '203', 'construction_site_id' => '799', 'construction_assigned_id' => '800', 'created_at' => '2023-10-27 18:00:31', 'updated_at' => '2023-10-27 18:00:31'),
            array('id' => '204', 'construction_site_id' => '799', 'construction_assigned_id' => '802', 'created_at' => '2023-10-27 18:00:37', 'updated_at' => '2023-10-27 18:00:37'),
            array('id' => '205', 'construction_site_id' => '934', 'construction_assigned_id' => '931', 'created_at' => '2023-10-29 08:27:58', 'updated_at' => '2023-10-29 08:27:58'),
            array('id' => '206', 'construction_site_id' => '968', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-30 04:03:37', 'updated_at' => '2023-10-30 04:03:37'),
            array('id' => '210', 'construction_site_id' => '969', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-30 14:16:25', 'updated_at' => '2023-10-30 14:16:25'),
            array('id' => '211', 'construction_site_id' => '969', 'construction_assigned_id' => '961', 'created_at' => '2023-10-30 14:16:48', 'updated_at' => '2023-10-30 14:16:48'),
            array('id' => '212', 'construction_site_id' => '969', 'construction_assigned_id' => '962', 'created_at' => '2023-10-30 14:16:54', 'updated_at' => '2023-10-30 14:16:54'),
            array('id' => '214', 'construction_site_id' => '627', 'construction_assigned_id' => '901', 'created_at' => '2023-10-30 15:47:42', 'updated_at' => '2023-10-30 15:47:42'),
            array('id' => '215', 'construction_site_id' => '854', 'construction_assigned_id' => '852', 'created_at' => '2023-10-30 15:50:18', 'updated_at' => '2023-10-30 15:50:18'),
            array('id' => '216', 'construction_site_id' => '854', 'construction_assigned_id' => '853', 'created_at' => '2023-10-30 15:50:26', 'updated_at' => '2023-10-30 15:50:26'),
            array('id' => '217', 'construction_site_id' => '976', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-31 03:34:34', 'updated_at' => '2023-10-31 03:34:34'),
            array('id' => '218', 'construction_site_id' => '977', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-31 05:46:49', 'updated_at' => '2023-10-31 05:46:49'),
            array('id' => '219', 'construction_site_id' => '978', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-31 05:48:57', 'updated_at' => '2023-10-31 05:48:57'),
            array('id' => '220', 'construction_site_id' => '979', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-31 07:04:25', 'updated_at' => '2023-10-31 07:04:25'),
            array('id' => '221', 'construction_site_id' => '981', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-31 07:19:43', 'updated_at' => '2023-10-31 07:19:43'),
            array('id' => '222', 'construction_site_id' => '982', 'construction_assigned_id' => NULL, 'created_at' => '2023-10-31 08:15:24', 'updated_at' => '2023-10-31 08:15:24'),
            array('id' => '223', 'construction_site_id' => '982', 'construction_assigned_id' => '972', 'created_at' => '2023-10-31 08:16:27', 'updated_at' => '2023-10-31 08:16:27'),
            array('id' => '224', 'construction_site_id' => '982', 'construction_assigned_id' => '973', 'created_at' => '2023-10-31 08:16:34', 'updated_at' => '2023-10-31 08:16:34'),
            array('id' => '225', 'construction_site_id' => '982', 'construction_assigned_id' => '970', 'created_at' => '2023-10-31 08:16:47', 'updated_at' => '2023-10-31 08:16:47'),
            array('id' => '227', 'construction_site_id' => '982', 'construction_assigned_id' => '971', 'created_at' => '2023-10-31 08:18:04', 'updated_at' => '2023-10-31 08:18:04'),
            array('id' => '228', 'construction_site_id' => '351', 'construction_assigned_id' => '966', 'created_at' => '2023-10-31 15:35:20', 'updated_at' => '2023-10-31 15:35:20'),
            array('id' => '229', 'construction_site_id' => '351', 'construction_assigned_id' => '975', 'created_at' => '2023-10-31 15:35:28', 'updated_at' => '2023-10-31 15:35:28'),
            array('id' => '230', 'construction_site_id' => '351', 'construction_assigned_id' => '984', 'created_at' => '2023-10-31 15:35:56', 'updated_at' => '2023-10-31 15:35:56'),
            array('id' => '231', 'construction_site_id' => '351', 'construction_assigned_id' => '974', 'created_at' => '2023-10-31 15:36:04', 'updated_at' => '2023-10-31 15:36:04'),
            array('id' => '232', 'construction_site_id' => '351', 'construction_assigned_id' => '983', 'created_at' => '2023-10-31 15:36:11', 'updated_at' => '2023-10-31 15:36:11'),
            array('id' => '233', 'construction_site_id' => '351', 'construction_assigned_id' => '985', 'created_at' => '2023-10-31 15:49:57', 'updated_at' => '2023-10-31 15:49:57'),
            array('id' => '234', 'construction_site_id' => '960', 'construction_assigned_id' => '958', 'created_at' => '2023-10-31 16:04:21', 'updated_at' => '2023-10-31 16:04:21'),
            array('id' => '235', 'construction_site_id' => '960', 'construction_assigned_id' => '959', 'created_at' => '2023-10-31 16:04:29', 'updated_at' => '2023-10-31 16:04:29'),
            array('id' => '236', 'construction_site_id' => '351', 'construction_assigned_id' => '986', 'created_at' => '2023-10-31 16:23:57', 'updated_at' => '2023-10-31 16:23:57'),
            array('id' => '237', 'construction_site_id' => '982', 'construction_assigned_id' => '987', 'created_at' => '2023-10-31 18:03:03', 'updated_at' => '2023-10-31 18:03:03'),
            array('id' => '238', 'construction_site_id' => '934', 'construction_assigned_id' => '933', 'created_at' => '2023-11-02 08:41:21', 'updated_at' => '2023-11-02 08:41:21'),
            array('id' => '239', 'construction_site_id' => '934', 'construction_assigned_id' => '932', 'created_at' => '2023-11-02 08:41:42', 'updated_at' => '2023-11-02 08:41:42'),
            array('id' => '240', 'construction_site_id' => '934', 'construction_assigned_id' => '935', 'created_at' => '2023-11-02 08:41:48', 'updated_at' => '2023-11-02 08:41:48'),
            array('id' => '241', 'construction_site_id' => '824', 'construction_assigned_id' => '822', 'created_at' => '2023-11-02 13:37:42', 'updated_at' => '2023-11-02 13:37:42'),
            array('id' => '242', 'construction_site_id' => '824', 'construction_assigned_id' => '823', 'created_at' => '2023-11-02 13:37:56', 'updated_at' => '2023-11-02 13:37:56'),
            array('id' => '243', 'construction_site_id' => '881', 'construction_assigned_id' => '880', 'created_at' => '2023-11-02 14:03:00', 'updated_at' => '2023-11-02 14:03:00'),
            array('id' => '244', 'construction_site_id' => '664', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-02 15:11:03', 'updated_at' => '2023-11-02 15:11:03'),
            array('id' => '245', 'construction_site_id' => '733', 'construction_assigned_id' => '732', 'created_at' => '2023-11-02 16:57:22', 'updated_at' => '2023-11-02 16:57:22'),
            array('id' => '246', 'construction_site_id' => '733', 'construction_assigned_id' => '988', 'created_at' => '2023-11-02 16:57:30', 'updated_at' => '2023-11-02 16:57:30'),
            array('id' => '247', 'construction_site_id' => '736', 'construction_assigned_id' => '735', 'created_at' => '2023-11-03 07:32:52', 'updated_at' => '2023-11-03 07:32:52'),
            array('id' => '248', 'construction_site_id' => '817', 'construction_assigned_id' => '816', 'created_at' => '2023-11-03 08:00:01', 'updated_at' => '2023-11-03 08:00:01'),
            array('id' => '249', 'construction_site_id' => '817', 'construction_assigned_id' => '819', 'created_at' => '2023-11-03 09:25:14', 'updated_at' => '2023-11-03 09:25:14'),
            array('id' => '251', 'construction_site_id' => '351', 'construction_assigned_id' => '989', 'created_at' => '2023-11-03 14:29:00', 'updated_at' => '2023-11-03 14:29:00'),
            array('id' => '252', 'construction_site_id' => '826', 'construction_assigned_id' => '826', 'created_at' => '2023-11-04 11:11:17', 'updated_at' => '2023-11-04 11:11:17'),
            array('id' => '254', 'construction_site_id' => '611', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 04:57:51', 'updated_at' => '2023-11-06 04:57:51'),
            array('id' => '255', 'construction_site_id' => '612', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 04:57:56', 'updated_at' => '2023-11-06 04:57:56'),
            array('id' => '256', 'construction_site_id' => '813', 'construction_assigned_id' => '815', 'created_at' => '2023-11-06 05:25:20', 'updated_at' => '2023-11-06 05:25:20'),
            array('id' => '257', 'construction_site_id' => '812', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:27:43', 'updated_at' => '2023-11-06 05:27:43'),
            array('id' => '258', 'construction_site_id' => '744', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:29:13', 'updated_at' => '2023-11-06 05:29:13'),
            array('id' => '259', 'construction_site_id' => '751', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:29:45', 'updated_at' => '2023-11-06 05:29:45'),
            array('id' => '260', 'construction_site_id' => '749', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:29:53', 'updated_at' => '2023-11-06 05:29:53'),
            array('id' => '261', 'construction_site_id' => '750', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:29:59', 'updated_at' => '2023-11-06 05:29:59'),
            array('id' => '262', 'construction_site_id' => '747', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:30:11', 'updated_at' => '2023-11-06 05:30:11'),
            array('id' => '263', 'construction_site_id' => '766', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:32:45', 'updated_at' => '2023-11-06 05:32:45'),
            array('id' => '264', 'construction_site_id' => '802', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:33:33', 'updated_at' => '2023-11-06 05:33:33'),
            array('id' => '265', 'construction_site_id' => '800', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 05:34:17', 'updated_at' => '2023-11-06 05:34:17'),
            array('id' => '266', 'construction_site_id' => '885', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 06:03:27', 'updated_at' => '2023-11-06 06:03:27'),
            array('id' => '267', 'construction_site_id' => '846', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 07:47:12', 'updated_at' => '2023-11-06 07:47:12'),
            array('id' => '268', 'construction_site_id' => '879', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 08:02:14', 'updated_at' => '2023-11-06 08:02:14'),
            array('id' => '269', 'construction_site_id' => '739', 'construction_assigned_id' => '742', 'created_at' => '2023-11-06 09:40:37', 'updated_at' => '2023-11-06 09:40:37'),
            array('id' => '270', 'construction_site_id' => '739', 'construction_assigned_id' => '738', 'created_at' => '2023-11-06 09:40:49', 'updated_at' => '2023-11-06 09:40:49'),
            array('id' => '271', 'construction_site_id' => '990', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-06 10:50:04', 'updated_at' => '2023-11-06 10:50:04'),
            array('id' => '272', 'construction_site_id' => '990', 'construction_assigned_id' => '6', 'created_at' => '2023-11-06 10:50:25', 'updated_at' => '2023-11-06 10:50:25'),
            array('id' => '273', 'construction_site_id' => '730', 'construction_assigned_id' => '729', 'created_at' => '2023-11-06 12:05:00', 'updated_at' => '2023-11-06 12:05:00'),
            array('id' => '274', 'construction_site_id' => '730', 'construction_assigned_id' => '864', 'created_at' => '2023-11-06 12:05:07', 'updated_at' => '2023-11-06 12:05:07'),
            array('id' => '275', 'construction_site_id' => '854', 'construction_assigned_id' => '850', 'created_at' => '2023-11-06 17:00:57', 'updated_at' => '2023-11-06 17:00:57'),
            array('id' => '276', 'construction_site_id' => '854', 'construction_assigned_id' => '851', 'created_at' => '2023-11-06 17:01:18', 'updated_at' => '2023-11-06 17:01:18'),
            array('id' => '277', 'construction_site_id' => '871', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-07 07:12:51', 'updated_at' => '2023-11-07 07:12:51'),
            array('id' => '278', 'construction_site_id' => '836', 'construction_assigned_id' => '834', 'created_at' => '2023-11-07 07:27:03', 'updated_at' => '2023-11-07 07:27:03'),
            array('id' => '279', 'construction_site_id' => '836', 'construction_assigned_id' => '835', 'created_at' => '2023-11-07 07:27:10', 'updated_at' => '2023-11-07 07:27:10'),
            array('id' => '280', 'construction_site_id' => '868', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-07 07:36:29', 'updated_at' => '2023-11-07 07:36:29'),
            array('id' => '281', 'construction_site_id' => '870', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-07 07:38:04', 'updated_at' => '2023-11-07 07:38:04'),
            array('id' => '282', 'construction_site_id' => '874', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-07 07:39:26', 'updated_at' => '2023-11-07 07:39:26'),
            array('id' => '283', 'construction_site_id' => '869', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-07 07:44:55', 'updated_at' => '2023-11-07 07:44:55'),
            array('id' => '284', 'construction_site_id' => '617', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-07 09:08:28', 'updated_at' => '2023-11-07 09:08:28'),
            array('id' => '285', 'construction_site_id' => '614', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-07 09:08:33', 'updated_at' => '2023-11-07 09:08:33'),
            array('id' => '286', 'construction_site_id' => '615', 'construction_assigned_id' => NULL, 'created_at' => '2023-11-07 09:08:38', 'updated_at' => '2023-11-07 09:08:38')
        );
        $count = 0;
        $uniqueConstructionCondominis = ConstructionCondomini::distinct()->select('construction_site_id', 'construction_assigned_id')->get();
        // $construction_condominis = ConstructionCondomini::whereColumn('construction_assigned_id', 'construction_site_id')->get();
        // $construction_condominis = ConstructionCondomini::whereColumn('construction_assigned_id', '=', 'construction_site_id')->get();
        // $construction_condominis = ConstructionCondomini::whereColumn('construction_site_id', 'construction_assigned_id')->get();
        //   foreach(  $uniqueConstructionCondominis as $uniqueConstructionCondomini){
        //         dd($uniqueConstructionCondomini);
        //   }

        $allUniqueConstructionCondominis = ConstructionCondomini::distinct()->get();
        $uniqueRecords = $allUniqueConstructionCondominis->unique('construction_assigned_id');

        $construction_condominis = $uniqueRecords->toArray();


        $filteredIds = $uniqueRecords->pluck('id')->toArray();



        // Delete records that are not in the filtered collection
        $data32  =     ConstructionCondomini::whereNotIn('id', $filteredIds)->delete();

        foreach ($construction_condominis as $construction_condomini) {
            $check =  ConstructionCondomini::where('construction_site_id', $construction_condomini['construction_site_id'])->where('construction_assigned_id', $construction_condomini['construction_assigned_id'])->first();
            $data = ConstructionCondomini::where('construction_assigned_id', $construction_condomini['construction_site_id'])->first();
            if ($data != null) {
                dd($data);
                //  $delete =  $data->delete();
                // if($delete){
                //     echo 'total deleted records are' . $count;
                // }
            }

            if ($construction_condomini['construction_assigned_id'] !== null && $check != null && $data == null) {

                // $store =  ConstructionCondomini::create(['construction_site_id' => $construction_condomini['construction_site_id'], 'construction_assigned_id' => $construction_condomini['construction_assigned_id']]);
                // if($store){
                $count++;
                // }

            }
        }
        // echo 'total records are' . $count;
    }

    public function recreateInfissi()
    {

        $assignedImpresa = array(
            array('assignedid' => '11', 'fk_cantiere' => '684', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => '9000', 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '12', 'fk_cantiere' => '422', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => '500', 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '13', 'fk_cantiere' => '624', 'assign_infissi' => NULL, 'infissi_amount' => '2', 'idraulico' => NULL, 'idraulico_amount' => '23', 'elettricista' => NULL, 'elettricista_amount' => '323', 'edile' => NULL, 'edile_amount' => '90', 'edile2' => NULL, 'edile_amount2' => '12', 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => '23', 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '14', 'fk_cantiere' => '29', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => '9900', 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => '5000', 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-04-29', 'idraulico_date' => '2022-04-29', 'elettricista_date' => '2022-04-29', 'edile_date' => '2022-04-29', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '15', 'fk_cantiere' => '550', 'assign_infissi' => '747', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '16', 'fk_cantiere' => '449', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => '696', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-17', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '17', 'fk_cantiere' => '454', 'assign_infissi' => NULL, 'infissi_amount' => '900', 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => '7890', 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-05-29', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '18', 'fk_cantiere' => '150', 'assign_infissi' => NULL, 'infissi_amount' => '100000', 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-05-29', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '19', 'fk_cantiere' => '207', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-04-29', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '20', 'fk_cantiere' => '706', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => '865', 'elettricista_amount' => NULL, 'edile' => '669', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => '696', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-06-09', 'idraulico_date' => '2022-08-03', 'elettricista_date' => '2023-11-06', 'edile_date' => '2022-05-09', 'edile2_date' => NULL, 'foto_date' => '2023-03-02', 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '21', 'fk_cantiere' => '724', 'assign_infissi' => '388', 'infissi_amount' => '0', 'idraulico' => '527', 'idraulico_amount' => '0', 'elettricista' => '532', 'elettricista_amount' => '0', 'edile' => '525', 'edile_amount' => '0', 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => '519', 'fotovoltaico_amount' => '0', 'infissi_date' => '2022-05-10', 'idraulico_date' => '2022-05-10', 'elettricista_date' => '2022-05-10', 'edile_date' => '2022-05-10', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '22', 'fk_cantiere' => '491', 'assign_infissi' => '749', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '23', 'fk_cantiere' => '730', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '673', 'idraulico_amount' => NULL, 'elettricista' => '666', 'elettricista_amount' => NULL, 'edile' => '686', 'edile_amount' => NULL, 'edile2' => '689', 'edile_amount2' => NULL, 'fotovoltaicoid' => '696', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-16', 'idraulico_date' => '2022-05-17', 'elettricista_date' => '2022-05-16', 'edile_date' => '2022-05-17', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '24', 'fk_cantiere' => '705', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-31', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-01-16', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '25', 'fk_cantiere' => '731', 'assign_infissi' => '639', 'infissi_amount' => '0', 'idraulico' => '672', 'idraulico_amount' => '0', 'elettricista' => '658', 'elettricista_amount' => '0', 'edile' => '689', 'edile_amount' => '0', 'edile2' => '689', 'edile_amount2' => '0', 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => '0', 'infissi_date' => '2022-05-13', 'idraulico_date' => '2022-05-13', 'elettricista_date' => '2022-05-13', 'edile_date' => '2022-05-13', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '26', 'fk_cantiere' => '729', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '672', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-05-17', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '27', 'fk_cantiere' => '275', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-17', 'idraulico_date' => '2023-01-26', 'elettricista_date' => '2022-12-12', 'edile_date' => '2022-08-09', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '28', 'fk_cantiere' => '57', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '29', 'fk_cantiere' => '240', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '30', 'fk_cantiere' => '251', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '31', 'fk_cantiere' => '314', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '32', 'fk_cantiere' => '564', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '33', 'fk_cantiere' => '169', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '34', 'fk_cantiere' => '601', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-17', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-30', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '35', 'fk_cantiere' => '573', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '36', 'fk_cantiere' => '570', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '37', 'fk_cantiere' => '580', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '38', 'fk_cantiere' => '295', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '39', 'fk_cantiere' => '276', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '40', 'fk_cantiere' => '278', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '41', 'fk_cantiere' => '378', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '42', 'fk_cantiere' => '337', 'assign_infissi' => '765', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '43', 'fk_cantiere' => '335', 'assign_infissi' => '765', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '44', 'fk_cantiere' => '420', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '45', 'fk_cantiere' => '557', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '46', 'fk_cantiere' => '558', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '47', 'fk_cantiere' => '613', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '48', 'fk_cantiere' => '612', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '49', 'fk_cantiere' => '628', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '50', 'fk_cantiere' => '370', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '51', 'fk_cantiere' => '626', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '52', 'fk_cantiere' => '649', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '53', 'fk_cantiere' => '641', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '54', 'fk_cantiere' => '642', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '55', 'fk_cantiere' => '632', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '56', 'fk_cantiere' => '633', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '57', 'fk_cantiere' => '690', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '58', 'fk_cantiere' => '691', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '59', 'fk_cantiere' => '279', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '60', 'fk_cantiere' => '296', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '61', 'fk_cantiere' => '297', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '62', 'fk_cantiere' => '298', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '63', 'fk_cantiere' => '672', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '64', 'fk_cantiere' => '671', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '65', 'fk_cantiere' => '627', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '66', 'fk_cantiere' => '588', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-17', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '67', 'fk_cantiere' => '675', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '673', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '663', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => '696', 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-05-18', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '68', 'fk_cantiere' => '470', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-02', 'idraulico_date' => '2022-08-02', 'elettricista_date' => '2022-08-02', 'edile_date' => '2022-08-02', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '69', 'fk_cantiere' => '427', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '70', 'fk_cantiere' => '676', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '71', 'fk_cantiere' => '300', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '72', 'fk_cantiere' => '534', 'assign_infissi' => '643', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '2022-12-13', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '73', 'fk_cantiere' => '163', 'assign_infissi' => '642', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '74', 'fk_cantiere' => '426', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-05', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-09-25', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '578'),
            array('assignedid' => '75', 'fk_cantiere' => '445', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-01', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '76', 'fk_cantiere' => '526', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '77', 'fk_cantiere' => '264', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '78', 'fk_cantiere' => '614', 'assign_infissi' => '643', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '79', 'fk_cantiere' => '345', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '80', 'fk_cantiere' => '310', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '683', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '81', 'fk_cantiere' => '493', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '669', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-08-02', 'elettricista_date' => NULL, 'edile_date' => '2022-08-01', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '82', 'fk_cantiere' => '442', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '83', 'fk_cantiere' => '553', 'assign_infissi' => '747', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '84', 'fk_cantiere' => '409', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '85', 'fk_cantiere' => '657', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '86', 'fk_cantiere' => '270', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-26', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '87', 'fk_cantiere' => '461', 'assign_infissi' => '795', 'infissi_amount' => NULL, 'idraulico' => '796', 'idraulico_amount' => NULL, 'elettricista' => '788', 'elettricista_amount' => NULL, 'edile' => '787', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-03', 'idraulico_date' => '2022-08-03', 'elettricista_date' => '2022-08-02', 'edile_date' => '2022-08-02', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '88', 'fk_cantiere' => '535', 'assign_infissi' => '643', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '2022-12-13', 'edile_date' => '2022-10-05', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '89', 'fk_cantiere' => '484', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '90', 'fk_cantiere' => '459', 'assign_infissi' => '806', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '692', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-30', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '91', 'fk_cantiere' => '418', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '92', 'fk_cantiere' => '458', 'assign_infissi' => '807', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-31', 'idraulico_date' => '2022-09-07', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '93', 'fk_cantiere' => '253', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '672', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '669', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '94', 'fk_cantiere' => '411', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-02', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '95', 'fk_cantiere' => '265', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '96', 'fk_cantiere' => '485', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-08-02', 'elettricista_date' => '2022-08-02', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '97', 'fk_cantiere' => '697', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '98', 'fk_cantiere' => '574', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '99', 'fk_cantiere' => '532', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2023-01-26', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '100', 'fk_cantiere' => '681', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '101', 'fk_cantiere' => '368', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-13', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-12-15', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '102', 'fk_cantiere' => '425', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '103', 'fk_cantiere' => '319', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => '669', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '104', 'fk_cantiere' => '389', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => '678', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '669', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-08-09', 'elettricista_date' => '0000-00-00', 'edile_date' => '2022-08-08', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '105', 'fk_cantiere' => '374', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '106', 'fk_cantiere' => '175', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '107', 'fk_cantiere' => '261', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '108', 'fk_cantiere' => '178', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-06-28', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '109', 'fk_cantiere' => '739', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '110', 'fk_cantiere' => '260', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-08-05', 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '111', 'fk_cantiere' => '322', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '682', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '112', 'fk_cantiere' => '495', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-08-02', 'elettricista_date' => '2022-08-02', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '113', 'fk_cantiere' => '215', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-30', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '114', 'fk_cantiere' => '353', 'assign_infissi' => '807', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '669', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-31', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-09-08', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '115', 'fk_cantiere' => '377', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '116', 'fk_cantiere' => '440', 'assign_infissi' => '642', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '117', 'fk_cantiere' => '223', 'assign_infissi' => '643', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '692', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '118', 'fk_cantiere' => '523', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => '793', 'idraulico_amount' => NULL, 'elettricista' => '687', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-08-03', 'elettricista_date' => '2022-08-03', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '119', 'fk_cantiere' => '155', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '683', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '120', 'fk_cantiere' => '268', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '121', 'fk_cantiere' => '189', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '122', 'fk_cantiere' => '210', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '123', 'fk_cantiere' => '407', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '124', 'fk_cantiere' => '287', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '2022-11-23', 'edile_date' => '2022-05-18', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '125', 'fk_cantiere' => '206', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-08-02', 'elettricista_date' => '0000-00-00', 'edile_date' => '2022-08-09', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '126', 'fk_cantiere' => '655', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => '693', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2023-01-26', 'elettricista_date' => '2022-08-09', 'edile_date' => '2022-08-09', 'edile2_date' => '2022-08-09', 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '127', 'fk_cantiere' => '393', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '674', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '2022-08-11', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '128', 'fk_cantiere' => '531', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '810', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => '693', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-09-07', 'elettricista_date' => '2022-08-09', 'edile_date' => '2022-08-09', 'edile2_date' => '2022-08-09', 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '129', 'fk_cantiere' => '447', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '130', 'fk_cantiere' => '272', 'assign_infissi' => '643', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '131', 'fk_cantiere' => '448', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '132', 'fk_cantiere' => '584', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-29', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '576'),
            array('assignedid' => '133', 'fk_cantiere' => '302', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '2022-05-18', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '134', 'fk_cantiere' => '301', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '135', 'fk_cantiere' => '656', 'assign_infissi' => '643', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '136', 'fk_cantiere' => '224', 'assign_infissi' => '709', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '137', 'fk_cantiere' => '473', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '672', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-11', 'idraulico_date' => '0000-00-00', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '138', 'fk_cantiere' => '437', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '139', 'fk_cantiere' => '412', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-09-14', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '140', 'fk_cantiere' => '506', 'assign_infissi' => '646', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '141', 'fk_cantiere' => '622', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-05', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '142', 'fk_cantiere' => '292', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '683', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '143', 'fk_cantiere' => '585', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-05', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '144', 'fk_cantiere' => '512', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '145', 'fk_cantiere' => '154', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '146', 'fk_cantiere' => '441', 'assign_infissi' => '750', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '147', 'fk_cantiere' => '214', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-30', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '148', 'fk_cantiere' => '305', 'assign_infissi' => '806', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-01', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '149', 'fk_cantiere' => '504', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => NULL, 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '150', 'fk_cantiere' => '415', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '151', 'fk_cantiere' => '738', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '672', 'idraulico_amount' => NULL, 'elettricista' => '658', 'elettricista_amount' => NULL, 'edile' => '686', 'edile_amount' => NULL, 'edile2' => '689', 'edile_amount2' => NULL, 'fotovoltaicoid' => '696', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '2022-05-18', 'elettricista_date' => '2022-05-18', 'edile_date' => '2022-05-18', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '152', 'fk_cantiere' => '685', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '153', 'fk_cantiere' => '434', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '154', 'fk_cantiere' => '621', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => NULL, 'edile_date' => '2022-12-15', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '155', 'fk_cantiere' => '529', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-05-18', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '156', 'fk_cantiere' => '306', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2023-01-24', 'elettricista_date' => NULL, 'edile_date' => '2023-01-25', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '157', 'fk_cantiere' => '408', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-30', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '158', 'fk_cantiere' => '213', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '159', 'fk_cantiere' => '589', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '160', 'fk_cantiere' => '375', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '161', 'fk_cantiere' => '688', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '162', 'fk_cantiere' => '313', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '163', 'fk_cantiere' => '530', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '164', 'fk_cantiere' => '133', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '165', 'fk_cantiere' => '579', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '166', 'fk_cantiere' => '243', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '804', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-26', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '167', 'fk_cantiere' => '608', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => '2023-01-24', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '168', 'fk_cantiere' => '134', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '169', 'fk_cantiere' => '654', 'assign_infissi' => '642', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '170', 'fk_cantiere' => '388', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '797', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-03', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '171', 'fk_cantiere' => '582', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '692', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-05-18', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '172', 'fk_cantiere' => '460', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '173', 'fk_cantiere' => '277', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '174', 'fk_cantiere' => '549', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '175', 'fk_cantiere' => '339', 'assign_infissi' => '750', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '682', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-05-18', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '176', 'fk_cantiere' => '147', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-05-18', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '177', 'fk_cantiere' => '99', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '178', 'fk_cantiere' => '537', 'assign_infissi' => '709', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '785', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-02', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '179', 'fk_cantiere' => '188', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '180', 'fk_cantiere' => '468', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '181', 'fk_cantiere' => '616', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '182', 'fk_cantiere' => '372', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '183', 'fk_cantiere' => '382', 'assign_infissi' => '646', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-09', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '184', 'fk_cantiere' => '577', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-02', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '185', 'fk_cantiere' => '648', 'assign_infissi' => '806', 'infissi_amount' => NULL, 'idraulico' => '774', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-31', 'idraulico_date' => '0000-00-00', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '186', 'fk_cantiere' => '156', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '187', 'fk_cantiere' => '222', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '683', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '188', 'fk_cantiere' => '498', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-04', 'idraulico_date' => NULL, 'elettricista_date' => '2022-12-13', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '189', 'fk_cantiere' => '503', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-04', 'idraulico_date' => NULL, 'elettricista_date' => '2022-12-13', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '190', 'fk_cantiere' => '536', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '682', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-12-13', 'edile_date' => '2022-05-18', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '191', 'fk_cantiere' => '35', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-18', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '192', 'fk_cantiere' => '259', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-08-05', 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '193', 'fk_cantiere' => '575', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '194', 'fk_cantiere' => '451', 'assign_infissi' => '646', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '195', 'fk_cantiere' => '258', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-08-05', 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '196', 'fk_cantiere' => '309', 'assign_infissi' => '748', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '197', 'fk_cantiere' => '661', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '198', 'fk_cantiere' => '662', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '199', 'fk_cantiere' => '533', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '200', 'fk_cantiere' => '563', 'assign_infissi' => '747', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '201', 'fk_cantiere' => '545', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-05', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-01-16', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '202', 'fk_cantiere' => '723', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '203', 'fk_cantiere' => '274', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '204', 'fk_cantiere' => '701', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '205', 'fk_cantiere' => '571', 'assign_infissi' => '747', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '206', 'fk_cantiere' => '560', 'assign_infissi' => '781', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-09-07', 'elettricista_date' => NULL, 'edile_date' => '2022-09-16', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '207', 'fk_cantiere' => '352', 'assign_infissi' => '752', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '208', 'fk_cantiere' => '700', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '209', 'fk_cantiere' => '28', 'assign_infissi' => '642', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '210', 'fk_cantiere' => '497', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '211', 'fk_cantiere' => '303', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-08-02', 'elettricista_date' => '2022-08-02', 'edile_date' => '2022-09-14', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '212', 'fk_cantiere' => '443', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '213', 'fk_cantiere' => '72', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '214', 'fk_cantiere' => '494', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => '2022-11-09', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '215', 'fk_cantiere' => '548', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '216', 'fk_cantiere' => '450', 'assign_infissi' => '753', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '217', 'fk_cantiere' => '521', 'assign_infissi' => '646', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => '2022-08-22', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '218', 'fk_cantiere' => '667', 'assign_infissi' => '754', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '219', 'fk_cantiere' => '562', 'assign_infissi' => '747', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '220', 'fk_cantiere' => '288', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '221', 'fk_cantiere' => '744', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-04', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '222', 'fk_cantiere' => '618', 'assign_infissi' => '755', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '223', 'fk_cantiere' => '472', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '224', 'fk_cantiere' => '587', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => '602', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-03', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '225', 'fk_cantiere' => '748', 'assign_infissi' => '709', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-21', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '226', 'fk_cantiere' => NULL, 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '227', 'fk_cantiere' => NULL, 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '228', 'fk_cantiere' => '749', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => '671', 'elettricista_amount' => NULL, 'edile' => '683', 'edile_amount' => NULL, 'edile2' => '686', 'edile_amount2' => NULL, 'fotovoltaicoid' => '696', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-23', 'idraulico_date' => '2022-06-07', 'elettricista_date' => '2022-07-04', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '229', 'fk_cantiere' => '476', 'assign_infissi' => '807', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-01', 'idraulico_date' => '2022-08-05', 'elettricista_date' => '2022-10-05', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '230', 'fk_cantiere' => '750', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-05-23', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '231', 'fk_cantiere' => '751', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-05-23', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '232', 'fk_cantiere' => '752', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-05-24', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '233', 'fk_cantiere' => '552', 'assign_infissi' => '747', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '234', 'fk_cantiere' => '554', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '235', 'fk_cantiere' => '592', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '236', 'fk_cantiere' => '593', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '237', 'fk_cantiere' => '576', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '238', 'fk_cantiere' => '645', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '239', 'fk_cantiere' => '646', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '240', 'fk_cantiere' => '578', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '241', 'fk_cantiere' => '638', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '242', 'fk_cantiere' => '590', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '243', 'fk_cantiere' => '591', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '244', 'fk_cantiere' => '501', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-30', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '245', 'fk_cantiere' => '500', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-30', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '246', 'fk_cantiere' => '644', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '247', 'fk_cantiere' => '185', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '248', 'fk_cantiere' => '469', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-16', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '249', 'fk_cantiere' => '184', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '250', 'fk_cantiere' => '466', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '794', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-03', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '251', 'fk_cantiere' => '567', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '252', 'fk_cantiere' => '566', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '253', 'fk_cantiere' => '565', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '254', 'fk_cantiere' => '487', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => '680', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '669', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '2022-08-02', 'elettricista_date' => '2022-08-02', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '602', 'direttore' => '0'),
            array('assignedid' => '255', 'fk_cantiere' => '763', 'assign_infissi' => '752', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '658', 'elettricista_amount' => NULL, 'edile' => '689', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '2022-06-07', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '256', 'fk_cantiere' => '193', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '2022-06-07', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '257', 'fk_cantiere' => '373', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => '0000-00-00', 'edile_date' => '2022-09-14', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '258', 'fk_cantiere' => '743', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => '662', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '259', 'fk_cantiere' => '758', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '677', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '260', 'fk_cantiere' => '647', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => '693', 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '261', 'fk_cantiere' => '809', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '262', 'fk_cantiere' => '97', 'assign_infissi' => '768', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '263', 'fk_cantiere' => '757', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '264', 'fk_cantiere' => '196', 'assign_infissi' => '769', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '265', 'fk_cantiere' => '541', 'assign_infissi' => '807', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-16', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '266', 'fk_cantiere' => '522', 'assign_infissi' => '646', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '267', 'fk_cantiere' => '340', 'assign_infissi' => '766', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '268', 'fk_cantiere' => '814', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '685', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '577', 'direttore' => '0'),
            array('assignedid' => '269', 'fk_cantiere' => '815', 'assign_infissi' => '752', 'infissi_amount' => NULL, 'idraulico' => '673', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '689', 'edile_amount' => NULL, 'edile2' => '679', 'edile_amount2' => NULL, 'fotovoltaicoid' => '696', 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '577', 'direttore' => '0'),
            array('assignedid' => '270', 'fk_cantiere' => '812', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '271', 'fk_cantiere' => '780', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '272', 'fk_cantiere' => '816', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '273', 'fk_cantiere' => '583', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-16', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-09-14', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '274', 'fk_cantiere' => '817', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '275', 'fk_cantiere' => '818', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '276', 'fk_cantiere' => '819', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '658', 'elettricista_amount' => NULL, 'edile' => '689', 'edile_amount' => NULL, 'edile2' => '689', 'edile_amount2' => NULL, 'fotovoltaicoid' => '704', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '578', 'direttore' => '0'),
            array('assignedid' => '277', 'fk_cantiere' => '820', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '658', 'elettricista_amount' => NULL, 'edile' => '689', 'edile_amount' => NULL, 'edile2' => '689', 'edile_amount2' => NULL, 'fotovoltaicoid' => '704', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '278', 'fk_cantiere' => '821', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '685', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => '2022-07-15', 'elettricista_date' => '2022-07-15', 'edile_date' => '2022-07-15', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '279', 'fk_cantiere' => '822', 'assign_infissi' => '754', 'infissi_amount' => NULL, 'idraulico' => '672', 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '688', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-15', 'idraulico_date' => '2022-07-15', 'elettricista_date' => '2022-07-15', 'edile_date' => '2022-07-15', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '280', 'fk_cantiere' => '823', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '658', 'elettricista_amount' => NULL, 'edile' => '689', 'edile_amount' => NULL, 'edile2' => '693', 'edile_amount2' => NULL, 'fotovoltaicoid' => '704', 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-16', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '281', 'fk_cantiere' => '824', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-16', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '282', 'fk_cantiere' => '692', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '283', 'fk_cantiere' => '216', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-30', 'idraulico_date' => NULL, 'elettricista_date' => '2023-01-24', 'edile_date' => '2023-01-02', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '284', 'fk_cantiere' => NULL, 'assign_infissi' => '753', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '285', 'fk_cantiere' => NULL, 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '286', 'fk_cantiere' => NULL, 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '287', 'fk_cantiere' => NULL, 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '288', 'fk_cantiere' => NULL, 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => '704', 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '289', 'fk_cantiere' => '325', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '290', 'fk_cantiere' => NULL, 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '291', 'fk_cantiere' => NULL, 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '292', 'fk_cantiere' => NULL, 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '293', 'fk_cantiere' => NULL, 'assign_infissi' => '753', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '294', 'fk_cantiere' => NULL, 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '295', 'fk_cantiere' => NULL, 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '296', 'fk_cantiere' => NULL, 'assign_infissi' => '763', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '297', 'fk_cantiere' => NULL, 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '298', 'fk_cantiere' => NULL, 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '299', 'fk_cantiere' => '351', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => '798', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-08-05', 'elettricista_date' => NULL, 'edile_date' => '2022-08-03', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '300', 'fk_cantiere' => '792', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '301', 'fk_cantiere' => '421', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-03', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-09-14', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '302', 'fk_cantiere' => '456', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '303', 'fk_cantiere' => '546', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-05', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-01-16', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '304', 'fk_cantiere' => '556', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '305', 'fk_cantiere' => '615', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-09-14', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '306', 'fk_cantiere' => '598', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '307', 'fk_cantiere' => '640', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '308', 'fk_cantiere' => '542', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '309', 'fk_cantiere' => '525', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '310', 'fk_cantiere' => '702', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-01-16', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '311', 'fk_cantiere' => '666', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '312', 'fk_cantiere' => '524', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '313', 'fk_cantiere' => '572', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '693', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-08-09', 'elettricista_date' => '2022-08-09', 'edile_date' => '2022-08-09', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '314', 'fk_cantiere' => '619', 'assign_infissi' => '807', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-31', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '315', 'fk_cantiere' => '799', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '316', 'fk_cantiere' => '630', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '317', 'fk_cantiere' => '825', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '318', 'fk_cantiere' => '826', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '689', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '319', 'fk_cantiere' => '827', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '320', 'fk_cantiere' => '788', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '672', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '321', 'fk_cantiere' => '740', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2023-05-08', 'elettricista_date' => '2023-05-08', 'edile_date' => '2022-12-16', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '322', 'fk_cantiere' => '271', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '323', 'fk_cantiere' => '179', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '324', 'fk_cantiere' => '318', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '325', 'fk_cantiere' => '755', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-09', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '326', 'fk_cantiere' => '781', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '327', 'fk_cantiere' => '839', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '328', 'fk_cantiere' => '840', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '329', 'fk_cantiere' => '841', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '330', 'fk_cantiere' => '842', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '331', 'fk_cantiere' => '849', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '332', 'fk_cantiere' => '848', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '333', 'fk_cantiere' => '847', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '334', 'fk_cantiere' => '846', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '335', 'fk_cantiere' => '845', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '336', 'fk_cantiere' => '844', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '337', 'fk_cantiere' => '843', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '338', 'fk_cantiere' => '435', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2023-01-26', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '339', 'fk_cantiere' => '653', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-11', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '340', 'fk_cantiere' => '695', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '341', 'fk_cantiere' => '838', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '0000-00-00', 'idraulico_date' => '0000-00-00', 'elettricista_date' => '0000-00-00', 'edile_date' => '0000-00-00', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '342', 'fk_cantiere' => '581', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '589', 'direttore' => '0'),
            array('assignedid' => '343', 'fk_cantiere' => '130', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '344', 'fk_cantiere' => '177', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-08-01', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '345', 'fk_cantiere' => '801', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '346', 'fk_cantiere' => '765', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '347', 'fk_cantiere' => '810', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => '0', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-09', 'idraulico_date' => '2022-08-09', 'elettricista_date' => '2022-08-09', 'edile_date' => '2022-08-09', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '348', 'fk_cantiere' => '406', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => '782', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-08-02', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '349', 'fk_cantiere' => '852', 'assign_infissi' => '770', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-07-29', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '358', 'fk_cantiere' => '471', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '692', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '376', 'fk_cantiere' => '854', 'assign_infissi' => '754', 'infissi_amount' => NULL, 'idraulico' => '673', 'idraulico_amount' => NULL, 'elettricista' => '661', 'elettricista_amount' => NULL, 'edile' => '689', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-01', 'idraulico_date' => '2022-08-01', 'elettricista_date' => '2022-08-01', 'edile_date' => '2022-08-01', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '377', 'fk_cantiere' => '855', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '379', 'fk_cantiere' => '858', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '380', 'fk_cantiere' => '860', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '381', 'fk_cantiere' => '62', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '382', 'fk_cantiere' => '555', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => '789', 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-02', 'idraulico_date' => '2022-08-02', 'elettricista_date' => '2022-08-03', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '383', 'fk_cantiere' => '431', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-02', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2022-12-21', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '384', 'fk_cantiere' => '34', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '385', 'fk_cantiere' => '64', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '386', 'fk_cantiere' => '477', 'assign_infissi' => '781', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-03', 'idraulico_date' => '2022-08-05', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '387', 'fk_cantiere' => '668', 'assign_infissi' => '640', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-04', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '388', 'fk_cantiere' => '862', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '389', 'fk_cantiere' => '559', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '804', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-08-05', 'elettricista_date' => NULL, 'edile_date' => '2022-08-26', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '390', 'fk_cantiere' => '478', 'assign_infissi' => '781', 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => '808', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-09', 'idraulico_date' => '2022-08-05', 'elettricista_date' => '2022-08-31', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '391', 'fk_cantiere' => '481', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '804', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-08-05', 'elettricista_date' => NULL, 'edile_date' => '2022-08-26', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '392', 'fk_cantiere' => '475', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-08-05', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '393', 'fk_cantiere' => '866', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '394', 'fk_cantiere' => '871', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '395', 'fk_cantiere' => '872', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '775', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-08-10', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '396', 'fk_cantiere' => '873', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '803', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-08-10', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '397', 'fk_cantiere' => '709', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '398', 'fk_cantiere' => '863', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '399', 'fk_cantiere' => '762', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '400', 'fk_cantiere' => '390', 'assign_infissi' => '807', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-02', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '401', 'fk_cantiere' => '326', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '402', 'fk_cantiere' => '229', 'assign_infissi' => '639', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-26', 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-30', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '403', 'fk_cantiere' => '880', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '404', 'fk_cantiere' => '489', 'assign_infissi' => '807', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-01', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '405', 'fk_cantiere' => '424', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-30', 'idraulico_date' => NULL, 'elettricista_date' => '2022-12-13', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '406', 'fk_cantiere' => '664', 'assign_infissi' => '781', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-08-31', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '407', 'fk_cantiere' => '879', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '408', 'fk_cantiere' => '2', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '409', 'fk_cantiere' => '884', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '410', 'fk_cantiere' => '669', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '411', 'fk_cantiere' => '869', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-12-17', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '412', 'fk_cantiere' => '889', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '413', 'fk_cantiere' => '887', 'assign_infissi' => '642', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-21', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '576'),
            array('assignedid' => '414', 'fk_cantiere' => '890', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-08-14', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '415', 'fk_cantiere' => '677', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-09-07', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '416', 'fk_cantiere' => '696', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '417', 'fk_cantiere' => '162', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '418', 'fk_cantiere' => '893', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '660', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-12-13', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '419', 'fk_cantiere' => '674', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '776', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-08', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '420', 'fk_cantiere' => '894', 'assign_infissi' => '641', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-12', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '421', 'fk_cantiere' => '875', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '808', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2023-02-15', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '422', 'fk_cantiere' => '885', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-13', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '423', 'fk_cantiere' => '896', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '424', 'fk_cantiere' => '794', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '425', 'fk_cantiere' => '0', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-09-20', 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-20', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '426', 'fk_cantiere' => '897', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '427', 'fk_cantiere' => '680', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '428', 'fk_cantiere' => '687', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '429', 'fk_cantiere' => '698', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '430', 'fk_cantiere' => '682', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '431', 'fk_cantiere' => '678', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '432', 'fk_cantiere' => '679', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '433', 'fk_cantiere' => '683', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '434', 'fk_cantiere' => '686', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '777', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2022-09-21', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '435', 'fk_cantiere' => '716', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '436', 'fk_cantiere' => '898', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '437', 'fk_cantiere' => '903', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2023-03-28', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '438', 'fk_cantiere' => '904', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '439', 'fk_cantiere' => '900', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2023-03-14', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '440', 'fk_cantiere' => '902', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '441', 'fk_cantiere' => '428', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '442', 'fk_cantiere' => '180', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '443', 'fk_cantiere' => '808', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '444', 'fk_cantiere' => '637', 'assign_infissi' => '644', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2022-11-08', 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '445', 'fk_cantiere' => '905', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '825'),
            array('assignedid' => '446', 'fk_cantiere' => '901', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '447', 'fk_cantiere' => '699', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '565', 'direttore' => '0'),
            array('assignedid' => '448', 'fk_cantiere' => '392', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '812', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-01-16', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '449', 'fk_cantiere' => '291', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '450', 'fk_cantiere' => '804', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '451', 'fk_cantiere' => '895', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => '846', 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => '2023-03-02', 'coordinatore' => '846', 'direttore' => '0'),
            array('assignedid' => '452', 'fk_cantiere' => '293', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '453', 'fk_cantiere' => '65', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '454', 'fk_cantiere' => '8', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '455', 'fk_cantiere' => '907', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '456', 'fk_cantiere' => '480', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '457', 'fk_cantiere' => '165', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '458', 'fk_cantiere' => '803', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '459', 'fk_cantiere' => '910', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '460', 'fk_cantiere' => '376', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '461', 'fk_cantiere' => '381', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '462', 'fk_cantiere' => '917', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '463', 'fk_cantiere' => '922', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '464', 'fk_cantiere' => '927', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '465', 'fk_cantiere' => '931', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '466', 'fk_cantiere' => '932', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '467', 'fk_cantiere' => '859', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2022-12-06', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '779', 'direttore' => '0'),
            array('assignedid' => '468', 'fk_cantiere' => '46', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '469', 'fk_cantiere' => '930', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '470', 'fk_cantiere' => '908', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '471', 'fk_cantiere' => '933', 'assign_infissi' => '750', 'infissi_amount' => '200', 'idraulico' => '848', 'idraulico_amount' => '2000', 'elettricista' => '658', 'elettricista_amount' => '15000', 'edile' => '0', 'edile_amount' => '200', 'edile2' => '659', 'edile_amount2' => '2000', 'fotovoltaicoid' => '696', 'fotovoltaico_amount' => '15000', 'infissi_date' => '2023-04-04', 'idraulico_date' => '2023-04-04', 'elettricista_date' => '2022-12-07', 'edile_date' => '2022-12-07', 'edile2_date' => '2022-12-07', 'foto_date' => '2022-12-07', 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '472', 'fk_cantiere' => '257', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '473', 'fk_cantiere' => '569', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-07-28', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '474', 'fk_cantiere' => '938', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '475', 'fk_cantiere' => '764', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '476', 'fk_cantiere' => '939', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '477', 'fk_cantiere' => '658', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '683', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-01-17', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '478', 'fk_cantiere' => '946', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '479', 'fk_cantiere' => '945', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '480', 'fk_cantiere' => '950', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '683', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-07-28', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '589', 'direttore' => '589'),
            array('assignedid' => '481', 'fk_cantiere' => '797', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2023-09-25', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '482', 'fk_cantiere' => '798', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2023-09-25', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '483', 'fk_cantiere' => '538', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => '808', 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => '2023-05-08', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '484', 'fk_cantiere' => '953', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '485', 'fk_cantiere' => '173', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '486', 'fk_cantiere' => '135', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '487', 'fk_cantiere' => '951', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '488', 'fk_cantiere' => '944', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '489', 'fk_cantiere' => '952', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '490', 'fk_cantiere' => '958', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '491', 'fk_cantiere' => '961', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '492', 'fk_cantiere' => '962', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '493', 'fk_cantiere' => '959', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '494', 'fk_cantiere' => '973', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '495', 'fk_cantiere' => '976', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '496', 'fk_cantiere' => '693', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '497', 'fk_cantiere' => '964', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '498', 'fk_cantiere' => '937', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '499', 'fk_cantiere' => '936', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '500', 'fk_cantiere' => '979', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '501', 'fk_cantiere' => '935', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-06-06', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '502', 'fk_cantiere' => '963', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '662', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-07-24', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '503', 'fk_cantiere' => '783', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-05-29', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '504', 'fk_cantiere' => '237', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '505', 'fk_cantiere' => '509', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => '665', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => '2023-09-25', 'elettricista_date' => NULL, 'edile_date' => '2023-05-29', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '506', 'fk_cantiere' => '982', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '507', 'fk_cantiere' => '978', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '508', 'fk_cantiere' => '635', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-06-06', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '509', 'fk_cantiere' => '784', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '510', 'fk_cantiere' => '987', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '511', 'fk_cantiere' => '983', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '512', 'fk_cantiere' => '694', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '513', 'fk_cantiere' => '474', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '514', 'fk_cantiere' => '742', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '515', 'fk_cantiere' => '980', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '516', 'fk_cantiere' => '981', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '517', 'fk_cantiere' => '994', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '518', 'fk_cantiere' => '977', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '519', 'fk_cantiere' => '995', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-07-05', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '520', 'fk_cantiere' => '925', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '521', 'fk_cantiere' => '923', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => '851', 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => '2023-07-28', 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '522', 'fk_cantiere' => '924', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '523', 'fk_cantiere' => '715', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '524', 'fk_cantiere' => '988', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '525', 'fk_cantiere' => '915', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '526', 'fk_cantiere' => '913', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '527', 'fk_cantiere' => '916', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '528', 'fk_cantiere' => '1012', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '529', 'fk_cantiere' => '1020', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '530', 'fk_cantiere' => '1017', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '531', 'fk_cantiere' => '1021', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '532', 'fk_cantiere' => '1019', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '533', 'fk_cantiere' => '1023', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '534', 'fk_cantiere' => '1025', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '535', 'fk_cantiere' => '1028', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '536', 'fk_cantiere' => '1033', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '537', 'fk_cantiere' => '1034', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '538', 'fk_cantiere' => '1030', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '539', 'fk_cantiere' => '1032', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '540', 'fk_cantiere' => '969', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '541', 'fk_cantiere' => '971', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '542', 'fk_cantiere' => '1038', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '543', 'fk_cantiere' => '1039', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '544', 'fk_cantiere' => '741', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '545', 'fk_cantiere' => '599', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '546', 'fk_cantiere' => '423', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '547', 'fk_cantiere' => '1048', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '548', 'fk_cantiere' => '1060', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '549', 'fk_cantiere' => '1061', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '550', 'fk_cantiere' => '1058', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '551', 'fk_cantiere' => '1059', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '552', 'fk_cantiere' => '1062', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '553', 'fk_cantiere' => '1064', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '554', 'fk_cantiere' => '891', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '555', 'fk_cantiere' => '1065', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '556', 'fk_cantiere' => '1063', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '557', 'fk_cantiere' => '1070', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '558', 'fk_cantiere' => '926', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '559', 'fk_cantiere' => '1067', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '560', 'fk_cantiere' => '911', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '561', 'fk_cantiere' => '985', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '562', 'fk_cantiere' => '1072', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '563', 'fk_cantiere' => '1073', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '564', 'fk_cantiere' => '954', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '565', 'fk_cantiere' => '1075', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '566', 'fk_cantiere' => '1077', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '567', 'fk_cantiere' => '1082', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '568', 'fk_cantiere' => '1084', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '569', 'fk_cantiere' => '1083', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '570', 'fk_cantiere' => '1081', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '571', 'fk_cantiere' => '1080', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '572', 'fk_cantiere' => '1078', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '573', 'fk_cantiere' => '1088', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '574', 'fk_cantiere' => '1086', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '575', 'fk_cantiere' => '1087', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '576', 'fk_cantiere' => '1076', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '577', 'fk_cantiere' => '1085', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '578', 'fk_cantiere' => '1090', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '579', 'fk_cantiere' => '1093', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '580', 'fk_cantiere' => '1029', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '581', 'fk_cantiere' => '986', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '582', 'fk_cantiere' => '1016', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '583', 'fk_cantiere' => '1024', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '584', 'fk_cantiere' => '1042', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '585', 'fk_cantiere' => '1079', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '586', 'fk_cantiere' => '1091', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '587', 'fk_cantiere' => '1092', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '588', 'fk_cantiere' => '1094', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '589', 'fk_cantiere' => '1098', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '590', 'fk_cantiere' => '1097', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '591', 'fk_cantiere' => '455', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '592', 'fk_cantiere' => '1099', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '593', 'fk_cantiere' => '1100', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '594', 'fk_cantiere' => '1101', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '595', 'fk_cantiere' => '1102', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '596', 'fk_cantiere' => '1103', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '597', 'fk_cantiere' => '643', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '598', 'fk_cantiere' => '1105', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '599', 'fk_cantiere' => '1066', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '600', 'fk_cantiere' => '1106', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '601', 'fk_cantiere' => '1107', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '602', 'fk_cantiere' => '1109', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '603', 'fk_cantiere' => '1114', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '604', 'fk_cantiere' => '1117', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '605', 'fk_cantiere' => '1118', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '606', 'fk_cantiere' => '1120', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '607', 'fk_cantiere' => '611', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '608', 'fk_cantiere' => '629', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '609', 'fk_cantiere' => '1018', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '610', 'fk_cantiere' => '1132', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '611', 'fk_cantiere' => '1128', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '612', 'fk_cantiere' => '1125', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '613', 'fk_cantiere' => '1130', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '614', 'fk_cantiere' => '1124', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '615', 'fk_cantiere' => '1127', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '616', 'fk_cantiere' => '1043', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '617', 'fk_cantiere' => '1069', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '618', 'fk_cantiere' => '1110', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '619', 'fk_cantiere' => '1112', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '620', 'fk_cantiere' => '1111', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '621', 'fk_cantiere' => '1113', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '622', 'fk_cantiere' => '1119', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '623', 'fk_cantiere' => '1122', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '624', 'fk_cantiere' => '1126', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '625', 'fk_cantiere' => '1121', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '626', 'fk_cantiere' => '1134', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '627', 'fk_cantiere' => '1139', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '628', 'fk_cantiere' => '1138', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '629', 'fk_cantiere' => '1141', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '630', 'fk_cantiere' => '1136', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '631', 'fk_cantiere' => '1054', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '632', 'fk_cantiere' => '1051', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '633', 'fk_cantiere' => '1049', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '634', 'fk_cantiere' => '1055', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '635', 'fk_cantiere' => '1050', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '636', 'fk_cantiere' => '1053', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '637', 'fk_cantiere' => '1142', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '638', 'fk_cantiere' => '1144', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '639', 'fk_cantiere' => '1143', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '640', 'fk_cantiere' => '1145', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '641', 'fk_cantiere' => '1044', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '642', 'fk_cantiere' => '1153', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '643', 'fk_cantiere' => '1133', 'assign_infissi' => '864', 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2023-11-06', 'idraulico_date' => NULL, 'elettricista_date' => '2023-11-06', 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '644', 'fk_cantiere' => '1165', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '645', 'fk_cantiere' => '1166', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '646', 'fk_cantiere' => '1045', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '647', 'fk_cantiere' => '1071', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '648', 'fk_cantiere' => '928', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '649', 'fk_cantiere' => '1167', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '650', 'fk_cantiere' => '1046', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '651', 'fk_cantiere' => '1168', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '652', 'fk_cantiere' => '1170', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '653', 'fk_cantiere' => '1187', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '654', 'fk_cantiere' => '1183', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '655', 'fk_cantiere' => '1175', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '656', 'fk_cantiere' => '1181', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '657', 'fk_cantiere' => '1182', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '658', 'fk_cantiere' => '1180', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '659', 'fk_cantiere' => '1185', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '660', 'fk_cantiere' => '1186', 'assign_infissi' => NULL, 'infissi_amount' => NULL, 'idraulico' => NULL, 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => NULL, 'idraulico_date' => NULL, 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0'),
            array('assignedid' => '661', 'fk_cantiere' => '1195', 'assign_infissi' => '645', 'infissi_amount' => NULL, 'idraulico' => '848', 'idraulico_amount' => NULL, 'elettricista' => NULL, 'elettricista_amount' => NULL, 'edile' => NULL, 'edile_amount' => NULL, 'edile2' => NULL, 'edile_amount2' => NULL, 'fotovoltaicoid' => NULL, 'fotovoltaico_amount' => NULL, 'infissi_date' => '2023-11-06', 'idraulico_date' => '2023-11-06', 'elettricista_date' => NULL, 'edile_date' => NULL, 'edile2_date' => NULL, 'foto_date' => NULL, 'coordinatore' => '0', 'direttore' => '0')
        );
        //        dd($users);
        //i(SK) deleted result
        //  $getInfissiJobs = ConstructionJobDetail::whereDate('created_at' , '2023-09-12')->delete();

        foreach ($assignedImpresa as $assign) {
            //            dd($assign);
            $count = 0;

            $fixture = $assign['assign_infissi'];
            if ($fixture) {
                $fixture = $this->getUserData($fixture);
            } elseif ($fixture == 0) {
                $fixture = null;
            } else {
                $fixture = null;
            }
            $fixturePrice = $assign['infissi_amount'];
            $plumbing = $assign['idraulico'];
            if ($plumbing) {
                $plumbing = $this->getUserData($plumbing);
            } elseif ($plumbing == 0) {
                $plumbing = null;
            } else {
                $plumbing = null;
            }
            $plumbingPrice = $assign['idraulico_amount'];
            $electrical = $assign['elettricista'];
            if ($electrical) {
                $electrical = $this->getUserData($electrical);
            } elseif ($electrical == 0) {
                $electrical = null;
            } else {
                $electrical = null;
            }
            $electricalPrice = $assign['elettricista_amount'];
            $construction = $assign['edile'];
            if ($construction) {
                $construction = $this->getUserData($construction);
            } elseif ($construction == 0) {
                $construction = null;
            } else {
                $construction = null;
            }
            $constructionPrice = $assign['edile_amount'];
            $construction2 = $assign['edile2'];
            if ($construction2) {
                $construction2 = $this->getUserData($construction2);
            } elseif ($construction2 == 0) {
                $construction2 = null;
            } else {
                $construction2 = null;
            }
            $construction2Price = $assign['edile_amount2'];
            $photovoltaic = $assign['fotovoltaicoid'];
            if ($photovoltaic) {
                $photovoltaic = $this->getUserData($photovoltaic);
            } elseif ($photovoltaic == 0) {
                $photovoltaic = null;
            } else {
                $photovoltaic = null;
            }
            $photovoltaicPrice = $assign['fotovoltaico_amount'];
            $coordinator = $assign['coordinatore'];
            if ($coordinator) {
                $coordinator = $this->getUserData($coordinator);
            } elseif ($coordinator == 0) {
                $coordinator = null;
            } else {
                $coordinator = null;
            }
            $constructionManager = $assign['direttore'];
            if ($constructionManager) {
                $constructionManager = $this->getUserData($constructionManager);
            } elseif ($constructionManager == 0) {
                $constructionManager = null;
            } else {
                $constructionManager = null;
            }



            $attributes = [
                'fixtures' => $fixture,
                'fixtures_company_price' => $fixturePrice,
                'plumbing' => $plumbing,
                'plumbing_company_price' => $plumbingPrice,
                'electrical' => $electrical,
                'electrical_installations_company_price' => $electricalPrice,
                'construction' => $construction,
                'construction_company1_price' => $constructionPrice,
                'construction2' => $construction2,
                'construction_company2_price' => $construction2Price,
                'photovoltaic' => $photovoltaic,
                'photovoltaic_price' => $photovoltaicPrice,
                'coordinator' => $coordinator,
                'construction_manager' => $constructionManager,
            ];

            $clientId = Cantiere::where('cantiereId', $assign['fk_cantiere'])->pluck('fk_cliente')->first();
            $constructionSiteId = ConstructionSite::where('oldid', $clientId)->pluck('id')->first();

            ConstructionJobDetail::updateOrInsert(['construction_site_id' => $constructionSiteId], $attributes);

            $count++;
        }
        echo 'total records insert : ' . $count;
    }

    private function getUserData($id)
    {
        $users = array(
            array('userid' => '424', 'usernome' => 'ALECCI ALESSANDRO', 'useremail' => 'alessandro.alecci@libero.it', 'usertelefono' => '3287149794', 'usercomune' => 'VERRUA PO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '307a5d6f79edfce9bb8335419066cf45', 'orignalpass' => 'Rv76xQxg', 'role' => '2', 'status' => '1', 'userprov' => 'PV', 'userres' => 'Via Case Sparse n.6', 'usercf' => 'LCCLSN81R19M109A', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'PAVIA', 'useriscr' => '3950', 'usercomunen' => 'VOGHERA', 'userdatan' => '1981-10-19', 'userprovn' => 'PV'),
            array('userid' => '517', 'usernome' => 'PASQUALE', 'useremail' => 'pasquale.greengen@gmail.com', 'usertelefono' => '3335618930', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'b6940334f7b60c87eb6207f3719d6137', 'orignalpass' => 'tBPAtDO8', 'role' => '1', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '551', 'usernome' => 'MARIA', 'useremail' => 'segreteria.greengen@gmail.com', 'usertelefono' => '3381605540', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'e9bf21a711ad53194fc735b13aabe65d', 'orignalpass' => 'hxnc1mbN', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '561', 'usernome' => 'LACATENA RAFFAELE', 'useremail' => 'ingegnerlacatena@gmail.com', 'usertelefono' => '3282498710', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '383243d8ba59e3d297a89b94e08d925a', 'orignalpass' => 'UlXp0QTj', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'Via Monopoli n. 10', 'usercf' => 'LCTRFL73B19C134N', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => ' 7071', 'usercomunen' => 'Castellana Grotte', 'userdatan' => '1973-02-19', 'userprovn' => 'BA'),
            array('userid' => '562', 'usernome' => 'PIERCARLO CASTELLANA', 'useremail' => 'geompiercarlo@hotmail.it', 'usertelefono' => '3286425245', 'usercomune' => 'PUTIGNANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'd2e3d75b464a9ff2863ef46b34a3ef33', 'orignalpass' => 'YaDatWmM', 'role' => '2', 'status' => '1', 'userprov' => 'BARI', 'userres' => 'VIA S.P. GIOIA DEL COLLE, 22', 'usercf' => 'CSTPCR76A27H096T', 'usercoll' => 'GEOMETRI ', 'usercomcoll' => 'PROVINCIA DI BARI', 'useriscr' => '3933', 'usercomunen' => 'PUTIGNANO', 'userdatan' => '1976-01-27', 'userprovn' => 'BARI'),
            array('userid' => '564', 'usernome' => 'AQUILINO LUCA', 'useremail' => 'aquilinoluca83@gmail.com', 'usertelefono' => '3333215335', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'a83f0d9cad3d39be4a9973514f6c1988', 'orignalpass' => '2yAyP4Ej', 'role' => '2', 'status' => '1', 'userprov' => 'BARI', 'userres' => 'Vio Ungoretti 8/C', 'usercf' => 'QLNLCU83B07F915W', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '8556', 'usercomunen' => 'NOCI', 'userdatan' => '1983-02-07', 'userprovn' => 'BARI'),
            array('userid' => '565', 'usernome' => 'PALMISANO VITO', 'useremail' => 'palmisanovito75@gmail.com', 'usertelefono' => '3284799487', 'usercomune' => 'MARTINA FRANCA', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '53bf8ead76db3c1331f82800522d13f7', 'orignalpass' => '61NvJKO5', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '566', 'usernome' => 'ARTURO DE MARCO', 'useremail' => 'arturodemarco25@gmail.com', 'usertelefono' => '3288397858', 'usercomune' => 'TRICASE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'a046762eeb7bbcdc31e6ab092789f83b', 'orignalpass' => 'RIqJC9gF', 'role' => '2', 'status' => '1', 'userprov' => 'LE', 'userres' => 'VIA F. GAETANO, 01', 'usercf' => 'DMRRTR94D252133B', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'LECCE', 'useriscr' => '4014', 'usercomunen' => 'ZUGO ', 'userdatan' => '1994-04-25', 'userprovn' => 'CH'),
            array('userid' => '567', 'usernome' => 'FRANCESCO PAOLO BALDINI', 'useremail' => 'studiotecbaldini@gmail.com', 'usertelefono' => '3470037683', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '61f3d8feb88510b1526a29d4176cd723', 'orignalpass' => 'RN0Dr1zh', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => 'BLDFNC66S17H096Y', 'usercoll' => 'GEOMETRI E GEOMETRI LAUREATI', 'usercomcoll' => 'BARI', 'useriscr' => '3049', 'usercomunen' => 'PUTIGNANO', 'userdatan' => '1966-11-17', 'userprovn' => 'BA'),
            array('userid' => '568', 'usernome' => 'CAVALLO ANGELO', 'useremail' => 'geom.cavalloangelo@gmail.com', 'usertelefono' => '3382480831', 'usercomune' => 'SAMMICHELE SALENTINO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'c874d08e5521641d68eab7d18b595a0d', 'orignalpass' => 'LJm3VgYn', 'role' => '2', 'status' => '1', 'userprov' => 'BR', 'userres' => 'VIA DUCA D', 'usercf' => 'CVLNGL66A24I045Y', 'usercoll' => ' geometri', 'usercomcoll' => 'BRINDISI', 'useriscr' => '973', 'usercomunen' => 'SAN MICHELE S.', 'userdatan' => '1966-01-24', 'userprovn' => 'BR'),
            array('userid' => '569', 'usernome' => 'CECCIOLI GIANCARLO', 'useremail' => 'giancarlo.ceccioli@gmail.com', 'usertelefono' => '3472703007', 'usercomune' => 'MASSAFRA', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '6a1ea7ee9e7ffc4817218a0e086944a5', 'orignalpass' => 'ZpyfMCKL', 'role' => '2', 'status' => '1', 'userprov' => 'TA', 'userres' => 'VIA G. FALCONE 7/A', 'usercf' => 'CCCGCR67H30M088T', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'TA', 'useriscr' => '1869', 'usercomunen' => 'VITTORIA', 'userdatan' => '1967-06-30', 'userprovn' => 'RG'),
            array('userid' => '570', 'usernome' => 'MICHELE DE BIASE', 'useremail' => 'geom.debiase@gmail.com', 'usertelefono' => '3496158691', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'd5e704394f278681844ca9b2d1b3126d', 'orignalpass' => 'd91zrVsh', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VICO I DON F.GIGANTE,7/B', 'usercf' => 'DBSMHL55L14A149N', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '1920', 'usercomunen' => 'ALBEROBELLO', 'userdatan' => '1955-07-14', 'userprovn' => 'BA'),
            array('userid' => '571', 'usernome' => 'GIULIO LERARIO', 'useremail' => 'giuliolerario@hotmail.it', 'usertelefono' => '3339508140', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'b0b4c26ccd7cdef71604bfe0be444263', 'orignalpass' => 'UJc3cQsd', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => '', 'usercf' => 'LRRGLI87A25H096R', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '10699', 'usercomunen' => 'PUTIGNANO', 'userdatan' => '1987-01-25', 'userprovn' => 'BA'),
            array('userid' => '572', 'usernome' => 'VINCENZO INDIVERI', 'useremail' => 'ing.vincenzoindiveri@gmail.com', 'usertelefono' => '3386666679', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '97fb0f275487354156ffafa84f276f28', 'orignalpass' => 'eyiCxKdh', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA S. DONATO, 63', 'usercf' => 'NDVVCN60E27F376T', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '4546', 'usercomunen' => 'MONOPOLI', 'userdatan' => '1960-05-27', 'userprovn' => 'BA'),
            array('userid' => '573', 'usernome' => 'INTRECCIO FILIPPO', 'useremail' => 'studio.intreccio@gmail.com', 'usertelefono' => '3398236818', 'usercomune' => 'MOLA DI BARI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '61f7f729de42ee46aaa317bbb682577e', 'orignalpass' => 'gmGvQwaS', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'PADRE PIO, 21', 'usercf' => 'NTRFPP52S05F280Z', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '8829', 'usercomunen' => 'MOLA DI BARI', 'userdatan' => '1952-11-05', 'userprovn' => 'BA'),
            array('userid' => '574', 'usernome' => 'LEONARDO IVONE', 'useremail' => 'leonardoivone65@gmail.com', 'usertelefono' => '3355378093', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'd7297edef38c08c591545683890e677c', 'orignalpass' => 'xhtuZrLV', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'via Della Resistenza n. 115', 'usercf' => 'VNILRD65R27C134V', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '3796', 'usercomunen' => 'Castellana Grotte', 'userdatan' => '1965-10-27', 'userprovn' => 'BA'),
            array('userid' => '575', 'usernome' => 'LONARDELLI', 'useremail' => 'steve3707unico@gmail.com', 'usertelefono' => '3687893319', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '091582b5564b80a8916fdac3c74023f3', 'orignalpass' => 'kmHwpVEe', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'Via Tratturo Fanelli, 26', 'usercf' => 'LNRSFN61B16C134L', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '3707', 'usercomunen' => 'CASTELLANA G', 'userdatan' => '1961-02-16', 'userprovn' => 'BA'),
            array('userid' => '576', 'usernome' => 'LUCIA SGOBBA', 'useremail' => 'spazio97putignano@gmail.com', 'usertelefono' => '3205622050', 'usercomune' => 'NOCI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ac7a654ce40c3689d0bd930495e3bf80', 'orignalpass' => '0ovIIbRN', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'via Maggior de Cataldo, 7/B', 'usercf' => 'SGBLCU78S54C134P', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BARI', 'useriscr' => '2403', 'usercomunen' => 'CASTELLANA G', 'userdatan' => '1978-11-14', 'userprovn' => 'BA'),
            array('userid' => '577', 'usernome' => 'LUIGI EPIFANI', 'useremail' => 'luigi.epifani72@gmail.com', 'usertelefono' => '3391713701', 'usercomune' => 'OSTUNI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '5fb7e4339df3c2b7ef849386eee9213f', 'orignalpass' => 'eYvzPa4B', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '578', 'usernome' => 'NOCITO MARCO', 'useremail' => 'geometranocito@libero.it', 'usertelefono' => '3314462448', 'usercomune' => 'TARANTO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '3332737482252bd8b3645a7a0f967efc', 'orignalpass' => 'pH688os1', 'role' => '2', 'status' => '1', 'userprov' => 'TA', 'userres' => 'SAN GIORGIO IONICO', 'usercf' => 'NCTMRC84P14L049B', 'usercoll' => 'GEOMETRI ', 'usercomcoll' => 'TARANTO', 'useriscr' => '2030', 'usercomunen' => 'TARANTO ', 'userdatan' => '1984-09-14', 'userprovn' => 'TA'),
            array('userid' => '579', 'usernome' => 'RAMIRRA MICHELE', 'useremail' => 'ramirramicheleaurelio@gmail.com', 'usertelefono' => '3881146486', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '6c00d53347314abe495f13b3629ea55c', 'orignalpass' => 'OOoibFrq', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '580', 'usernome' => 'ROMANAZZI TOMMASO', 'useremail' => 'romanazzitommaso10@gmail.com', 'usertelefono' => '3335097052', 'usercomune' => 'PUTIGNANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '008f88d5506703ac8f5cb82c5eeb4613', 'orignalpass' => 'nrKrGuZb', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'Via A. De Gasperi n. 35', 'usercf' => 'RMNTMS89D27F376B', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => ' 4696', 'usercomunen' => 'MONOPOLI', 'userdatan' => '1989-04-27', 'userprovn' => 'BA'),
            array('userid' => '581', 'usernome' => 'ZACCARIA DOMENICO', 'useremail' => 'ingzaccaria@libero.it', 'usertelefono' => '3204917463', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '4dd41e624fe8a66b9f97bd0c8ca32a44', 'orignalpass' => 'OPVC5qEW', 'role' => '2', 'status' => '1', 'userprov' => 'Bari', 'userres' => 'cont sant antonio d\'ascula,216', 'usercf' => 'ZCCDNC81P04F376R', 'usercoll' => 'ingegneri ', 'usercomcoll' => 'BARI', 'useriscr' => '8632', 'usercomunen' => 'MONOPOLI', 'userdatan' => '1981-09-04', 'userprovn' => 'BA'),
            array('userid' => '582', 'usernome' => 'ROMANAZZI GIANFRANCO', 'useremail' => 'romanazzi2780@libero.it', 'usertelefono' => '3383864290', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '2e27b638c45468469afb140ce7667c46', 'orignalpass' => 'cmPBlZwt', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'via cap. V. Di Mola,15', 'usercf' => 'RMNGFR65E25L049B', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '2780', 'usercomunen' => 'TARANTO', 'userdatan' => '1965-05-25', 'userprovn' => 'TA'),
            array('userid' => '583', 'usernome' => 'CAROLI DONATO', 'useremail' => 'donacaro88@gmail.com', 'usertelefono' => '3293137897', 'usercomune' => 'MARTINA FRANCA', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '93b76f1024e2dcc649d94afd3ca64e3d', 'orignalpass' => 'RM67lrP2', 'role' => '2', 'status' => '1', 'userprov' => 'TA', 'userres' => 'VIA LETIZIA MARINOSCI, 1', 'usercf' => 'CRLDNT88E11E986X', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'TARANTO', 'useriscr' => '2125', 'usercomunen' => 'MARTINA FRANCA', 'userdatan' => '1988-05-11', 'userprovn' => 'TA'),
            array('userid' => '584', 'usernome' => 'GIANNOCCARO DIEGO', 'useremail' => 'dg@diegogiannoccaro.it', 'usertelefono' => '0808895394', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '9606a4b69953b5402df7e4017db08b2a', 'orignalpass' => 'igKH9ipd', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '585', 'usernome' => 'FORTUNATO LEONARDO', 'useremail' => 'leonardo.fortunato66@gmail.com', 'usertelefono' => '3456741563', 'usercomune' => 'MOLA DI BARI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '6c625581e4df5d32ef995111c2c3e7de', 'orignalpass' => 'jXA0CqOd', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '586', 'usernome' => 'FEDELE FILIPPO', 'useremail' => 'fedele.filippo@gmail.com', 'usertelefono' => '3483804795', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '0b7c5830830627513c8f0bb42a209725', 'orignalpass' => 'GLuwXrfa', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA SAN DONATO,25', 'usercf' => 'FDLFPP68B12F376C', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '5306', 'usercomunen' => 'MONOPOLI', 'userdatan' => '1968-02-12', 'userprovn' => 'BA'),
            array('userid' => '587', 'usernome' => 'DE DONATO', 'useremail' => 'biante1@virgilio.it', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'fb53129f938da1317f049365bdec7e26', 'orignalpass' => 'wZIEnVVh', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '588', 'usernome' => 'GIAMPAOLO PINTO', 'useremail' => 'geompintog@libero.it', 'usertelefono' => '3477522211', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ef0e9731704e9671e05634ca4662f95c', 'orignalpass' => 'h2on8kOG', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '589', 'usernome' => 'MICHELE CARRIERI', 'useremail' => 'ingmichelecarrieri@gmail.com', 'usertelefono' => '3471624155', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ab6c01333333f8409d61910c50fe4878', 'orignalpass' => 'MuWG2pnr', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA Francesco Angiulli n. 24', 'usercf' => 'CRRMHL76L24E986A', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => ' 6164', 'usercomunen' => 'Martina Franca', 'userdatan' => '1976-07-24', 'userprovn' => 'TA'),
            array('userid' => '590', 'usernome' => 'CITO SABRINA MARINA', 'useremail' => 'arch.citosabrina@gmail.com', 'usertelefono' => '3476257899', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ae8552a622ecb419d8a88f902642a73c', 'orignalpass' => 'lf3VJoyD', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA GIOVANNI XXIII, 50', 'usercf' => 'CTISRN68H60A662Z', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BARI', 'useriscr' => '1687', 'usercomunen' => 'BARI', 'userdatan' => '1968-06-20', 'userprovn' => 'BA'),
            array('userid' => '591', 'usernome' => 'CANCELLIERE ANTONIO', 'useremail' => 'ca.antonio@tiscalinet.it', 'usertelefono' => '3298044004', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'd85caf19e5f04d8316d746ceb4307f06', 'orignalpass' => 'YpXDWOKb', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '592', 'usernome' => 'DE PASQUALE NICOLA', 'useremail' => 'arch.depasquale@libero.it', 'usertelefono' => '3383472104', 'usercomune' => 'FASANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '90a8fadd50c1cc13236ca9a1851ff15c', 'orignalpass' => '38EqNdVO', 'role' => '2', 'status' => '1', 'userprov' => 'BR', 'userres' => '', 'usercf' => '', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BRINDISI', 'useriscr' => '276', 'usercomunen' => 'FASANO', 'userdatan' => '1967-01-01', 'userprovn' => 'BR'),
            array('userid' => '593', 'usernome' => 'SFORZA GIANCARLO', 'useremail' => 'gcsforza@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'b95bcb7324301ab52d2fe9d070e5d660', 'orignalpass' => '5P4KD38v', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '595', 'usernome' => 'CAPUTO NICOLA', 'useremail' => 'nicogeo.cap@hotmail.it', 'usertelefono' => '3396785731', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '98366c2d105ffce395879f2fb7ac162a', 'orignalpass' => 'isGgZdoj', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '596', 'usernome' => 'MATARRESE BENIAMINO', 'useremail' => 'ing.beniaminomatarrese@yahoo.it', 'usertelefono' => '3336446467', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '59e4442439bd690ad24aeb5fd35d0e2c', 'orignalpass' => 'uh8b0Vcg', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA BALENZANO n. 20', 'usercf' => 'MTRBMN74D19A149W', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '7395', 'usercomunen' => 'ALBEROBELLO', 'userdatan' => '1974-04-19', 'userprovn' => 'BA'),
            array('userid' => '597', 'usernome' => 'LORIZIO ANTONIO', 'useremail' => 'glorizio@inwind.it', 'usertelefono' => '3349774825', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ad3de45d99949aeb413c4098d567d101', 'orignalpass' => 'KjELePAV', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '598', 'usernome' => 'ARDITO FRANCESCO', 'useremail' => 'francescoarditopunto@gmail.com', 'usertelefono' => '3391031445', 'usercomune' => 'POLIGNANO A MARE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '7d09bbf63ba49eecc2fe44898c852ecc', 'orignalpass' => 'rClTLOOA', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => '', 'usercomunen' => '', 'userdatan' => '0000-00-00', 'userprovn' => ''),
            array('userid' => '599', 'usernome' => 'CAPONE ANTONIO', 'useremail' => 'antonio.capone@easicert.it', 'usertelefono' => '3347432421', 'usercomune' => 'BARI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '876e50536a1609faad6f6bfa80d5e9ca', 'orignalpass' => 'AN0TlENM', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '600', 'usernome' => 'CONTENTO DANIELE', 'useremail' => 'ing.daniele.contento@gmail.com', 'usertelefono' => '3333488122', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '673106ccb35f6a02493de18e338b2c55', 'orignalpass' => 'iJiGzjuA', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA A. PESCE, 66', 'usercf' => 'CNTDNL84A06F376J', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '9774', 'usercomunen' => 'MONOPOLI', 'userdatan' => '1984-01-06', 'userprovn' => 'BA'),
            array('userid' => '602', 'usernome' => 'FRANCO', 'useremail' => 'campanella1970@gmail.com', 'usertelefono' => '3339128946', 'usercomune' => 'PUTIGNANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8c50443fabae7406d5ac1bfa8653b27a', 'orignalpass' => 'b0wMxyCd', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '603', 'usernome' => 'MASI LORENZO', 'useremail' => 'MASI.LORENZO@hotmail.it', 'usertelefono' => '335301551', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f3ed700a8cae5904c205f309b89907ac', 'orignalpass' => 'g1qZExMF', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '604', 'usernome' => 'SANTE LUIGI CARAMIA', 'useremail' => 'luigi.caramia@libero.it', 'usertelefono' => '3387098136', 'usercomune' => 'LOCOROTONDO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '2ab15c47412034254d8592db83dc34f0', 'orignalpass' => 'v53F5Pfy', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA ALBEROBELLO,170', 'usercf' => 'CRMSNT61E13E645K', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '2699', 'usercomunen' => 'LOCOROTONDO', 'userdatan' => '1961-05-13', 'userprovn' => 'BA'),
            array('userid' => '605', 'usernome' => 'DINO MAGISTA', 'useremail' => 'dino.magista@gmail.com', 'usertelefono' => '3331233373', 'usercomune' => 'CONVERSANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '5654ffb092cb2dc73c8aa94ffb1b656c', 'orignalpass' => 'dikbl33P', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA L. PIRANDELLO,18', 'usercf' => 'MGSBNR58E20C975U', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '2218', 'usercomunen' => 'CONVERSANO ', 'userdatan' => '1958-05-20', 'userprovn' => 'BA'),
            array('userid' => '606', 'usernome' => 'CHIARELLI PIERANGELO', 'useremail' => 'pierangelo.chiarelli@ufficioweb.com', 'usertelefono' => '3395404585', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f2986879365d778cb3f6923ab3e69147', 'orignalpass' => 'abBRHF4J', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '607', 'usernome' => 'OCCHILUPO LUIGI', 'useremail' => 'luigi.occhilupo@legnodesign.it', 'usertelefono' => '3396348605', 'usercomune' => 'VERNOLE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '932ba97f5efe17707ab057c468429ad6', 'orignalpass' => 'JhI69etU', 'role' => '2', 'status' => '1', 'userprov' => 'LECCE', 'userres' => 'VERNOLE', 'usercf' => 'CCHLGU70C06L049O', 'usercoll' => 'GEOMETRI LAUREATI', 'usercomcoll' => 'LECCE', 'useriscr' => '2855', 'usercomunen' => 'TARANTO', 'userdatan' => '1970-03-06', 'userprovn' => 'TA'),
            array('userid' => '608', 'usernome' => 'PALMISANI LORENZO', 'useremail' => 'lorenzo.palmisani@libero.it', 'usertelefono' => '3382279946', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ed3bcb2d8a77ae8bf691045f195a84eb', 'orignalpass' => 'ALSiASub', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BRINDISI', 'useriscr' => '1533', 'usercomunen' => '', 'userdatan' => '0000-00-00', 'userprovn' => ''),
            array('userid' => '609', 'usernome' => 'GENNARO ORIGLIETTI', 'useremail' => 'gennaro.origlietti@gmail.com', 'usertelefono' => '3275470980', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'db01f424b1f0c9b332965659b465cec9', 'orignalpass' => 'VJbfBy7v', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA MAZZINI n. 44', 'usercf' => 'RGLGNR90L18F839Y', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'CASERTA', 'useriscr' => ' 4631', 'usercomunen' => 'Napoli ', 'userdatan' => '1990-07-18', 'userprovn' => 'NA'),
            array('userid' => '610', 'usernome' => 'FRANCESCO DAMATO', 'useremail' => 'ingdamato@hotmail.com', 'usertelefono' => '3338705574', 'usercomune' => 'PULSANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '78eff2c1bf3f9932fa3af0ac3b5272a4', 'orignalpass' => 'odiKjVup', 'role' => '2', 'status' => '1', 'userprov' => 'TA', 'userres' => 'VIA CASALINI 35', 'usercf' => 'DMTFNC85L02L049A', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'TARANTO', 'useriscr' => '133/B', 'usercomunen' => 'TARANTO', 'userdatan' => '1985-07-02', 'userprovn' => 'TA'),
            array('userid' => '611', 'usernome' => 'CASSANO MIRKO', 'useremail' => 'geometra.cassano@libero.it', 'usertelefono' => '3292178130', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '461cd9e509b2063136e073d5db16d60b', 'orignalpass' => 'SX5hjxWV', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '612', 'usernome' => 'TOMMASO GIGANTE', 'useremail' => 'tommasogigante@gmail.com', 'usertelefono' => '3478409854', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '2ddc5f809b240982b16528100a28b653', 'orignalpass' => 'cjfsvh6C', 'role' => '2', 'status' => '1', 'userprov' => 'ba', 'userres' => 'VIA XXIV MAGGIO n. 4', 'usercf' => 'GGNTMS81E05A662I', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BARI', 'useriscr' => '2787', 'usercomunen' => 'Bari', 'userdatan' => '1981-05-05', 'userprovn' => 'ba'),
            array('userid' => '613', 'usernome' => 'DOMINGA SALVIA', 'useremail' => 'salvia.dominga@gmail.com', 'usertelefono' => '3407358429', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '0240088e0ff51c602c4640123986fdad', 'orignalpass' => 'Y8ELSO10', 'role' => '2', 'status' => '1', 'userprov' => 'BARI', 'userres' => 'MONOPOLI', 'usercf' => 'SLVDNG87P45D508N', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BA', 'useriscr' => '10736', 'usercomunen' => 'FASANO', 'userdatan' => '1987-09-05', 'userprovn' => 'BR'),
            array('userid' => '614', 'usernome' => 'LEPORE GIUSEPPE', 'useremail' => 'leporegiuseppe_1970@libero.it', 'usertelefono' => '3939234590', 'usercomune' => 'MOLA DI BARI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '51bddd15d2965f25a5f8ff82bca2c5f1', 'orignalpass' => 'euV9Xg3W', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIALE EUROPA UNITA, 20/C', 'usercf' => 'LPRGPP70S29C975Q', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '3526', 'usercomunen' => 'CONVERSANO ', 'userdatan' => '1970-11-29', 'userprovn' => 'BA'),
            array('userid' => '615', 'usernome' => 'GIOVANNI ROBERTO', 'useremail' => 'giovanni.roberto68@gmail.com', 'usertelefono' => '3389545768', 'usercomune' => 'PUTIGNANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'c1e2c2e84f6c755b4acc181b4565c711', 'orignalpass' => 'TDdHbUNN', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '616', 'usernome' => 'VITALE GIAMPIERO', 'useremail' => 'ING.GVITALE@gmail.com', 'usertelefono' => '3282494090', 'usercomune' => 'OSTUNI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '1d26e6a2830bba60f659f7acc50cd794', 'orignalpass' => 'jcJwfXOY', 'role' => '2', 'status' => '1', 'userprov' => 'BR', 'userres' => 'VIA ANGIULLI, 43', 'usercf' => '', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BRINDISI', 'useriscr' => '1177', 'usercomunen' => 'CEGLIE MESSAPICA', 'userdatan' => '1976-02-18', 'userprovn' => 'BR'),
            array('userid' => '618', 'usernome' => 'PINTO ROBERTA', 'useremail' => 'Arch.robertapinto@gmail.com', 'usertelefono' => '3357327438', 'usercomune' => 'NOCI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'b417d1872a4075a36563ac2c7d35cc8c', 'orignalpass' => '2ydevKji', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'ZONA A,28', 'usercf' => 'PNTRRT88E42F280I', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BARI', 'useriscr' => '3610', 'usercomunen' => 'Mola di Bari', 'userdatan' => '1988-05-02', 'userprovn' => 'BA'),
            array('userid' => '619', 'usernome' => 'NICOLA CARDO', 'useremail' => 'studio63nicola@gmail.com', 'usertelefono' => '3395025285', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ababf636f45a6c392fc41e6bd19ff28c', 'orignalpass' => 'Jza3aHPp', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '620', 'usernome' => 'RE DAVID COLAGRANDE GIOV.', 'useremail' => 'geom.colagrande@gmail.com', 'usertelefono' => '3928208437', 'usercomune' => 'CONVERSANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8011cd7348ef8534e31485da75e93f65', 'orignalpass' => 'gn2mN7VX', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '623', 'usernome' => 'LAGUARDIA VALERIO', 'useremail' => 'laguardiavalerio@gmail.com', 'usertelefono' => '3896872082', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '13104485fb381849f20def70880556d9', 'orignalpass' => 'wxH3R1rH', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA ROCCO SCOTELLARO,6', 'usercf' => 'LGRVLR90A23F376V', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '4563', 'usercomunen' => 'MONOPOLI', 'userdatan' => '1990-01-23', 'userprovn' => 'BA'),
            array('userid' => '624', 'usernome' => 'FRANCESCO GIODICE', 'useremail' => 'geometrafrancescogiodice@gmail.com', 'usertelefono' => '3394221365', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'faf415a34787ffcc0b56aee22e7eac21', 'orignalpass' => 'Gkn3Dwxl', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '625', 'usernome' => 'IVONE GIANVITO', 'useremail' => 'geom.gianvitoivone@libero.it', 'usertelefono' => '3347472416', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f1502d7eedcc7193f65ee85c22ac5085', 'orignalpass' => 'FUvFzCyl', 'role' => '2', 'status' => '1', 'userprov' => 'ba', 'userres' => 'via della Resistenza,115', 'usercf' => 'VNIGVT93T30C134D', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '4631', 'usercomunen' => 'Castellana Grotte', 'userdatan' => '1993-12-30', 'userprovn' => 'ba'),
            array('userid' => '626', 'usernome' => 'BARLETTA VERONICA', 'useremail' => 'veronicabarletta@virgilio.it', 'usertelefono' => '3926857140', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'd118d4205e1a53fb70eeadd491cc7e4d', 'orignalpass' => 'WccreGTA', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '627', 'usernome' => 'GIUSEPPE PERRICCI', 'useremail' => 'giuseppe.perricci@libero.it', 'usertelefono' => '3492541046', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '72c71e1ffdb9eb3e2f422bc063d2dc56', 'orignalpass' => 'io6IZpui', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'CONTRADA LAMASCRASCIOLA 590/A', 'usercf' => 'PRR GPP 75S07 F376H', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '4605', 'usercomunen' => 'MONOPOLI', 'userdatan' => '1975-11-07', 'userprovn' => 'BA'),
            array('userid' => '628', 'usernome' => 'CAPITANEO PIETRO FRANCESCO', 'useremail' => 'pietro.capitaneo@gmail.com', 'usertelefono' => '3483315018', 'usercomune' => 'BARI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8cff8f6201aa940bee2266991f6f2758', 'orignalpass' => 'B2ixIJp3', 'role' => '2', 'status' => '1', 'userprov' => 'BARI', 'userres' => 'BARI', 'usercf' => 'CPTPRF64H20A662X', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BARI', 'useriscr' => '1071', 'usercomunen' => 'BARI', 'userdatan' => '1964-06-20', 'userprovn' => 'BARI'),
            array('userid' => '629', 'usernome' => 'VITO DALESSANDRO', 'useremail' => 'vitodalessandro@live.it', 'usertelefono' => '3928251650', 'usercomune' => 'CONVERSANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '0e963eed31005104abbd770ca7ab2253', 'orignalpass' => 'Awc1EiAz', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => '', 'usercf' => 'DLSVTI83A23C975S', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '10350', 'usercomunen' => 'Conversano', 'userdatan' => '1983-01-13', 'userprovn' => 'BA'),
            array('userid' => '630', 'usernome' => 'FRANCESCO ABATE', 'useremail' => 'abate.francesco@libero.it', 'usertelefono' => '3204725555', 'usercomune' => 'MAGLIE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '41e2cba2cfbcb38dabc9347581f7560e', 'orignalpass' => 'w9NfXtcw', 'role' => '2', 'status' => '1', 'userprov' => 'LE', 'userres' => 'VIA GALLIPOLI,97', 'usercf' => 'BTAFNC61T04E815T', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'LECCE', 'useriscr' => '2104', 'usercomunen' => 'MAGLIE', 'userdatan' => '1961-12-04', 'userprovn' => 'LE'),
            array('userid' => '631', 'usernome' => 'AQUILINO PIETRO G.', 'useremail' => 'fosarchitetti@gmail.com', 'usertelefono' => '3395691569', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ea3281d0279fb150e956f79a59ee162e', 'orignalpass' => 'w0wve1eC', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'via Putignano n. 92', 'usercf' => 'QLNPRG78R09A662Q', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'PROVINCIA DI BARI', 'useriscr' => '2858', 'usercomunen' => 'BARI', 'userdatan' => '1978-10-09', 'userprovn' => 'BA'),
            array('userid' => '633', 'usernome' => 'SPATARI NICODEMO', 'useremail' => 'nicospatari@libero.it', 'usertelefono' => '3894995109', 'usercomune' => 'MAMMOLA ', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '376751e73a9a2999f18b24f22836083a', 'orignalpass' => '29gA6oVt', 'role' => '2', 'status' => '1', 'userprov' => 'RC', 'userres' => 'VIA MULINO,66', 'usercf' => 'SPTNDM85M04D976O', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'REGGIO CALBRIA', 'useriscr' => 'A2695', 'usercomunen' => 'LOCRI', 'userdatan' => '1985-08-04', 'userprovn' => 'RC'),
            array('userid' => '634', 'usernome' => 'VENUTO GIANPIERO', 'useremail' => 'gp.venuto@virgilio.it', 'usertelefono' => '3356639817', 'usercomune' => 'TARANTO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '01cf2fb7a0b14f17cf2e3a0f6ca6ad65', 'orignalpass' => 'IEHLfKs4', 'role' => '2', 'status' => '1', 'userprov' => 'ta', 'userres' => 'Via Giovinazzi, 91 ', 'usercf' => 'VNTGPR66H21L049K', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'TARANTO', 'useriscr' => '1375', 'usercomunen' => 'Taranto ', 'userdatan' => '1966-06-21', 'userprovn' => 'ta'),
            array('userid' => '635', 'usernome' => 'DARCONZA GIANLUCA', 'useremail' => 'gianluca.darconza@gmail.com', 'usertelefono' => '3472460229', 'usercomune' => 'PUTIGNANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f831463c703ba0eeb70ba0ba40201d1e', 'orignalpass' => 'd3pBDt9j', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA ROMA, 2', 'usercf' => 'DRCGLC79M26H096E', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '4187', 'usercomunen' => 'PUTIGNANO', 'userdatan' => '1979-08-26', 'userprovn' => 'BA'),
            array('userid' => '636', 'usernome' => 'MAURO SCHETTINI', 'useremail' => 'schettinimauro@libero.it', 'usertelefono' => '3382700842', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '00c5b1d468a1ad12fc3537efe3f3588f', 'orignalpass' => '29liSrgG', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA TRATTURO SPAGNUOLO,10', 'usercf' => 'SCHMRA70A21H096Z', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '4614', 'usercomunen' => 'PUTIGNANO', 'userdatan' => '1970-01-21', 'userprovn' => 'BA'),
            array('userid' => '637', 'usernome' => 'CIURLIA ANTONIO', 'useremail' => 'antoniociurlia64@gmail.com', 'usertelefono' => '3394822677', 'usercomune' => 'TAURISANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'dacbce28ba46f5babc617060c9555a6c', 'orignalpass' => '4uEms6XC', 'role' => '2', 'status' => '1', 'userprov' => 'LE', 'userres' => 'VIA C. BAGLIVO,36', 'usercf' => 'CRLNTN64M02L064F', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'LECCE', 'useriscr' => '772', 'usercomunen' => 'TAURISANO', 'userdatan' => '1964-08-02', 'userprovn' => 'LE'),
            array('userid' => '639', 'usernome' => 'TG ALLUMINIO', 'useremail' => 'sales@tgalluminio.it', 'usertelefono' => '0804176636', 'usercomune' => 'MONOPOLI', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '3cfec1791df91587bd3171ad2e38baa7', 'orignalpass' => 'axlZeIg9', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '640', 'usernome' => 'EUROCOLOR CeG', 'useremail' => 'f.campanella@fgminfissi.it', 'usertelefono' => '3935470072', 'usercomune' => 'PUTIGNANO', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'ccff530f10695ac19d49f7b788b782e6', 'orignalpass' => 'kk8GjOSy', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '641', 'usernome' => 'DE LEONARDIS', 'useremail' => 'newinfiss@gmail.com', 'usertelefono' => '3487410980', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'cc79e428688cb23be800c695a25f66cc', 'orignalpass' => 'HmsSWArl', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '642', 'usernome' => 'FRANCHINI', 'useremail' => 'angelo.franchini@fvbs.it', 'usertelefono' => '3282616489', 'usercomune' => 'NOCI', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '61cda14f77f25f4caa7aee45b9bce182', 'orignalpass' => 'lQ0bQlHs', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '643', 'usernome' => 'SAAV', 'useremail' => 'saavsrl@libero.it', 'usertelefono' => '3288671614', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'de1f2f05fec70c1f464d456a67132c1b', 'orignalpass' => 'ShzxO1g3', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '644', 'usernome' => 'ARREDO2', 'useremail' => 'arredo2srl@gmail.com', 'usertelefono' => '3897846095', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '32455aa92094a53e03ae42ce91a719c1', 'orignalpass' => '4RMxr2qo', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '645', 'usernome' => 'INFISSI DE CARLO', 'useremail' => 'decarlo@decarlo.it', 'usertelefono' => '0998833511', 'usercomune' => 'MOTTOLA', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'ea07cf5bb5a9593ec4e1a4b6dc09ee42', 'orignalpass' => 'EZNiaom8', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '646', 'usernome' => 'BARBIERI', 'useremail' => 'stlavori@gmail.com', 'usertelefono' => '3292357718', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '5f217d4ce86c8dc8de24da0c8d33b2dc', 'orignalpass' => 'STiklHLU', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '649', 'usernome' => 'ROBERTO', 'useremail' => 'roberto.greengen@gmail.com', 'usertelefono' => '3335618930', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '209bd754bdeb01334e133f47a6f16b4c', 'orignalpass' => '82MGS6iL', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '650', 'usernome' => 'GABRIELE', 'useremail' => 'gabriele.greengen@gmail.com', 'usertelefono' => '3661808656', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'a5815c0b4b888ac3908ff680b93af6bd', 'orignalpass' => 'qToVTEH4', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '651', 'usernome' => 'ANTONELLA', 'useremail' => 'cessione.greengen@gmail.com', 'usertelefono' => '3935127046', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'b9e32526c2041521101e8d073b4ff708', 'orignalpass' => 'Ejsb9jiV', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '652', 'usernome' => 'MIRKO', 'useremail' => 'ordini.greengen@gmail.com', 'usertelefono' => '3319871623', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'd87a49a0722e91a3389ada804804e137', 'orignalpass' => 'rnZGOhgT', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '653', 'usernome' => 'DANIELE', 'useremail' => 'daniele2.greengen@gmail.com', 'usertelefono' => '3924504741', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'd2f637bab7f2b0e6fc32123d594ff353', 'orignalpass' => '1DbjRAIq', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '654', 'usernome' => 'PAOLO MICCOLIS', 'useremail' => 'paolo.greengen@gmail.com', 'usertelefono' => '3389846421', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '667e91f195e270b95d8d4fb7b023fb1b', 'orignalpass' => 'u5CA92dt', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '655', 'usernome' => 'BENEDETTO', 'useremail' => 'greengengroupsrl@gmail.com', 'usertelefono' => '3280474105', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '1009bfbee628f04ab30fa4ad7c8a9167', 'orignalpass' => 'Mk9TEW0W', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '656', 'usernome' => 'MIRIAM LACATENA', 'useremail' => 'miriam.greengen@gmail.com', 'usertelefono' => '3289521571', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '186ba02ee1be48da9294dacdc14d7c8e', 'orignalpass' => 'T5NiFpu1', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '657', 'usernome' => 'ANGELICA', 'useremail' => 'angelica.galiano96@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '124fc1330dc561dec015dc110622344b', 'orignalpass' => 'epvEDerL', 'role' => '1', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '658', 'usernome' => 'GIANNI AMMIRATO', 'useremail' => 'gianni.ammirato@gmail.com', 'usertelefono' => '3388324909', 'usercomune' => '', 'usertipo' => 'Elettricista', 'nomecontatto' => '', 'userpassword' => 'b62f76729a79813794986af1121242f9', 'orignalpass' => 'Fnkm47Hv', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '659', 'usernome' => 'BENEDIL', 'useremail' => 'BENEDIL@ggg.it', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '2e0b0552e43f81adf6cd6d6041d91df2', 'orignalpass' => 'xajfIzB1', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '660', 'usernome' => 'BIANCO DOMENICO', 'useremail' => 'DOMENICOBIANCO58@GMAIL.COM', 'usertelefono' => '', 'usercomune' => 'PUTIGNANO', 'usertipo' => 'Elettricista', 'nomecontatto' => 'BIANCO DOMENICO', 'userpassword' => '3171ad1c242edf2a2880f47c5ff0bf78', 'orignalpass' => 'Tum1WRLP', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '661', 'usernome' => 'BP ELETTRICA', 'useremail' => 'BP ELETTRICA', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Elettricista', 'nomecontatto' => '', 'userpassword' => 'f4ad4ffb781951a3047382e1d7c45420', 'orignalpass' => 'ZV4G5t3a', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '662', 'usernome' => 'BUILDING THE FUTURE SRL', 'useremail' => 'giambattistalaera58@gmail.com', 'usertelefono' => '3336670252', 'usercomune' => 'Castellana Grotte', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '7e570e01893781de81b1cc0b84d8cd7c', 'orignalpass' => 'ZegfqLIq', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '663', 'usernome' => 'COGEM SAS', 'useremail' => 'dinolog58@libero.it', 'usertelefono' => '0803217073', 'usercomune' => 'CONVERSANO', 'usertipo' => 'Edile', 'nomecontatto' => 'LOGRECO', 'userpassword' => 'fb4239804934c52de2fd8c2eaa53f448', 'orignalpass' => 'YnJE4XjE', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '664', 'usernome' => 'CONFORTI ONOFRIO', 'useremail' => 'onofrioconforti@libero.it', 'usertelefono' => '', 'usercomune' => 'NOCI', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '8a08404f80874c87924e2990f3c30008', 'orignalpass' => 'v7HDLLw7', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '665', 'usernome' => 'DE CRESCENZO LORENZO', 'useremail' => 'motoclubfoxvalley@libero.it', 'usertelefono' => '3478298497', 'usercomune' => 'PALAGIANO', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => '0b6b2b83012845cbe5b686582595a1e9', 'orignalpass' => 'h5lz6I46', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '666', 'usernome' => 'FABIO DE FILIPPO', 'useremail' => 'FABIO DE FILIPPO', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Elettricista', 'nomecontatto' => 'FABIO DE FILIPPO', 'userpassword' => '6a6f45826eaa13ebdd52d41c12b98aeb', 'orignalpass' => 'dhfEiZ0F', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '667', 'usernome' => 'FP EDIL SRLS', 'useremail' => 'FP EDIL SRLS', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => 'PRISCIANO FRANCESCO', 'userpassword' => 'c823049db49c0db07c67fa78c3cc6907', 'orignalpass' => 'kRKfKGV6', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '668', 'usernome' => 'FUTUR SER DI ANTIONO ERRIQUEZ', 'useremail' => 'FUTUR SER DI ANTIONO ERRIQUEZ', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => 'ANTIONO ERRIQUEZ', 'userpassword' => '6de09cda01713334fd54ca8816c488c5', 'orignalpass' => 'U9mzrU0H', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '669', 'usernome' => 'VITO GALIANO', 'useremail' => 'vito.galiano@pec.it', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => 'VITO GALIANO', 'userpassword' => '120e38c7aca80c7bd73f70cac78a3652', 'orignalpass' => '6dxBOYCZ', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '670', 'usernome' => 'GED', 'useremail' => 'GED', 'usertelefono' => '', 'usercomune' => 'MASSAFRA', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => 'c2799b5c31bff195290a887a20a91549', 'orignalpass' => 'Kh2o6vwG', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '671', 'usernome' => 'GIANNI SIMONE', 'useremail' => 'GIANNI SIMONE', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Elettricista', 'nomecontatto' => '', 'userpassword' => 'af543198fb80cd051f191c92a8f0c943', 'orignalpass' => '2fDO2Z2Z', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '672', 'usernome' => 'GIUSEPPE PERTA IMPRESA', 'useremail' => 'GIUSEPPEPERTA@GGG.IT', 'usertelefono' => '', 'usercomune' => 'ALBEROBELLO', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => '664a2324065042bd707f0ef3b751810f', 'orignalpass' => '3PfyNeer', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '673', 'usernome' => 'IDROTERMICA D ALESSANDRO', 'useremail' => 'IDROTERMICA D ALESSANDRO', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => 'f3edc9c7924a172bc6bbe4b52a52cf5e', 'orignalpass' => '7qjrco5G', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '674', 'usernome' => 'LOREL TEC', 'useremail' => 'LORELTEC@GGG.IT', 'usertelefono' => '', 'usercomune' => 'ALBEROBELLO', 'usertipo' => 'Elettricista', 'nomecontatto' => 'LEO PIEPOLI', 'userpassword' => 'fb706ecc6adf2a9c9fdd46b0628a029f', 'orignalpass' => 'DzWE9HIt', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '675', 'usernome' => 'LORUSSO GIUSEPPE', 'useremail' => 'LORUSSOGIUSEPPE64@GMAIL.COM', 'usertelefono' => '', 'usercomune' => 'ALTAMURA', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '3c9292aff81066dc182169550ec842d5', 'orignalpass' => 'pFElFQEB', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '676', 'usernome' => 'LORUSSO SRLS', 'useremail' => 'LORUSSO SRLS', 'usertelefono' => '', 'usercomune' => 'CONVERSANO', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '1e62fb9276c5b1e6e988d058e6157397', 'orignalpass' => '72ik5Xow', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '677', 'usernome' => 'MARCO CONSOLI', 'useremail' => 'MARCOCONSOLI@ggg.it', 'usertelefono' => '', 'usercomune' => 'CISTERNINO', 'usertipo' => 'Idraulico', 'nomecontatto' => 'MARCO CONSOLI', 'userpassword' => 'ff8f34cafc18469bd72b8c314826c125', 'orignalpass' => 'rxzajzaX', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '678', 'usernome' => 'FRANCESCO MARTELLOTTA', 'useremail' => 'ecoimpianti80@libero.it', 'usertelefono' => '3925636318', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => '8a8a0f46aebd2c644b403e9434012754', 'orignalpass' => '0fpyQlVz', 'role' => '3', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '679', 'usernome' => 'MIRDITA SRL', 'useremail' => 'MIRDITASRL@GGG.IT', 'usertelefono' => '', 'usercomune' => 'PUTIGNANO', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '5645e1585ce3c0da62d08378030c38da', 'orignalpass' => 'ShGvGOAf', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '680', 'usernome' => 'MIRZI VITO', 'useremail' => 'studiocondito@gmail.com', 'usertelefono' => '', 'usercomune' => 'PUTIGNANO', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => '192c144a68fcaadea0c2b4e1ea890d6f', 'orignalpass' => 'enOwR11W', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '681', 'usernome' => 'NOCITA', 'useremail' => 'nocitametaldesign@gmail.com', 'usertelefono' => '0836411800', 'usercomune' => 'BAGNOLO DEL SALENTO', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => 'f3574035a328915e01c3137e0fc20273', 'orignalpass' => 'F7A31mAo', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '682', 'usernome' => 'PR COSTRUZIONI', 'useremail' => 'annadrgentile@gmail.com', 'usertelefono' => '', 'usercomune' => 'NOCI', 'usertipo' => 'Edile', 'nomecontatto' => 'PIERPI RAGUSA', 'userpassword' => '84407dc260da9956f24ebeeb88f5f988', 'orignalpass' => 'BRKzLpZV', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '683', 'usernome' => 'PALAZZO GIANNI', 'useremail' => 'giannipalazzo78@gmail.com', 'usertelefono' => '', 'usercomune' => 'ALBEROBELLO', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '75f7fbef6fe66c6a40cdd3376c0f3e8f', 'orignalpass' => 'I5pAiczn', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '684', 'usernome' => 'PANFILO BENITO', 'useremail' => 'PANFILOBENITO@ggg.it', 'usertelefono' => '3392305566', 'usercomune' => 'NOICATTARO', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '3c983b9186dcf8945aada71e7ebc3d01', 'orignalpass' => 'h5rKCRN9', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '685', 'usernome' => 'PERTA SRL', 'useremail' => 'info@pertasrl.it', 'usertelefono' => '', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Edile', 'nomecontatto' => 'DARIO PERTA', 'userpassword' => '62ad3ba7c864c7043c2c67e9728fb038', 'orignalpass' => 'gzlcAYoa', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '686', 'usernome' => 'PISANI', 'useremail' => 'PISANI', 'usertelefono' => '', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '5780bed084cbb630f89ddd4d30b64ab2', 'orignalpass' => '1IHHx8rf', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '687', 'usernome' => 'RF IMPIANTI', 'useremail' => 'RF@IMPIANTI.COM', 'usertelefono' => '', 'usercomune' => 'LECCE', 'usertipo' => 'Elettricista', 'nomecontatto' => 'STEFANELLI ANGELO', 'userpassword' => 'eaeb592a4b28506252930548bf67c6c3', 'orignalpass' => 'hvun8IL2', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '688', 'usernome' => 'SAP COSTRUZIONI', 'useremail' => 'SAP COSTRUZIONI', 'usertelefono' => '', 'usercomune' => 'ALBEROBELLO', 'usertipo' => 'Edile', 'nomecontatto' => 'ANGELILLO MARIO', 'userpassword' => 'e11906ccd261e38ba56bc2f03ca269ed', 'orignalpass' => 'W4wU6sv0', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '689', 'usernome' => 'SEVERINMO', 'useremail' => 'aecedile@gmail.com', 'usertelefono' => '', 'usercomune' => 'MATERA', 'usertipo' => 'Edile', 'nomecontatto' => 'SEVERINO DE CARLO', 'userpassword' => 'adfa8bc3d0b691e7668ef5a273206930', 'orignalpass' => 'xaZCvI8F', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '690', 'usernome' => 'TECNOIMPIANTI SNC', 'useremail' => 'TECNOIMPIANTI SNC', 'usertelefono' => '', 'usercomune' => 'NOCI', 'usertipo' => 'Elettricista', 'nomecontatto' => 'SIMONE MARIO', 'userpassword' => '5693e58f3d34a2821b677d8a41c18bca', 'orignalpass' => 'O0N4Np4A', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '691', 'usernome' => 'STEFANO POLIGNANO', 'useremail' => 'STEFANO POLIGNANO', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => '7f1b5444bbfeba2190e17d7f60cc476e', 'orignalpass' => 'XiQ271vJ', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '692', 'usernome' => 'VIERRE RESTAURI', 'useremail' => 'vierre.restauri@tiscali.it', 'usertelefono' => '335434245', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Edile', 'nomecontatto' => 'VINCENZO RIZZI', 'userpassword' => 'dd67ed21a0fc2558c2f05caa7d1e173f', 'orignalpass' => 'D45B83Nv', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '693', 'usernome' => 'VITO LONGO', 'useremail' => 'VITO LONGO', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '723d7881fe72ade0be2074c9c1920673', 'orignalpass' => 'Pqgfswlb', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '694', 'usernome' => 'Test. Commercialista 1', 'useremail' => 'Test.Commercialista@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '0de589a29707cf45858128fd13732717', 'orignalpass' => 'TPoFmyq9', 'role' => '4', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '695', 'usernome' => 'Test. Commercialista 2', 'useremail' => 'tes.commercialista2@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f4bb283b458415f7dead83ee1a42bbb2', 'orignalpass' => 'LrRJwB1z', 'role' => '4', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '696', 'usernome' => 'Test Fotovoltaico', 'useremail' => 'test.ingegnere@gmail.com', 'usertelefono' => '', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '086a7e7e53e26f7a8fb19b74620811ea', 'orignalpass' => 'tIkljtHB', 'role' => '5', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '697', 'usernome' => 'Test Commercialista2', 'useremail' => 'Test.Commercialista2@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f9e356d70a58548592c087e40baf90f1', 'orignalpass' => 'bgmRtb9p', 'role' => '4', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '698', 'usernome' => 'TEST3', 'useremail' => 'tes.commercialista3@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'afbc8ab111e265e8ec76340e6f302433', 'orignalpass' => 'JUMolBNr', 'role' => '4', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '699', 'usernome' => 'PASQUALE IL TECNICO', 'useremail' => 'ale.rizzi02@gmail.com', 'usertelefono' => '3335618930', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '891b228b734cd09c609e7861d8790d46', 'orignalpass' => '7qaHcJQZ', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '700', 'usernome' => 'Angelica Galiano', 'useremail' => 'angelica.galiano96+1@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '5e259a2d47792917f1b61fbdec350b39', 'orignalpass' => 'NW6BswMK', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '701', 'usernome' => 'd\'aprile', 'useremail' => 'angelica.galiano96+123@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'd\'aprile', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '9949cf4f2fbbcd92c21a26c90fdcd1b6', 'orignalpass' => 'bXVspidC', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '702', 'usernome' => 'd\'aprile', 'useremail' => 'angelica.galiano96+111@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'd\'aprile', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '88dc451a3e6ac746282e616413a4df25', 'orignalpass' => 'nHjCJiUt', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '703', 'usernome' => 'd\'aprile', 'useremail' => 'angelica.galiano96+1111@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'd\'aprile', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'cf8a2c5703ec8884f6ff69bb1da9490e', 'orignalpass' => 'ugMZFonE', 'role' => '4', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '704', 'usernome' => 'd\'aprile', 'useremail' => 'angelica.galiano96+222@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'd\'aprile', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'e820695ff32d94af024de52d4684a741', 'orignalpass' => '7FFcWfj7', 'role' => '5', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '705', 'usernome' => 'd\'aprile test', 'useremail' => 'angelica.galiano96+999@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'd\'aprile test', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'fda6d543d00112701bfe1475aaa0cd73', 'orignalpass' => 'NJGWoWZA', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '706', 'usernome' => 'd\'aprile', 'useremail' => 'angelica.galiano96+666@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'd\'aprile', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '2c050db578e227bd3219967b7ab88b67', 'orignalpass' => 'Po6qMxGl', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '707', 'usernome' => 'Onofrio Manghisi', 'useremail' => 'onofrio08@gmail.com', 'usertelefono' => '3455994272', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => NULL, 'nomecontatto' => NULL, 'userpassword' => '9f27410725ab8cc8854a2769c7a516b8', 'orignalpass' => 'green', 'role' => '1', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '708', 'usernome' => 'Angelica Galiano', 'useremail' => 'emailpercosegratis3@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8e0a6f44831358fa1965413521caab4a', 'orignalpass' => 'aiBIi5jK', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '709', 'usernome' => 'INTERNOM', 'useremail' => 'mazzarelli.giu@gmail.com', 'usertelefono' => '3395691569', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Infissi', 'nomecontatto' => 'MAZZARELLI GIUSEPPE', 'userpassword' => '93d2972c5bd327b9c306829aa9af8f0a', 'orignalpass' => 'BDUv0sZF', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '710', 'usernome' => 'ANGELO RINALDI ', 'useremail' => 'angelodomenico.rinaldi@gmail.com', 'usertelefono' => '335471669', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '1e0c95311c632aaba5081885f1cfa21a', 'orignalpass' => 'tUimTbXu', 'role' => '2', 'status' => '1', 'userprov' => 'Ba', 'userres' => 'CASTELLANA GROTTE', 'usercf' => 'RNLNLD58P24C134Q', 'usercoll' => 'Ingegneri di Bari ', 'usercomcoll' => 'BARI', 'useriscr' => ' A-4386', 'usercomunen' => 'Castellana Grotte', 'userdatan' => '1958-09-24', 'userprovn' => 'BA'),
            array('userid' => '711', 'usernome' => 'CAMICIA DOMENICO', 'useremail' => 'domenicocamicia@gmail.com', 'usertelefono' => '3408557235', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '41ae277c2574d11219afd59e78d97eff', 'orignalpass' => 'kcjA96nr', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA NAZARIO SAURO n. 36 ', 'usercf' => 'CMCDNC90P27C134A', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BARI', 'useriscr' => '3884', 'usercomunen' => 'CASTELLANA GROTTE', 'userdatan' => '1990-09-27', 'userprovn' => 'BA'),
            array('userid' => '712', 'usernome' => 'RODIO ANDREA', 'useremail' => 'andrearodio1@gmail.com', 'usertelefono' => '3286554024', 'usercomune' => 'LOCOROTONDO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '03ffeb331b0bb056ae1770255ba1d238', 'orignalpass' => 'N17S33zE', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'S.C. 102 C.DA RIZZO n. 28', 'usercf' => 'RDONDR85H19C741P', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BA', 'useriscr' => '3127', 'usercomunen' => ' Cisternino', 'userdatan' => '1985-06-19', 'userprovn' => 'BR'),
            array('userid' => '713', 'usernome' => 'Angelica Galiano', 'useremail' => 'documentazione.greengen@gmail.com', 'usertelefono' => '3206211067', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '55948211e4c7040d576a040ac772a6cb', 'orignalpass' => 'FdwDbDtK', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '714', 'usernome' => 'DANIELE', 'useremail' => 'daniele.greengen@gmail.com', 'usertelefono' => '', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'a27441321b467e95243bbeb13e9fa528', 'orignalpass' => 'Igkus8HY', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '715', 'usernome' => 'GIANCARLO', 'useremail' => 'geometraceccioli@gmail.com', 'usertelefono' => '', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '369b91b035f0060faec33597c00a4ca8', 'orignalpass' => 'Fe3HJrjw', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '716', 'usernome' => 'FRANCESCO CIURLIA ing.', 'useremail' => 'frciurlia@gmail.com', 'usertelefono' => '3382492009', 'usercomune' => 'Taurisano', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'e90df6fb4afa4836041380917dda081d', 'orignalpass' => 'bkPehMNr', 'role' => '2', 'status' => '1', 'userprov' => 'LE', 'userres' => 'VIA L.ARIOSTO,25', 'usercf' => 'CRLFNC64H14L064W', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'LECCE', 'useriscr' => '1890', 'usercomunen' => 'TAURISANO', 'userdatan' => '1964-06-14', 'userprovn' => 'LE'),
            array('userid' => '717', 'usernome' => 'RAFFAELLA BIANCO ING.', 'useremail' => 'raffaellabianco@libero.it', 'usertelefono' => '3926895552', 'usercomune' => 'PUTIGNANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '1e23522fa271532c7759840d653058a3', 'orignalpass' => 'm7GhmM9a', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VUIA MULINI,3', 'usercf' => ' BNCRFL73H65H096R', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '6238', 'usercomunen' => 'PUTIGNANO', 'userdatan' => '1973-06-25', 'userprovn' => 'BA'),
            array('userid' => '718', 'usernome' => 'DONATO BRAMATO', 'useremail' => 'bramato.donato@libero.it', 'usertelefono' => '3381082060', 'usercomune' => 'TRICASE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '1c077cc0e43b77b15b7e9685cf2d6b62', 'orignalpass' => 'QK6cnm3W', 'role' => '2', 'status' => '1', 'userprov' => 'LE', 'userres' => 'via Roberto Ardigò snc', 'usercf' => 'BRMDNT59A22L419T', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'LECCE', 'useriscr' => '1163', 'usercomunen' => 'TRICASE', 'userdatan' => '1959-01-22', 'userprovn' => 'LE'),
            array('userid' => '719', 'usernome' => 'SETTANNI GIUSEPPE', 'useremail' => 'settannigiuseppe@tiscali.it', 'usertelefono' => '0808094176', 'usercomune' => 'TRIGGIANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '42a6f940ed1d8281d379eeeee20ab2fe', 'orignalpass' => 'qn9xZtx7', 'role' => '4', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '720', 'usernome' => 'FRANCESCO RIZZI', 'useremail' => 'ragfrancescorizzi@gmail.com', 'usertelefono' => '0804962298', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8c4479b54616d468ff97677169d46ee3', 'orignalpass' => 'P4KaXDeF', 'role' => '4', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '721', 'usernome' => 'GIOVANNI GUARINO', 'useremail' => 'gio.guarino@libero.it', 'usertelefono' => '3406138425', 'usercomune' => 'TARANTO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'c08e94c1da416342b01f926ed56b0ea0', 'orignalpass' => '8Y9PbqKG', 'role' => '4', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '722', 'usernome' => 'G. DANIELLO', 'useremail' => 'g.daniello@confartigianatobari.it', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'efcb0bf7cb77d7e69b3cca8d9306959f', 'orignalpass' => 'zeh0g7A3', 'role' => '4', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '723', 'usernome' => 'z test ', 'useremail' => 'qadeerabbas0347@gmail.com', 'usertelefono' => '78676868686', 'usercomune' => 'Grootte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '522663251e8ef0e49400ed060f5182c9', 'orignalpass' => 'oZjKloM1', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '724', 'usernome' => 'test', 'useremail' => 'angelica.galianogb@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '56951b86938939d5aaee13d791378009', 'orignalpass' => 'wO9I7xPp', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '725', 'usernome' => 'Angelica Galiano', 'useremail' => 'angelica.galiano96@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '929b6379fd8a0f90013a44ff335ef38c', 'orignalpass' => '6r1CBpqH', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '727', 'usernome' => 'Angelica Galiano', 'useremail' => 'angelica.galiano96+operaio@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '3d237a7f38d0b5823bed2fb862581e9c', 'orignalpass' => 'zDESPJhx', 'role' => '7', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '728', 'usernome' => 'Angelica Galiano', 'useremail' => 'angelica.galiano96+operaio2@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '4af3b71d68aa0af42337e2c25a5de8d3', 'orignalpass' => 'xLTY24ST', 'role' => '7', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '729', 'usernome' => 'GIUSEPPE PERTA', 'useremail' => 'pertagiuseppe69@gmail.com', 'usertelefono' => '3921064788', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '5342cd6bb6342e8ecaedb59e9d626dcc', 'orignalpass' => '1pc5CxN1', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '731', 'usernome' => 'Leo Piepoli', 'useremail' => 'leopiepoli1@gmail.com', 'usertelefono' => '3385327891', 'usercomune' => 'Alberobello', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '87b577e38f1121034be87c112d1b5b50', 'orignalpass' => 'O0pOkAZC', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '732', 'usernome' => 'Annalisa', 'useremail' => 'annalisa.greengen@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'c44bc0dc2c61ee08f4a4477087237da8', 'orignalpass' => 'CDzaYUqD', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '733', 'usernome' => 'Alessio Sumerano', 'useremail' => 'ale.sumerano@icloud.com', 'usertelefono' => '3291938079', 'usercomune' => 'Alberobello', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '2d4222c65d0c02eb35f7bf09bba9d1ee', 'orignalpass' => 'skwcH3eI', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '734', 'usernome' => 'Flavio Ciliberti', 'useremail' => 'ccliberty@hotmail.it', 'usertelefono' => '3200856852', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '61d5f8bcbb65e01279fc4253cd5e80aa', 'orignalpass' => 'zkNIpdf2', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '735', 'usernome' => 'LUIGI SCHITTULLI', 'useremail' => 'gigimarica72@gmail.com', 'usertelefono' => '3299554787', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '2607b36a1ff1067247f5ddca471d496f', 'orignalpass' => '0vqt1z1o', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '736', 'usernome' => 'Danilo Pace', 'useremail' => 'dp.skipper90@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'bdf25a129090f159ff234198fe2cdde5', 'orignalpass' => 'oO0LMWDp', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '737', 'usernome' => 'Ruben Campos', 'useremail' => 'rudaca037@gmail.com', 'usertelefono' => '3881904578', 'usercomune' => 'Bari', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f54af848f1332d2755b5ba65728d1a78', 'orignalpass' => 'YOLEj5TZ', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '739', 'usernome' => 'NICO GENTILE', 'useremail' => 'nicogentile1984@gmail.com', 'usertelefono' => '3204096063', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '7c3e67352efca4ac044ac6f9c44b676d', 'orignalpass' => 'fxuSb746', 'role' => '7', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '740', 'usernome' => 'NICO GENTILE', 'useremail' => 'nicogentile1994@gmail.com', 'usertelefono' => '3204096063', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '7b0d29ec347ac70d9e1e70d447a6bb41', 'orignalpass' => '1EzlHgK6', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '741', 'usernome' => 'ANGELO MAGAZZESE', 'useremail' => 'magazzese88@gmail.com', 'usertelefono' => '3661856565', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8add7fa66eafff4e9d4134bc4ecc107c', 'orignalpass' => 'M7xXdstx', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '742', 'usernome' => 'felipe isensee', 'useremail' => 'felipe.isensee27@gmail.com', 'usertelefono' => '3890233009', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '02a2e455396744e3f27962a86e9a17b5', 'orignalpass' => 'XmtKT4Jr', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '743', 'usernome' => 'Francesco Ladogana', 'useremail' => 'francescoladogana76@gmail.com', 'usertelefono' => '3476447273', 'usercomune' => 'Monopoli', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '748d7866b9f6ea1abd9a1564efd8495e', 'orignalpass' => 'FOiLYk4N', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '744', 'usernome' => 'Raffaele Veccaro', 'useremail' => 'raffaele.94veccaro@gmail.com', 'usertelefono' => '3276660937', 'usercomune' => 'Alberobello', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '480ff256ed77e2047e39d345114366f8', 'orignalpass' => 'GHY9TpdK', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '745', 'usernome' => 'GIORGIO COLUCCI', 'useremail' => 'coluccigiorgio96@gmail.com', 'usertelefono' => '3516417057', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '1926eb58d7511153a6f7396c1b7eb496', 'orignalpass' => '1hwddft6', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '746', 'usernome' => 'Domenico Amatulli', 'useremail' => 'domenicoamatulli32@gmail.com', 'usertelefono' => '3894405049', 'usercomune' => 'Noci', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '67a209e5383c5057bdcf45d917c698b1', 'orignalpass' => '7s4zQm0Q', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '747', 'usernome' => 'CM SERRAMENTI SAS di COMMISSO M. & C.', 'useremail' => 'cmserramenti@cmserramenti.it', 'usertelefono' => '0964388034', 'usercomune' => 'SIDERNO', 'usertipo' => 'Infissi', 'nomecontatto' => 'MICHELE COMMISSO', 'userpassword' => 'bdc21ceacb9a32160be82cc2470bcd15', 'orignalpass' => 'jpkU3aZy', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '748', 'usernome' => 'MIHAILA TEODOR NICOLAE', 'useremail' => 'flipper_11@yahoo.com', 'usertelefono' => '', 'usercomune' => 'MILANO', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'bef3b2922f8d97bd3b532df91ef7e64a', 'orignalpass' => 'wSGyfKmi', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '749', 'usernome' => 'GIOVART 1990', 'useremail' => 'GIOVART1990@GGG.COM', 'usertelefono' => '', 'usercomune' => 'SAN GIORGIO IONICO', 'usertipo' => 'Infissi', 'nomecontatto' => 'MARIATERESA CORALLO', 'userpassword' => '74283214f642fa53ff412d40e04f957f', 'orignalpass' => 'dj8bp9UD', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '750', 'usernome' => 'IMEC SUD', 'useremail' => 'info@imecsud.it', 'usertelefono' => '338891328', 'usercomune' => 'NOCI', 'usertipo' => 'Infissi', 'nomecontatto' => 'GENNARO MAELLARO', 'userpassword' => '9f7cd400038f7d19ff26c2550fcd7814', 'orignalpass' => 'w7IhsyZH', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '751', 'usernome' => 'DE NUNZIO PIETRO', 'useremail' => 'denunzio@ggg.com', 'usertelefono' => '3687657661', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'c393da0f8b348f800d4cebe6d58ac61d', 'orignalpass' => '13Gahzg7', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '752', 'usernome' => 'METAL S.R.L.', 'useremail' => 'metallserramenti@gmail.com', 'usertelefono' => '0804240425', 'usercomune' => 'POLIGNANO A MARE', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '7b63fcc3bce9dded02e6bc03c12ca5f3', 'orignalpass' => 'nLrIJn0s', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '753', 'usernome' => 'PALAZZO MICHELE', 'useremail' => 'palazzo.fabbro@libero.it', 'usertelefono' => '3291697815', 'usercomune' => 'PEZZE DI GRECO', 'usertipo' => 'Infissi', 'nomecontatto' => 'MICHELE PALAZZO', 'userpassword' => '0ff0d9fa01b2df5b897b48d089704572', 'orignalpass' => 'sK8XFyK3', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '754', 'usernome' => 'IMAF', 'useremail' => 'imafcastellana@alice.it', 'usertelefono' => '0804968576', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Infissi', 'nomecontatto' => 'ANGELO PASCALE', 'userpassword' => '67656ef458991f16449c0396fad58731', 'orignalpass' => 'REKXaxCB', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '755', 'usernome' => 'GISOTTI FILIPPO', 'useremail' => 'openofficeopen@gmail.com', 'usertelefono' => '3401968966', 'usercomune' => 'GIOIA DEL COLLE', 'usertipo' => 'Infissi', 'nomecontatto' => 'FILIPPO GISOTTI', 'userpassword' => 'fce89133b27bff4baf6e008037d35a40', 'orignalpass' => 'HvXHIl0m', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '756', 'usernome' => 'Angelica Galiano', 'useremail' => 'documentazione.greengen@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '198892012c05231ea08dd336af479039', 'orignalpass' => 'EGTbE7m7', 'role' => '7', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '757', 'usernome' => 'ILARIO IACOVELLI', 'useremail' => 'ilarioche_guevara@live.it', 'usertelefono' => '3517488705', 'usercomune' => 'PUTIGNANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '04443a8b47658068c4a1800ef73aedc5', 'orignalpass' => 'Dme8He4F', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '758', 'usernome' => 'ANTHONY', 'useremail' => 'galianoanthony581@gmail.com', 'usertelefono' => '3209793492', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '4ff8f9d59d121508ec48e1c4dcc4680d', 'orignalpass' => 'yl2PIiwE', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '759', 'usernome' => 'ANGELICA IL TECNICO', 'useremail' => 'angelica.galiano96+tecnico@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8fbf24502de3afd91b47007e3bf75ce2', 'orignalpass' => 'Lt4NJ834', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '760', 'usernome' => 'GIUSEPPE BOTTA', 'useremail' => 'ing.giuseppebotta@gmail.com', 'usertelefono' => '3487303881', 'usercomune' => 'PALO DEL COLLE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'aed751068c60fb98c656a418f59b0755', 'orignalpass' => 'FLUEWeYm', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA MADONNA DELLA STELLA, 20', 'usercf' => 'BTTGPP61T180971I', 'usercoll' => 'Ordine degli ingegneri ', 'usercomcoll' => 'BARI', 'useriscr' => '5054', 'usercomunen' => 'Genzano lucania', 'userdatan' => '1961-12-18', 'userprovn' => 'PZ'),
            array('userid' => '761', 'usernome' => 'GIOVANNI FRANCESE', 'useremail' => 'giovanni.francese7928@pec.ordingbari.it', 'usertelefono' => '3284824268', 'usercomune' => 'MOLFETTA', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'bf869788e985e46f386f96d401587c99', 'orignalpass' => 'VYWcIpvs', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA PAPA LUCIANI,30', 'usercf' => '', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '7928', 'usercomunen' => 'BARI', 'userdatan' => '1977-05-26', 'userprovn' => 'BA'),
            array('userid' => '762', 'usernome' => 'STEFANO PALMISANO', 'useremail' => 'stefanopalmisano88@gmail.com', 'usertelefono' => '3479783328', 'usercomune' => 'LOCOROTONDO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'b2739f1d839018854e3c7e276c63b8fc', 'orignalpass' => 'lPFTjEWm', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'via Lombardia,11', 'usercf' => 'PLMSFN88B04F376Q', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '10979', 'usercomunen' => 'MONOPOLI', 'userdatan' => '1988-02-04', 'userprovn' => 'BA'),
            array('userid' => '763', 'usernome' => 'PISCITELLI', 'useremail' => 'PISCITELLI@GGG.COM', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'be2b877daaac68eb0423b241a73c10ac', 'orignalpass' => 'cWObQSY1', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '764', 'usernome' => 'COSERPLAST', 'useremail' => 'COSERPLAST@GGG.COM', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '0f08f74cc89e787217c12e26d6c659fd', 'orignalpass' => 'F1pT77nq', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '765', 'usernome' => 'PARADISO', 'useremail' => 'PARADISO@GGG.COM', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'f596d2bd0b288aaacaa35e6366f73bd4', 'orignalpass' => 'NH5kDOuf', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '766', 'usernome' => 'SIAL SERRAMENTI', 'useremail' => 'SIALSERRRAMENTI@GGG.COM', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'c73e48eb3f519fb20a6dc0933adb0bba', 'orignalpass' => 'cRjotMnA', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '767', 'usernome' => 'GIORDANO', 'useremail' => 'GIORDANO@GGG.COM', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '0042c221ae67868bd2ee61092ff2b951', 'orignalpass' => 'JI1zzyRG', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '768', 'usernome' => 'FALEGN. REGINA ', 'useremail' => 'REGINA@GGG.COM', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'fa25654160456f0fd23f4522498b9116', 'orignalpass' => '2HrX89WH', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '769', 'usernome' => 'METAL SERVICE', 'useremail' => 'METALSERVICE@GGG.COM', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '2438045d4732ea28b1c997d3a2d9a991', 'orignalpass' => 'R0BGXkNE', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '770', 'usernome' => '.test', 'useremail' => 'angelica.galiano96@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => '5fc262fe20a96ede72ea81cbf099f982', 'orignalpass' => 'DWAfeDQs', 'role' => '3', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '772', 'usernome' => 'test', 'useremail' => 'test12313123@gmail.com', 'usertelefono' => '48519159', 'usercomune' => 'test', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '198698b7d06a6e90c957b54d464a3bb3', 'orignalpass' => '4Pp05Ttx', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '773', 'usernome' => 'SIMONE', 'useremail' => 'simonnarracci05@gmail.com', 'usertelefono' => '3667040808', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ac8c14f35440dd7fb983311fda4b8186', 'orignalpass' => 'jYgzP4uC', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '774', 'usernome' => 'iteg impianti sas di paolo fiume sas', 'useremail' => 'paolofiume@libero.it', 'usertelefono' => '3487553968', 'usercomune' => 'monopoli', 'usertipo' => 'Idraulico', 'nomecontatto' => 'paolo fiume', 'userpassword' => 'f7e02ac29fdfc11ff4442affbcd5f356', 'orignalpass' => 'IxXBJoru', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '775', 'usernome' => 'GREENGEN GROUP SRL', 'useremail' => 'foto.greengen@gmail.com$', 'usertelefono' => '', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Idraulico', 'nomecontatto' => 'GREENGEN', 'userpassword' => 'e1df98614014663ae2f671b935c20542', 'orignalpass' => 'mWJyAwco', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '776', 'usernome' => 'GREENGEN GROUP SRL', 'useremail' => 'gestioneclienti.greengen@gmail.com$', 'usertelefono' => '', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Elettricista', 'nomecontatto' => 'GREENGEN', 'userpassword' => 'cd385199931f987f174f0194eb4cb8aa', 'orignalpass' => 'tEIwnagh', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '777', 'usernome' => 'PIU SAMUELE', 'useremail' => 'ansami2011@libero.it', 'usertelefono' => '3335473016', 'usercomune' => 'TARANTO', 'usertipo' => 'Elettricista', 'nomecontatto' => 'calo', 'userpassword' => '2194ae741f6ccb36ba4943fae890f90a', 'orignalpass' => 'uUB1KR5K', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '778', 'usernome' => 'ANCONA GIANPAOLO', 'useremail' => 'gianpaoloancona@hotmail.com', 'usertelefono' => '3890586520', 'usercomune' => 'gioia del colle', 'usertipo' => 'Edile', 'nomecontatto' => 'ANCONA GIANPAOLO', 'userpassword' => '7dcaaf5b549828715b3d59a8af0aa4ea', 'orignalpass' => 'fwAMW6Y4', 'role' => '3', 'status' => '0', 'userprov' => 'BAR', 'userres' => 'Strada Vicinale Vecchia di Matera,177', 'usercf' => 'NCNGPL85P16E038C', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '10946', 'usercomunen' => 'Gioia del Colle', 'userdatan' => '1985-09-16', 'userprovn' => 'Bari'),
            array('userid' => '779', 'usernome' => 'MAZZARELLI GIUSEPPE FOS', 'useremail' => 'fosarchitetti@gmail.com.', 'usertelefono' => '', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '360d1f6dd673655c5c53503282794050', 'orignalpass' => 'R18Xo1Wi', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '780', 'usernome' => 'ANGELICA GALIANO', 'useremail' => 'angelica.galiano96@gmail.com', 'usertelefono' => '3280474105', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '26537b1e391cc9bddb042f84df4f02fa', 'orignalpass' => 'pUvgdRRO', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '781', 'usernome' => 'SOLEMAR', 'useremail' => 'solemarsrls@libero.it', 'usertelefono' => '0994004058', 'usercomune' => 'MASSAFRA', 'usertipo' => 'Infissi', 'nomecontatto' => '', 'userpassword' => 'f0f2e501c46e93008160b8514346aa69', 'orignalpass' => 'aqNWTeVR', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '782', 'usernome' => 'GIUSEPPE CATALDI', 'useremail' => '3356567297@GMAIL.COM', 'usertelefono' => '3356567297', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => 'Elettricista', 'nomecontatto' => 'GIUSEPPE CATALDI', 'userpassword' => '6e6b162e8216b99aedd64e84893ea682', 'orignalpass' => 'AFJOdwfN', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '783', 'usernome' => 'ALESSIA', 'useremail' => 'gestioneclienti.greengen@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '7f0f597cea7943e589a9d34ad02be0a2', 'orignalpass' => 'z4nrd8hO', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '784', 'usernome' => 'New creations construzioni', 'useremail' => 'info@newcreationscostruzioni.it', 'usertelefono' => '3921859559', 'usercomune' => 'CONVERSANO', 'usertipo' => 'Edile', 'nomecontatto' => 'Nicola Centrone', 'userpassword' => 'e7eb70de1ed3eed05f74659d0fa11e59', 'orignalpass' => 'Pene69KW', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '785', 'usernome' => 'VIBA PROJECT', 'useremail' => 'vibaprojectservicesrls@libero.it', 'usertelefono' => '3483948039', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => 'Angelo ', 'userpassword' => '4aaf952001152edc49a81918cecbb0d2', 'orignalpass' => 'jbHeeccf', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '786', 'usernome' => 'FRANCO LAVENEZIANA', 'useremail' => '3385999681@GMAIL.COM', 'usertelefono' => '3385999681', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => 'LAVENEZIANA CARTONGESSO', 'userpassword' => '02cf1cce9a43e222b6569c0ac3327562', 'orignalpass' => 'Z1xVUgTr', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '787', 'usernome' => 'Marzionne', 'useremail' => 'MARZIONNENATALINO@PEC.IT', 'usertelefono' => '3335250051', 'usercomune' => 'CONVERSANO', 'usertipo' => 'Edile', 'nomecontatto' => 'Natalino Marzionne', 'userpassword' => '078b5c106d11508e09d2e8a414ed2f95', 'orignalpass' => 'gFzK75PC', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '788', 'usernome' => 'SAVINO', 'useremail' => 'savinomichele@peclegalmail.it', 'usertelefono' => '3389271378', 'usercomune' => 'CONVERSANO', 'usertipo' => 'Elettricista', 'nomecontatto' => 'Savino Michele', 'userpassword' => 'b8a9d8e6ab1b34cbbe6bec3a48a24678', 'orignalpass' => 'yGmY7yZL', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '789', 'usernome' => 'CHRISTIAN PACE', 'useremail' => 'christianpace@ggg.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => '7f63f5c23aaa1511c2dacf282b7011f4', 'orignalpass' => 'mf2wtwkq', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '790', 'usernome' => 'PALLINO', 'useremail' => 'apedil@open.legalmail.it', 'usertelefono' => '', 'usercomune' => 'PUTIGNANO', 'usertipo' => 'Edile', 'nomecontatto' => 'A.P. EDIL', 'userpassword' => '3bf7f14a24730c3c2a40d622ad6741ad', 'orignalpass' => 'ODHtuWV5', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '791', 'usernome' => 'FERRANTE DOMENICO', 'useremail' => 'ingegneredomenicoferrante@gmail.com', 'usertelefono' => '3382414503', 'usercomune' => 'LOCOROTONDO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8275c7a4a684cc76550b265795928085', 'orignalpass' => '3MiECgTK', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '792', 'usernome' => 'NICO IMPIANTI', 'useremail' => 'nicoimpianti18@libero.it', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Elettricista', 'nomecontatto' => 'FARINA NICOLA', 'userpassword' => '0e19a630c5a1318d6c5c5ac3b21e3352', 'orignalpass' => 'Xy3S49kE', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '793', 'usernome' => 'ELIO ', 'useremail' => 'elio.marziale.em@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => 'b44ec3633b2b58de7c8d3cfbe96a27c5', 'orignalpass' => 'zG403QdE', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '794', 'usernome' => 'TARANTINO S.R.L.', 'useremail' => 'leviediomero2@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => 'TARANTINO GIANLUCA', 'userpassword' => 'ba680a5e80daad03c54e6d38b6b41613', 'orignalpass' => 'RNYc9V7Q', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '795', 'usernome' => 'RENNA INFISSI', 'useremail' => 'renna.infissi@tiscali.it', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Infissi', 'nomecontatto' => 'MARIO E PAOLO FRANCESCO RENNA', 'userpassword' => '7256514bfa8d41a090bdda04885a7733', 'orignalpass' => 'Ni8pgiEu', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '796', 'usernome' => 'SAVINO', 'useremail' => 'savinomichele@peclegalmail.it', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Idraulico', 'nomecontatto' => 'SAVINO MICHELE', 'userpassword' => 'eddfc6cb79f222d02f4da912d10845ce', 'orignalpass' => 'nGb0GxWK', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '797', 'usernome' => 'EDIL 2000', 'useremail' => 'info@edil2000.it', 'usertelefono' => '0881520996', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => 'PAOLO MICCOLIS', 'userpassword' => 'adc06ae03a0ffbcb9b999b4cc8c8b85b', 'orignalpass' => '8KCxzsoj', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '798', 'usernome' => 'SIKA', 'useremail' => 'info@sika.it', 'usertelefono' => '0254778111', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '59f0695e8cb844e05b16b2f46602419c', 'orignalpass' => 'RZYiRm8m', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '799', 'usernome' => 'Angelica TEST', 'useremail' => 'example@gmail.com', 'usertelefono' => '0315655142', 'usercomune' => 'Castellana Grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '16a298cda1b1e34e9e4a2078ed8c1917', 'orignalpass' => 'gtiPMDmV', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '800', 'usernome' => 'CHARLIE CLARK', 'useremail' => 'charlie_clark@me.com', 'usertelefono' => '', 'usercomune' => 'UK', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'e6b9bc6735bad214ed29ee58f85949ca', 'orignalpass' => 'x7iWuSmz', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '801', 'usernome' => 'Angelica Galiano', 'useremail' => 'documentazione.greengen@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '24727f3079ec8a981ab967b397f4e796', 'orignalpass' => 'OfqmGKnx', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '802', 'usernome' => 'Angelica Galiano', 'useremail' => 'documentazione.greengen@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '5f09b5fa7128dbaff84b88eeed5daa71', 'orignalpass' => 'fSljRwUp', 'role' => '6', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '803', 'usernome' => 'Angelica', 'useremail' => 'angelica.galianogb@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => 'Idraulico', 'nomecontatto' => 'Galiano', 'userpassword' => 'de9c8ad9cd2c822f1ed8b1780623e802', 'orignalpass' => 'Wmb7eg24', 'role' => '3', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '804', 'usernome' => 'ARPA NUOVE TECNOLOGIE SRL', 'useremail' => 'info@arpanuovetecnologie.it', 'usertelefono' => '3495085746', 'usercomune' => 'PUTIGNANO', 'usertipo' => 'Edile', 'nomecontatto' => 'DI CARLO', 'userpassword' => 'df86afb213eab4143add4596ff9b4ad2', 'orignalpass' => 'UlgJyRlM', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => '', 'usercomunen' => '', 'userdatan' => '0000-00-00', 'userprovn' => ''),
            array('userid' => '805', 'usernome' => 'LA NUOVA COSTRUZIONE SRLS', 'useremail' => 'ESEMPIO@GMAIL.COM', 'usertelefono' => '1111111111', 'usercomune' => 'Castellana Grotte', 'usertipo' => 'Edile', 'nomecontatto' => 'LA NUOVA COSTRUZIONE SRLS', 'userpassword' => '510a72fccf0aac7e6e635785138046cb', 'orignalpass' => '3PN3PcDX', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '806', 'usernome' => 'NUOVA SUPERBLINDO S.R.L.', 'useremail' => 'nuovasuperblindosrl@gmail.com', 'usertelefono' => '368982216', 'usercomune' => 'MONOPOLI', 'usertipo' => 'Infissi', 'nomecontatto' => 'ORONZO NARRACCI', 'userpassword' => 'e4dca2d5142e5e6baf486f3fed655e16', 'orignalpass' => 'm9BLxEQJ', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '807', 'usernome' => 'SUNGRIT', 'useremail' => 'enniocolucci@libero.it', 'usertelefono' => '3487465033', 'usercomune' => 'TARANTO', 'usertipo' => 'Infissi', 'nomecontatto' => 'ENNIO COLUCCI', 'userpassword' => 'f5d14a048d095f97ea9ead588e2170a3', 'orignalpass' => 'gjPE8dP4', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '808', 'usernome' => 'CALO\' ELETTRICISTA', 'useremail' => 'calo@gmail.com', 'usertelefono' => '', 'usercomune' => 'TALSANO', 'usertipo' => 'Elettricista', 'nomecontatto' => 'ANTONELLO CALO\'', 'userpassword' => '8c8b17f897e573b16ab15c77617b1f7d', 'orignalpass' => 'ApRAicdR', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '809', 'usernome' => 'MARTELLOTTA FRANCESCO', 'useremail' => 'ecoimpianti80@libero.it', 'usertelefono' => '3925636318', 'usercomune' => 'CASTELLANA GROTTE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '73e45607c23c70160e94ae2f653a058a', 'orignalpass' => 'qoSA6GcH', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '810', 'usernome' => 'SPORTELLI', 'useremail' => 'SPORTELLI@EMAIL.COM', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Idraulico', 'nomecontatto' => '', 'userpassword' => '832e28e3bc51c24190351d2bb586c20c', 'orignalpass' => 'OaKUKfLJ', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '811', 'usernome' => 'ENERGY SYSTEM', 'useremail' => 'energysystem2012@gmail.com', 'usertelefono' => '3317550183', 'usercomune' => 'MARTINA FRANCA', 'usertipo' => 'Elettricista', 'nomecontatto' => 'FRANCO LANZILLOTTA', 'userpassword' => '10f852f47ecf5ff94244ddc867b37840', 'orignalpass' => '326luUmo', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '812', 'usernome' => 'ZINTECH SRLS', 'useremail' => 'michelezingaro@libero.it', 'usertelefono' => '3397074961', 'usercomune' => 'BARI', 'usertipo' => 'Edile', 'nomecontatto' => 'Michele Zingaro ', 'userpassword' => '9eff3984d4cee2540a5d2a38ead8f3ef', 'orignalpass' => 'Lwo7SQt6', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '813', 'usernome' => 'ANCONA GIANPAOLO ', 'useremail' => 'test.gianpaolo@gmailm.com', 'usertelefono' => '', 'usercomune' => 'GIOIA DEL COLLE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '551402aca91e156b478e48da84630b4b', 'orignalpass' => 'sLzkxI6H', 'role' => '2', 'status' => '0', 'userprov' => 'BA', 'userres' => 'Strada Vicinale Vecchia di Matera,177', 'usercf' => 'NCNGPL85P16E038C', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '10946', 'usercomunen' => 'Gioia del Colle', 'userdatan' => '1985-09-16', 'userprovn' => 'BA'),
            array('userid' => '814', 'usernome' => 'VINELLA LUCA', 'useremail' => 'VINELLALUCAesempio@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => 'Edile', 'nomecontatto' => 'piercarlo castellana', 'userpassword' => 'c1d2f14ee720b4139cb6cdcfb32c1b06', 'orignalpass' => 'O3IypqkD', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '815', 'usernome' => 'Impresa edile Satalino Carmelo', 'useremail' => 'Carmelo.satalino@libero.it', 'usertelefono' => '3312534987', 'usercomune' => 'POLIGNANO A MARE', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '2275438eaad4cbea6021cf5b07635c52', 'orignalpass' => '4xnrCOus', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '816', 'usernome' => 'LATROFA', 'useremail' => 'impresaedilelatrofasrls@pec.it', 'usertelefono' => '3383715777', 'usercomune' => 'Castellana Grotte', 'usertipo' => 'Edile', 'nomecontatto' => 'DOMENICO', 'userpassword' => 'b4bcea705aca4aaa3ee241cb1f448203', 'orignalpass' => 'yRef2K3U', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '817', 'usernome' => 'EDILIZIA ', 'useremail' => 'CASULLIDONATO@GMAIL.COM', 'usertelefono' => '3347787573', 'usercomune' => 'PUTIGNANO', 'usertipo' => 'Edile', 'nomecontatto' => 'INNOVATIVA', 'userpassword' => '405220ae6a6ee2ac7da4acfb0be4e9b7', 'orignalpass' => 'WpvqAlqJ', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '818', 'usernome' => 'GIOVANNI PALMISANO', 'useremail' => 'arch.palmisano@gmail.com', 'usertelefono' => '3388545741', 'usercomune' => 'MARTINA FRANCA', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'af8b51504977e7aa124bdf9425e860ef', 'orignalpass' => 'CIgQuacr', 'role' => '2', 'status' => '1', 'userprov' => 'TA', 'userres' => 'VIALE EUROPA, 117', 'usercf' => 'PLMGNN64B05E986V', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'TARANTO', 'useriscr' => '512', 'usercomunen' => 'MARTINA FRANCA', 'userdatan' => '1964-02-05', 'userprovn' => 'TA'),
            array('userid' => '819', 'usernome' => 'CISTERNINO GIUSEPPE', 'useremail' => 'giuseppe.cisternino6127@pec.ordingbari.it', 'usertelefono' => '3492314674', 'usercomune' => 'CASTELLANA G.', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '401748c1b1adceac0ca3a5f6ef802942', 'orignalpass' => 'IEMZ0XdR', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => '', 'usercf' => 'CSTGPP76B05C134H', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => 'A6127', 'usercomunen' => 'CASTELLANA G.', 'userdatan' => '1976-02-05', 'userprovn' => 'BA'),
            array('userid' => '820', 'usernome' => 'FACCHINO NICOLA', 'useremail' => 'ing.nicola.facchino@legalmail.it', 'usertelefono' => '33927228776', 'usercomune' => 'MOLA DI BARI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'bd01570b1f450b6c3f8958e2b64349ae', 'orignalpass' => '4zALH9uF', 'role' => '2', 'status' => '1', 'userprov' => 'ba', 'userres' => 'via S. Onofrio,46', 'usercf' => 'FCCNCL72C18F280I', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => ' 8829', 'usercomunen' => 'Mola di Bari', 'userdatan' => '1972-03-18', 'userprovn' => 'ba'),
            array('userid' => '821', 'usernome' => 'CONVERTINI FRANCESCO', 'useremail' => 'convertini.francesco@ingpec.eu', 'usertelefono' => '3339941728', 'usercomune' => 'Fasano', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '65b8f57929775f58237a8d02d701d39a', 'orignalpass' => 'P9t4cOFv', 'role' => '2', 'status' => '1', 'userprov' => 'BR', 'userres' => 'VIA EROI DELLO SPAZIO,26', 'usercf' => 'CNVFNC87D93D508A', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BRINDISI', 'useriscr' => '50/B', 'usercomunen' => 'FASANO', 'userdatan' => '1987-04-03', 'userprovn' => 'BR'),
            array('userid' => '822', 'usernome' => 'REDAVID ANNIBALE', 'useremail' => 'annibale.redavid@ingpec.eu', 'usertelefono' => '3398613598', 'usercomune' => 'CONVERSANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '1ecfd6376b6b41fd6d01e78c7315f7b5', 'orignalpass' => 'EOOxafcU', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA CONTE COSIMO, n° 31', 'usercf' => 'RDVNBL89C03A662H', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '11203', 'usercomunen' => 'BARI', 'userdatan' => '1989-03-03', 'userprovn' => 'BA'),
            array('userid' => '823', 'usernome' => 'DE VITO ANTONIO', 'useremail' => 'antonio.devito@geopec.it', 'usertelefono' => '3285433661', 'usercomune' => 'MARTINA FRANCA', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '9c8b0b30be9104a10d8bd0f0558f1418', 'orignalpass' => 'jtf2kwDH', 'role' => '2', 'status' => '1', 'userprov' => 'TA', 'userres' => 'corso messapia 163', 'usercf' => 'DVTNTN76R14E986P', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'TARANTO', 'useriscr' => '1897', 'usercomunen' => 'MARTINA FRANCA', 'userdatan' => '1976-01-14', 'userprovn' => 'TA'),
            array('userid' => '824', 'usernome' => 'Giodice Maurizio', 'useremail' => 'arch.giodice@gmail.com', 'usertelefono' => '3200632784', 'usercomune' => 'ROMA', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '485592d01ae2cc8d03cbe2897a8e55d5', 'orignalpass' => 'y2vSQ0LI', 'role' => '2', 'status' => '1', 'userprov' => 'RM', 'userres' => 'VIA B. BRUNI,40', 'usercf' => 'GDCMRZ88R14C134X', 'usercoll' => 'ARCHITETTI', 'usercomcoll' => 'BARI', 'useriscr' => '3300', 'usercomunen' => 'CASTELLANA GR.', 'userdatan' => '1988-10-14', 'userprovn' => 'BA'),
            array('userid' => '825', 'usernome' => 'FRANCESCO BARLETTA', 'useremail' => 'fb_designer@libero.it', 'usertelefono' => '3288618196', 'usercomune' => 'PUTIGNANO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'a8e793e1071c9152c4c22d0a4b711f15', 'orignalpass' => 'iG6SPg5y', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'PUTIGNANO', 'usercf' => 'BRLFNC84S23H096U', 'usercoll' => 'INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '11371', 'usercomunen' => 'PUTIGNANO', 'userdatan' => '1984-11-23', 'userprovn' => 'BA'),
            array('userid' => '826', 'usernome' => 'TEST DA ELIMINARE', 'useremail' => 'gadregilte@vusra.com', 'usertelefono' => '', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ac11c6e6766a80238dd0908e116f1760', 'orignalpass' => '4k4BPso6', 'role' => '2', 'status' => '0', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '827', 'usernome' => 'ROBERTO', 'useremail' => 'rbonghettocaffio@gmail.com', 'usertelefono' => '3669592945', 'usercomune' => 'ALBEROBELLO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '5bf026010891d4a5135b6256d8f84a28', 'orignalpass' => 'uyJn9U8C', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '828', 'usernome' => 'MODESTO GUGLIELMI', 'useremail' => 'modesto.guglielmi@geopec.it', 'usertelefono' => '', 'usercomune' => 'POLIGNANO A MARE', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '898c5613ba2f7b02b34b6620bd1da932', 'orignalpass' => 'veLyiSxt', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => '', 'usercf' => 'GGLMST91A08H096N', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'BARI', 'useriscr' => '4667', 'usercomunen' => 'PUTIGNANO', 'userdatan' => '1992-01-08', 'userprovn' => 'BA'),
            array('userid' => '829', 'usernome' => 'ALBANESE SALVATORE G.', 'useremail' => 'salvatoreguerino.albanese@geopec.it', 'usertelefono' => '3389593674', 'usercomune' => 'SIDERNO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '701990e89a8a47f57ff5af6f0b3fccf6', 'orignalpass' => 'uVtOpNSd', 'role' => '2', 'status' => '1', 'userprov' => 'RC', 'userres' => 'SIDERNO', 'usercf' => 'LBNSVT70H28E212M', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'REGGIO CALBRIA', 'useriscr' => '2243', 'usercomunen' => 'GROTTERIA', 'userdatan' => '1970-06-28', 'userprovn' => 'RC'),
            array('userid' => '830', 'usernome' => 'GIUSEPPE MAZZARELLI', 'useremail' => 'mazzarelligiu@gmail.com', 'usertelefono' => '0804965486', 'usercomune' => 'CASTELLANA G.', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '26f52b545ba514e270a6d5148c808ca1', 'orignalpass' => '6RE3LgHB', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => 'MZZGPP81R30C124G', 'usercoll' => 'DEGLI ARCHITETTI', 'usercomcoll' => 'BARI', 'useriscr' => '3667', 'usercomunen' => 'CASTELLANA G.', 'userdatan' => '1981-10-30', 'userprovn' => 'BA'),
            array('userid' => '831', 'usernome' => 'PALUMBO GIANDAVIDE', 'useremail' => 'giandavidepalumbo@libero.it', 'usertelefono' => '3335799905', 'usercomune' => 'MOLA DI BARI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'eabc0dcc06a63bfa7a54a860b2605220', 'orignalpass' => '3p3LtxsS', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'MOLA DI BARI', 'usercf' => 'PLMGDV75C03F280P', 'usercoll' => 'DEGLI INGEGNERI', 'usercomcoll' => 'BARI', 'useriscr' => '7527', 'usercomunen' => 'MOLA DI BARI', 'userdatan' => '1975-03-03', 'userprovn' => 'BA'),
            array('userid' => '832', 'usernome' => 'CAPUTO ANDREA', 'useremail' => 'andrea.caputo1@geopec.it', 'usertelefono' => '3454990294', 'usercomune' => 'TURI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '4b354d6ebf55140738b6c3ab3b9b7564', 'orignalpass' => 'rbburuRZ', 'role' => '2', 'status' => '1', 'userprov' => 'BA', 'userres' => 'VIA ANTONIO GRAMISCI n°32', 'usercf' => 'CPTNDR81D22L049M', 'usercoll' => 'GEOMETRI', 'usercomcoll' => 'TARANTO', 'useriscr' => '1920', 'usercomunen' => 'TARANTO', 'userdatan' => '1981-04-22', 'userprovn' => 'TA'),
            array('userid' => '833', 'usernome' => 'ANCONA GIANPAOLO', 'useremail' => 'gianpaoloancona@hotmail.com', 'usertelefono' => '3890586520', 'usercomune' => 'Gioia del Colle', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '47fb5a929cc02e3566b00f6d768f331e', 'orignalpass' => 'gq12eY9t', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '834', 'usernome' => 'Marco parchitelli', 'useremail' => 'marcoparchitelli84@gmail.com', 'usertelefono' => '3452235333', 'usercomune' => 'noci', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '16219a8ff5fbbc9dd888273da47f83f2', 'orignalpass' => 'GVq9ftf2', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '835', 'usernome' => 'Alessandro muraglia', 'useremail' => 'muragliaa6@gmail.com', 'usertelefono' => '3291133787', 'usercomune' => 'Castellana grotte', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '8c95b42858fc7f78522a2712acde818f', 'orignalpass' => 'ljbqNVHA', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '836', 'usernome' => 'Vincenzo Mastronardi', 'useremail' => 'masvin1976@gmail.com', 'usertelefono' => '3892625193', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'cf9660a8fe87d85fb564423960332e3a', 'orignalpass' => 'GvXrNyBk', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '837', 'usernome' => 'CONSEGNE MAGAZZINO', 'useremail' => 'angelica.galiano96+magazzino@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '575e5f86a8f4052466b573e515d3ded1', 'orignalpass' => 'AslZiOaF', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '838', 'usernome' => 'VITO PALMISANO', 'useremail' => 'vito.palmisanogreengen@gmail.com', 'usertelefono' => '3490577837', 'usercomune' => 'LOCOROTONDO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'e9ae08ed14b03d943138ae1b33f51a91', 'orignalpass' => 'ISECFV48', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => '', 'usercomunen' => '', 'userdatan' => '0000-00-00', 'userprovn' => ''),
            array('userid' => '839', 'usernome' => 'NICO LADOGANA', 'useremail' => 'ladogana.nico@gmail.com', 'usertelefono' => '3333471667', 'usercomune' => 'MONOPOLI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '19666707e11ca76a3d62a5d65fcb1476', 'orignalpass' => '0q55lkpe', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '840', 'usernome' => 'VINCENZO BELCASTRO ', 'useremail' => 'VBELCASTRO@OAPPC_RC.IT', 'usertelefono' => '3289247739', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '24e4bcd892f90afe20a5ef7815fd56b0', 'orignalpass' => 'OVcJLESF', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '841', 'usernome' => 'VANESSA PELUSO', 'useremail' => 'vanessa.peluso95@gmail.com', 'usertelefono' => '', 'usercomune' => 'TARANTO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '453e6fe192ab8560e25c01b4c1ebc125', 'orignalpass' => 'v0MByaiL', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => '', 'usercomunen' => '', 'userdatan' => '0000-00-00', 'userprovn' => ''),
            array('userid' => '842', 'usernome' => 'GIGANTE PIETRO', 'useremail' => 'GIGANTEPIETRO@PEC.IT', 'usertelefono' => '336822607', 'usercomune' => 'NOCI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f8d68d73043c3379d84b49d1cb2fa596', 'orignalpass' => 'aEWngIJ0', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '843', 'usernome' => 'VGS Edilizia ', 'useremail' => 'vgsedilizia@gmail.com', 'usertelefono' => '330937522', 'usercomune' => 'GINOSA', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => 'c5e171f718fbe794d5ab69b74784e0f8', 'orignalpass' => '4zgP3JYm', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '844', 'usernome' => 'Inaam ul haq', 'useremail' => 'waleedhaq339@gmail.com', 'usertelefono' => '03136282699', 'usercomune' => 'testse', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '147bffd7fcc45d00fb011b898249e7f2', 'orignalpass' => 'in8LIajZ', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '845', 'usernome' => 'DAVIDE GREENGEN ', 'useremail' => 'davide.greengen@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'ddc93c19760a108790023b9bc454953c', 'orignalpass' => 'nZTKnoZD', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '846', 'usernome' => 'CALIA GIANGRANCO', 'useremail' => 'caliagianfranco@libero.it', 'usertelefono' => '3281315825', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'cc0d8e7688d2c6acb3fca4a64a6bebfc', 'orignalpass' => 'amUa99He', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '847', 'usernome' => 'PrintMart', 'useremail' => 'testingjust247@gmail.com', 'usertelefono' => '25', 'usercomune' => 'Iusto odit eum sint', 'usertipo' => 'Idraulico', 'nomecontatto' => 'testbusiness', 'userpassword' => 'e38d3b37ae78841e9aec0afd7d076040', 'orignalpass' => 'F9LhRiOQ', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '848', 'usernome' => 'idraulico', 'useremail' => 'm.anamulhaq339@gmail.com', 'usertelefono' => '63', 'usercomune' => 'Voluptatum lorem aut', 'usertipo' => 'Idraulico', 'nomecontatto' => 'inaam idraulico', 'userpassword' => '59f321ca3324a10b3eeccef56468c4d5', 'orignalpass' => '2LjbO76o', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '849', 'usernome' => 'ANCONA FRANCESCO ', 'useremail' => 'francesco.ancona2@gmail.com', 'usertelefono' => '3383990177', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '806b55963305bdcbb49a64316dbc77b8', 'orignalpass' => 'G3is12Eb', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '850', 'usernome' => 'MAGAZZINO GREENGEN', 'useremail' => 'angelica.greengen@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'c779462a2db7ae986350923dd2ce71c2', 'orignalpass' => 'LONg9xr5', 'role' => '6', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '851', 'usernome' => 'NICU CALITA', 'useremail' => 'Calitanicu25@gmail.com', 'usertelefono' => '393271565890', 'usercomune' => 'PUTIGNANO', 'usertipo' => 'Edile', 'nomecontatto' => '', 'userpassword' => '5664311d8f51105fb21aad7a9b30d6d6', 'orignalpass' => 'LtjyLBzJ', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '852', 'usernome' => 'VITO MAGAZZINO', 'useremail' => 'vitoilrosso70@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'f6f144698c730cda50879b9225064298', 'orignalpass' => 'NUOectVi', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '853', 'usernome' => 'MESITI ROCCO', 'useremail' => 'roccomesiti500el@gmail.com', 'usertelefono' => '3398907144', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'd5922dea5413d91e596c17bc07093dce', 'orignalpass' => '8x16EUiS', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '854', 'usernome' => 'GIANPAOLO ', 'useremail' => 'scangidd86@gmail.com', 'usertelefono' => '3285892140', 'usercomune' => 'NOCI', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '73f416dc816c2cfcb738d4923475edb6', 'orignalpass' => '1LeT7heV', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '856', 'usernome' => 'ANTONELLO', 'useremail' => 'mottolaantonello62@gmail.com', 'usertelefono' => '', 'usercomune' => '', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => '5e12358ed2f460f31f9af64103774568', 'orignalpass' => 'PznipTIi', 'role' => '7', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '857', 'usernome' => 'test', 'useremail' => 'test@gmail.com', 'usertelefono' => '238748297892', 'usercomune' => 'SIDERNO', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'c041cfbeef0067572d2c69262548f6c3', 'orignalpass' => 'ZocLIu42', 'role' => '2', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '858', 'usernome' => 'Sergio Milo', 'useremail' => 'sergio.milo@ingpec.eu', 'usertelefono' => '3275407812', 'usercomune' => 'LEQUILE ', 'usertipo' => '', 'nomecontatto' => '', 'userpassword' => 'cb4956675be979cefd0afb1962435cd2', 'orignalpass' => 'ZmcLlu6G', 'role' => '2', 'status' => '1', 'userprov' => 'LE', 'userres' => 'Via G. Verdi n.7', 'usercf' => 'mlisrg90a25d862u', 'usercoll' => 'Ordine degli ingegneri', 'usercomcoll' => 'LECCE', 'useriscr' => '4112', 'usercomunen' => 'GALATINA', 'userdatan' => '1990-01-25', 'userprovn' => 'LE'),
            array('userid' => '859', 'usernome' => 'Illum repellendus ', 'useremail' => 'zeha@mailinator.com', 'usertelefono' => '40', 'usercomune' => 'Tempor vel rerum del', 'usertipo' => 'Idraulico', 'nomecontatto' => 'Eius aut amet conse', 'userpassword' => '2d3b448ea61c8413c598d0975e75bb6f', 'orignalpass' => 'V9T1qPCu', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '861', 'usernome' => 'Saleh', 'useremail' => 'Salehktk1@gmail.com', 'usertelefono' => '03139445981', 'usercomune' => '', 'usertipo' => 'Elettricista', 'nomecontatto' => 'Muhammad', 'userpassword' => '65bc901f847f54de05070e977223c5f1', 'orignalpass' => 'EjkFOR8D', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '862', 'usernome' => 'Saleh', 'useremail' => 'Salehktk12@gmail.com', 'usertelefono' => '03139445981', 'usercomune' => '', 'usertipo' => 'Idraulico', 'nomecontatto' => 'Muhammad', 'userpassword' => 'd6e65875fbdb21eec27ab8e3a6faa901', 'orignalpass' => '3bdcqHgn', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '863', 'usernome' => 'PETRERA ', 'useremail' => 'domenicopetrera72@gmail.com', 'usertelefono' => '', 'usercomune' => 'mottola', 'usertipo' => 'Edile', 'nomecontatto' => 'DOMENICO PETEREA', 'userpassword' => 'd54d6616391ccd96b2b48e7cc7a70bda', 'orignalpass' => 'oPIAo1Ll', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '864', 'usernome' => 'sharjeel company', 'useremail' => 'sharjeelkhokhar94@gmail.com', 'usertelefono' => '3265625222', 'usercomune' => 'test', 'usertipo' => 'Infissi', 'nomecontatto' => 'sharjeel', 'userpassword' => '2f8890eedfb3bd37a31d0ec44e029b53', 'orignalpass' => '9qV9ytJo', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => '', 'usercomunen' => '', 'userdatan' => '0000-00-00', 'userprovn' => ''),
            array('userid' => '865', 'usernome' => 'Angelica', 'useremail' => 'angelica.greengen+fotovoltaico@gmail.com', 'usertelefono' => '07934898107', 'usercomune' => '', 'usertipo' => 'Elettricista', 'nomecontatto' => 'Galiano', 'userpassword' => '71f30e0735c0294b5ed14c5156d084cb', 'orignalpass' => 'G1HdYqTx', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '866', 'usernome' => 'testing impressa ', 'useremail' => 'poxytoxuf@mailinator.com', 'usertelefono' => '51', 'usercomune' => 'Dolores laborum quae', 'usertipo' => 'Edile', 'nomecontatto' => 'uieworbn3', 'userpassword' => '2e1f4c4d817f1c949b9810d0b42fb885', 'orignalpass' => '3GVgAf0G', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => ''),
            array('userid' => '867', 'usernome' => 'testing impressa nov6-23', 'useremail' => 'imussadiqaz@gmail.com', 'usertelefono' => '03433583808', 'usercomune' => 'checking impressa working or not', 'usertipo' => 'Elettricista', 'nomecontatto' => 'impressa testing ', 'userpassword' => 'bdfd127aaa8c1ae03feedbdb3ed16039', 'orignalpass' => 'bc2S5Tev', 'role' => '3', 'status' => '1', 'userprov' => '', 'userres' => '', 'usercf' => '', 'usercoll' => '', 'usercomcoll' => '', 'useriscr' => NULL, 'usercomunen' => '', 'userdatan' => NULL, 'userprovn' => '')
        );
        $foundArrays = array_filter($users, function ($array) use ($id) {
            return isset($array['userid']) && $array['userid'] === $id;
        });
        if ($foundArrays) {
            foreach ($foundArrays as $innerArray) {
                $userEmail = $innerArray['useremail'];
            }
            $getOurUser = User::where('email', $userEmail)->first();
            if ($getOurUser) {
                return $getOurUser->id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }



    public function changeUserRolesToAdmin()
    {
        $count = 0;
        $adminRole = Role::where('name', 'admin')->first();
        //        dd($adminRole);

        $userRole = Role::where('name', 'user')->first();
        // Get all users and update their roles
        $usersWithUserRole = $userRole->users;

        foreach ($usersWithUserRole as $user) {
            $user->syncRoles([$adminRole->name]);
            $count++;
        }

        // Optionally, you can output a message indicating success
        return "All users have been assigned the 'admin' role." . $count;
    }
    public function addFotovolticFiles()
    {

        $cons = ConstructionSite::where('page_status', 4)->get();

        $count = 0;

        foreach ($cons as $singlecons) {
            $single = $singlecons->ReliefDocument()->where('folder_name', 'Documenti Fotovoltaico')->first();

            if ($single == null) {

                $relief = $singlecons->StatusRelief;

                $relief_doc1_for_reilief = [
                    'status_relief_id' => $relief->id,
                    'construction_site_id' => $singlecons->id,
                    'allow' => 'admin,technician,businessconsultant,photovoltaic,user',
                    'folder_name' => 'Documenti Fotovoltaico',
                    'state' => '0',
                ];



                $relief_doc_sub_file8 = $relief->ReliefDocument()->updateOrCreate($relief_doc1_for_reilief);
                $relief_doc_sub1_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,technician,businessconsultant,photovoltaic,user',
                    'file_name' => 'Bolletta Luce',
                    'description' => 'Dellimmobile (prime 2 pagine)',
                    'bydefault' => '1',
                ];
                $relief_doc_sub2_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,technician,businessconsultant,photovoltaic,user',
                    'file_name' => 'Carta D Identità Intestatario Bollette',
                    'description' => '(Fronte - retro) in corso di validità',
                    'bydefault' => '1',
                ];
                $relief_doc_sub3_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,photovoltaic,user',
                    'file_name' => 'Codici accesso portale',
                    'bydefault' => '1',
                ];
                $relief_doc_sub4_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,photovoltaic,user',
                    'file_name' => 'Contratto GSE',
                    'bydefault' => '1',
                ];
                $relief_doc_sub5_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,photovoltaic,user',
                    'file_name' => 'Estensione Garanzia FTV',
                    'bydefault' => '1',
                ];
                $relief_doc_sub6_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,technician',
                    'file_name' => 'Estratto Di Mappa',
                    'description' => 'Aggiornato',
                    'bydefault' => '1',
                ];
                $relief_doc_sub7_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,photovoltaic',
                    'file_name' => 'Iban',
                    'bydefault' => '1',
                ];
                $relief_doc_sub8_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,photovoltaic,user',
                    'file_name' => 'Mandato RAP',
                    'bydefault' => '1',
                ];
                $relief_doc_sub9_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'allow' => 'admin,photovoltaic',
                    'file_name' => 'Sezione H',
                    'bydefault' => '1',
                ];
                $relief_doc_sub10_files_for8 = [
                    'relief_doc_id' => $relief_doc_sub_file8->id,
                    'construction_site_id' => $singlecons->id,
                    'ref_folder_name' => $relief_doc_sub_file8->folder_name,
                    'file_name' => 'ANTONACCI IMMACOLATA relazione fotovoltaico',
                    'allow' => 'admin,user',
                    'bydefault' => '1',
                ];
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub1_files_for8);
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub2_files_for8);
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub3_files_for8);
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub4_files_for8);
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub5_files_for8);
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub6_files_for8);
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub7_files_for8);
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub8_files_for8);
                $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub9_files_for8);
                $count++;
            }
        }

        return $count;
    }

    public function metirialupdating()
    {
        $count = 0;
        $all = ConstructionSite::all();

        foreach ($all as $alls) {
            $data = $alls->ConstructionMaterial;

            if (count($data) > 0) {
                foreach ($data as $key1 => $datas) {

                    $AssegnaMateriali = AssegnaMateriali::where('fk_cantiere', $alls->oldid)->get();
                    $matchFound = false;

                    foreach ($AssegnaMateriali as $key2 => $AssegnaMaterialis) {
                        if ($key1 == $key2) {


                            $update  = ConstructionMaterial::findOrfail($datas->id);

                            $update->consegnato = $AssegnaMaterialis->montato ?  $AssegnaMaterialis->montato : null;
                            $update->montato = $AssegnaMaterialis->montato2 ? $AssegnaMaterialis->montato2 : null;
                            $update->update();
                            $count++;
                            $matchFound = true;
                            break;
                        }
                    }

                    if ($matchFound) {
                        break;
                    }
                }
            }
        }

        echo "updated records are " . $count;
    }


    public  function add_data_into_chiled()
    {
        $var  = ConstructionSite::find(845);
        $type_of_deduction_values = $var->ConstructionSiteSetting->type_of_deduction;
        $type_of_deduction_values = explode(',', $type_of_deduction_values);


        $cons_id = [
            'construction_site_id' => $var->id
        ];

        $var->StatusPreAnalysis()->updateOrCreate($cons_id);

        $var->StatusTechnician()->updateOrCreate($cons_id);
        $relief = $var->StatusRelief()->updateOrCreate($cons_id);

        // $relief_doc1 = [
        //     'status_relief_id' => $relief->id,
        //     'construction_site_id' => $var->id,
        //     'allow' => 'admin,technician,user',
        //     'folder_name' => 'Altri Documenti Rilevanti',

        // ];
        $relief_doc2 = [
            'status_relief_id' => $relief->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin',
            'folder_name' => 'Diagnosi Energetica',
            'description' => 'Ape regionale, legge 10 e ricevuta',
            'allow' => 'admin,technician,businessconsultant,user',

        ];



        // here we need to add sub file for Diagnosi Energetica 


        $relief_doc3 = [
            'status_relief_id' => $relief->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'folder_name' => 'Documenti Libretto Impianti',
            'description' => 'e catasto impianti',

        ];
        $relief_doc4 = [
            'status_relief_id' => $relief->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'folder_name' => 'Documenti Rilievo',
            'description' => 'Scheda dati ante opera e interventi',

        ];
        $relief_doc5 = [
            'status_relief_id' => $relief->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'folder_name' => 'Documenti Clienti',
            'description' => 'Atto di provenienza, carta didentità, codice fiscale e visura catastale',

        ];
        $relief_doc6 = [
            'status_relief_id' => $relief->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,user, businessconsultant',
            'folder_name' => 'Documenti Co-intestatari',
            'description' => 'Carta didentità e consenso lavori',

        ];
        $relief_doc7 = [
            'status_relief_id' => $relief->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'folder_name' => 'Documenti Fine Lavori',
            'description' => 'Verbali consegna chiavetta e lavori, Sopralluogo fine lavori...',

        ];
        $relief_doc8 = [
            'status_relief_id' => $relief->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'folder_name' => 'Schemi Impianti',
            'description' => 'Pianta lastrico solare e pianta imp. Termico',

        ];
        $relief_doc9 = [
            'status_relief_id' => $relief->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'folder_name' => 'Pratiche Comunali',
            'description' => 'Cilas, notifica preliminare e planimetria catastale',
        ];

        // check type of deduction
        // dd('here');

        if ($type_of_deduction_values) {

            $var_id = $var->id;

            if (in_array("Fotovoltaico", $type_of_deduction_values) || in_array("fotovoltaico", $type_of_deduction_values)) {

                $fotovoltaic = ReliefDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti Fotovoltaico')->first();

                $schemi = ReliefDoc::where('construction_site_id', $var_id)->where('folder_name', 'Schemi Impianti')->first();

                if ($fotovoltaic && $schemi) {
                    // dd("dalta k da");
                    $fotovoltaic->state = 1;
                    $schemi->state = 1;
                    $fotovoltaic->save();
                    $schemi->save();

                    $relief_doc11 = [];
                } else {
                    // dd('here');
                    $relief_doc11 = [
                        'status_relief_id' => $relief->id,
                        'construction_site_id' => $var->id,
                        'allow' => 'admin,technician,businessconsultant,photovoltaic,user',
                        'folder_name' => 'Documenti Fotovoltaico',
                        'state' => 1,
                    ];
                    //
                    // $relief_doc10 = [
                    //     'status_relief_id' => $relief->id,
                    //     'construction_site_id' => $var->id,
                    //     'allow' => 'admin,technician,businessconsultant,photovoltaic',
                    //     'folder_name' => 'Schemi Impianti',
                    //     'description' => 'Pianta lastrico solare e pianta imp. Termico',

                    // ];
                    //
                    // sub files
                    // $data =  $relief->ReliefDocument()->updateOrCreate($relief_doc10);
                    // dd($data);
                    // dd($relief_doc11);
                    $this->types_of_deduction_fotovoltaic($var, $relief, $relief_doc11);
                }
            } else {
                //    dd('dere');
                $relief_doc11 = [];
                $fotovoltaic = ReliefDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti Fotovoltaico')->first();
                $schemi = ReliefDoc::where('construction_site_id', $var_id)->where('folder_name', 'Schemi Impianti')->first();
                if ($fotovoltaic && $schemi) {
                    if ($fotovoltaic->state == 1 && $schemi->state == 1) {
                        $fotovoltaic->state = 0;
                        $schemi->state = 0;
                        $fotovoltaic->save();
                        $schemi->save();
                    } else {
                        $fotovoltaic->state = 1;
                        $schemi->state = 1;
                        $fotovoltaic->save();
                        $schemi->save();
                    }
                }
            }
        } else {


            $relief_doc11 = [];

            $fotovoltaic = ReliefDoc::where('construction_site_id', $var->id)->where('folder_name', 'Documenti Fotovoltaico')->first();
            $schemi = ReliefDoc::where('construction_site_id', $var->id)->where('folder_name', 'Schemi Impianti')->first();

            if ($fotovoltaic && $schemi) {
                if ($fotovoltaic->state == 1 && $schemi->state == 1) {
                    $fotovoltaic->state = 0;
                    $schemi->state = 0;
                    $fotovoltaic->save();
                    $schemi->save();
                } else {
                    $fotovoltaic->state = 1;
                    $schemi->state = 1;
                    $fotovoltaic->save();
                    $schemi->save();
                }
            } else {

                $relief_doc1_for_reilief = [
                    'status_relief_id' => $relief->id,
                    'construction_site_id' => $var->id,
                    'allow' => 'admin,technician,businessconsultant,photovoltaic,user',
                    'folder_name' => 'Documenti Fotovoltaico',
                    'state' => '0',
                ];

                $this->types_of_deduction_fotovoltaic($var, $relief, $relief_doc1_for_reilief);
            }
        }

        // $relief->ReliefDocument()->updateOrCreate($relief_doc1);
        //
        $relief_doc9_sub =   $relief->ReliefDocument()->updateOrCreate($relief_doc9);
        $relief->ReliefDocument()->updateOrCreate($relief_doc8);
        $relief_doc9_sub_1 = [
            'relief_doc_id' => $relief_doc9_sub->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc9_sub->folder_name,
            'file_name' => 'Cila protocollata 50-65-90',
            'description' => 'Unico file completo ufficiale',
            'bydefault' => 1,

        ];
        $relief_doc9_sub_2 = [
            'relief_doc_id' => $relief_doc9_sub->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc9_sub->folder_name,
            'file_name' => 'Cilas Protocollata 110',
            'description' => 'Unico file completo ufficiale',
            'bydefault' => 1,
        ];
        $relief_doc9_sub_3 = [
            'relief_doc_id' => $relief_doc9_sub->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc9_sub->folder_name,
            'file_name' => 'Delega Notifica Preliminare',
            'bydefault' => 1,

        ];
        $relief_doc9_sub_4 = [
            'relief_doc_id' => $relief_doc9_sub->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc9_sub->folder_name,
            'file_name' => 'Notifica Preliminare',
            'description' => 'Con GREENGEN appaltatrice + TUTTE le aziende presenti in cantiere',
            'bydefault' => 1,

        ];
        $relief_doc9_sub_5 = [
            'relief_doc_id' => $relief_doc9_sub->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc9_sub->folder_name,
            'file_name' => 'Planimetria Catastale',
            'description' => 'Aggiornata',
            'bydefault' => 1,

        ];
        $relief_doc9_sub_6 = [
            'relief_doc_id' => $relief_doc9_sub->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc9_sub->folder_name,
            'file_name' => 'Protocollo Cila 50-65-90',
            'description' => 'Unico file completo ufficiale',
            'bydefault' => 1,
        ];
        $relief_doc9_sub_7 = [
            'relief_doc_id' => $relief_doc9_sub->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc9_sub->folder_name,
            'file_name' => 'Protocollo cilas 110',
            'description' => 'No foto - solo doc ufficiale (PEC o ricevuta)',
            'bydefault' => 1,
        ];
        $relief_doc9_sub->ReliefDocumentFile()->updateOrCreate($relief_doc9_sub_1);
        $relief_doc9_sub->ReliefDocumentFile()->updateOrCreate($relief_doc9_sub_2);
        $relief_doc9_sub->ReliefDocumentFile()->updateOrCreate($relief_doc9_sub_3);
        $relief_doc9_sub->ReliefDocumentFile()->updateOrCreate($relief_doc9_sub_4);
        $relief_doc9_sub->ReliefDocumentFile()->updateOrCreate($relief_doc9_sub_5);
        $relief_doc9_sub->ReliefDocumentFile()->updateOrCreate($relief_doc9_sub_6);
        $relief_doc9_sub->ReliefDocumentFile()->updateOrCreate($relief_doc9_sub_7);

        $relief_doc_file = $relief->ReliefDocument()->updateOrCreate($relief_doc2);

        // we uncommit from here
        $relief_doc_file1 = [
            'relief_doc_id' => $relief_doc_file->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_file->folder_name,
            'file_name' => 'Ape regionale',
            'description' => 'Documento ufficiale',
            'bydefault' => 1,
        ];
        $relief_doc_file2 = [
            'relief_doc_id' => $relief_doc_file->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_file->folder_name,
            'file_name' => 'Legge 10',
            'bydefault' => 1,
        ];
        $relief_doc_file3 = [
            'relief_doc_id' => $relief_doc_file->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_file->folder_name,
            'file_name' => 'Legge 10 SALDO',
            'bydefault' => 1,
        ];
        $relief_doc_file4 = [
            'relief_doc_id' => $relief_doc_file->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_file->folder_name,
            'file_name' => 'Ricevuta Ape Regione',
            'description' => 'Ricevuta invio Ape Regione',
            'bydefault' => 1,
        ];


        $relief_doc_file5 = [
            'relief_doc_id' => $relief_doc_file->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_file->folder_name,
            'file_name' => 'Notifica SALDO',
            // 'description'=> 'Ricevuta invio Ape Regione',
            'bydefault' => 1,
            'state' => 'saldo',
        ];

        $relief_doc_file6 = [
            'relief_doc_id' => $relief_doc_file->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_file->folder_name,
            'file_name' => 'Formulario rifiuti',
            // 'description'=> 'Ricevuta invio Ape Regione',
            'bydefault' => 1,
            'state' => 'saldo',
        ];



        $relief_doc_file->ReliefDocumentFile()->updateOrCreate($relief_doc_file1);
        $relief_doc_file->ReliefDocumentFile()->updateOrCreate($relief_doc_file2);
        $relief_doc_file->ReliefDocumentFile()->updateOrCreate($relief_doc_file3);
        $relief_doc_file->ReliefDocumentFile()->updateOrCreate($relief_doc_file4);
        $relief_doc_file->ReliefDocumentFile()->updateOrCreate($relief_doc_file5);
        $relief_doc_file->ReliefDocumentFile()->updateOrCreate($relief_doc_file6);
        //
        $relief_doc_sub_file3 = $relief->ReliefDocument()->updateOrCreate($relief_doc3);
        // add by default sub files
        $relief_doc_sub1_files_for3 = [
            'relief_doc_id' => $relief_doc_sub_file3->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file3->folder_name,
            'allow' => 'admin,user',
            'file_name' => 'Catasto Impianti',
            'description' => 'Primo RCEE',
            'bydefault' => '1',
        ];
        $relief_doc_sub2_files_for3 = [
            'relief_doc_id' => $relief_doc_sub_file3->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file3->folder_name,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'Libretto Impianti Ante',
            'description' => 'Se presente o redatto da Greengen',
            'bydefault' => '1',
        ];
        $relief_doc_sub3_files_for3 = [
            'relief_doc_id' => $relief_doc_sub_file3->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file3->folder_name,
            'allow' => 'admin,user',
            'file_name' => 'Libretto Impianti Post',
            'bydefault' => '1',
        ];
        $relief_doc_sub_file3->ReliefDocumentFile()->updateOrCreate($relief_doc_sub1_files_for3);
        $relief_doc_sub_file3->ReliefDocumentFile()->updateOrCreate($relief_doc_sub2_files_for3);
        $relief_doc_sub_file3->ReliefDocumentFile()->updateOrCreate($relief_doc_sub3_files_for3);
        //
        $relief_doc_sub_file4 = $relief->ReliefDocument()->updateOrCreate($relief_doc4);
        //

        $relief_doc_sub1_files_for4 = [
            'relief_doc_id' => $relief_doc_sub_file4->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file4->folder_name,
            'allow' => 'admin,technician,businessconsultant,user',
            'folder_name' => 'Scheda Dati Ante Opera',
        ];
        $relief_doc_sub2_files_for4 = [
            'relief_doc_id' => $relief_doc_sub_file4->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file4->folder_name,
            'allow' => 'admin,technician,businessconsultant,user',
            'folder_name' => 'Scheda Interventi',
        ];
        $relief_doc_sub3_files_for4 = [
            'relief_doc_id' => $relief_doc_sub_file4->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file4->folder_name,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'DWG',
            'bydefault' => '1',
        ];
        $relifdocfilesub1 = $relief_doc_sub_file4->ReliefDocumentFile()->updateOrCreate($relief_doc_sub1_files_for4);
        // add sub file 1
        // $relifdocfilesub1_folder = [
        //     'rel_doc_file_id'=>$relifdocfilesub1->id,
        //     'construction_site_id'=>$var->id,
        //     'rel_doc_file_folder_name'=>$relifdocfilesub1->folder_name,
        // ];
        // $relifdocfilesub1->RelifDocFileSub1()->updateOrCreate($relifdocfilesub1_folder);
        // add further files against sub folder
        $relifdocfilesub2 = $relief_doc_sub_file4->ReliefDocumentFile()->updateOrCreate($relief_doc_sub2_files_for4);
        // add file
        // $relifdocfilesub2_folder = [
        //     'rel_doc_file_id'=>$relifdocfilesub2->id,
        //     'construction_site_id'=>$var->id,
        //     'rel_doc_file_folder_name'=>$relifdocfilesub2->folder_name,
        // ];
        // $relifdocfilesub2->RelifDocFileSub1()->updateOrCreate($relifdocfilesub2_folder);
        //
        $relief_doc_sub_file4->ReliefDocumentFile()->updateOrCreate($relief_doc_sub3_files_for4);
        //
        $relief_doc_sub_file5 = $relief->ReliefDocument()->updateOrCreate($relief_doc5);
        // add sub files
        $relief_doc_sub1_files_for5 = [
            'relief_doc_id' => $relief_doc_sub_file5->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file5->folder_name,
            'allow' => 'admin,businessconsultant,user',
            'file_name' => 'Atto Di Provenienza',
            'description' => 'Dell"immobile (NO NOTA DI TRASCRIZIONE)',
            'bydefault' => '1',
        ];
        $relief_doc_sub2_files_for5_2 = [
            'relief_doc_id' => $relief_doc_sub_file5->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file5->folder_name,
            'file_name' => "Carta D'identità",
            'description' => '(Fronte - retro) in corso di validità',
            'bydefault' => '1',
        ];
        $relief_doc_sub3_files_for5_3 = [
            'relief_doc_id' => $relief_doc_sub_file5->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file5->folder_name,
            'allow' => 'admin,businessconsultant,user',
            'file_name' => 'Codice Fiscale',
            'description' => '(Fronte - retro) in corso di validità',
            'bydefault' => '1',
        ];
        // $relief_doc_sub3_files_for5_33 = [
        //     'relief_doc_id' => $relief_doc_sub_file5->id,
        //     'construction_site_id' => $var->id,
        //     'ref_folder_name' => $relief_doc_sub_file5->folder_name,
        //     'allow' => 'admin,businessconsultant,user',
        //     'file_name' => 'Estratto di Mappa',
        //     'description' => 'Aggiornato',
        //     'bydefault' => '1',
        // ];
        $relief_doc_sub4_files_for5_4 = [
            'relief_doc_id' => $relief_doc_sub_file5->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file5->folder_name,
            'file_name' => 'Partita Iva',
            'allow' => 'admin,user',
            'bydefault' => '1',
        ];
        // $relief_doc_sub5_files_for5 = [
        //     'relief_doc_id' => $relief_doc_sub_file5->id,
        //     'construction_site_id' => $var->id,
        //     'ref_folder_name' => $relief_doc_sub_file5->folder_name,
        //     'file_name' => 'Partita Iva',
        //     'bydefault' => '1',
        // ];
        $relief_doc_sub6_files_for5_5 = [
            'relief_doc_id' => $relief_doc_sub_file5->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file5->folder_name,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'Visura Catastale',
            'description' => 'Aggiornata',
            'bydefault' => '1',
        ];
        $relief_doc_sub_file5->ReliefDocumentFile()->updateOrCreate($relief_doc_sub1_files_for5);
        $relief_doc_sub_file5->ReliefDocumentFile()->updateOrCreate($relief_doc_sub2_files_for5_2);
        $relief_doc_sub_file5->ReliefDocumentFile()->updateOrCreate($relief_doc_sub3_files_for5_3);
        // $relief_doc_sub_file5->ReliefDocumentFile()->updateOrCreate($relief_doc_sub3_files_for5_33);
        $relief_doc_sub_file5->ReliefDocumentFile()->updateOrCreate($relief_doc_sub4_files_for5_4);
        // $relief_doc_sub_file5->ReliefDocumentFile()->updateOrCreate($relief_doc_sub5_files_for5_5);
        $relief_doc_sub_file5->ReliefDocumentFile()->updateOrCreate($relief_doc_sub6_files_for5_5);
        //
        $relief_doc_sub_file6 = $relief->ReliefDocument()->updateOrCreate($relief_doc6);
        //
        $relief_doc_sub1_files_for6_1 = [
            'relief_doc_id' => $relief_doc_sub_file6->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file6->folder_name,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'Carta D Identità Co-intestatario',
            'description' => '(Fronte - retro) in corso di validità',
            'bydefault' => '1',
        ];
        $relief_doc_sub2_files_for6_2 = [
            'relief_doc_id' => $relief_doc_sub_file6->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file6->folder_name,
            'allow' => 'admin,technician,businessconsultant',
            'file_name' => 'Consenso Lavori',
            'bydefault' => '1',
        ];
        $relief_doc_sub_file6->ReliefDocumentFile()->updateOrCreate($relief_doc_sub1_files_for6_1);
        $relief_doc_sub_file6->ReliefDocumentFile()->updateOrCreate($relief_doc_sub2_files_for6_2);
        //
        $relief_doc_sub_file7 = $relief->ReliefDocument()->updateOrCreate($relief_doc7);
        $relief->ReliefDocument()->updateOrCreate($relief_doc11);
        // add sub file against document7
        $relief_doc_sub1_files_for7_1 = [
            'relief_doc_id' => $relief_doc_sub_file7->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file7->folder_name,
            'file_name' => 'Verbale Consegna Chiavetta',
            'allow' => 'admin,user',
            'bydefault' => '1',
        ];
        $relief_doc_sub2_files_for7_2 = [
            'relief_doc_id' => $relief_doc_sub_file7->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file7->folder_name,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'Verbale Consegna Lavori',
            'bydefault' => '1',
        ];
        // $relief_doc_sub2_files_for7_2_2 = [
        //     'relief_doc_id' => $relief_doc_sub_file7->id,
        //     'construction_site_id' => $var->id,
        //     'ref_folder_name' => $relief_doc_sub_file7->folder_name,
        //     'allow' => 'admin,user',
        //     'file_name' => 'Verbale Consegna Infissi',
        //     'bydefault' => '1',
        // ];
        $relief_doc_sub3_files_for7_3 = [
            'relief_doc_id' => $relief_doc_sub_file7->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file7->folder_name,
            'file_name' => 'Sopralluogo Fine Lavori',
            'allow' => 'admin,user',
            'bydefault' => '1',
        ];
        $relief_doc_sub4_files_for7_4 = [
            'relief_doc_id' => $relief_doc_sub_file7->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file7->folder_name,
            'file_name' => 'Comunicazione Fine Lavori',
            'allow' => 'admin,user',
            'bydefault' => '1',
        ];
        $relief_doc_sub5_files_for7_5 = [
            'relief_doc_id' => $relief_doc_sub_file7->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file7->folder_name,
            'file_name' => 'Protocollo Comunicazione Fine Lavori',
            'allow' => 'admin,user',
            'bydefault' => '1',
        ];
        $relief_doc_sub_file7->ReliefDocumentFile()->updateOrCreate($relief_doc_sub1_files_for7_1);
        $relief_doc_sub_file7->ReliefDocumentFile()->updateOrCreate($relief_doc_sub2_files_for7_2);
        // $relief_doc_sub_file7->ReliefDocumentFile()->updateOrCreate($relief_doc_sub2_files_for7_2_2);
        $relief_doc_sub_file7->ReliefDocumentFile()->updateOrCreate($relief_doc_sub3_files_for7_3);
        $relief_doc_sub_file7->ReliefDocumentFile()->updateOrCreate($relief_doc_sub4_files_for7_4);
        $relief_doc_sub_file7->ReliefDocumentFile()->updateOrCreate($relief_doc_sub5_files_for7_5);
        //
        // status legge 10
        $stleg10 = $var->StatusLegge10()->updateOrCreate($cons_id);
        $leag10doc1 = [
            'status_leg10_id' => $stleg10->id,
            'construction_site_id' => $var->id,
            'file_name' => 'Ape Regionale',
            'description' => 'Documento ufficiale',
            'allow' => 'admin,user',
            'status' => 'MANCANTE',
            'bydefault' => 1,
        ];
        $leag10doc2 = [
            'status_leg10_id' => $stleg10->id,
            'construction_site_id' => $var->id,
            'file_name' => 'Legge 10',
            'allow' => 'admin,technician,businessconsultant,user',
            'status' => 'MANCANTE',
            'bydefault' => 1,
        ];
        $leag10doc3 = [
            'status_leg10_id' => $stleg10->id,
            'construction_site_id' => $var->id,
            'file_name' => 'Ricevuta Ape Regione',
            'description' => 'Ricevuta invio Ape Regione',
            'allow' => 'admin,user',
            'status' => 'MANCANTE',
            'bydefault' => 1,
        ];

        $stleg10->Legge10DocumentFile()->updateOrCreate($leag10doc1);
        $stleg10->Legge10DocumentFile()->updateOrCreate($leag10doc2);
        $stleg10->Legge10DocumentFile()->updateOrCreate($leag10doc3);
        // StatusComputation
        $var->StatusComputation()->updateOrCreate($cons_id);
        // StatusPrNoti
        $StatusPrNoti = $var->StatusPrNoti()->updateOrCreate($cons_id);

        // PrNotDoc
        $pri_not_doc1  = [
            'status_pr_noti_id' => $StatusPrNoti->id,
            'construction_site_id' => $var->id,
            'folder_name' => 'Altri Documenti Interni',
            'description' => 'Documenti vari interni',
            'allow' => 'admin,user',
            'state' => 1,
        ];


        $pri_not_doc2 = [
            'status_pr_noti_id' => $StatusPrNoti->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => "Conferme D Ordine",
            'description' => 'Infissi - imprese - ecc.',
            'state' => 1,
        ];
        $pri_not_doc3 = [
            'status_pr_noti_id' => $StatusPrNoti->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => 'Contratto Di Subappalto Impresa',
            'description' => 'Firmato - con allegato lavorazioni',
            'state' => 1,
        ];
        $pri_not_doc4 = [
            'status_pr_noti_id' => $StatusPrNoti->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,photovoltaic,user',
            'folder_name' => 'Dico',
            'description' => 'Completo di impaginazione, timbro',
            'state' => 1,
        ];
        // --------------------------

        if ($type_of_deduction_values) {

            $state110 = 0;
            $state50 = 0;
            $state65 = 0;
            $state90 = 0;
            $stateFotovoltaico = 0;

            foreach ($type_of_deduction_values as $value) {
                if ($value == "110") {
                    $state110 = 1;
                }
                if ($value == "50") {
                    $state50 = 1;
                }
                if ($value == "65") {
                    $state65 = 1;
                }

                if ($value == "90") {
                    $state90 = 1;
                }
                if ($value == "Fotovoltaico") {
                    $stateFotovoltaico = 1;
                }
            }
        } else {

            $state110 = 0;
            $state50 = 0;
            $state65 = 0;
            $state90 = 0;
            $stateFotovoltaico = 0;
        }

        // document stateFotovoltaico
        // $pri_not_doc_stateFotovoltaico =
        //     [
        //         'status_pr_noti_id' => $StatusPrNoti->id,
        //         'construction_site_id' => $var->id,
        //         'folder_name' => 'Documenti Fotovoltaico',
        //         'state' => $stateFotovoltaico,
        //     ];

        //-------------->
        $pri_not_doc9 = [
            'status_pr_noti_id' => $StatusPrNoti->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => 'Documenti Conformità',
            'state' => 1,
        ];
        $pri_not_doc10 = [
            'status_pr_noti_id' => $StatusPrNoti->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => 'Documenti Rilevanti',
            'description' => 'Documenti condivisi rilevanti',
            'state' => 1,
        ];
        $pri_not_doc11 = [
            'status_pr_noti_id' => $StatusPrNoti->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => 'Documenti Sicurezza',
            'description' => 'PSC, POS e allegati',
            'state' => 1,
        ];
        $pri_not_doc12 = [
            'status_pr_noti_id' => $StatusPrNoti->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => 'Documentazione Varia',
            'state' => 1,
        ];
        $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc1);

        $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc2);
        $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc3);

        $dico = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc4); //add by default some file against this dico

        $dico_folder1 = [
            'pr_not_doc_id' => $dico->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,photovoltaic,user',
            'file_name' => 'DICO Impianto Elettrico',
            'bydefault' => 1,
            'state' => 1,
        ];
        $dico_folder2 = [
            'pr_not_doc_id' => $dico->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,photovoltaic,user',
            'file_name' => 'DICO Impianto Fotovoltaico',
            'bydefault' => 1,
            'state' => 1,
        ];
        $dico_folder3 = [
            'pr_not_doc_id' => $dico->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,photovoltaic,user',
            'file_name' => 'DICO Impianto Idrico-Fognante',
            'bydefault' => 1,
            'state' => 1,
        ];
        $dico_folder4 = [
            'pr_not_doc_id' => $dico->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,photovoltaic,user',
            'file_name' => 'DICO Impianto Termico',
            'bydefault' => 1,
            'state' => 1,
        ];
        $dico->PrNotDocFile()->updateOrCreate($dico_folder1);
        $dico->PrNotDocFile()->updateOrCreate($dico_folder2);
        $dico->PrNotDocFile()->updateOrCreate($dico_folder3);
        $dico->PrNotDocFile()->updateOrCreate($dico_folder4);
        //here add document 50, 65,90,110 and photovoltaic
        if ($type_of_deduction_values) {
            // dd($type_of_deduction_values);
            $var_id = $var->id;

            if (in_array("110", $type_of_deduction_values)) {

                // document 110
                $check = PrNotDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti 110')->first();
                if ($check) {
                    $check->state = 1;
                    $check->save();
                } else {
                    $pri_not_doc8 =
                        [
                            'status_pr_noti_id' => $StatusPrNoti->id,
                            'construction_site_id' => $var->id,
                            'folder_name' => 'Documenti 110',
                            'description' => 'Computo, asseverazione, fatture e visto conformità',
                            'allow' => 'admin,user',
                            'state' => 1
                        ];
                    $sub110 = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc8);
                    $this->types_of_deduction110($var_id, $sub110);
                }
            } else {

                $check = PrNotDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti 110')->first();
                if ($check) {
                    if ($check->state == 1) {
                        // dd("if");
                        $check->state = 0;
                    } else {
                        // dd("else");
                        $check->state = 1;
                    }
                    $check->save();
                }
            }
            if (in_array("50", $type_of_deduction_values)) {
                // document 50
                $check = PrNotDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti 50')->first();
                if ($check) {
                    $check->state = 1;
                    $check->save();
                } else {
                    $pri_not_doc5 =
                        [
                            'status_pr_noti_id' => $StatusPrNoti->id,
                            'construction_site_id' => $var->id,
                            'folder_name' => 'Documenti 50',
                            'description' => 'Completa e firmata',
                            'allow' => 'admin,user',
                            'state' => 1
                        ];
                    $sub50 = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc5);
                    $this->types_of_deduction50($var_id, $sub50);
                }
            } else {

                $check = PrNotDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti 50')->first();
                if ($check) {
                    if ($check->state == 1) {
                        $check->state = 0;
                    } else {
                        $check->state = 1;
                    }
                    $check->save();
                }
            }
            if (in_array("65", $type_of_deduction_values)) {
                // document 65
                $check = PrNotDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti 65')->first();
                if ($check) {
                    $check->state = 1;
                    $check->save();
                } else {
                    $pri_not_doc6 =
                        [
                            'status_pr_noti_id' => $StatusPrNoti->id,
                            'construction_site_id' => $var->id,
                            'folder_name' => 'Documenti 65',
                            'description' => 'Computo, asseverazione, fatture e notifica protocollo',
                            'allow' => 'admin,user',
                            'state' => 1
                        ];
                    $sub65 = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc6);
                    $this->types_of_deduction65($var_id, $sub65);
                }
            } else {

                $check = PrNotDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti 65')->first();
                if ($check) {
                    if ($check->state == 1) {
                        $check->state = 0;
                    } else {
                        $check->state = 1;
                    }
                    $check->save();
                }
            }
            if (in_array("90", $type_of_deduction_values)) {
                // document 90
                $check = PrNotDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti 90')->first();
                if ($check) {
                    $check->state = 1;
                    $check->save();
                } else {

                    $pri_not_doc7 =
                        [
                            'status_pr_noti_id' => $StatusPrNoti->id,
                            'construction_site_id' => $var->id,
                            'folder_name' => 'Documenti 90',
                            'description' => 'Computo, asseverazione, fatture e notifica protocollo',
                            'allow' => 'admin,user',
                            'state' => 1
                        ];
                    $sub90 = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc7);
                    $this->types_of_deduction90($var_id, $sub90);
                }
            } else {

                $check = PrNotDoc::where('construction_site_id', $var_id)->where('folder_name', 'Documenti 90')->first();
                if ($check) {
                    if ($check->state == 1) {
                        $check->state = 0;
                    } else {
                        $check->state = 1;
                    }
                    $check->save();
                }
            }
            // if($value == "Fotovoltaico")
            // {
            //     $this->types_of_deduction_fotovoltaic($var_id,$subfotovoltaico);
            // }

        }
        // end

        // $subfotovoltaico = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc_stateFotovoltaico); //------------hide
        // function call
        // $var_id = $var->id;
        // $this->types_of_deduction($var_id,$sub50,$sub65,$sub90,$sub110, $subfotovoltaico);

        $pri_not_doc9_sub_file = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc9);
        $pri_not_doc9_sub_file1 = [
            'pr_not_doc_id' => $pri_not_doc9_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Conformità Infissi',
            'description' => 'Redatto dall azienda infissi',
            'bydefault' => 1,
            'state' => 1,

        ];
        $pri_not_doc9_sub_file->PrNotDocFile()->updateOrCreate($pri_not_doc9_sub_file1);
        //
        $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc10);
        $pri_not_doc_subfile1 = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc11);
        // create sub file documenti sicureza
        $documenti_sicureza_subfile0 = [
            'pr_not_doc_id' => $pri_not_doc_subfile1->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => 'Aggiornamenti Notifiche',
            'state' => 1,
        ];
        $documenti_sicureza_subfile1 = [
            'pr_not_doc_id' => $pri_not_doc_subfile1->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => 'Psc E Allegati',
            'state' => 1,
        ];
        $documenti_sicureza_subfile2 = [
            'pr_not_doc_id' => $pri_not_doc_subfile1->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'folder_name' => 'Pos Impresa',
            'state' => 1,
        ];
        $pri_not_doc_subfile1->TypeOfDedectionSub1()->updateOrCreate($documenti_sicureza_subfile0);
        $pri_not_doc_subfile1->TypeOfDedectionSub1()->updateOrCreate($documenti_sicureza_subfile1);
        $pri_not_doc_subfile1->TypeOfDedectionSub1()->updateOrCreate($documenti_sicureza_subfile2);

        // $doc12_sub_file13_1103 = [
        //     'pr_not_doc_id' => $pri_not_doc_subfile1->id,
        //     'construction_site_id' => $var->id,
        //     'allow' => 'admin,user',
        //     'file_name' => 'Notifica Preliminare',
        //     'description' => 'Prima notifica preliminare',
        //     'bydefault' => '1',
        //     'state' => 1,
        // ];


        // $pri_not_doc_subfile1->TypeOfDedectionSub1()->updateOrCreate($doc12_sub_file13_1103);

        // $doc12_sub_file13_1103 = [
        //     'pr_not_doc_id' => $pri_not_doc_subfile1->id,
        //     'construction_site_id' => $var->id,
        //     'allow' => 'admin,user',
        //     'file_name' => 'Notifica Preliminare',
        //     'description' => 'Prima notifica preliminare',
        //     'bydefault' => 1,
        //     'state' => 1,
        // ];
        // $pri_not_doc_subfile1->TypeOfDedectionSub1()->updateOrCreate($doc12_sub_file13_1103);




        $pr_not_doc12_sub_file = $StatusPrNoti->PrNotDoc()->updateOrCreate($pri_not_doc12);
        $doc12_sub_file1 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'file_name' => 'Autodich Assenza Irregolarità',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file2 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Cessione Bonus',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file3 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Contratto Cessione Credito',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file4 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Delega Accesso Atti',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file5 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Delega Accesso Planimetrie',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file6 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Delega Commercialista Guarino',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file7 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Inc Prof Ape Regione',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file8 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'file_name' => 'Inc Prof Zac',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file9 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'file_name' => 'Iva Agevolata',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file10 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'file_name' => 'Opzione Cessione Greengen-Guarino',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1,
        ];
        $doc12_sub_file11 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Privacy',
            'bydefault' => '1',
            'state' => 1,
        ];
        $doc12_sub_file12 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Procura',
            'bydefault' => '1',
            'state' => 1,
        ];
        $doc12_sub_file13 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Scan Antimafia',
            'bydefault' => '1',
            'state' => 1,
        ];
        // $doc12_sub_file14 = [
        //     'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
        //     'construction_site_id' => $var->id,
        //     'allow' => 'admin,user',
        //     'file_name' => 'Inc Prof Zacc',
        //     'bydefault' => '1',
        //     'state' => 1,
        // ];
        $doc12_sub_file13_50 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin',
            'file_name' => 'Delega Commercialista Rizzi 50',
            'bydefault' => '1',
            'state' => 1,
        ];
        $doc12_sub_file13_65 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin',
            'file_name' => 'Delega Commercialista Rizzi 65',
            'bydefault' => '1',
            'state' => 1,
        ];
        $doc12_sub_file13_90 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin',
            'file_name' => 'Delega Commercialista Rizzi 90',
            'bydefault' => '1',
            'state' => 1,
        ];
        $doc12_sub_file13_110 = [
            'pr_not_doc_id' => $pr_not_doc12_sub_file->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Delega Commercialista Rizzi 110',
            'bydefault' => '1',
            'state' => 1,
        ];

        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file1);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file2);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file3);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file4);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file5);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file6);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file7);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file8);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file9);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file10);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file11);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file12);
        $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file13);
        // $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file14);
        if (in_array("50", $type_of_deduction_values)) {
            $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file13_50);
        }
        if (in_array("65", $type_of_deduction_values)) {
            $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file13_65);
        }
        if (in_array("90", $type_of_deduction_values)) {
            $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file13_90);
        }
        if (in_array("110", $type_of_deduction_values)) {
            $pr_not_doc12_sub_file->PrNotDocFile()->updateOrCreate($doc12_sub_file13_110);
        }
        //add sub files

        // pri Noti Doc File PrNotDocFile
        //status regustration prac
        $reg_prac_doc = $var->statusRegPrac()->updateOrCreate($cons_id);
        // ReliefDoc
        $reg_prac_doc1 =  [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant',
            'file_name' => 'Cila Protocollata 50-65-90',
            'description' => 'Unico file completo ufficiale',
            'state' => 'MANCANTE',
            'bydefault' => 1
        ];
        $reg_prac_doc2 =   [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'Cilas Protocollata 110',
            'description' => 'Unico file completo ufficiale',
            'state' => 'MANCANTE',
            'bydefault' => 1,
        ];
        $reg_prac_doc3 =   [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin',
            'file_name' => 'Delega Notifica Preliminare',
            'state' => 'MANCANTE',
            'bydefault' => 1
        ];
        $reg_prac_doc4 =   [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'Notifica Preliminare',
            'description' => 'Prima notifica preliminare',
            'state' => 'MANCANTE',
            'bydefault' => 1
        ];
        $reg_prac_doc5 =   [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'Planimetria Catastale',
            'description' => 'Aggiornata',
            'state' => 'MANCANTE',
            'bydefault' => 1
        ];
        $reg_prac_doc6 =  [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant',
            'file_name' => 'Protocollo cila 50-65-90',
            'state' => 'MANCANTE',
            'bydefault' => 1
        ];
        $reg_prac_doc7 =  [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,technician,businessconsultant,user',
            'file_name' => 'Protocollo Cilas 110',
            'description' => 'No foto - solo doc ufficiale (PEC o ricevuta)',
            'state' => 'MANCANTE',
            'bydefault' => 1
        ];
        $reg_prac_doc7_7 =  [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Inc Prof Zacc', 'state' => 'MANCANTE',
            'bydefault' => 1
        ];
        $reg_prac_doc7_7_7 =  [
            'status_reg_prac_id' => $reg_prac_doc->id,
            'construction_site_id' => $var->id,
            'allow' => 'admin,user',
            'file_name' => 'Iva Agevoltata', 'state' => 'MANCANTE',
            'bydefault' => 1
        ];

        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc1);
        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc2);
        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc3);
        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc4);
        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc5);
        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc6);
        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc7);
        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc7_7);
        $reg_prac_doc->RegPracDoc()->updateOrCreate($reg_prac_doc7_7_7);

        // StatusWorkStarted
        $var->StatusWorkStarted()->updateOrCreate($cons_id);
        // StatusSAL
        $var->StatusSAL()->updateOrCreate($cons_id);
        // StatusEneaBalance
        $var->StatusEneaBalance()->updateOrCreate($cons_id);
        // StatusWorkClose
        $var->StatusWorkClose()->updateOrCreate($cons_id);
        return true;
    }

    // here we add data by relationship in types of deduction schema
    public function types_of_deduction50($var_id, $sub50)
    {
        // for document 50
        $folder1_50 = [
            'pr_not_doc_id' => $sub50->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti SAL 50',
            'description' => 'Completa e firmata',
            'allow' => 'admin,user',
            'state' => 1,
        ];
        $folder1_50_50 = [
            'pr_not_doc_id' => $sub50->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti SALDO 50',
            'description' => 'Computo, asseverazione, fatture e notifica protocollo',
            'allow' => 'admin,user',
            'state' => 1,
        ];
        // $folder2_50 = [
        //     'pr_not_doc_id' => $sub50->id,
        //     'construction_site_id' => $var_id,
        //     'folder_name' => 'Documents BALANCE 50',
        //     'allow' => 'admin,user',
        //     'state' => 1,
        // ];
        $folder3_50 =
            [
                'pr_not_doc_id' => $sub50->id,
                'construction_site_id' => $var_id,
                'folder_name' => 'Fattura SAL 50',
                'allow' => 'admin,user',
                'state' => 0,
            ];
        // $folder4_50 =
        //     [
        //         'pr_not_doc_id' => $sub50->id,
        //         'construction_site_id' => $var_id,
        //         'file_name' => 'Contract 50',
        //         'allow' => 'admin,user',
        //         'bydefault' => 1,
        //     ];
        $folder5_50 = [
            'pr_not_doc_id' => $sub50->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Contratto 50',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_50_1 = $sub50->TypeOfDedectionSub1()->updateOrCreate($folder1_50);
        $sub2_50_2 = $sub50->TypeOfDedectionSub1()->updateOrCreate($folder1_50_50);
        //  $sub50->TypeOfDedectionSub1()->updateOrCreate($folder2_50);
        $sub50->TypeOfDedectionSub1()->updateOrCreate($folder3_50);
        // $sub50->TypeOfDedectionSub1()->updateOrCreate($folder4_50);
        $sub50->TypeOfDedectionSub1()->updateOrCreate($folder5_50);
        $sub_folder_1 = [
            'type_of_dedection_sub1_id' => $sub2_50_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Asseverazione SAL 50',
            'description' => 'Documento ufficiale',
            'bydefault' => '1',
            'state' => 1
        ];
        $sub_folder_2 = [
            'type_of_dedection_sub1_id' => $sub2_50_1->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Bonifico E Ritenute SAL 50',
            'allow' => 'admin,user',
            'bydefault' => '1',
            'state' => 1
        ];
        $sub_folder_3 = [
            'type_of_dedection_sub1_id' => $sub2_50_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Computo 50',
            'description' => 'Completo di impaginazione, timbrof',
            'bydefault' => '1',
            'state' => 1
        ];
        $sub_folder_4 = [
            'type_of_dedection_sub1_id' => $sub2_50_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Fattura 50',
            'description' => 'Completo di impaginazione, timbro',
            'bydefault' => '1',
            'state' => 1
        ];
        $sub_folder_5 = [
            'type_of_dedection_sub1_id' => $sub2_50_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Scan Visto di Conformita Sal 50',
            'bydefault' => '1',
            'state' => 1
        ];
        // $sub_folder_5_5 = [
        //     'type_of_dedection_sub1_id' => $sub2_50_1->id,
        //     'construction_site_id' => $var_id,
        //     'allow' => 'admin,businessconsultant',
        //     'folder_name' => 'Ricevuta Di Invio Ade Sal 50',
        //     'bydefault' => '1',
        //     'state' => 1
        // ];
        $sub_folder_6 = [
            'type_of_dedection_sub1_id' => $sub2_50_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Dichiarazione Sostitutiva Atto Di Notorietà 50',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_7 = [
            'type_of_dedection_sub1_id' => $sub2_50_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Opzione Cessione 50',
            'bydefault' => 1,
            'state' => 1
        ];
        //-- $sub2_50_2
        $sub_folder_8 = [
            'type_of_dedection_sub1_id' => $sub2_50_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Asseverazione SALDO 50',
            'description' => 'Documento ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_9 = [
            'type_of_dedection_sub1_id' => $sub2_50_2->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Bonifico E Ritenute SALDO 50',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_10 = [
            'type_of_dedection_sub1_id' => $sub2_50_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Computo SALDO 50',
            'description' => 'Completo di impaginazione, timbro e riepilogo SAL',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_11 = [
            'type_of_dedection_sub1_id' => $sub2_50_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Fattura SALDO 50',
            'description' => 'Fattura ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_11_chevita = [
            'type_of_dedection_sub1_id' => $sub2_50_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Contratto 50',
            'bydefault' => 1,
            'state' => 0
        ];
        $sub_folder_12 = [
            'type_of_dedection_sub1_id' => $sub2_50_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformita SALDO 50',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_13 = [
            'type_of_dedection_sub1_id' => $sub2_50_2->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Dichiarazione Enea SALDO 50',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_50_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_1);
        $sub2_50_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_2);
        $sub2_50_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_3);
        $sub2_50_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_4);

        $sub2_50_1_1 =  $sub2_50_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_5);

        // $sub2_50_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_5_5);
        $sub2_50_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_6);
        $sub2_50_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_7);
        $sub2_50_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_8);
        $sub2_50_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_9);
        $sub2_50_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_10);
        $sub2_50_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_11);
        $sub2_50_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_11_chevita);
        $sub_SALDO_50 =  $sub2_50_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_12);


        $sub2_50_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_13);


        $sub2_50_2_1 = [
            'type_of_dedection_sub2_id' => $sub2_50_1_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Scan Visto di Conformita Sal 50',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub2_50_2_2 = [
            'type_of_dedection_sub2_id' => $sub2_50_1_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformita Firmato Sal 50',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_50_2_3 = [
            'type_of_dedection_sub2_id' => $sub2_50_1_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Ricevuta Di Invio Ade Sal 50',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_50_1_1->TypeOfDedectionFiles()->updateOrCreate($sub2_50_2_1);
        $sub2_50_1_1->TypeOfDedectionFiles()->updateOrCreate($sub2_50_2_2);
        $sub2_50_1_1->TypeOfDedectionFiles()->updateOrCreate($sub2_50_2_3);

        $sub2_saldo_50_2_1 = [
            'type_of_dedection_sub2_id' => $sub_SALDO_50->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Scan Visto di Conformità Sal 50',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_saldo_50_2_2 = [
            'type_of_dedection_sub2_id' => $sub_SALDO_50->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformità Firmato SALDO 50',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_saldo_50_2_3 = [
            'type_of_dedection_sub2_id' => $sub_SALDO_50->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Ricevuta Di Invio Ade SALDO 50',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub_SALDO_50->TypeOfDedectionFiles()->updateOrCreate($sub2_saldo_50_2_1);
        $sub_SALDO_50->TypeOfDedectionFiles()->updateOrCreate($sub2_saldo_50_2_2);
        $sub_SALDO_50->TypeOfDedectionFiles()->updateOrCreate($sub2_saldo_50_2_3);


        return true;
    }

    // here we add data by relationship in types of deduction schema
    public function types_of_deduction65($var_id, $sub65)
    {
        $folder1_65 = [
            'pr_not_doc_id' => $sub65->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti SAL 65',
            'description' => 'Completa e firmata',
            'allow' => 'admin,user',
            'state' => 1,
        ];
        $folder1_65_65 = [
            'pr_not_doc_id' => $sub65->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti SALDO 65',
            'description' => 'Computo, asseverazione, fatture e notifica protocollo',
            'allow' => 'admin,user',
            'state' => 1,
        ];
        // $folder2_65 = [
        //     'pr_not_doc_id' => $sub65->id,
        //     'construction_site_id' => $var_id,
        //     'folder_name' => 'Documents BALANCE 65',
        //     'allow' => 'admin,user',
        //     'state' => 1,
        // ];
        $folder4_65 = [
            'pr_not_doc_id' => $sub65->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Fattura SAL 65',
            'allow' => 'admin,user',
            'state' => 0,
        ];
        // $folder3_65 =
        //     [
        //         'pr_not_doc_id' => $sub65->id,
        //         'construction_site_id' => $var_id,
        //         'file_name' => 'Contract 65',
        //         'allow' => 'admin,user',
        //         'bydefault' => 1,
        //         'state' => 1,
        //     ];
        $folder5_65 = [
            'pr_not_doc_id' => $sub65->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Contratto 65',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];

        // document 65
        $sub2_65_1 = $sub65->TypeOfDedectionSub1()->updateOrCreate($folder1_65);
        $sub_folder1_65_65 = $sub65->TypeOfDedectionSub1()->updateOrCreate($folder1_65_65);

        // $sub2_65_2 = $sub65->TypeOfDedectionSub1()->updateOrCreate($folder2_65);
        // $sub2_65_3 = $sub65->TypeOfDedectionSub1()->updateOrCreate($folder3_65);
        $sub65->TypeOfDedectionSub1()->updateOrCreate($folder4_65);
        $sub65->TypeOfDedectionSub1()->updateOrCreate($folder5_65);
        //--for sub2_65_1
        $sub_folder_14 = [
            'type_of_dedection_sub1_id' => $sub2_65_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Asseverazione SAL 65',
            'description' => 'Documento ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_15 = [
            'type_of_dedection_sub1_id' => $sub2_65_1->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Bonifico e ritenute SAL 65',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_16 = [
            'type_of_dedection_sub1_id' => $sub2_65_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Computo 65',
            'description' => 'Completo di impaginazione, timbro',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_17 = [
            'type_of_dedection_sub1_id' => $sub2_65_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Fattura 65',
            'description' => 'Completo di impaginazione, timbro',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_18 = [
            'type_of_dedection_sub1_id' => $sub2_65_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => "Visto di Conformita' Sal 65",
            'bydefault' => 1,
            'state' => 1
        ];
        // $sub_folder_18_18 = [
        //     'type_of_dedection_sub1_id' => $sub2_65_1->id,
        //     'construction_site_id' => $var_id,
        //     'allow' => 'admin,businessconsultant',
        //     'folder_name' => 'Visto Di Conformità Firmato Sal 65',
        //     'bydefault' => 1,
        //     'state' => 1
        // ];
        // $sub_folder_18_18_18 = [
        //     'type_of_dedection_sub1_id' => $sub2_65_1->id,
        //     'construction_site_id' => $var_id,
        //     'allow' => 'admin,businessconsultant',
        //     'folder_name' => 'Ricevuta Di Invio Ade Sal 65',
        //     'bydefault' => 1,
        //     'state' => 1
        // ];
        $sub_folder_19 = [
            'type_of_dedection_sub1_id' => $sub2_65_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Dichiarazione Sostitutiva Atto Di Notorietà 65',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_20 = [
            'type_of_dedection_sub1_id' => $sub2_65_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Opzione Cessione 65',
            'bydefault' => 1,
            'state' => 1
        ];
        //-- $sub2_65_2
        $sub_folder_21 = [
            'type_of_dedection_sub1_id' => $sub_folder1_65_65->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Asseverazione SALDO 65',
            'description' => 'Documento ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_22 = [
            'type_of_dedection_sub1_id' => $sub_folder1_65_65->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Bonifico E Ritenute SALDO 65',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_23 = [
            'type_of_dedection_sub1_id' => $sub_folder1_65_65->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Computo SALDO 65',
            'description' => 'Completo di impaginazione, timbro e riepilogo SAL',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_24 = [
            'type_of_dedection_sub1_id' => $sub_folder1_65_65->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Fattura SALDO 65',
            'description' => 'Fattura ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_24_for_chavetta = [
            'type_of_dedection_sub1_id' => $sub_folder1_65_65->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Fattura SAL 65',
            'description' => 'Completo di impaginazione, timbro',
            'bydefault' => 1,
            'state' => 0
        ];
        $sub_folder_25 = [
            'type_of_dedection_sub1_id' => $sub_folder1_65_65->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformita SALDO 65',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_26 = [
            'type_of_dedection_sub1_id' => $sub_folder1_65_65->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Dichiarazione Enea SALDO 65',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_14);
        $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_15);
        $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_16);
        $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_17);

        $sub2_65_1_1 =  $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_18);

        // $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_18_18);
        // $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_18_18_18);
        $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_19);
        $sub2_65_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_20);
        $sub_folder1_65_65->TypeOfDedectionSub2()->updateOrCreate($sub_folder_21);
        $sub_folder1_65_65->TypeOfDedectionSub2()->updateOrCreate($sub_folder_22);
        $sub_folder1_65_65->TypeOfDedectionSub2()->updateOrCreate($sub_folder_23);
        $sub_folder1_65_65->TypeOfDedectionSub2()->updateOrCreate($sub_folder_24);
        $sub_folder1_65_65->TypeOfDedectionSub2()->updateOrCreate($sub_folder_24_for_chavetta);

        $sub_sub_folder_25 = $sub_folder1_65_65->TypeOfDedectionSub2()->updateOrCreate($sub_folder_25);

        $sub_folder1_65_65->TypeOfDedectionSub2()->updateOrCreate($sub_folder_26);


        $sub2_65_2_1 = [
            'type_of_dedection_sub2_id' => $sub2_65_1_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Scan Visto di Conformità Sal 65',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub2_65_2_2 = [
            'type_of_dedection_sub2_id' => $sub2_65_1_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformita Firmato Sal 65',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_65_2_3 = [
            'type_of_dedection_sub2_id' => $sub2_65_1_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Ricevuta Di Invio Ade Sal 65',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_65_1_1->TypeOfDedectionFiles()->updateOrCreate($sub2_65_2_1);
        $sub2_65_1_1->TypeOfDedectionFiles()->updateOrCreate($sub2_65_2_2);
        $sub2_65_1_1->TypeOfDedectionFiles()->updateOrCreate($sub2_65_2_3);



        $sub_sub_folder_25_1 = [
            'type_of_dedection_sub2_id' => $sub_sub_folder_25->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Scan Visto di Conformità SALDO 65',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_sub_folder_25_2 = [
            'type_of_dedection_sub2_id' => $sub_sub_folder_25->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformità Firmato SALDO 65',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_sub_folder_25_3 = [
            'type_of_dedection_sub2_id' => $sub_sub_folder_25->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Ricevuta Di Invio Ade SALDO 65',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_sub_folder_25->TypeOfDedectionFiles()->updateOrCreate($sub_sub_folder_25_1);
        $sub_sub_folder_25->TypeOfDedectionFiles()->updateOrCreate($sub_sub_folder_25_2);
        $sub_sub_folder_25->TypeOfDedectionFiles()->updateOrCreate($sub_sub_folder_25_3);
        //--end 65
        return true;
    }
    // here we add data by relationship in types of deduction schema
    public function types_of_deduction90($var_id, $sub90)
    {
        // for document 90
        $folder1_90 = [
            'pr_not_doc_id' => $sub90->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti SAL 90',
            'description' => 'Completa e firmata',
            'allow' => 'admin,user',
            'state' => 1,
        ];
        $folder2_90 = [
            'pr_not_doc_id' => $sub90->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti SALDO 90',
            'description' => 'Computo, asseverazione, fatture e notifica protocollo',
            'allow' => 'admin,user',
            'state' => 1,
        ];
        // $folder3_90 =
        //     [
        //         'pr_not_doc_id' => $sub90->id,
        //         'construction_site_id' => $var_id,
        //         'folder_name' => 'Fattura SAL 90',
        //         'allow' => 'admin,user',
        //         'state' => 0,
        //     ];
        // $folder4_90 =
        //     [
        //         'pr_not_doc_id' => $sub90->id,
        //         'construction_site_id' => $var_id,
        //         'file_name' => 'Contract 90',
        //         'allow' => 'admin,user',
        //         'bydefault' => 1,
        //         'state' => 0,
        //     ];
        $folder5_90 = [
            'pr_not_doc_id' => $sub90->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Contratto 90',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];

        // document 90
        $sub2_90_1 = $sub90->TypeOfDedectionSub1()->updateOrCreate($folder1_90);
        $sub2_90_2 = $sub90->TypeOfDedectionSub1()->updateOrCreate($folder2_90);
        // $sub2_90_3 = $sub90->TypeOfDedectionSub1()->updateOrCreate($folder3_90);
        // $sub90->TypeOfDedectionSub1()->updateOrCreate($folder4_90);
        $sub90->TypeOfDedectionSub1()->updateOrCreate($folder5_90);
        //--for sub2_90_1

        $sub_folder_27 = [
            'type_of_dedection_sub1_id' => $sub2_90_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Asseverazione SAL 90',
            'description' => 'Documento ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_29 = [
            'type_of_dedection_sub1_id' => $sub2_90_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Computo 90',
            'description' => 'Completo di impaginazione, timbro',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_30 = [
            'type_of_dedection_sub1_id' => $sub2_90_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Fattura 90',
            'description' => 'Completo di impaginazione, timbro',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_31 = [
            'type_of_dedection_sub1_id' => $sub2_90_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto di Conformita Sal 90',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_32 = [
            'type_of_dedection_sub1_id' => $sub2_90_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Dichiarazione Sostitutiva Atto Di Notorietà 90',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_33 = [
            'type_of_dedection_sub1_id' => $sub2_90_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Opzione Cessione 90',
            'bydefault' => 1,
            'state' => 1
        ];
        //-- $sub2_90_2
        $sub_folder_34 = [
            'type_of_dedection_sub1_id' => $sub2_90_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant, user',
            'folder_name' => 'Asseverazione SALDO 90',
            'description' => 'Documento ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_34_1 = [
            'type_of_dedection_sub1_id' => $sub2_90_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Fattura SAL 90',
            'description' => 'Completo di impaginazione, timbro',
            'bydefault' => 1,
            'state' => 0
        ];
        $sub_folder_36 = [
            'type_of_dedection_sub1_id' => $sub2_90_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Computo SALDO 90',
            'description' => 'Completo di impaginazione, timbro e riepilogo SAL',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_37 = [
            'type_of_dedection_sub1_id' => $sub2_90_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Fattura SALDO 90',
            'description' => 'Fattura ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_38 = [
            'type_of_dedection_sub1_id' => $sub2_90_2->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant, user',
            'folder_name' => 'Visto Di Conformita SALDO 90',
            'bydefault' => 1,
            'state' => 1
        ];


        $sub2_90_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_27);
        $sub2_90_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_29);
        $sub2_90_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_30);
        $sub_folder_31_sub = $sub2_90_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_31);
        $sub2_90_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_32);
        $sub2_90_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_33);
        $sub2_90_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_34);
        $sub2_90_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_34_1);
        $sub2_90_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_36);
        $sub2_90_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_37);
        $data_3 =  $sub2_90_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_38);



        // here we need to set the subfile properly

        $sub_folder_38_38 = [
            'type_of_dedection_sub2_id' => $data_3->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Scan Visto Di Conformita Sal 90',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_38_38_38 = [
            'type_of_dedection_sub2_id' => $data_3->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformita Firmato Sal 90',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_38_38_38_38 = [
            'type_of_dedection_sub2_id' => $data_3->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Ricevuta Di Invio Ade Sal 90',
            'bydefault' => 1,
            'state' => 1
        ];
        $data_3->TypeOfDedectionFiles()->updateOrCreate($sub_folder_38_38);
        $data_3->TypeOfDedectionFiles()->updateOrCreate($sub_folder_38_38_38);
        $data_3->TypeOfDedectionFiles()->updateOrCreate($sub_folder_38_38_38_38);



        $sub_folder_31_sub_1 = [
            'type_of_dedection_sub2_id' => $sub_folder_31_sub->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Scan Visto di Conformità Sal 90',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_31_sub_2 = [
            'type_of_dedection_sub2_id' => $sub_folder_31_sub->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformità Firmato Sal 90',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_31_sub_3 = [
            'type_of_dedection_sub2_id' => $sub_folder_31_sub->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Ricevuta Di Invio Ade Sal 90',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_31_sub->TypeOfDedectionFiles()->updateOrCreate($sub_folder_31_sub_1);
        $sub_folder_31_sub->TypeOfDedectionFiles()->updateOrCreate($sub_folder_31_sub_2);
        $sub_folder_31_sub->TypeOfDedectionFiles()->updateOrCreate($sub_folder_31_sub_3);

        return true;
    }

    // here we add data by relationship in types of deduction schema
    public function types_of_deduction110($var_id, $sub110)
    {
        // for document 110
        $folder1_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti Sal 110',
            'description' => 'Documenti SAL 30%',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $folder2_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti 2SAL',
            'description' => 'Documenti 2SAL%',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $folder3_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Dichiarazione 30',
            'description' => 'Dichiarazione lavori 30% + allegati',
            'allow' => 'admin,user',
            'type' => 'folder',
            'bydefault' => 1,
            'state' => 1
        ];
        $folder4_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Documenti Saldo 110',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];

        $folder6_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Contratto 110',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $folder11_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Contratto Di Mandato Senza Rappresentanza',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        // this folder will only show in chiavetta page
        $folder7_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Ricevuta Di Invio Ade Sal 110',
            'allow' => 'admin,user',
            'state' => 0
        ];
        $folder8_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Ricevuta Di Invio Ade Saldo 110',
            'allow' => 'admin,user',
            'state' => 0
        ];

        $folder9_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformita Firmato Sal 110',
            'state' => 0
        ];
        $folder10_110 = [
            'pr_not_doc_id' => $sub110->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Visto Di Conformita Firmato Saldo 110',
            'allow' => 'admin,user',
            'state' => 0
        ];


        // document 110
        $sub2_110_1 = $sub110->TypeOfDedectionSub1()->updateOrCreate($folder1_110);

        $sub2_110_2 = $sub110->TypeOfDedectionSub1()->updateOrCreate($folder2_110);
        $sub2_110_3 = $sub110->TypeOfDedectionSub1()->updateOrCreate($folder3_110);
        $sub2_110_4 = $sub110->TypeOfDedectionSub1()->updateOrCreate($folder4_110);

        $sub2_110_6 = $sub110->TypeOfDedectionSub1()->updateOrCreate($folder7_110);
        $sub2_110_6 = $sub110->TypeOfDedectionSub1()->updateOrCreate($folder8_110);
        $sub2_110_6 = $sub110->TypeOfDedectionSub1()->updateOrCreate($folder9_110);
        $sub2_110_6 = $sub110->TypeOfDedectionSub1()->updateOrCreate($folder10_110);
        $sub110->TypeOfDedectionSub1()->updateOrCreate($folder11_110);
        $sub110->TypeOfDedectionSub1()->updateOrCreate($folder6_110);
        // sub folder -> $sub2_110_1
        $sub_folder_39 = [
            'type_of_dedection_sub1_id' => $sub2_110_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Asseverazione Sal 110',
            'description' => 'Documento ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_40 = [
            'type_of_dedection_sub1_id' => $sub2_110_1->id,
            'construction_site_id' => $var_id,

            'folder_name' => 'Visto Di Conformita Sal 110',

            'allow' => 'admin,businessconsultant',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_41 = [
            'type_of_dedection_sub1_id' => $sub2_110_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Computo Sal 110',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_42 = [
            'type_of_dedection_sub1_id' => $sub2_110_1->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Fattura Sal 110',
            'description' => 'Fattura ufficiale',
            'allow' => 'admin,businessconsultant',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_43 = [
            'type_of_dedection_sub1_id' => $sub2_110_1->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Computo Metrico Firmato Impresa',
            'description' => 'Tutte le pagine',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_44 = [
            'type_of_dedection_sub1_id' => $sub2_110_1->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Computo Metrico Firmato Cliente',
            'description' => 'Tutte le pagine',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_Dichiarazione = [
            'type_of_dedection_sub1_id' => $sub2_110_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Dichiarazione Sostitutiva Atto Di Notorietà 110',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_45 = [
            'type_of_dedection_sub1_id' => $sub2_110_1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'file_name' => 'Opzione Cessione 110',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub2_110_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_39);
        $sub2_110_subfile1 = $sub2_110_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_40);
        //

        $deduc_sub_sile1 = [
            'type_of_dedection_sub2_id' => $sub2_110_subfile1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',

            'folder_name' => 'Scan Visto Di Conformita Sal 110',

            'bydefault' => 1,
            'state' => 1
        ];
        $deduc_sub_sile2 = [
            'type_of_dedection_sub2_id' => $sub2_110_subfile1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Visto Di Conformita Firmato Sal 110',
            'bydefault' => 1,
            'state' => 1
        ];
        $deduc_sub_sile3 = [
            'type_of_dedection_sub2_id' => $sub2_110_subfile1->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => 'Ricevuta Di Invio Ade Sal 110',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub2_110_subfile1->TypeOfDedectionFiles()->updateOrCreate($deduc_sub_sile1);
        $sub2_110_subfile1->TypeOfDedectionFiles()->updateOrCreate($deduc_sub_sile2);
        $sub2_110_subfile1->TypeOfDedectionFiles()->updateOrCreate($deduc_sub_sile3);
        //
        $sub2_110_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_41);
        $sub2_110_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_42);
        $sub2_110_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_43);
        $sub2_110_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_44);
        $sub2_110_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_45);
        $sub2_110_1->TypeOfDedectionSub2()->updateOrCreate($sub_folder_Dichiarazione);

        // ----end and save
        // sub folder -> $sub2_110_2
        $sub_folder_46 = [
            'type_of_dedection_sub1_id' => $sub2_110_2->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Asseverazione 2SAL',
            'description' => 'Documento ufficiale',

            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_47 = [
            'type_of_dedection_sub1_id' => $sub2_110_2->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Visto Di Conformita 2SAL',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_48 = [
            'type_of_dedection_sub1_id' => $sub2_110_2->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'COMPUTO 2SAL',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_49 =  [
            'type_of_dedection_sub1_id' => $sub2_110_2->id,
            'construction_site_id' => $var_id,
            'folder_name' => 'Fattura 2SAL',
            'description' => 'Fattura ufficiale',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_50 = [
            'type_of_dedection_sub1_id' => $sub2_110_2->id,
            'construction_site_id' => $var_id,
            'file_name' => 'Computo Metrico Firmato Impresa 2SAL',
            'allow' => 'admin,user',
            'bydefault' => 1,
            'state' => 1
        ];
        // $sub_folder_51 = [
        //     'type_of_dedection_sub1_id' => $sub2_110_2->id,
        //     'construction_site_id' => $var_id,
        //     'file_name' => 'Computo Metrico Firmato Cliente 2SAL',
        //     'allow' => 'admin,user',
        //     'bydefault' => 1,
        //     'state' => 1
        // ];
        // $sub_folder_52 = [
        //     'type_of_dedection_sub1_id' => $sub2_110_2->id,
        //     'construction_site_id' => $var_id,
        //     'file_name' => 'Dichiarazione Sostitutiva Atto Di Notorietà 2SAL',
        //     'allow' => 'admin,user',
        //     'bydefault' => 1,
        //     'state' => 1
        // ];
        // $sub_folder_53 = [
        //     'type_of_dedection_sub1_id' => $sub2_110_2->id,
        //     'construction_site_id' => $var_id,
        //     'file_name' => 'Opzione Cessione 2SAL',
        //     'allow' => 'admin,user',
        //     'bydefault' => 1,
        //     'state' => 1
        // ];
        $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_46);
        $sub_folder_47_sub = $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_47);
        $sub_folder_47_sub_1 =  [
            'type_of_dedection_sub2_id' => $sub_folder_47_sub->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => "Scan Visto Di Conformita' 2SAL",
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_47_sub_2 =  [
            'type_of_dedection_sub2_id' => $sub_folder_47_sub->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => "Visto Di Conformita Firmato 2SAL",
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_47_sub_3 =  [
            'type_of_dedection_sub2_id' => $sub_folder_47_sub->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant',
            'folder_name' => "Ricevuta Di Invio Ade 2SAL",
            'bydefault' => 1,
            'state' => 1
        ];

        $sub_folder_47_sub->TypeOfDedectionFiles()->updateOrCreate($sub_folder_47_sub_1);
        $sub_folder_47_sub->TypeOfDedectionFiles()->updateOrCreate($sub_folder_47_sub_2);
        $sub_folder_47_sub->TypeOfDedectionFiles()->updateOrCreate($sub_folder_47_sub_3);


        $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_48);
        $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_48);
        $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_49);
        // $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_50);
        // $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_51);
        // $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_52);
        // $sub2_110_2->TypeOfDedectionSub2()->updateOrCreate($sub_folder_53);
        // ---end and save
        // $sub2_110_4
        $sub_folder_54 = [
            'type_of_dedection_sub1_id' => $sub2_110_4->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Asseverazione Saldo 110',
            'description' => 'Documento ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_55 = [
            'type_of_dedection_sub1_id' => $sub2_110_4->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Computo Saldo 110',
            'description' => 'Completo di impaginazione, timbro e riepilogo SAL',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_56 = [
            'type_of_dedection_sub1_id' => $sub2_110_4->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Fattura Saldo 110',
            'description' => 'Fattura ufficiale',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_file_56 = [
            'type_of_dedection_sub1_id' => $sub2_110_4->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'file_name' => 'DURC di congruità',
            'bydefault' => 1,
            'state' => 1
        ];
        $sub_folder_57 = [
            'type_of_dedection_sub1_id' => $sub2_110_4->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Visto Di Conformita Saldo 110',
            'bydefault' => 1,
            'state' => 1
        ];

        $sub2_110_4->TypeOfDedectionSub2()->updateOrCreate($sub_folder_54);
        $sub2_110_4->TypeOfDedectionSub2()->updateOrCreate($sub_folder_55);
        $sub2_110_4->TypeOfDedectionSub2()->updateOrCreate($sub_folder_56);

        $MainFolder =   $sub2_110_4->TypeOfDedectionSub2()->updateOrCreate($sub_folder_57);
        $sub2_110_4->TypeOfDedectionSub2()->updateOrCreate($sub_file_56);
        //    dd($MainFolder->TypeOfDedectionFiles);
        $VistoDiConformitaSub1 =  [
            'type_of_dedection_sub2_id' => $MainFolder->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Scan Visto Di Conformita Saldo 110',
            'bydefault' => 1,
            'state' => 1
        ];
        $VistoDiConformitaSub2 =  [
            'type_of_dedection_sub2_id' => $MainFolder->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Visto Di Conformita Firmato Saldo 110',
            'bydefault' => 1,
            'state' => 1
        ];
        $VistoDiConformitaSub3 =  [
            'type_of_dedection_sub2_id' => $MainFolder->id,
            'construction_site_id' => $var_id,
            'allow' => 'admin,businessconsultant,user',
            'folder_name' => 'Ricevuta Di Invio Ade Saldo 110',
            'bydefault' => 1,
            'state' => 1
        ];

        $MainFolder->TypeOfDedectionFiles()->updateOrCreate($VistoDiConformitaSub1);
        $MainFolder->TypeOfDedectionFiles()->updateOrCreate($VistoDiConformitaSub2);
        $MainFolder->TypeOfDedectionFiles()->updateOrCreate($VistoDiConformitaSub3);
        return true;
    }

    // add fotovoltaic
    public function types_of_deduction_fotovoltaic($var, $relief, $relief_doc11)
    {
        $relief_doc_sub_file8 = $relief->ReliefDocument()->updateOrCreate($relief_doc11);

        $relief_doc_sub1_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,technician,businessconsultant,photovoltaic,user',
            'file_name' => 'Bolletta Luce',
            'description' => 'Dellimmobile (prime 2 pagine)',
            'bydefault' => '1',
        ];
        $relief_doc_sub2_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,technician,businessconsultant,photovoltaic,user',
            'file_name' => 'Carta D Identità Intestatario Bollette',
            'description' => '(Fronte - retro) in corso di validità',
            'bydefault' => '1',
        ];
        $relief_doc_sub3_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,photovoltaic,user',
            'file_name' => 'Codici accesso portale',
            'bydefault' => '1',
        ];
        $relief_doc_sub4_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,photovoltaic,user',
            'file_name' => 'Contratto GSE',
            'bydefault' => '1',
        ];
        $relief_doc_sub5_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,photovoltaic,user',
            'file_name' => 'Estensione Garanzia FTV',
            'bydefault' => '1',
        ];
        $relief_doc_sub6_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,technician',
            'file_name' => 'Estratto Di Mappa',
            'description' => 'Aggiornato',
            'bydefault' => '1',
        ];
        $relief_doc_sub7_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,photovoltaic',
            'file_name' => 'Iban',
            'bydefault' => '1',
        ];
        $relief_doc_sub8_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,photovoltaic,user',
            'file_name' => 'Mandato RAP',
            'bydefault' => '1',
        ];
        $relief_doc_sub9_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'allow' => 'admin,photovoltaic',
            'file_name' => 'Sezione H',
            'bydefault' => '1',
        ];
        $relief_doc_sub10_files_for8 = [
            'relief_doc_id' => $relief_doc_sub_file8->id,
            'construction_site_id' => $var->id,
            'ref_folder_name' => $relief_doc_sub_file8->folder_name,
            'file_name' => 'ANTONACCI IMMACOLATA relazione fotovoltaico',
            'allow' => 'admin,user',
            'bydefault' => '1',
        ];
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub1_files_for8);
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub2_files_for8);
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub3_files_for8);
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub4_files_for8);
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub5_files_for8);
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub6_files_for8);
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub7_files_for8);
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub8_files_for8);
        $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub9_files_for8);
        // $relief_doc_sub_file8->ReliefDocumentFile()->updateOrCreate($relief_doc_sub10_files_for8);

        return true;
    }
    // old code
    // private function types_of_deduction($var_id,$sub50,$sub65,$sub90,$sub110,$subfotovoltaico)
    // {
    //     // ----$subfotovoltaico
    //     $subfotovoltaico1 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Bolletta Luce',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico2 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Carta d identità intestatario bollette',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico3 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Codici accesso portale',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico4 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Contratto GSE',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico5 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Estensione garanzia FTV',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico6 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Estratto di Mappa',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico7 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Iban',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico8 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Mandato RAP',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico9 = [
    //         'pr_not_doc_id'=>$subfotovoltaico->id,
    //         'construction_site_id' => $var_id,
    //         'file_name'=>'Sezione H',
    //         'state'=> 1,
    //     ];
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico1);
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico2);
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico3);
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico4);
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico5);
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico6);
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico7);
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico8);
    //     $subfotovoltaico->TypeOfDedectionSub1()->create($subfotovoltaico9);
    //     //--------------------types of deduction sub2

    //     //--------------------end


    // }
    /**
     * here we get session id
     */
    public function check_latest_status($page, $currentStatus)
    {
        $this->status_changes($page, $currentStatus);
        return true;
    }

    private function status_changes($page, $currentStatus)
    {
        $construct_id = $this->session_get("construction_id");
        $status = 1;

        $id = [
            'construction_site_id' => $construct_id
        ];

        $constructionSite = ConstructionSite::where('id', $construct_id)->first();

        /**
         * checking status
         */
        // $latestStatus = null;
        $StatusPreAnalysis = $constructionSite->StatusPreAnalysis->state;
        $StatusTechnician = $constructionSite->StatusTechnician->state;
        $StatusRelief = $constructionSite->StatusRelief->state;
        $StatusLegge10 = $constructionSite->StatusLegge10->state;
        $StatusComputation = $constructionSite->StatusComputation->state;
        $StatusPrNoti = $constructionSite->StatusPrNoti->state;
        $statusRegPrac = $constructionSite->statusRegPrac->state;
        $StatusWorkStarted = $constructionSite->StatusWorkStarted->state;
        $StatusSAL = $constructionSite->StatusSAL->state;
        $statusEneaBalance = $constructionSite->statusEneaBalance->state;
        $statusWorkClose = $constructionSite->statusWorkClose->state;

        switch ($page) {
            case 'pre_analysis':

                if ($StatusPreAnalysis == 'To be invoiced') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Preanalisi Fatturato';
                    }
                } elseif ($StatusPreAnalysis == 'Revenue') {

                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Preanalisi Fatturato';
                    }
                } elseif ($StatusPreAnalysis == 'Cashed out' || $StatusPreAnalysis == 'Not due') {
                    if ($StatusTechnician == 'Not Assigned' || $StatusTechnician == null) {
                        $latestStatus = 'Tecnico Da Assegnare';
                        $constructionSite->StatusTechnician->updateOrCreate($id, ['state' => 'Not Assigned']);
                    } else {
                        if ($StatusRelief == 'To assign' || $StatusRelief == null) {
                            $latestStatus = 'agevolazione da assegnare';
                            $constructionSite->StatusRelief->updateOrCreate($id, ['state' => 'To assign']);
                        } elseif ($StatusRelief == 'Received') {
                            if ($StatusLegge10 == 'Waiting' || $StatusLegge10 == null) {
                                $latestStatus = 'legge 10 in attesa';
                                $constructionSite->StatusLegge10->updateOrCreate($id, ['state' => 'Waiting']);
                            } elseif ($StatusLegge10 == 'Completed') {
                                if ($StatusComputation == 'Waiting' || $StatusComputation == null) {
                                    $latestStatus = 'Computo In Attesa';
                                    $constructionSite->StatusComputation->updateOrCreate($id, ['state' => 'Waiting']);
                                } elseif ($StatusComputation == 'Completed') {
                                    if ($StatusPrNoti == 'Waiting' || $StatusPrNoti == null) {
                                        $latestStatus = 'Notifica Preliminare In Attesa';
                                        $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => 'Waiting']);
                                    } elseif ($StatusPrNoti == 'Completed') {
                                        if ($statusRegPrac == 'Waiting' || $statusRegPrac == null) {
                                            $latestStatus = 'Pratica Protocollata In Attesa';
                                            $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => 'Waiting']);
                                        } elseif ($statusRegPrac == 'Completed') {
                                            if ($StatusWorkStarted == 'Waiting' || $StatusWorkStarted == null || $StatusWorkStarted == 'Deliever') {
                                                // $latestStatus = 'Lavori Iniziati In Attesa' == null ? 'inattesa' : strtolower($StatusWorkStarted);
                                                $latestStatus = 'Lavori Iniziati In Attesa';
                                                $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);
                                            } elseif ($StatusWorkStarted == 'Completed') {
                                                if ($StatusSAL == 'Waiting' || $StatusSAL == null) {
                                                    $latestStatus = 'SAL in attesa';
                                                    $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                                                } elseif ($StatusSAL == 'Completed') {
                                                    if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                                                        $latestStatus = 'Saldo Enea In Attesa';
                                                        $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                                                    } elseif ($statusEneaBalance == 'Completed') {
                                                        if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                                                            $latestStatus = 'Chiuso In Attesa';
                                                            $status = 1;
                                                            $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                                                        } elseif ($statusWorkClose == 'Completed') {
                                                            $latestStatus = 'Chiusa';
                                                            $status = 0;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case 'status_technisan':

                if ($StatusTechnician == 'Not Assigned') {

                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Tecnico Da Assegnare';
                    }
                    $constructionSite->StatusTechnician->updateOrCreate($id, ['state' => 'Not Assigned']);
                } elseif ($StatusTechnician == null) {
                    // $latestStatus = 'Tecnico Da Assegnare';
                    $constructionSite->StatusTechnician->updateOrCreate($id, ['state' => null]);
                } else {
                    if ($StatusRelief == 'To assign' || $StatusRelief == null) {
                        $latestStatus = 'Rilievo In Attesa';
                        $constructionSite->StatusRelief->updateOrCreate($id, ['state' => 'To assign']);
                    } elseif ($StatusRelief == 'Received') {
                        if ($StatusLegge10 == 'Waiting' || $StatusLegge10 == null) {
                            $latestStatus = 'legge 10 In Attesa';
                            $constructionSite->StatusLegge10->updateOrCreate($id, ['state' => 'Waiting']);
                        } elseif ($StatusLegge10 == 'Completed') {
                            if ($StatusComputation == 'Waiting' || $StatusComputation == null) {
                                $latestStatus = 'Computo In Attesa';
                                $constructionSite->StatusComputation->updateOrCreate($id, ['state' => 'Waiting']);
                            } elseif ($StatusComputation == 'Completed') {
                                if ($StatusPrNoti == 'Waiting' || $StatusPrNoti == null) {
                                    $latestStatus = 'Notifica Preliminare In Attesa';
                                    $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => 'Waiting']);
                                } elseif ($StatusPrNoti == 'Completed') {
                                    if ($statusRegPrac == 'Waiting' || $statusRegPrac == null) {
                                        $latestStatus = 'Pratica Protocollata In Attesa';
                                        $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => 'Waiting']);
                                    } elseif ($statusRegPrac == 'Completed') {
                                        if ($StatusWorkStarted == 'Waiting' || $StatusWorkStarted == null || $StatusWorkStarted == 'Deliever') {
                                            $latestStatus = 'Lavori Iniziati In Attesa';
                                            $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);
                                        } elseif ($StatusWorkStarted == 'Completed') {
                                            if ($StatusSAL == 'Waiting' || $StatusSAL == null) {
                                                $latestStatus = 'SAL in attesa';
                                                $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                                            } elseif ($StatusSAL == 'Completed') {
                                                if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                                                    $latestStatus = 'Saldo Enea In Attesa';
                                                    $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                                                } elseif ($statusEneaBalance == 'Completed') {
                                                    if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                                                        $latestStatus = 'Chiuso In Attesa';
                                                        $status = 1;
                                                        $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                                                    } elseif ($statusWorkClose == 'Completed') {
                                                        $latestStatus = 'Chiusa';
                                                        $status = 0;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case 'status_relief':

                if ($StatusRelief == 'To assign') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Saldo Enea In Attesa';
                    }

                    $constructionSite->StatusRelief->updateOrCreate($id, ['state' => 'To assign']);
                } elseif ($StatusRelief == null) {
                    $constructionSite->StatusRelief->updateOrCreate($id, ['state' => null]);
                } elseif ($StatusRelief == 'Received') {
                    if ($StatusLegge10 == 'Waiting' || $StatusLegge10 == null) {
                        $latestStatus = 'legge 10 in attesa';
                        $constructionSite->StatusLegge10->updateOrCreate($id, ['state' => 'Waiting']);
                    } elseif ($StatusLegge10 == 'Completed') {
                        if ($StatusComputation == 'Waiting' || $StatusComputation == null) {
                            $latestStatus = 'Computo In Attesa';
                            $constructionSite->StatusComputation->updateOrCreate($id, ['state' => 'Waiting']);
                        } elseif ($StatusComputation == 'Completed') {
                            if ($StatusPrNoti == 'Waiting' || $StatusPrNoti == null) {
                                $latestStatus = 'Notifica Preliminare In Attesa';
                                $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => 'Waiting']);
                            } elseif ($StatusPrNoti == 'Completed') {
                                if ($statusRegPrac == 'Waiting' || $statusRegPrac == null) {
                                    $latestStatus = 'Pratica Protocollata In Attesa';
                                    $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => 'Waiting']);
                                } elseif ($statusRegPrac == 'Completed') {
                                    if ($StatusWorkStarted == 'Waiting' || $StatusWorkStarted == null || $StatusWorkStarted == 'Deliever') {
                                        $latestStatus = 'Lavori Iniziati In Attesa';
                                        $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);
                                    } elseif ($StatusWorkStarted == 'Completed') {
                                        if ($StatusSAL == 'Waiting' || $StatusSAL == null) {
                                            $latestStatus = 'SAL in attesa';
                                            $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                                        } elseif ($StatusSAL == 'Completed') {
                                            if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                                                $latestStatus = 'Saldo Enea In Attesa';
                                                $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                                            } elseif ($statusEneaBalance == 'Completed') {
                                                if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                                                    $latestStatus = 'Chiuso In Attesa';
                                                    $status = 1;
                                                    $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                                                } elseif ($statusWorkClose == 'Completed') {
                                                    $latestStatus = 'Chiusa';
                                                    $status = 0;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case 'status_leg10':
                if ($StatusLegge10 == 'Waiting') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'legge 10 in attesa';
                    }

                    $constructionSite->StatusLegge10->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif ($StatusLegge10 == null) {
                    $constructionSite->StatusLegge10->updateOrCreate($id, ['state' => null]);
                } elseif ($StatusLegge10 == 'Completed') {
                    if ($StatusComputation == 'Waiting' || $StatusComputation == null) {
                        $latestStatus = 'Computo In Attesa';
                        $constructionSite->StatusComputation->updateOrCreate($id, ['state' => 'Waiting']);
                    } elseif ($StatusComputation == 'Completed') {
                        if ($StatusPrNoti == 'Waiting' || $StatusPrNoti == null) {
                            $latestStatus = 'Notifica Preliminare In Attesa';
                            $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => 'Waiting']);
                        } elseif ($StatusPrNoti == 'Completed') {
                            if ($statusRegPrac == 'Waiting' || $statusRegPrac == null) {
                                $latestStatus = 'Pratica Protocollata In Attesa';
                                $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => 'Waiting']);
                            } elseif ($statusRegPrac == 'Completed') {
                                if ($StatusWorkStarted == 'Waiting' || $StatusWorkStarted == null || $StatusWorkStarted == 'Deliever') {
                                    $latestStatus = 'Lavori Iniziati In Attesa';
                                    $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);
                                } elseif ($StatusWorkStarted == 'Completed') {
                                    if ($StatusSAL == 'Waiting' || $StatusSAL == null) {
                                        $latestStatus = 'SAL in attesa';
                                        $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                                    } elseif ($StatusSAL == 'Completed') {
                                        if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                                            $latestStatus = 'Saldo Enea In Attesa';
                                            $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                                        } elseif ($statusEneaBalance == 'Completed') {
                                            if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                                                $latestStatus = 'Chiuso In Attesa';
                                                $status = 1;
                                                $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                                            } elseif ($statusWorkClose == 'Completed') {
                                                $latestStatus = 'Chiusa';
                                                $status = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case 'status_computation':

                if ($StatusComputation == 'Waiting') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Computo In Attesa';
                    }




                    // $constructionSite->StatusComputation->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif ($StatusComputation == null) {
                    $constructionSite->StatusComputation->updateOrCreate($id, ['state' => null]);
                } elseif ($StatusComputation == 'Completed') {
                    if ($StatusPrNoti == 'Waiting' || $StatusPrNoti == null) {
                        $latestStatus = 'Notifica Preliminare In Attesa';
                        $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => 'Waiting']);
                    } elseif ($StatusPrNoti == 'Completed') {
                        if ($statusRegPrac == 'Waiting' || $statusRegPrac == null) {
                            $latestStatus = 'Pratica Protocollata In Attesa';
                            $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => 'Waiting']);
                        } elseif ($statusRegPrac == 'Completed') {
                            if ($StatusWorkStarted == 'Waiting' || $StatusWorkStarted == null || $StatusWorkStarted == 'Deliever') {
                                $latestStatus = 'Lavori Iniziati In Attesa';
                                $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);
                            } elseif ($StatusWorkStarted == 'Completed') {
                                if ($StatusSAL == 'Waiting' || $StatusSAL == null) {
                                    $latestStatus = 'SAL in attesa';
                                    $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                                } elseif ($StatusSAL == 'Completed') {
                                    if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                                        $latestStatus = 'Saldo Enea In Attesa';
                                        $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                                    } elseif ($statusEneaBalance == 'Completed') {
                                        if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                                            $latestStatus = 'Chiuso In Attesa';
                                            $status = 1;
                                            $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                                        } elseif ($statusWorkClose == 'Completed') {
                                            $latestStatus = 'Chiusa';
                                            $status = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case 'status_prenoti':
                if ($StatusPrNoti == 'Waiting') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Notifica Preliminare In Attesa';
                    }

                    // $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif ($StatusPrNoti == null) {
                    $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => null]);
                } elseif ($StatusPrNoti == 'Completed') {
                    if ($statusRegPrac == 'Waiting' || $statusRegPrac == null) {
                        $latestStatus = 'Pratica Protocollata In Attesa';
                        $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => 'Waiting']);
                    } elseif ($statusRegPrac == 'Completed') {
                        if ($StatusWorkStarted == 'Waiting' || $StatusWorkStarted == null || $StatusWorkStarted == 'Deliever') {
                            $latestStatus = 'Lavori Iniziati In Attesa';
                            $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);
                        } elseif ($StatusWorkStarted == 'Completed') {
                            if ($StatusSAL == 'Waiting' || $StatusSAL == null) {
                                $latestStatus = 'SAL in attesa';
                                $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                            } elseif ($StatusSAL == 'Completed') {
                                if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                                    $latestStatus = 'Saldo Enea In Attesa';
                                    $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                                } elseif ($statusEneaBalance == 'Completed') {
                                    if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                                        $latestStatus = 'Chiuso In Attesa';
                                        $status = 1;
                                        $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                                    } elseif ($statusWorkClose == 'Completed') {
                                        $latestStatus = 'Chiusa';
                                        $status = 0;
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case 'status_regprac':

                if ($statusRegPrac == 'Waiting') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Pratica Protocollata In Attesa';
                    }

                    $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif ($statusRegPrac == null) {
                    $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => null]);
                } elseif ($statusRegPrac == 'Completed') {
                    if ($StatusWorkStarted == 'Waiting' || $StatusWorkStarted == null || $StatusWorkStarted == 'Deliever') {
                        $latestStatus = 'Lavori Iniziati In Attesa';
                        $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);
                    } elseif ($StatusWorkStarted == 'Completed') {
                        if ($StatusSAL == 'Waiting' || $StatusSAL == null) {
                            $latestStatus = 'SAL in attesa';
                            $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                        } elseif ($StatusSAL == 'Completed') {
                            if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                                $latestStatus = 'Saldo Enea In Attesa';
                                $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                            } elseif ($statusEneaBalance == 'Completed') {
                                if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                                    $latestStatus = 'Chiuso In Attesa';
                                    $status = 1;
                                    $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                                } elseif ($statusWorkClose == 'Completed') {
                                    $latestStatus = 'Chiusa';
                                    $status = 0;
                                }
                            }
                        }
                    }
                }

                break;
            case 'status_workstarted':

                if ($StatusWorkStarted == 'Waiting'  || $StatusWorkStarted == 'Deliever') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Lavori Iniziati In Attesa';
                    }

                    $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif ($StatusWorkStarted == null) {
                    $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => null]);
                } elseif ($StatusWorkStarted == 'Completed') {
                    if ($StatusSAL == 'Waiting' || $StatusSAL == null) {
                        $latestStatus = 'SAL in attesa';
                        $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                    } elseif ($StatusSAL == 'Completed') {
                        if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                            $latestStatus = 'Saldo Enea In Attesa';
                            $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                        } elseif ($statusEneaBalance == 'Completed') {
                            if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                                $latestStatus = 'Chiuso In Attesa';
                                $status = 1;
                                $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                            } elseif ($statusWorkClose == 'Completed') {
                                $latestStatus = 'Chiusa';
                                $status = 0;
                            }
                        }
                    }
                }

                break;
            case 'status_sal':

                if ($StatusSAL == 'Waiting') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'SAL in attesa';
                    }

                    $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif ($StatusSAL == null) {
                    $constructionSite->StatusSAL->updateOrCreate($id, ['state' => null]);
                } elseif ($StatusSAL == 'Completed') {
                    if ($statusEneaBalance == 'Waiting' || $statusEneaBalance == null) {
                        $latestStatus = 'Saldo Enea In Attesa';
                        $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                    } elseif ($statusEneaBalance == 'Completed') {
                        if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                            $latestStatus = 'Chiuso In Attesa';
                            $status = 1;
                            $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                        } elseif ($statusWorkClose == 'Completed') {
                            $latestStatus = 'Chiusa';
                            $status = 0;
                        }
                    }
                }

                break;
            case 'status_eneablnc':

                if ($statusEneaBalance == 'Waiting') {
                    if ($constructionSite->latest_status == null) {
                        $latestStatus = 'Saldo Enea In Attesa';
                    }


                    $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif ($statusEneaBalance == null) {
                    $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => null]);
                } elseif ($statusEneaBalance == 'Completed') {
                    if ($statusWorkClose == 'Waiting' || $statusWorkClose == null) {
                        $latestStatus = 'Chiuso In Attesa';
                        $status = 1;
                        $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                    } elseif ($statusWorkClose == 'Completed') {
                        $latestStatus = 'Chiusa';
                        $status = 0;
                    }
                }

                break;
            case 'status_workclose':

                if ($statusWorkClose == 'Waiting') {
                    $latestStatus = 'Chiuso In Attesa';
                    $status = 1;
                    $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif ($statusWorkClose == null) {
                    $status = 1;
                    $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => null]);
                } elseif ($statusWorkClose == 'Completed') {
                    $latestStatus = 'Chiusa';
                    $status = 0;
                }

                break;
            default:
                // code to execute when $value does not match any of the options
                break;
        }
        if (isset($latestStatus)) {
            $construction_latest_status = [
                'latest_status' => $latestStatus,
                'status' => $status
            ];
        } else {
            $construction_latest_status = [
                'status' => $status
            ];
        }




        $constructionSite->update($construction_latest_status);
    }

    private function status_changes2($page, $currentStatus)
    {
        $construct_id = $this->session_get("construction_id");


        $constructionSite = ConstructionSite::where('id', $construct_id)->first();


        // Sample status mapping (replace with your actual mappings)
        $statusMapping = [
            'pre_analysis' => [
                'To be invoiced' => 'Preanalisi Fatturato',
                'Revenue' => 'Preanalisi Da Fatturare',
                'Cashed out' => 'Tecnico Da Assegnare',
                'Not due' => 'Tecnico Da Assegnare',
            ],
            'status_technisan' => [
                // 'Not Assigned' => 'Tecnico Da Assegnare',
                'Assigned' => 'Rilievo In Attesa',
            ],
            'status_relief' => [
                // 'To assign' => 'Rilievo In Attesa',
                'Received' => 'legge 10 in attesa',
            ],
            'status_leg10'  => [
                'Completed' => 'Computo In Attesa',
                // 'Waiting' =>  'legge 10 in attesa',
            ],
            'status_computation' => [
                'Completed' => 'Notifica Preliminare In Attesa',
                // 'Waiting' =>  'Computo In Attesa',
            ],
            'status_prenoti' => [
                'Completed' => 'Pratica Protocollata In Attesa',
                // 'Waiting'   => 'Notifica Preliminare In Attesa',
            ],
            'status_eneablnc' => [
                // 'Waiting' => 'Pratica Protocollata In Attesa',
                'Completed' => 'Lavori Iniziati In Attesa',
            ],
            'status_workstarted' => [
                // 'Waiting' => 'Lavori Iniziati In Attesa',
                // 'Deliever' => 'Lavori Iniziati In Attesa',
                'Completed' => 'SAL in Attesa',
            ],
            'status_sal' => [
                // 'Waiting' => 'SAL in Attesa',
                'Completed' => 'Saldo Enea In Attesa',
            ],
            'status_eneablnc' => [
                'Completed' => 'Chiuso In Attesa',
                //   'Waiting' => 'Saldo Enea In Attesa',
            ],
            'status_workclose' => [
                'Completed' => 'Chiuso',
                // 'Waiting' => 'Chiuso In Attesa',
            ]

        ];





        // Check if the current status for the page exists in the mapping
        if ($page ==  'pre_analysis') {
            $StatusTechnician = $constructionSite->StatusTechnician->state;
            if ($StatusTechnician ==  null || $StatusTechnician == 'Not Assigned') {
                $constructionSite->StatusTechnician->update(['state' => 'Not Assigned']);
            }
            $StatusRelief = $constructionSite->StatusRelief->state;
            if ($StatusRelief ==  null || $StatusRelief == 'To assign') {
                $constructionSite->StatusRelief->update(['state' => 'To assign']);
            }
            $StatusLegge10 = $constructionSite->StatusLegge10->state;
            if ($StatusLegge10 ==  null || $StatusLegge10 == 'Waiting') {
                $constructionSite->StatusLegge10->update(['state' => 'Waiting']);
            }
            $StatusComputation = $constructionSite->StatusComputation->state;
            if ($StatusComputation ==  null || $StatusComputation == 'Waiting') {
                $constructionSite->StatusComputation->update(['state' => 'Waiting']);
            }
            $StatusPrNoti = $constructionSite->StatusPrNoti->state;
            if ($StatusPrNoti ==  null || $StatusPrNoti == 'Waiting') {
                $constructionSite->StatusPrNoti->update(['state' => 'Waiting']);
            }
            $statusRegPrac = $constructionSite->statusRegPrac->state;
            if ($statusRegPrac ==  null || $statusRegPrac == 'Waiting') {
                $constructionSite->statusRegPrac->update(['state' => 'Waiting']);
            }
            $StatusWorkStarted = $constructionSite->StatusWorkStarted->state;
            if ($StatusWorkStarted ==  null || $StatusWorkStarted == 'Waiting') {
                $constructionSite->StatusWorkStarted->update(['state' => 'Waiting']);
            }
            $StatusSAL = $constructionSite->StatusSAL->state;
            if ($StatusSAL ==  null || $StatusSAL == 'Waiting') {
                $constructionSite->StatusSAL->update(['state' => 'Waiting']);
            }
            $statusEneaBalance = $constructionSite->statusEneaBalance->state;


            $statusWorkClose = $constructionSite->statusWorkClose->state;
        } elseif ($page ==  'status_technisan') {
        } elseif ($page ==  'status_relief') {
        } elseif ($page == 'status_leg10') {
        } elseif ($page == 'status_computation') {
        } elseif ($page == 'status_prenoti') {
        } elseif ($page == 'status_eneablnc') {
        } elseif ($page == 'status_workstarted') {
        } elseif ($page ==  'status_sal') {
        } elseif ($page ==  'status_eneablnc') {
        } elseif ($page ==  'status_workclose') {
        }

        $statusWorkClose == 'Waiting' || $statusWorkClose == null ? $status = '1' : $status = '0';

        $constructionSite->update(['status' => $status]);

        if (isset($statusMapping[$page][$currentStatus])) {

            $latestStatus = $statusMapping[$page][$currentStatus];
            $constructionSite->update(['latest_status' => $latestStatus]);
        } else {
            $latestStatus = 'Unknown Status';

            echo 'Latest status: ' . $latestStatus;
        }
    }


    public function CloseStatus()
    {
        $cantiere = Checkbox::where('boxc', 1)->get();

        //$cantiere = Checkbox::get();
        $counter = 0;

        foreach ($cantiere as $data) {

            $cantiereId = $data->fk_cantiere;
            $clienteIdget = Cantiere::where('cantiereId', $cantiereId)->first();

            if ($clienteIdget) {
                $clienteId = $clienteIdget->FK_cliente;
            } else {
                $clienteId = null;
            }

            if ($clienteId != null) {

                $construction_Site = ConstructionSite::where('oldid', $clienteId)->first();
                if ($construction_Site != null) {
                    $construction_Site->status = 0;
                    $construction_Site->update();
                    $counter++;
                } else {
                    echo "<br/> Nessun record per " . $clienteId . " <hr/>";
                }
            } else {
                echo "<br/> Nessun record per " . $cantiereId . " <hr/>";
            }
        }
        echo "<br/> Total records: " . $counter . " <hr/>";
    }

    public function condominiChildDelete()
    {
        $consCondomini  = ConstructionCondomini::get();
        $count  =    0;
        foreach ($consCondomini as $consCondomini) {


            if (ucwords(strtolower(optional($consCondomini->ConstructionSiteSettingforParent)->type_of_property)) != 'Condominio') {

                echo  'construction_site_id:' . $consCondomini->construction_site_id . 'assigned_id:' . $consCondomini->construction_assigned_id  . 'poperty: ' . optional($consCondomini->ConstructionSiteSettingforParent)->type_of_property . '</br></br>';

                $consCondomini->delete();

                $count++;
                // echo $count;
            }
        }
        echo 'total records are ' . $count;
    }

    public function getCoordinates()
    {
        $constructionSites = ConstructionSite::get();
        foreach ($constructionSites as $data) {
            $construction_site_id = $data->id;
            $address = null;
            if ($data->PropertyData->property_street != null || $data->PropertyData->property_house_number != null  || $data->PropertyData->property_common != null) {
                $address = $data->PropertyData->property_street . '+' . $data->PropertyData->property_house_number . '+' . $data->PropertyData->property_common;
            }
            //$address = 'C.da Curcio 3 Alberobello';

            if ($address != null) {
                //dd($address);
                try {

                    $apiKey = 'AIzaSyBJx5-Ibg8Crb8yWXfYW1ssOccCbQa4PJo';
                    $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                        'address' => $address,
                        'key' => $apiKey,
                    ]);
                    $data = $response->json();
                    //dd($data);

                    if (isset($data['results'][0]['geometry']['location'])) {
                        $location = $data['results'][0]['geometry']['location'];

                        $catiereLat = $location['lat'];
                        $catiereLng = $location['lng'];

                        echo $catiereLat . ", " . $catiereLng  . ", " .  $construction_site_id . "</br>";
                    } else {
                        echo "Invalid address for Id:" . $construction_site_id . "</br>";
                    }
                } catch (\Exception $e) {
                    echo "Geocoding failed for Id:" . $construction_site_id . " " . $e->getMessage() . "</br>";
                }
            }
        }
        return response()->json(['message' => 'Data fetched successfully']);
    }

    public function saveNewCantiereCoordinates()
    {

        $headdata = ConstructionSite::where('id', 511)->first();


        if($headdata->pin_location != null){
            $address = $headdata->pin_location;
            
        }else{
            $address = $headdata->PropertyData->property_street . ' ' . $headdata->PropertyData->property_house_number . ' ' . $headdata->PropertyData->property_common;
        }
            // Encode the address for use in the API request
            $encodedAddress = urlencode($address);

            // Google Maps API Key (Replace with your own API key)
            $apiKey = 'AIzaSyBJx5-Ibg8Crb8yWXfYW1ssOccCbQa4PJo';

            // Construct the Geocoding API request URL
            $apiUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedAddress}&key={$apiKey}";

            // Make the API request
            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);

            // Check if the request was successful
            if ($data && $data['status'] === 'OK') {
                // Extract latitude and longitude
                $latitude = $data['results'][0]['geometry']['location']['lat'];
                $longitude = $data['results'][0]['geometry']['location']['lng'];

                // Output the results
                dd($latitude, $longitude);
                echo "Latitude: $latitude, Longitude: $longitude";
            } else {
                // Handle the error
                echo "Error fetching data from Google Maps Geocoding API.";
            }

        $ConstructionLocation = ConstructionLocation::pluck('construction_site_id')->toarray();


        ConstructionSite::whereNotIn('id', $ConstructionLocation)->where('page_status', 4)->chunk(100, function ($constructionSites) {


            foreach ($constructionSites as $data) {
                $id = $data->id;

                $address = null;
                if ($data->PropertyData->property_street != null || $data->PropertyData->property_house_number != null  || $data->PropertyData->property_common != null) {
                    $address = $data->PropertyData->property_street . '+' . $data->PropertyData->property_house_number . '+' . $data->PropertyData->property_common;
                }

                if ($address != null) {

                    try {

                        $apiKey = 'AIzaSyBJx5-Ibg8Crb8yWXfYW1ssOccCbQa4PJo';
                        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                            'address' => $address,
                            'key' => $apiKey,
                        ]);
                        $data = $response->json();

                        if (isset($data['results'][0]['geometry']['location'])) {

                            $location = $data['results'][0]['geometry']['location'];


                            $radius = 0.000225; // 25 meters in degrees

                            $latitude = $location['lat'];
                            $longitude = $location['lng'];

                            ConstructionLocation::updateOrCreate(
                                ['construction_site_id' => $id],
                                ['latitude' => $latitude, 'langitude' => $longitude]
                            );
                        }
                    } catch (\Exception $e) {
                        echo "Geocoding failed for Id:" . $id . " " . $e->getMessage() . "\n";
                    }
                }elseif($data->pin_location != null){

                }
            }
        });
    }

    // public function CommetApi()
    // {
    //     try {
    //         $this->saveNewCantiereCoordinates();

    //         $client = new Client();
    //         $loginResponse = $client->post('https://almecdiag.net:9891/login', [
    //             'json' => [
    //                 'email' => 'greengengroupsrl@gmail.com',
    //                 'password' => 'Greengengroup!',
    //             ],
    //         ]);

    //         if ($loginResponse->getStatusCode() === 200) {
    //             $authToken = json_decode($loginResponse->getBody(), true)['token'];

    //             $startDate = Carbon::now()->subHours(10); // Current date and time
    //             $endDate = Carbon::now(); // Subtract 500 hours

    //             $dtFrom = $startDate->format('Y-m-d\TH:i:s.v\Z');
    //             $dtTo = $endDate->format('Y-m-d\TH:i:s.v\Z');


    //             $positionsResponse = $client->get('https://almecdiag.net:9891/export/positions', [
    //                 'headers' => [
    //                     'Authorization' => 'Bearer ' . $authToken,
    //                 ],
    //                 'query' => [
    //                     'c_machine' => '862493059018931',
    //                     'dt_from' => $dtFrom,
    //                     'dt_to' => $dtTo,
    //                 ],
    //             ]);

    //             // Check if fetching positions was successful

    //             if ($positionsResponse->getStatusCode() === 200) {
    //                 $positionsData = json_decode($positionsResponse->getBody(), true);

    //             //     $catiereFilePath = public_path('assets/apicomet/cantieri.txt');
    //             //     // Read the contents of the catiere.txt file
    //             //     $catiereData = File::get($catiereFilePath);
    //             //     // Split the file contents into an array of lines
    //             //     $catiereLines = explode("\n", $catiereData);

    //             //         // dd($catiereLines);

    //             //     // Iterate over each line in catiere.txt
    //             //     foreach ($catiereLines as $catiereLine) {


    //             //         if (empty($catiereLine)) {
    //             //             continue;
    //             //         }

    //             //          $data =   explode(", ", $catiereLine);
    //             //          $data1 = array_map('trim', $data);

    //             //         ConstructionLocation::updateOrCreate( ['construction_site_id' => $data1[2]],['latitude' => $data1[0], 'langitude' => $data1[1] ]);
    //             //     }

    //              $matchingLocations = [];
    //              $counter =  0;

    //             foreach ($positionsData['data'] as $position) {
    //                 if (isset($position['lat']) && isset($position['lng']) && isset($position['date'])) {
    //                     $lat = $position['lat'];
    //                     $lng = $position['lng'];
    //                     $date = $position['date'];

    //                 //  $date = Carbon::parse($position['date'])->format('Y-m-d H:i:s');

    //                     // Retrieve all records with the given latitude
    //                     $check = ConstructionLocation::where('latitude', $lat)->get();

    //                    if (count($check) > 1) {

    //                         $counter++;

    //                         foreach ($check as $location) {

    //                             $matchingLocations[] = [
    //                                 'construction_site_id' => $location->construction_site_id,
    //                                 'latitude' => $location->latitude,
    //                                 'longitude' => $location->langitude,
    //                                 'date' => $date
    //                             ];
    //                         }



    //                     }
    //                 }
    //             }

    //             // Remove duplicates from the $matchingLocations array
    //             $matchingLocations = array_unique($matchingLocations, SORT_REGULAR);


    //           foreach ($matchingLocations as $matchingLocation) {
    //             ConstructionMaterial::updateOrCreate(
    //                 [
    //                     'construction_site_id' => $matchingLocation['construction_site_id'],
    //                     'material_list_id' => 295,
    //                 ],
    //                 [
    //                     'updated_at' => $matchingLocation['date'],
    //                 ],
    //                 [
    //                     'timestamps' => false,
    //                 ]
    //             );
    //         }

    //                 // return response()->json(['message' => 'Data fetched successfully']);
    //             } else {
    //                 // Handle the error when fetching positions
    //                 // return response()->json(['error' => 'Error fetching positions'], $positionsResponse->getStatusCode());
    //             }
    //         } else {
    //             // Handle the error when logging in
    //             // return response()->json(['error' => 'Error logging in'], $loginResponse->getStatusCode());
    //         }
    //     } catch (\Exception $e) {
    //         // dd($e);
    //         // Handle other exceptions
    //         // return response()->json(['error' => 'An unexpected error occurred'], 500);
    //     }
    // }



    public function CommetApi()
    {
        $ConstructionSites = ConstructionSite::where('pin_location', '!=', null)->pluck('id');
        
        dd($ConstructionSites);
        

        try {
            // $this->saveNewCantiereCoordinates();
            // Step 1: Login and obtain the authentication token
            $client = new Client();
            $loginResponse = $client->post('https://almecdiag.net:9891/login', [
                'json' => [
                    'email' => 'greengengroupsrl@gmail.com',
                    'password' => 'Greengengroup!',
                ],
            ]);

            // Check if login was successful
            if ($loginResponse->getStatusCode() === 200) {
                $authToken = json_decode($loginResponse->getBody(), true)['token'];

                $startDate = Carbon::now()->subHours(24 * 30); // Current date and time
                $endDate = Carbon::now(); // Subtract 500 hours

                // Format dates in ISO8601 format
                $dtFrom = $startDate->format('Y-m-d\TH:i:s.v\Z');
                $dtTo = $endDate->format('Y-m-d\TH:i:s.v\Z');


                $positionsResponse = $client->get('https://almecdiag.net:9891/export/positions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $authToken,
                    ],
                    'query' => [
                        'c_machine' => '862493059018931',
                        'dt_from' => $dtFrom,
                        'dt_to' => $dtTo,
                    ],
                ]);

                // Check if fetching positions was successful

                if ($positionsResponse->getStatusCode() === 200) {
                    $positionsData = json_decode($positionsResponse->getBody(), true);

                    //     $catiereFilePath = public_path('assets/apicomet/cantieri.txt');
                    //     // Read the contents of the catiere.txt file
                    //     $catiereData = File::get($catiereFilePath);
                    //     // Split the file contents into an array of lines
                    //     $catiereLines = explode("\n", $catiereData);

                    //         // dd($catiereLines);

                    //     // Iterate over each line in catiere.txt
                    //     foreach ($catiereLines as $catiereLine) {


                    //         if (empty($catiereLine)) {
                    //             continue;
                    //         }

                    //          $data =   explode(", ", $catiereLine);
                    //          $data1 = array_map('trim', $data);

                    //         ConstructionLocation::updateOrCreate( ['construction_site_id' => $data1[2]],['latitude' => $data1[0], 'langitude' => $data1[1] ]);
                    //     }

                    function haversine($lat1, $lon1, $lat2, $lon2) {
                     
                        $R = 6371000; // Earth radius in meters
                        $dlat = deg2rad($lat2 - $lat1);
                        $dlon = deg2rad($lon2 - $lon1);
                        $a = sin($dlat / 2) * sin($dlat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlon / 2) * sin($dlon / 2);
                        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                        $distance = $R * $c;
                    
                        return $distance;
                    }

                    $matchingLocations = [];
                    $counter =  0;
                    $range = 50;

                    //  $savedLat =    40.8825278;
                    //  $savedLng =  17.1720556;
            
                  
                    // dd($positionsData['data']);
                    foreach ($positionsData['data'] as $position) {
                        if (isset($position['lat']) && isset($position['lng']) && isset($position['date'])) {
                            $lat = $position['lat'];
                            $lng = $position['lng'];
                            $date = $position['date'];

                            $ConstructionLocations  = ConstructionLocation::get();
                        foreach($ConstructionLocations as $ConstructionLocation){
                            $savedLat =  $ConstructionLocation->latitude;
                            $savedLng =  $ConstructionLocation->savedLng;

                            $distance = haversine($savedLat, $savedLng, $position['lat'], $position['lng']);
                         
                            if ($distance <= $range) {
                                if ($distance >= 1000) {
                                    $distanceInKm = $distance / 1000;
                                    echo 'Vehicle is within the ' . number_format($distanceInKm, 2) . ' kilometers range of the saved location.';
                                } else {
                                    echo 'Vehicle is within the ' . number_format($distance, 2) . ' meters range of the saved location on' .  $date . '</br>';
                                }
                                // You can adjust the number_format precision as needed.
                            }

                        }
                          
                       
                            // $check = ConstructionLocation::where('latitude', $lat)->get();


                        //   if (count($check) > 1) {
                        //         $counter++;

                        //         foreach ($check as $location) {

                        //             $matchingLocations[] = [
                        //                 'construction_site_id' => $location->construction_site_id,
                        //                 'latitude' => $location->latitude,
                        //                 'longitude' => $location->langitude,
                        //                 'date' => $date
                        //             ];
                        //         }
                        //     }
                        }
                    }
                  
                    
            
                    // foreach ($positionsData['data'] as $position) {
                    //     $lat = $position['lat'];
                    //     $lng = $position['lng'];
                    //     $date = $position['date'];
    
                    //     if (isset($position['lat']) && isset($position['lng'])) {
                    //         $distance = haversine($savedLat, $savedLng, $position['lat'], $position['lng']);
                         
                    //         if ($distance <= $range) {
                    //             dd($position['lat'], $position['lng']);
                    //             $found = true;
                    //             break; // Exit the loop once the vehicle is found within the range
                    //         }
                    //     }
                    // }
                    
                    // if ($found) {
                    //     dd('Vehicle is within the ' . $range . ' meter range of the saved location.');
                    // } else {
                    //     dd('Vehicle is not within the ' . $range . ' meter range of the saved location.');
                    // }

                    // Remove duplicates from the $matchingLocations array
                    $matchingLocations = array_unique($matchingLocations, SORT_REGULAR);

                    // dd($matchingLocations);
                    // foreach ($matchingLocations as $matchingLocation) {
                    //     ConstructionMaterial::updateOrCreate(
                    //         [
                    //             'construction_site_id' => $matchingLocation['construction_site_id'],
                    //             'material_list_id' => 295,
                    //         ],
                    //         [
                    //             'updated_at' => $matchingLocation['date'],
                    //         ],
                    //         [
                    //             'timestamps' => false,
                    //         ]
                    //     );
                    // }

                    // return response()->json(['message' => 'Data fetched successfully']);
                } else {
                    // Handle the error when fetching positions
                    // return response()->json(['error' => 'Error fetching positions'], $positionsResponse->getStatusCode());
                }
            } else {
                // Handle the error when logging in
                // return response()->json(['error' => 'Error logging in'], $loginResponse->getStatusCode());
            }
        } catch (\Exception $e) {
            // dd($e);
            // Handle other exceptions
            // return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    public function db()
    {
        // Get the name of the current database 
        $currentDatabaseName = DB::connection()->getDatabaseName();

        // Specify the file path for the SQL dump
        $dumpFilePath = "backup/{$currentDatabaseName}_backup.sql";

        // Get all table names in the database
        $tables = DB::select('SHOW TABLES');

        // Initialize an empty string to store the SQL dump
        $sqlDump = '';

        foreach ($tables as $table) {
            $tableName = reset($table);

            // Get the CREATE TABLE statement for each table
            $createTableStatement = DB::select("SHOW CREATE TABLE $tableName")[0]->{'Create Table'};

            // Get the data for each table
            $tableData = DB::table($tableName)->get()->toArray();

            // Generate INSERT statements for each row in the table
            foreach ($tableData as $row) {
                $columns = implode('`, `', array_keys((array) $row));
                $values = implode("', '", array_values((array) $row));
                $sqlDump .= "INSERT INTO `$tableName` (`$columns`) VALUES ('$values');\n";
            }

            $sqlDump .= "\n\n";
        }

        // Save the SQL dump to a file
        file_put_contents(storage_path("app/{$dumpFilePath}"), $createTableStatement . ";\n\n" . $sqlDump);

        // Download the SQL file
        return Response::download(storage_path("app/{$dumpFilePath}"));
    }

    public function down()
    {

        $cliente = systemStatus::first();

        $cliente->status  == 1 ? $cliente->status = 0 : $cliente->status = 1;
        $cliente->update();

        return  $cliente->status == 1 ? 'greengen Application has been activated successfully!' : 'greengen Application has been Deactivated successfully!';
    }
}

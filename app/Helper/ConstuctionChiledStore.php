<?php

namespace App\Helper;

use App\Models\PrNotDoc;

use App\Models\ReliefDoc;
use App\Models\ConstructionSite;
use App\Http\Controllers\Controller;
use App\Models\TypeOfDedectionFiles;

class ConstuctionChiledStore extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    /**
     * add new record
     */
    public  function add_data_into_chiled($var, $type_of_deduction_values)
    {
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
            }   else {
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
            }else{
             
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
            if($type_of_deduction_values != null){

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
        if($type_of_deduction_values != null){

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
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'Preanalisi Fatturato';
                    }
                  
                } elseif ($StatusPreAnalysis == 'Revenue') {

                    if($constructionSite->latest_status == null){
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
                  
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'Tecnico Da Assegnare';
                    }
                    $constructionSite->StatusTechnician->updateOrCreate($id, ['state' => 'Not Assigned']);
                }elseif( $StatusTechnician == null){
                    // $latestStatus = 'Tecnico Da Assegnare';
                    $constructionSite->StatusTechnician->updateOrCreate($id, ['state' => null]);
                }
                else {
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

                if ($StatusRelief == 'To assign' ) {
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'Saldo Enea In Attesa';
                    }
                    
                    $constructionSite->StatusRelief->updateOrCreate($id, ['state' => 'To assign']);
                }elseif( $StatusRelief == null) {
                    $constructionSite->StatusRelief->updateOrCreate($id, ['state' => null]);
                }
                
                elseif ($StatusRelief == 'Received') {
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
                if ($StatusLegge10 == 'Waiting' ) {
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'legge 10 in attesa';
                    }
                   
                    $constructionSite->StatusLegge10->updateOrCreate($id, ['state' => 'Waiting']);
                }elseif($StatusLegge10 == null){
                    $constructionSite->StatusLegge10->updateOrCreate($id, ['state' => null]);
                }
                 elseif ($StatusLegge10 == 'Completed') {
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
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'Computo In Attesa';
                    }



                
                    // $constructionSite->StatusComputation->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif($StatusComputation == null){
                    $constructionSite->StatusComputation->updateOrCreate($id, ['state' => null]);
                }
                elseif ($StatusComputation == 'Completed') {
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
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'Notifica Preliminare In Attesa';
                    }
                
                    // $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => 'Waiting']);
                } elseif($StatusPrNoti == null){
                    $constructionSite->StatusPrNoti->updateOrCreate($id, ['state' => null]);
                }
                
                elseif ($StatusPrNoti == 'Completed') {
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
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'Pratica Protocollata In Attesa';
                    }
                  
                    $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => 'Waiting']);
                }elseif($statusRegPrac == null){
                    $constructionSite->statusRegPrac->updateOrCreate($id, ['state' => null]);
                }
                
                elseif ($statusRegPrac == 'Completed') {
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
                      if($constructionSite->latest_status == null){
                        $latestStatus = 'Lavori Iniziati In Attesa';
                    }
                  
                    $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => 'Waiting']);

                }elseif($StatusWorkStarted == null){
                    $constructionSite->StatusWorkStarted->updateOrCreate($id, ['state' => null]);
                }
                 elseif ($StatusWorkStarted == 'Completed') {
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

                if ($StatusSAL == 'Waiting' ) {
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'SAL in attesa';
                    }
                   
                    $constructionSite->StatusSAL->updateOrCreate($id, ['state' => 'Waiting']);
                }elseif($StatusSAL == null){
                    $constructionSite->StatusSAL->updateOrCreate($id, ['state' => null]);
                }
                
                
                elseif ($StatusSAL == 'Completed') {
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
                    if($constructionSite->latest_status == null){
                        $latestStatus = 'Saldo Enea In Attesa';
                    }

                    
                    $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => 'Waiting']);
                }elseif($statusEneaBalance == null){
                    $constructionSite->statusEneaBalance->updateOrCreate($id, ['state' => null]);
                }
                
                elseif ($statusEneaBalance == 'Completed') {
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

                } elseif($statusWorkClose == null){
                    $status = 1;
                    $constructionSite->statusWorkClose->updateOrCreate($id, ['state' => null]);
                }
                
                elseif ($statusWorkClose == 'Completed') {
                    $latestStatus = 'Chiusa';
                    $status = 0;
                }

                break;
            default:
                // code to execute when $value does not match any of the options
                break;
        }
     if(isset($latestStatus)){
        $construction_latest_status = [
            'latest_status' => $latestStatus,
            'status' => $status
        ];
     }else{
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
            'status_workclose'=>[
                'Completed' => 'Chiuso',
                // 'Waiting' => 'Chiuso In Attesa',
            ]
            
        ];


        


        // Check if the current status for the page exists in the mapping
        if($page ==  'pre_analysis'){
            $StatusTechnician = $constructionSite->StatusTechnician->state;
            if($StatusTechnician ==  null || $StatusTechnician == 'Not Assigned'){
                $constructionSite->StatusTechnician->update(['state' => 'Not Assigned']);
            }
            $StatusRelief = $constructionSite->StatusRelief->state;
            if($StatusRelief ==  null || $StatusRelief == 'To assign'){
                $constructionSite->StatusRelief->update(['state' => 'To assign']);
            }
            $StatusLegge10 = $constructionSite->StatusLegge10->state;
            if($StatusLegge10 ==  null || $StatusLegge10 == 'Waiting'){
                $constructionSite->StatusLegge10->update(['state' => 'Waiting']);
            }
            $StatusComputation = $constructionSite->StatusComputation->state;
            if($StatusComputation ==  null || $StatusComputation == 'Waiting'){
                $constructionSite->StatusComputation->update(['state' => 'Waiting']);
            }
            $StatusPrNoti = $constructionSite->StatusPrNoti->state;
            if($StatusPrNoti ==  null || $StatusPrNoti == 'Waiting'){
                $constructionSite->StatusPrNoti->update(['state' => 'Waiting']);
            }
            $statusRegPrac = $constructionSite->statusRegPrac->state;
            if($statusRegPrac ==  null || $statusRegPrac == 'Waiting'){
                $constructionSite->statusRegPrac->update(['state' => 'Waiting']);
            }
            $StatusWorkStarted = $constructionSite->StatusWorkStarted->state;
            if($StatusWorkStarted ==  null || $StatusWorkStarted == 'Waiting'){
                $constructionSite->StatusWorkStarted->update(['state' => 'Waiting']);
            }
            $StatusSAL = $constructionSite->StatusSAL->state;
            if($StatusSAL ==  null || $StatusSAL == 'Waiting'){
                $constructionSite->StatusSAL->update(['state' => 'Waiting']);
            }
            $statusEneaBalance = $constructionSite->statusEneaBalance->state;
            

            $statusWorkClose = $constructionSite->statusWorkClose->state;
        }elseif($page ==  'status_technisan'){

        }elseif($page ==  'status_relief'){

        }elseif($page == 'status_leg10'){

        }elseif($page == 'status_computation' ){

        }elseif($page == 'status_prenoti'){
            
        }elseif($page == 'status_eneablnc'){
            
        }elseif($page == 'status_workstarted'){

        }elseif($page ==  'status_sal'){

        }elseif($page ==  'status_eneablnc'){

        }elseif($page ==  'status_workclose'){

        }

        $statusWorkClose == 'Waiting' || $statusWorkClose == null ? $status = '1': $status = '0'; 

        $constructionSite->update(['status' => $status]);

        if (isset($statusMapping[$page][$currentStatus])) {

            $latestStatus = $statusMapping[$page][$currentStatus];
            $constructionSite->update(['latest_status' => $latestStatus]);



        } else {
            $latestStatus = 'Unknown Status';

             echo 'Latest status: ' . $latestStatus;
        }

       
    }
}

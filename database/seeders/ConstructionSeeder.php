<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConstructionSite;
use App\Models\DocumentAndContact;
use App\Models\PropertyData;
use App\Models\ConstructionSiteSetting;
// seeder
use App\Models\StatusPreAnalysis;
use App\Models\StatusTechnician;
// files model
use App\Models\StatusRelief;
use App\Models\ReliefDoc;
//
use App\Models\StatusLeg10;
use App\Models\Leg10File;
//
use App\Models\StatusComputation;
//
use App\Models\StatusWorkStarted;
//
use App\Models\StatusPrNoti;
use App\Models\PrNotDoc;
use App\Models\statusRegPrac;
use App\Models\RegPracDoc;
use App\Models\StatusSAL;
use App\Models\StatusWorkClose;
use App\Models\StatusEneaBalance;

class ConstructionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    //
    $construction = new ConstructionSite();
    $construction->name = "asim";
    $construction->surename = 'bajwa';
    $construction->date_of_birth = '2001-12-12';
    $construction->town_of_birth = 'kpk';
    $construction->province = 'KPK';
    $construction->residence_street = 'noor road 5th street';
    $construction->residence_house_number = '21';
    $construction->residence_postal_code = '46000';
    $construction->residence_common = 'This is my common';
    $construction->residence_province = 'islamabad';
    $construction->save();
    // Document And Contact;
    $doc_contact = new DocumentAndContact();
    $doc_contact->construction_site_id = $construction->id;
    $doc_contact->document_number = 'A1B2C3';
    $doc_contact->issued_by = '2022-11-12';
    $doc_contact->release_date = '2022-12-12';
    $doc_contact->expiration_date = '2023-01-01';
    $doc_contact->fiscal_document_number = '1234';
    $doc_contact->vat_number = '051801007';
    $doc_contact->contact_email = 'mariasdsd@yahoo.it';
    $doc_contact->contact_number = '0804962405';
    $doc_contact->alt_refrence_name = 'sstjf';
    $doc_contact->alt_contact_number = '0804962401';
    $doc_contact->save();
    // property data
    $property_data = new PropertyData();
    $property_data->construction_site_id = $construction->id;
    $property_data->property_street = 'MICHELE LATORRE STREET';
    $property_data->property_house_number = '12';
    $property_data->property_postal_code = '123';
    $property_data->property_common = 'Castellana Grotte common';
    $property_data->property_province = 'PNK';
    $property_data->cadastral_section = null;
    $property_data->cadastral_dati = null;
    $property_data->cadastral_particle = null;
    $property_data->sub_ordinate = null;
    $property_data->pod_code = null;
    $property_data->status = '0';
    $property_data->save();
    $cons_site_setting = new ConstructionSiteSetting();
    $cons_site_setting->construction_site_id = $construction->id;
    $cons_site_setting->type_of_property = 'Condominio';
    $cons_site_setting->type_of_construction = 'External';
    $cons_site_setting->type_of_deduction = '110,50,90,Fotovoltaico';
    $cons_site_setting->save();
    // get date from construction table(timespam)
    $created_at = explode(' ', $construction->creadet_at);
    //
    // status StatusPreAnalysis
    $statusPreAnalays = new StatusPreAnalysis();
    $statusPreAnalays->construction_site_id = $construction->id;
    $statusPreAnalays->save();
    // status technisan
    $statusTechnisan = new StatusTechnician();
    $statusTechnisan->construction_site_id = $construction->id;
    $statusTechnisan->save();
    // StatusRelief
    $StatusRelief = new StatusRelief();
    $StatusRelief->construction_site_id = $construction->id;
    $StatusRelief->save();
    // ReliefDoc
    $ReliefDoc1 = new ReliefDoc();
    $ReliefDoc1->status_relief_id = $StatusRelief->id;
    $ReliefDoc1->folder_name = 'Altri Documenti Rilevanti';
    $ReliefDoc1->state = 'Vouto';
    $ReliefDoc1->save();

    $ReliefDoc2 = new ReliefDoc();
    $ReliefDoc2->status_relief_id = $StatusRelief->id;
    $ReliefDoc2->folder_name = 'Documenti Libretto Impianti';
    $ReliefDoc2->state = 'Vouto';
    $ReliefDoc2->save();

    $ReliefDoc3 = new ReliefDoc();
    $ReliefDoc3->status_relief_id = $StatusRelief->id;
    $ReliefDoc3->folder_name = 'Documenti Rilievo';
    $ReliefDoc3->state = 'Vouto';
    $ReliefDoc3->save();

    $ReliefDoc4 = new ReliefDoc();
    $ReliefDoc4->status_relief_id = $StatusRelief->id;
    $ReliefDoc4->folder_name = 'Documenti Clienti';
    $ReliefDoc4->state = 'Vouto';
    $ReliefDoc4->save();

    $ReliefDoc5 = new ReliefDoc();
    $ReliefDoc5->status_relief_id = $StatusRelief->id;
    $ReliefDoc5->folder_name = 'Documenti Co-intestatari';
    $ReliefDoc5->state = 'Vouto';
    $ReliefDoc5->save();

    $ReliefDoc6 = new ReliefDoc();
    $ReliefDoc6->status_relief_id = $StatusRelief->id;
    $ReliefDoc6->folder_name = 'Documenti Fine Lavori';
    $ReliefDoc6->state = 'Vouto';
    $ReliefDoc6->save();

    $ReliefDoc7 = new ReliefDoc();
    $ReliefDoc7->status_relief_id = $StatusRelief->id;
    $ReliefDoc7->folder_name = 'Schemi Impianti';
    $ReliefDoc7->state = 'Vouto';
    $ReliefDoc7->save();


    //leag10
    $StatusLeg10 = new StatusLeg10();
    $StatusLeg10->construction_site_id = $construction->id;
    $StatusLeg10->save();
    // Leg10File file
    $Leg10File1 = new Leg10File();
    $Leg10File1->status_leg10_id = $StatusLeg10->id;
    $Leg10File1->construction_site_id = $construction->id;
    $Leg10File1->file_name = 'Ape regionale';
    $Leg10File1->state = 'MANCANTE';
    $Leg10File1->save();

    $Leg10File2 = new Leg10File();
    $Leg10File2->status_leg10_id = $StatusLeg10->id;
    $Leg10File2->construction_site_id = $construction->id;
    $Leg10File2->file_name = 'Legge 10';
    $Leg10File2->state = 'MANCANTE';
    $Leg10File2->save();

    $Leg10File3 = new Leg10File();
    $Leg10File3->status_leg10_id = $StatusLeg10->id;
    $Leg10File3->construction_site_id = $construction->id;
    $Leg10File3->file_name = 'Ricevuta Ape Regione';
    $Leg10File3->state = 'MANCANTE';
    $Leg10File3->save();
    // StatusComputation
    $StatusComputation1 = new StatusComputation();
    $StatusComputation1->construction_site_id = $construction->id;
    $StatusComputation1->save();
    // StatusPrNoti
    $StatusPrNoti = new StatusPrNoti();
    $StatusPrNoti->construction_site_id = $construction->id;
    $StatusPrNoti->save();
    // PrNotDoc
    $PrNotDoc1 = new PrNotDoc();
    $PrNotDoc1->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc1->folder_name = 'Altri Documenti Interni';
    $PrNotDoc1->state = '0';
    $PrNotDoc1->save();

    $PrNotDoc2 = new PrNotDoc();
    $PrNotDoc2->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc2->folder_name = 'Conferme d ordine';
    $PrNotDoc2->state = '0';
    $PrNotDoc2->save();

    $PrNotDoc3 = new PrNotDoc();
    $PrNotDoc3->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc3->folder_name = 'Contratto di subappalto impresa';
    $PrNotDoc3->state = '0';
    $PrNotDoc3->save();

    $PrNotDoc4 = new PrNotDoc();
    $PrNotDoc4->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc4->folder_name = 'DICO';
    $PrNotDoc4->state = '0';
    $PrNotDoc4->save();

    $PrNotDoc5 = new PrNotDoc();
    $PrNotDoc5->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc5->folder_name = 'Documenti 50';
    $PrNotDoc5->state = '0';
    $PrNotDoc5->save();

    $PrNotDoc6 = new PrNotDoc();
    $PrNotDoc6->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc6->folder_name = 'Documenti Clienti';
    $PrNotDoc6->state = '0';
    $PrNotDoc6->save();

    $PrNotDoc7 = new PrNotDoc();
    $PrNotDoc7->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc7->folder_name = 'Documenti Co-intestatari';
    $PrNotDoc7->state = '0';
    $PrNotDoc7->save();

    $PrNotDoc8 = new PrNotDoc();
    $PrNotDoc8->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc8->folder_name = 'Documenti Conformità';
    $PrNotDoc8->state = '0';
    $PrNotDoc8->save();

    $PrNotDoc9 = new PrNotDoc();
    $PrNotDoc9->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc9->folder_name = 'Documenti Fine Lavori';
    $PrNotDoc9->state = '0';
    $PrNotDoc9->save();

    $PrNotDoc10 = new PrNotDoc();
    $PrNotDoc10->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc10->folder_name = 'Documenti Libretto Impianti';
    $PrNotDoc10->state = '0';
    $PrNotDoc10->save();

    $PrNotDoc11 = new PrNotDoc();
    $PrNotDoc11->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc11->folder_name = 'Documenti Rilevanti';
    $PrNotDoc11->state = '0';
    $PrNotDoc11->save();

    $PrNotDoc12 = new PrNotDoc();
    $PrNotDoc12->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc12->folder_name = 'Documenti Rilievo';
    $PrNotDoc12->state = '0';
    $PrNotDoc12->save();

    $PrNotDoc13 = new PrNotDoc();
    $PrNotDoc13->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc13->folder_name = 'Documentazione Varia';
    $PrNotDoc13->state = '0';
    $PrNotDoc13->save();

    $PrNotDoc14 = new PrNotDoc();
    $PrNotDoc14->status_pr_noti_id = $StatusPrNoti->id;
    $PrNotDoc14->folder_name = 'Schemi Impianti';
    $PrNotDoc14->state = '0';
    $PrNotDoc14->save();
    //
    $statusRegPrac = new statusRegPrac();
    $statusRegPrac->construction_site_id = $construction->id;
    $statusRegPrac->save();
    // ReliefDoc
    $RegPracDoc1 = new RegPracDoc();
    $RegPracDoc1->status_reg_prac_id = $statusRegPrac->id;
    $RegPracDoc1->construction_site_id = $construction->id;
    $RegPracDoc1->file_name = 'Cila protocollata 50-65-90';
    $RegPracDoc1->state = 'MANCANTE';
    $RegPracDoc1->save();

    $RegPracDoc2 = new RegPracDoc();
    $RegPracDoc2->status_reg_prac_id = $statusRegPrac->id;
    $RegPracDoc2->construction_site_id = $construction->id;
    $RegPracDoc2->file_name = 'Cilas protocollata 110';
    $RegPracDoc2->state = 'MANCANTE';
    $RegPracDoc2->save();

    $RegPracDoc3 = new RegPracDoc();
    $RegPracDoc3->status_reg_prac_id = $statusRegPrac->id;
    $RegPracDoc3->construction_site_id = $construction->id;
    $RegPracDoc3->file_name = 'Delega Notifica Preliminare';
    $RegPracDoc3->state = 'MANCANTE';
    $RegPracDoc3->save();

    $RegPracDoc4 = new RegPracDoc();
    $RegPracDoc4->status_reg_prac_id = $statusRegPrac->id;
    $RegPracDoc4->construction_site_id = $construction->id;
    $RegPracDoc4->file_name = 'Notifica Preliminare';
    $RegPracDoc4->state = 'MANCANTE';
    $RegPracDoc4->save();

    $RegPracDoc5 = new RegPracDoc();
    $RegPracDoc5->status_reg_prac_id = $statusRegPrac->id;
    $RegPracDoc5->construction_site_id = $construction->id;
    $RegPracDoc5->file_name = 'Planimetria Catastale';
    $RegPracDoc5->state = 'MANCANTE';
    $RegPracDoc5->save();

    $RegPracDoc6 = new RegPracDoc();
    $RegPracDoc6->status_reg_prac_id = $statusRegPrac->id;
    $RegPracDoc6->construction_site_id = $construction->id;
    $RegPracDoc6->file_name = 'Protocollo cila 50-65-90';
    $RegPracDoc6->state = 'MANCANTE';
    $RegPracDoc6->save();

    $RegPracDoc7 = new RegPracDoc();
    $RegPracDoc7->status_reg_prac_id = $statusRegPrac->id;
    $RegPracDoc7->construction_site_id = $construction->id;
    $RegPracDoc7->file_name = 'Protocollo cilas 110';
    $RegPracDoc7->state = 'MANCANTE';
    $RegPracDoc7->save();

    // StatusWorkStarted
    $StatusWorkStarted = new StatusWorkStarted();
    $StatusWorkStarted->construction_site_id = $construction->id;
    $StatusWorkStarted->save();
    // StatusSAL
    $StatusSAL = new StatusSAL();
    $StatusSAL->construction_site_id = $construction->id;
    $StatusSAL->save();
    // StatusEneaBalance
    $StatusEneaBalance = new StatusEneaBalance();
    $StatusEneaBalance->construction_site_id = $construction->id;
    $StatusEneaBalance->save();
    // StatusWorkClose
    $StatusWorkClose = new StatusWorkClose();
    $StatusWorkClose->construction_site_id = $construction->id;
    $StatusWorkClose->save();
  }
}

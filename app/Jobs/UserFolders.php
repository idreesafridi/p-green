<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
// models

use App\Models\User;
use App\Models\StatusRelief;
use App\Models\ConstructionSite;
use App\Models\ReliefDoc;
use App\Models\RelDocFile;
use App\Models\RelifDocFileSub1;
use App\Models\StatusLeg10;
use App\Models\Leg10File;
use App\Models\StatusPrNoti;
use App\Models\PrNotDoc;
use App\Models\PrNotDocFile;
use App\Models\TypeOfDedectionSub1;
use App\Models\TypeOfDedectionSub2;
use App\Models\TypeOfDedectionFiles;
use App\Models\TypeOfDedectionFiles2;
use App\Models\statusRegPrac;
use App\Models\RegPracDoc;

use Illuminate\Support\Facades\Storage;

class UserFolders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $folders = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($folders)
    {
        //
        $this->folders = $folders;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $old_image = null;
        // check folders or file RelifDoc
        $updated_id = $this->folders[2];
        $updatedOn =   $this->folders[12];



        $filename = ucwords(strtolower($this->folders[4])); // capitalize first letter of each word
        // $filename = $this->folders[4]; // capitalize first letter of each word
        $foldername = ucwords(strtolower($this->folders[5])); // capitalize first letter of each word
        $allow = $this->folders[10];
        // check oflder
        $updated_data  = User::where('id', $updated_id)->first();
        $contsruction  = ConstructionSite::where('oldid', $this->folders[0])->first();
        if ($contsruction) {
            $id = $contsruction->id;
            $checkrelifdocfolder = $this->checkfolder($id, $updated_data, $foldername, $filename, $allow, $updatedOn, $contsruction);
            // dd($checkrelifdocfolder);
        } else {
            echo "construction site settings data failed for: " . $updated_id, $foldername, $filename . "<br/>";
        }
    }
    // for folder
    public function checkfolder($id, $updated_data, $foldername, $filename, $allow, $updatedOn, $contsruction)
    {

        // $folderdata = $model::where('construction_site_id',$id)->where('folder_name',$foldername)->first();

        if (str_contains($allow, "bin")) {
            $status = 0;
            $binPathReplace = str_replace(['bin/', ' '], ['', '%20'], $allow);
            $livePath = 'https://greengen.crisaloid.com/bin/' . $binPathReplace;
            // $directory = 'public/construction-assets/'.$id.'/bin/'.$foldername;

            $path = $foldername;
            // dd("bin",$foldername,$directory);
        } else if (str_contains($allow, "documents")) {
            $status = 1;
            $binPathReplace = str_replace(['documents/', ' '], ['', '%20'], $allow);
            $livePath = 'https://greengen.crisaloid.com/documents/' . $binPathReplace;
            // $directory = 'public/construction-assets/'.$id.'/'.$foldername.'/';
            $path = $foldername . '/';
            // dd("document",$foldername,$directory);
        }

        $arr = [
            'construction_site_id' => $id == null ? null : $id,
            'folder'  => $foldername,
            'status' => $status,
            'path' => $path,
        ];



        $data =   $this->checkfile($id, $updated_data, $foldername, $filename, $status, $livePath, $updatedOn, $contsruction, $arr);
        // ConstructionSite::create($arr);



        return True;
    }
    // for file
    public function checkfile($id, $updated_data, $foldername, $filename, $status, $livePath, $updatedOn, $contsruction, $arr)
    {
        // try {
            // code for relief Documents
            $reldoc_folders_first = $contsruction->ReliefDocument->filter(function ($reliefDocument) use ($foldername) {
                return strnatcasecmp($reliefDocument->folder_name, $foldername) === 0;
            });
            // dd($reldoc_folders_first);
            if ($reldoc_folders_first->isNotEmpty()) {

                $reldoc_folders_first_data = $reldoc_folders_first->first();

                $filteredFiles = $reldoc_folders_first_data->ReliefDocumentFile->filter(function ($relDocFile) use ($filename) {
                    return strnatcasecmp($relDocFile->file_name, $filename) === 0;
                });

                if ($filteredFiles->isNotEmpty() && $status == 1) {
                    $filteredFiles_data = $filteredFiles->first();
                    $sent_path  = 'Releif document files/' . $reldoc_folders_first_data->folder_name;
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    $filteredFiles_data->construction_site_id = $id;
                    $filteredFiles_data->state =  $status;
                    $filteredFiles_data->relief_doc_id = $reldoc_folders_first_data->id;
                    $filteredFiles_data->file_name  = $file;
                    $filteredFiles_data->file_path =  $path;
                    $filteredFiles_data->updated_on = $updatedOn;
                    $filteredFiles_data->updated_by = $updated_data->name;
                    $filteredFiles_data->update();
                    return $sent_path;
                } else {
                    $sent_path  = 'Releif document files/' . $reldoc_folders_first_data->folder_name;
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    $reldocfile = new RelDocFile();
                    $reldocfile->construction_site_id = $id;
                    $reldocfile->relief_doc_id = $reldoc_folders_first_data->id;
                    $reldocfile->state =  $status;
                    $reldocfile->file_name = $file;
                    $reldocfile->file_path = $path;
                    $reldocfile->updated_on = $updatedOn;
                    $reldocfile->updated_by = $updated_data->name;
                    $reldocfile->save();
                    return $sent_path;
                }
            }



            $PrNotDoc_folders = $contsruction->PrNotDoc->filter(function ($PrNotDocs) use ($foldername) {
                return strnatcasecmp($PrNotDocs->folder_name, $foldername) === 0;
            });
            // dd($reldoc_folders_first);
            if ($PrNotDoc_folders->isNotEmpty()) {

                $PrNotDocfoldersResult = $PrNotDoc_folders->first();

                $TypeOfDedectionSub1files = $PrNotDocfoldersResult->PrNotDocFile->filter(function ($PrNotDocFile) use ($filename) {
                    return strnatcasecmp($PrNotDocFile->file_name, $filename) === 0;
                });

                $TypeOfDedectionSub1 = $PrNotDocfoldersResult->TypeOfDedectionSub1->filter(function ($TypeOfDedectionSub1R) use ($filename) {
                    return strnatcasecmp($TypeOfDedectionSub1R->file_name, $filename) === 0;
                });

                if ($TypeOfDedectionSub1->isNotEmpty() && $status == 1) {
                 
                    $TypeOfDedectionSub1final =  $TypeOfDedectionSub1->first();
                    $sent_path =  $PrNotDocfoldersResult->folder_name;
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    $TypeOfDedectionSub1final->construction_site_id = $id;
                    $TypeOfDedectionSub1final->state =  $status;
                    $TypeOfDedectionSub1final->pr_not_doc_id = $PrNotDocfoldersResult->id;
                    $TypeOfDedectionSub1final->file_name  = $file;
                    $TypeOfDedectionSub1final->file_path = $path;
                    $TypeOfDedectionSub1final->updated_on = $updatedOn;
                    $TypeOfDedectionSub1final->updated_by = $updated_data->name;
                    $TypeOfDedectionSub1final->update();
                    return $sent_path;
                }

                if ($TypeOfDedectionSub1files->isNotEmpty()) {
                    if ($TypeOfDedectionSub1files->isNotEmpty()) {
                        $getprnt2 = $TypeOfDedectionSub1files->first();
                        if($status ==  0)
                        {
                            $newfile = new PrNotDocFile();
                            $sent_path =  'PreNotification Document files/' . $PrNotDocfoldersResult->folder_name;
                            $file = $filename;
                            $filename = $filename . '.pdf';
                            $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                            $newfile->construction_site_id = $id;
                            $newfile->state =  $status;
                            $newfile->pr_not_doc_id = $PrNotDocfoldersResult->id;
                            $newfile->file_name  = $file;
                            $newfile->file_path = $path;
                            $newfile->updated_on = $updatedOn;
                            $newfile->updated_by = $updated_data->name;
                            $newfile->save();
                            return $sent_path;
                        }else{
                            $sent_path =  'PreNotification Document files/' . $PrNotDocfoldersResult->folder_name;
                            $file = $filename;
                            $filename = $filename . '.pdf';
                            $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                            $getprnt2->construction_site_id = $id;
                            $getprnt2->state =  $status;
                            $getprnt2->pr_not_doc_id = $PrNotDocfoldersResult->id;
                            $getprnt2->file_name  = $file;
                            $getprnt2->file_path = $path;
                            $getprnt2->updated_on = $updatedOn;
                            $getprnt2->updated_by = $updated_data->name;
                            $getprnt2->update();
                            return $sent_path;
                        }
                      
                    } else {
                        $newfile = new PrNotDocFile();
                        $sent_path =  'PreNotification Document files/' . $PrNotDocfoldersResult->folder_name;
                        $file = $filename;
                        $filename = $filename . '.pdf';
                        $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                        $newfile->construction_site_id = $id;
                        $newfile->state =  $status;
                        $newfile->pr_not_doc_id = $PrNotDocfoldersResult->id;
                        $newfile->file_name  = $file;
                        $newfile->file_path = $path;
                        $newfile->updated_on = $updatedOn;
                        $newfile->updated_by = $updated_data->name;
                        $newfile->save();
                        return $sent_path;
                    }
                } else {
                    $newfile = new PrNotDocFile();
                    $sent_path =  'PreNotification Document files/' . $PrNotDocfoldersResult->folder_name;
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    $newfile->construction_site_id = $id;
                    $newfile->state =  $status;
                    $newfile->pr_not_doc_id = $PrNotDocfoldersResult->id;
                    $newfile->file_name  = $file;
                    $newfile->file_path = $path;
                    $newfile->updated_on = $updatedOn;
                    $newfile->updated_by = $updated_data->name;
                    $newfile->save();
                    return $sent_path;
                }
            }


            $TypeOfDedectionSub1 = $contsruction->TypeOfDedectionSub1->where('state', 1)->filter(function ($TypeOfDedectionSub1s) use ($foldername) {
                return strnatcasecmp($TypeOfDedectionSub1s->folder_name, $foldername) === 0;
            });

            if ($TypeOfDedectionSub1->isNotEmpty()) {

                $TypeOfDedectionSub1Result = $TypeOfDedectionSub1->first();


                $TypeOfDedectionSub1ResultFiles = $TypeOfDedectionSub1Result->TypeOfDedectionSub2->filter(function ($TypeOfDedectionSub2) use ($filename) {
                    return strnatcasecmp($TypeOfDedectionSub2->file_name, $filename) === 0;
                });


                if ($TypeOfDedectionSub1ResultFiles->isNotEmpty() && $status == 1) {
                    $TypeOfDedectionSub1ResultFilesresult = $TypeOfDedectionSub1ResultFiles->first();

                    $get_prnt1 = PrNotDoc::where('id', $TypeOfDedectionSub1Result->pr_not_doc_id)->first();
                    $sent_path = $get_prnt1->folder_name . '/' . $TypeOfDedectionSub1Result->folder_name;
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    // dd("7",$path);
                    $TypeOfDedectionSub1ResultFilesresult->construction_site_id = $id;
                    $TypeOfDedectionSub1ResultFilesresult->state =  $status;
                    $TypeOfDedectionSub1ResultFilesresult->type_of_dedection_sub1_id = $TypeOfDedectionSub1Result->id;
                    $TypeOfDedectionSub1ResultFilesresult->file_path = $path;
                    $TypeOfDedectionSub1ResultFilesresult->file_name = $file;
                    $TypeOfDedectionSub1ResultFilesresult->updated_on = $updatedOn;
                    $TypeOfDedectionSub1ResultFilesresult->updated_by = $updated_data->name;
                    $TypeOfDedectionSub1ResultFilesresult->update();
                    return $sent_path;
                } else {
                    $TypeOfDedectionSub2 = new TypeOfDedectionSub2();
                    $get_prnt1 = PrNotDoc::where('id', $TypeOfDedectionSub1Result->pr_not_doc_id)->first();
                    $sent_path = $get_prnt1->folder_name . '/' . $TypeOfDedectionSub1Result->folder_name;
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    // dd("7",$path);
                    $TypeOfDedectionSub2->construction_site_id = $id;
                    $TypeOfDedectionSub2->state =  $status;
                    $TypeOfDedectionSub2->type_of_dedection_sub1_id = $TypeOfDedectionSub1Result->id;
                    $TypeOfDedectionSub2->file_path = $path;
                    $TypeOfDedectionSub2->file_name = $file;
                    $TypeOfDedectionSub2->updated_on = $updatedOn;
                    $TypeOfDedectionSub2->updated_by = $updated_data->name;
                    $TypeOfDedectionSub2->save();
                    return $sent_path;
                }
            }


            $TypeOfDedectionSub2 = $contsruction->TypeOfDedectionSub2->filter(function ($TypeOfDedectionSub2s) use ($foldername) {
                return strnatcasecmp($TypeOfDedectionSub2s->folder_name, $foldername) === 0;
            });

            if ($TypeOfDedectionSub2->isNotEmpty()) {

                $TypeOfDedectionSub2Result = $TypeOfDedectionSub2->first();


                $TypeOfDedectionSub2ResultFiles = $TypeOfDedectionSub2Result->TypeOfDedectionFiles->filter(function ($TypeOfDedectionFiles) use ($filename) {
                    return strnatcasecmp($TypeOfDedectionFiles->file_name, $filename) === 0;
                });

                if ($TypeOfDedectionSub2ResultFiles->isNotEmpty() && $status == 1) {
                    $TypeOfDedectionSub2Final = $TypeOfDedectionSub2ResultFiles->first();

                    $TypeOfDedectionSub1 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('id', $TypeOfDedectionSub2Result->type_of_dedection_sub1_id)->first();
                    $get_prnt1 = PrNotDoc::where('id', $TypeOfDedectionSub1->pr_not_doc_id)->first();
                    $sent_path = $get_prnt1->folder_name . '/' . $TypeOfDedectionSub1->folder_name . '/' . $TypeOfDedectionSub2Result->folder_name;
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    $TypeOfDedectionSub2Final->construction_site_id = $id;
                    $TypeOfDedectionSub2Final->state =  $status;
                    $TypeOfDedectionSub2Final->type_of_dedection_sub2_id = $TypeOfDedectionSub2Result->id;
                    $TypeOfDedectionSub2Final->file_path =  $path;
                    $TypeOfDedectionSub2Final->file_name =  $file;
                    $TypeOfDedectionSub2Final->updated_on = $updatedOn;
                    $TypeOfDedectionSub2Final->updated_by = $updated_data->name;
                    $TypeOfDedectionSub2Final->update();
                    return $sent_path;
                } else {
                    $TypeOfDedectionFiles =  new TypeOfDedectionFiles();
                    $TypeOfDedectionSub1 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('id', $TypeOfDedectionSub2Result->type_of_dedection_sub1_id)->first();
                    $get_prnt1 = PrNotDoc::where('id', $TypeOfDedectionSub1->pr_not_doc_id)->first();
                    $sent_path = $get_prnt1->folder_name . '/' . $TypeOfDedectionSub1->folder_name . '/' . $TypeOfDedectionSub2Result->folder_name;
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    $TypeOfDedectionFiles->construction_site_id = $id;
                    $TypeOfDedectionFiles->state =  $status;
                    $TypeOfDedectionFiles->type_of_dedection_sub2_id = $TypeOfDedectionSub2Result->id;
                    $TypeOfDedectionFiles->file_path =  $path;
                    $TypeOfDedectionFiles->file_name =  $file;
                    $TypeOfDedectionFiles->updated_on = $updatedOn;
                    $TypeOfDedectionFiles->updated_by = $updated_data->name;
                    $TypeOfDedectionFiles->save();
                    return $sent_path;
                }
            }

            $TypeOfDedectionFiles = $contsruction->TypeOfDedectionFiles->filter(function ($TypeOfDedectionFilesR) use ($foldername) {
                return strnatcasecmp($TypeOfDedectionFilesR->folder_name, $foldername) === 0;
            });

            if ($TypeOfDedectionFiles->isNotEmpty()) {

                $TypeOfDedectionFilesResult = $TypeOfDedectionFiles->first();


                $TypeOfDedectionFiles2 = $TypeOfDedectionFilesResult->TypeOfDedectionFiles2->filter(function ($TypeOfDedectionFiles2R) use ($filename) {
                    return strnatcasecmp($TypeOfDedectionFiles2R->file_name, $filename) === 0;
                });

                if ($TypeOfDedectionFiles2->isNotEmpty() && $status == 1) {

                    $TypeOfDedectionFiles2Final = $TypeOfDedectionFiles2->first();
                    $TypeOfDedectionSub2 = TypeOfDedectionSub2::where('construction_site_id', $id)->where('id', $TypeOfDedectionFilesResult->type_of_dedection_sub2_id)->first();
                    $TypeOfDedectionSub1 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('id', $TypeOfDedectionSub2->type_of_dedection_sub1_id)->first();
                    $get_prnt1 = PrNotDoc::where('id', $TypeOfDedectionSub1->pr_not_doc_id)->first();
                    $sent_path = $get_prnt1->folder_name . '/' . $TypeOfDedectionSub1->folder_name . '/' . $TypeOfDedectionSub2->folder_name . '/' . $TypeOfDedectionFilesResult->folder_name;

                    // $newfile = new TypeOfDedectionFiles2();
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    // dd("8",$path);
                    $TypeOfDedectionFiles2Final->construction_site_id = $id;
                    $TypeOfDedectionFiles2Final->state =  $status;
                    $TypeOfDedectionFiles2Final->type_of_dedection_file_id = $TypeOfDedectionFilesResult->id;
                    $TypeOfDedectionFiles2Final->file_path =  $path;
                    $TypeOfDedectionFiles2Final->file_name =  $file;
                    $TypeOfDedectionFiles2Final->updated_on = $updatedOn;
                    $TypeOfDedectionFiles2Final->updated_by = $updated_data->name;
                    $TypeOfDedectionFiles2Final->update();
                    return $sent_path;
                } else {

                    $TypeOfDedectionFiles2 =  new TypeOfDedectionFiles2();
                    $TypeOfDedectionSub2 = TypeOfDedectionSub2::where('construction_site_id', $id)->where('id', $TypeOfDedectionFilesResult->type_of_dedection_sub2_id)->first();
                    $TypeOfDedectionSub1 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('id', $TypeOfDedectionSub2->type_of_dedection_sub1_id)->first();
                    $get_prnt1 = PrNotDoc::where('id', $TypeOfDedectionSub1->pr_not_doc_id)->first();
                    $sent_path = $get_prnt1->folder_name . '/' . $TypeOfDedectionSub1->folder_name . '/' . $TypeOfDedectionSub2->folder_name . '/' . $TypeOfDedectionFilesResult->folder_name;

                    // $newfile = new TypeOfDedectionFiles2();
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    // dd("8",$path);
                    $TypeOfDedectionFiles2->construction_site_id = $id;
                    $TypeOfDedectionFiles2->state =  $status;
                    $TypeOfDedectionFiles2->type_of_dedection_file_id = $TypeOfDedectionFilesResult->id;
                    $TypeOfDedectionFiles2->file_path =  $path;
                    $TypeOfDedectionFiles2->file_name =  $file;
                    $TypeOfDedectionFiles2->updated_on = $updatedOn;
                    $TypeOfDedectionFiles2->updated_by = $updated_data->name;
                    $TypeOfDedectionFiles2->save();
                }
            }

            $RegPracDocResult = $contsruction->RegPracDoc->filter(function ($RegPracDoc) use ($filename) {
                return strnatcasecmp($RegPracDoc->file_name, $filename) === 0;
            });
            if($RegPracDocResult->isNotEmpty()){
            if ($RegPracDocResult->isNotEmpty() && $status == 1) {
                $RegPracDocResultFinal = $RegPracDocResult->first();
                $statusregprac = statusRegPrac::where('construction_site_id', $id)->where('id', $RegPracDocResultFinal->status_reg_prac_id)->first();
                $sent_path =  'Reg Practice File/';
                $file = $filename;
                $filename = $filename . '.pdf';
                $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                // dd("9",$path);
                $RegPracDocResultFinal->construction_site_id = $id;
                $RegPracDocResultFinal->state =  $status;
                $RegPracDocResultFinal->status_reg_prac_id = $statusregprac->id;
                $RegPracDocResultFinal->file_path = $path;
                $RegPracDocResultFinal->file_name =  $file;
                // $data = RegPracDoc::create($arr);
                $RegPracDocResultFinal->updated_on = $updatedOn;
                $RegPracDocResultFinal->updated_by = $updated_data->name;
                $RegPracDocResultFinal->update();
                return $sent_path;
            }else{
                $RegPracDocResultFinal = new RegPracDoc();
                $statusregprac = statusRegPrac::where('construction_site_id', $id)->where('id', $RegPracDocResultFinal->status_reg_prac_id)->first();
                $sent_path =  'Reg Practice File/';
                $file = $filename;
                $filename = $filename . '.pdf';
                $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                // dd("9",$path);
                $RegPracDocResultFinal->construction_site_id = $id;
                $RegPracDocResultFinal->state =  $status;
                $RegPracDocResultFinal->status_reg_prac_id = $statusregprac->id;
                $RegPracDocResultFinal->file_path = $path;
                $RegPracDocResultFinal->file_name =  $file;
                // $data = RegPracDoc::create($arr);
                $RegPracDocResultFinal->updated_on = $updatedOn;
                $RegPracDocResultFinal->updated_by = $updated_data->name;
                $RegPracDocResultFinal->save();
                return $sent_path;
            }
        }

            $Legge10DocumentFile = $contsruction->Legge10DocumentFile->filter(function ($Legge10DocumentFileR) use ($filename) {
                return strnatcasecmp($Legge10DocumentFileR->file_name, $filename) === 0;
            });

            if ($Legge10DocumentFile->isNotEmpty()) {
                if ($Legge10DocumentFile->isNotEmpty() && $status ==  1) { 
                $Legge10DocumentFileFinel  = $Legge10DocumentFile->first();
                $stlegg10 = StatusLeg10::where('construction_site_id', $id)->where('id', $Legge10DocumentFileFinel->status_leg10_id)->first();
                $sent_path =  'Legge10 File/';
                $file = $filename;
                $filename = $filename . '.pdf';
                $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                $Legge10DocumentFileFinel->construction_site_id = $id;
                $Legge10DocumentFileFinel->state =  $status;
                $Legge10DocumentFileFinel->status_leg10_id = $stlegg10->id;
                $Legge10DocumentFileFinel->file_name  = $file;
                $Legge10DocumentFileFinel->file_path =  $path;
                $Legge10DocumentFileFinel->updated_on = $updatedOn;
                $Legge10DocumentFileFinel->updated_by = $updated_data->name;
                $Legge10DocumentFileFinel->update();
                return $sent_path;
                }else{
                    $Legge10DocumentFileFinel  =  new Leg10File();
                    $stlegg10 = StatusLeg10::where('construction_site_id', $id)->where('id', $Legge10DocumentFileFinel->status_leg10_id)->first();
                    $sent_path =  'Legge10 File/';
                    $file = $filename;
                    $filename = $filename . '.pdf';
                    $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
                    $Legge10DocumentFileFinel->construction_site_id = $id;
                    $Legge10DocumentFileFinel->state =  $status;
                    $Legge10DocumentFileFinel->status_leg10_id = $stlegg10->id;
                    $Legge10DocumentFileFinel->file_name  = $file;
                    $Legge10DocumentFileFinel->file_path =  $path;
                    $Legge10DocumentFileFinel->updated_on = $updatedOn;
                    $Legge10DocumentFileFinel->updated_by = $updated_data->name;
                    $Legge10DocumentFileFinel->save();
                    return $sent_path;
                }
            }
        // } catch (\Exception $e) {

        //     dd($e->getMessage());
        // }
        $path = $id . '/Missing-folder/' . $foldername;
        $directory = 'public/construction-assets/' . $path;
        // create directory
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        try {
            $old_data = Http::get($livePath)->body();
        } catch (\Throwable $th) {
            // dd($th);
        }
        // return path

        if ($old_data != null) {
            // Upload the files to the new server
            // $new_filepath = $directory . '/' . basename($livePath);
            $new_filepath = $directory . '/' . basename(urldecode($livePath));
            file_put_contents($new_filepath, $old_data);
        }
        return $path;
        // }


        // $RelDocFile_folders = ['Scheda Dati Ante Opera', 'Scheda Interventi'];

        // $RelDocFile_files = ['DWG', 'Atto Di Provenienza', 'Carta D Identità Co-intestatario', 'Codice Fiscale', 'Partita Iva',
        //     'Visura Catastale', 'Consenso Lavori', 'Verbale Consegna Chiavetta', 'Verbale Consegna Lavori', 'Sopralluogo Fine Lavori',
        //     'Comunicazione Fine Lavori', 'Protocollo Comunicazione Fine Lavori', 'Bolletta Luce', 'Carta D identità co-intestatario intestatario bollette',
        //     'Codici Accesso Portale', 'Contratto GSE', 'Estensione Garanzia FTV', 'Estratto Di Mappa', 'Iban', 'Mandato RAP', 'Sezione H', 'ANTONACCI IMMACOLATA relazione fotovoltaico', 'Catasto Impianti',
        //     'Libretto Impianti Ante', 'Libretto Impianti Post', 'Ape regionale', 'Legge 10', 'Legge 10 SALDO', 'Ricevuta Ape Regione'
        // ];
        // $RelDocFile_files_confusing = ["Carta D'Identità", 'Carta D Identità Intestatario Bollette'];

        // $Leg10File_files = ['Ape Regionale', 'Legge 10', 'Ricevuta Ape Regione'];
        //clear
        // $PrNotDoc_folders = ['Altri Documenti Interni', 'Conferme D Ordine', 'Contratto Di Subappalto Impresa', 'Dico', 'Documenti 50', 'Documenti 65', 'Documenti 90', 'Documenti 110', 'Documenti Assistenza', 'Documenti Conformità', 'Documenti Rilevanti', 'Documenti Sicurezza', 'Documentazione Varia'];
        // Conferme d'ordine
        // comented afer second sheet
        // $PrNotDocFile_files = [
        //     'DICO Impianto Elettrico', 'DICO Impianto Fotovoltaico', 'DICO Impianto Idrico-Fognante',
        //     'DICO Impianto Termico', 'Conformità Infissi', 'Autodich Assenza Irregolarità', 'Cessione Bonus', 'Contratto Cessione Credito',
        //     'Delega Accesso Atti', 'Delega Accesso Planimetrie', 'Delega Commercialista Guarino', 'Inc Prof Ape Regione', 'Inc Prof Zac',
        //     'Iva Agevolata', 'Opzione Cessione Greengen-Guarino', 'Privacy', 'Procura', 'Scan Antimafia',
        //     'Delega Commercialista Rizzi 50', 'Delega Commercialista Rizzi 65', 'Delega Commercialista Rizzi 90', 'Delega Commercialista Rizzi 110',
        // ];
        $PrNotDocFile_files = [
            'DICO Impianto Elettrico', 'DICO Impianto Fotovoltaico', 'DICO Impianto idrico-fognante',
            'DICO Impianto Termico', 'Conformità Infissi', 'Autodich Assenza Irregolarità', 'Cessione Bonus', 'Contratto Cessione Credito',
            'Delega Accesso Atti', 'Delega Accesso Planimetrie', 'Delega Commercialista Guarino', 'Inc Prof Ape Regione', 'Inc Prof Zac',
            'Iva Agevolata', 'Opzione Cessione Greengen-Guarino', 'Privacy', 'Procura', 'Scan Antimafia',
            'Inc Prof Zacc', 'Delega Commercialista Rizzi 110',
        ];

        $TypeOfDedectionSub1_folders = [
            'Aggiornamenti Notifiche', 'Psc E Allegati', 'Pos Impresa', 'Documenti SAL 50', 'Documenti SALDO 50',
            'Fattura SAL 50', 'Documenti SAL 65', 'Documenti SALDO 65', 'Fattura SAL 65', 'Documenti SAL 90', 'Fattura SAL 90',
            'Documenti SALDO 90', 'Documenti Sal 110', 'Documenti 2SAL', 'Dichiarazione 30', 'Documenti Saldo 110', 'Ricevuta Di Invio Ade Sal 110',
            'Ricevuta Di Invio Ade Saldo 110', 'Visto Di Conformita Firmato Sal 110', 'Visto Di Conformita Firmato Saldo 110',
            'Documenti SALDO 50',
        ];

        // $TypeOfDedectionSub1_folders = [
        //    'Aggiornamenti Notifiche', 'PSC E Allegati', 'Pos Impresa', 'Documenti SAL 50', 'Documenti SALDO 50',
        //    'Fattura SAL 50', 
        //         'Documenti SAL 65', 'Documenti SALDO 65',  'Documenti SAL 90',
        //      'Documenti SAL 110', 'Documenti 2SAL', 'Dichiarazione 30', 'Documenti SALDO 110',
        //     'Documenti SALDO 50',
        // ];

        $TypeOfDedectionSub1_files = [
            // 'Contract 50', 'Contract 65', 'Contract 90', 'Contratto 110',
            'Contratto 110',
            'Contratto Di Mandato Senza Rappresentanza', 'Contratto 90', 'Contratto 65',
        ];

        $TypeOfDedectionSub2_folders = [
            'Asseverazione SAL 50', 'Bonifico E Ritenute SAL 50', 'Computo 50', 'Fattura 50',
            'Scan Visto di Conformita Sal 50', 'Asseverazione SALDO 50', 'Bonifico E Ritenute SALDO 50',
            'Computo SALDO 50', 'Fattura SALDO 50', 'Visto Di Conformita SALDO 50',
            'Asseverazione SAL 65', 'Bonifico E Ritenute SAL 65', 'Computo 65', 'Fattura 65', 'Scan Visto Di Conformita Sal 65',
            'Asseverazione SALDO 65', 'Bonifico E Ritenute SALDO 65', 'Computo SALDO 65', 'Fattura SALDO 65', 'Fattura SAL 65',
            'Visto Di Conformita SALDO 65', 'Asseverazione SAL 90', 'Computo 90', 'Fattura 90',
            'Visto di Conformita Sal 90', 'Asseverazione SALDO 90',
            'Fattura Sal 90', 'Computo SALDO 90',
            'Fattura SALDO 90', 'Visto Di Conformita SALDO 90',

            'Asseverazione Sal 110', 'Visto Di Conformita Sal 110',

            'Computo Sal 110', 'Fattura Sal 110', 'Asseverazione 2SAL',
            'Visto Di Conformita 2SAL', 'COMPUTO 2SAL',
            'Fattura 2SAL', 'Asseverazione Saldo 110',
            'Computo Saldo 110', 'Fattura Saldo 110',
            'Visto Di Conformita SALDO 110',
        ];
        $TypeOfDedectionSub2_folders_confusing = [
            'Visto Di Conformita\' SALDO 50', 'Visto Di Conformita\' SALDO 110',
            'Visto Di Conformita\' SALDO 65', 'Visto di Conformita\' SALDO 90'
        ];
        // Visto di Conformita' SALDO 50
        // Visto Di Conformita' SALDO 110
        // Visto di Conformita' SALDO 65
        // Visto di Conformita' SALDO 90

        $TypeOfDedectionSub2_files = [
            'Dichiarazione Sostitutiva Atto Di Notorietà 50', 'Opzione Cessione 50', 'Dichiarazione Enea SALDO 50', 'Contratto 50',
            'Dichiarazione Sostitutiva Atto Di Notorietà 65', 'Opzione Cessione 65', 'Dichiarazione Enea SALDO 65', 'Dichiarazione Sostitutiva Atto Di Notorietà 90', 'Opzione Cessione 90',
            'Computo Metrico Firmato Impresa', 'DURC di congruità', 'Computo Metrico Firmato Cliente',  'Dichiarazione Sostitutiva Atto Di Notorietà 110', 'Opzione Cessione 110', 'Computo Metrico Firmato Impresa 2SAL',
            'Computo Metrico Firmato Cliente 2SAL', 'Dichiarazione Sostitutiva Atto Di Notorietà 2SAL', 'Opzione Cessione 2SAL'
        ];

        // $TypeOfDedectionFiles_folders = [
        //     'Scan Visto Di Conformita Sal 110',
        //     'Visto Di Conformita Firmato Sal 110',
        //     'Ricevuta Di Invio Ade Sal 110',

        //     "Scan Visto Di Conformita Sal 90",
        //     'Visto Di Conformita Firmato Sal 90',
        //     'Ricevuta Di Invio Ade Sal 90',

        //     'Scan Visto di Conformita Sal 50',
        //     'Visto Di Conformita Firmato Sal 50',
        //     'Ricevuta Di Invio Ade Sal 50',

        //     'Scan Visto di Conformità Sal 65',
        //     'Visto Di Conformita Firmato Sal 65',
        //     'Ricevuta Di Invio Ade Sal 65',


        // ];


        // $RegPracDoc = [
        //     'Cila Protocollata 50-65-90', 'Cilas Protocollata 110', 'Delega Notifica Preliminare',
        //     'Notifica Preliminare', 'Planimetria Catastale', 'Protocollo cila 50-65-90', 'Protocollo Cilas 110', 'Inc Prof Zacc', 'Iva Agevoltata',
        // ];

        // $RegPracDoc = [
        //     'Cila Protocollata 50-65-90', 'Cilas Protocollata 110', 'Delega Notifica Preliminare',
        //     'Notifica Preliminare', 'Planimetria Catastale', 'Protocollo Cila 50-65-90', 'Protocollo Cilas 110', 'Iva Agevoltata',
        // ];
        //
        // if (in_array($foldername, $reldoc_folders)) {

        //     if (in_array($filename, $RelDocFile_files)) {
        //         $reldocfile = RelDocFile::where('construction_site_id', $id)->where('file_name', $filename)->first();
        //         if ($reldocfile) {
        //             // $prnt1 = $reldocfile->ReliefDocument()->where('id', $reldocfile->relief_doc_id)->first();
        //             $prnt1 = ReliefDoc::where('id', $reldocfile->relief_doc_id)->first();
        //             $sent_path  = 'Releif document files/' . $prnt1->folder_name;
        //             $file = $filename;
        //             $filename = $filename . '.pdf';
        //             $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //             $reldocfile->construction_site_id = $id;
        //             $reldocfile->state =  $status;
        //             $reldocfile->relief_doc_id = $prnt1->id;
        //             $reldocfile->file_name  = $file;
        //             $reldocfile->file_path =  $path;
        //             $reldocfile->updated_on = $updatedOn;
        //             $reldocfile->updated_by = $updated_data->name;
        //             $reldocfile->update();
        //             return $sent_path;
        //         }
        //     } else if (in_array($filename, $RelDocFile_files_confusing)) {
        //         if ($filename = 'carta d identità') {
        //             $filename = 'Carta D identità co-intestatario';
        //         } else if ($filename = 'Carta D Identità Intestatario Bollette') {
        //             $filename = 'Carta D Identità Intestatario Bollette';
        //         }
        //         $reldocfile = RelDocFile::where('construction_site_id', $id)->where('file_name', $filename)->first();
        //         if ($reldocfile) {
        //             // $prnt1 = $reldocfile->ReliefDocument()::where('id', $reldocfile->relief_doc_id)->first();
        //             $prnt1 = ReliefDoc::where('id', $reldocfile->relief_doc_id)->first();
        //             $sent_path  = 'Releif document files/' . $prnt1->folder_name;
        //             // dd("3",$path);
        //             $file = $filename;
        //             $filename = $filename . '.pdf';
        //             $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //             $reldocfile->construction_site_id = $id;
        //             $reldocfile->state =  $status;
        //             $reldocfile->relief_doc_id = $prnt1->id;
        //             $reldocfile->file_name  = $file;
        //             $reldocfile->file_path =  $path;
        //             $reldocfile->updated_on = $updatedOn;
        //             $reldocfile->updated_by = $updated_data->name;
        //             $reldocfile->update();
        //             return $sent_path;
        //         }
        //     } else {
        //         $prnt1 = ReliefDoc::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //         if ($prnt1) {
        //             $sent_path  = 'Releif document files/' . $prnt1->folder_name;
        //             $file = $filename;
        //             $filename = $filename . '.pdf';
        //             $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //             $reldocfile = new RelDocFile();
        //             $reldocfile->construction_site_id = $id;
        //             $reldocfile->relief_doc_id = $prnt1->id;
        //             $reldocfile->state =  $status;
        //             $reldocfile->file_name = $file;
        //             $reldocfile->file_path = $path;
        //             $reldocfile->updated_on = $updatedOn;
        //             $reldocfile->updated_by = $updated_data->name;
        //             $reldocfile->save();
        //             return $sent_path;
        //         }
        //     }
        // }
        // if (in_array($foldername, $RelDocFile_folders)) {
        //     $RelDocFile = RelDocFile::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //     if ($RelDocFile) {
        //         $RelDoc = ReliefDoc::where('construction_site_id', $id)->where('id', $RelDocFile->relief_doc_id)->first();
        //         $RelifDocFileSub1 = new RelifDocFileSub1();
        //         $sent_path  = 'Releif document files/' . $RelDoc->folder_name . '/' . $RelDocFile->folder_name;
        //         $file = $filename;
        //         $filename = $filename . '.pdf';
        //         $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);

        //         $RelifDocFileSub1->construction_site_id = $id;
        //         $RelifDocFileSub1->state =  $status;
        //         $RelifDocFileSub1->rel_doc_file_id = $RelDocFile->id;
        //         $RelifDocFileSub1->file_name = $file;
        //         $RelifDocFileSub1->file_path = $path;
        //         $RelifDocFileSub1->updated_on = $updatedOn;
        //         $RelifDocFileSub1->updated_by = $updated_data->name;
        //         $RelifDocFileSub1->save();
        //         return $sent_path;
        //     }
        // }
        // if (in_array($filename, $Leg10File_files)) {
        //     // dd("4","leg10",$filename);
        //     $leg10file = Leg10File::where('construction_site_id', $id)->where('file_name', $filename)->first();
        //     if ($leg10file) {
        //         $stlegg10 = StatusLeg10::where('construction_site_id', $id)->where('id', $leg10file->status_leg10_id)->first();
        //         $sent_path =  'Legge10 File/';
        //         $file = $filename;
        //         $filename = $filename . '.pdf';
        //         $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);

        //         $leg10file->construction_site_id = $id;
        //         $leg10file->state =  $status;
        //         $leg10file->status_leg10_id = $stlegg10->id;
        //         $leg10file->file_name  = $file;
        //         $leg10file->file_path =  $path;
        //         $leg10file->updated_on = $updatedOn;
        //         $leg10file->updated_by = $updated_data->name;
        //         $leg10file->update();
        //         return $sent_path;
        //     }
        // }
        // if (in_array($foldername, $PrNotDoc_folders)) {
        //     if (in_array($filename, $TypeOfDedectionSub1_files)) {
        //         if (in_array($filename, $TypeOfDedectionSub1_files)) {
        //             // dd("call if type of deduction 1 304,",$filename);
        //             $getprnt2 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('file_name', $filename)->first();
        //             if ($getprnt2) {
        //                 $getprnt1 = PrNotDoc::where('id', $getprnt2->pr_not_doc_id)->where('folder_name', $foldername)->first();
        //                 $sent_path =  $getprnt1->folder_name;
        //                 $file = $filename;
        //                 $filename = $filename . '.pdf';
        //                 $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //                 $getprnt2->construction_site_id = $id;
        //                 $getprnt2->state =  $status;
        //                 $getprnt2->pr_not_doc_id = $getprnt1->id;
        //                 $getprnt2->file_name  = $file;
        //                 $getprnt2->file_path = $path;
        //                 $getprnt2->updated_on = $updatedOn;
        //                 $getprnt2->updated_by = $updated_data->name;
        //                 $getprnt2->update();
        //                 return $sent_path;
        //             }
        //         } else {
        //             $getprnt1 = PrNotDoc::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //             if ($getprnt1) {
        //                 $newfile = new TypeOfDedectionSub1();
        //                 $sent_path =  $getprnt1->folder_name;
        //                 $file = $filename;
        //                 $filename = $filename . '.pdf';
        //                 $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //                 $newfile->construction_site_id = $id;
        //                 $newfile->state =  $status;
        //                 $newfile->pr_not_doc_id = $getprnt1->id;
        //                 $newfile->file_name  = $file;
        //                 $newfile->file_path = $path;
        //                 $newfile->updated_on = $updatedOn;
        //                 $newfile->updated_by = $updated_data->name;
        //                 $newfile->save();
        //                 return $sent_path;
        //             }
        //         }
        //     }
        //     if (in_array($filename, $PrNotDocFile_files)) {
        //         if (in_array($filename, $PrNotDocFile_files)) {
        //             $PrNotDocfile = PrNotDocFile::where('construction_site_id', $id)->where('file_name', $filename)->first();
        //             if ($PrNotDocfile) {
        //                 $get_prnt1 = PrNotDoc::where('construction_site_id', $id)->where('id', $PrNotDocfile->pr_not_doc_id)->first();
        //                 $sent_path =  'PreNotification Document files/' . $get_prnt1->folder_name;
        //                 $file = $filename;
        //                 $filename = $filename . '.pdf';
        //                 $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //                 // dd("5",$path);
        //                 $PrNotDocfile->construction_site_id = $id;
        //                 $PrNotDocfile->state =  $status;
        //                 $PrNotDocfile->pr_not_doc_id = $get_prnt1->id;
        //                 $PrNotDocfile->file_name  = $file;
        //                 $PrNotDocfile->file_path = $path;
        //                 $PrNotDocfile->updated_on = $updatedOn;
        //                 $PrNotDocfile->updated_by = $updated_data->name;
        //                 $PrNotDocfile->update();
        //                 return $sent_path;
        //             }
        //         } else {

        //             $prnotdoce = PrNotDoc::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //             if ($prnotdoce) {
        //                 $newfile = new PrNotDocFile();
        //                 $sent_path =  'PreNotification Document files/' . $prnotdoce->folder_name;
        //                 $file = $filename;
        //                 $filename = $filename . '.pdf';
        //                 $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);

        //                 $newfile->construction_site_id = $id;
        //                 $newfile->state =  $status;
        //                 $newfile->pr_not_doc_id = $prnotdoce->id;
        //                 $newfile->file_name  = $file;
        //                 $newfile->file_path = $path;
        //                 $newfile->updated_on = $updatedOn;
        //                 $newfile->updated_by = $updated_data->name;
        //                 $newfile->save();
        //                 return $sent_path;
        //             }
        //         }
        //     }

        //     if (!in_array($filename, $TypeOfDedectionSub1_files) && !in_array($filename, $PrNotDocFile_files)) {
        //         $prnotdoce = PrNotDoc::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //         if ($prnotdoce) {
        //             $newfile = new PrNotDocFile();
        //             $sent_path =  'PreNotification Document files/' . $prnotdoce->folder_name;
        //             $file = $filename;
        //             $filename = $filename . '.pdf';
        //             $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);

        //             $newfile->construction_site_id = $id;
        //             $newfile->state =  $status;
        //             $newfile->pr_not_doc_id = $prnotdoce->id;
        //             $newfile->file_name  = $file;
        //             $newfile->file_path = $path;
        //             $newfile->updated_on = $updatedOn;
        //             $newfile->updated_by = $updated_data->name;
        //             $newfile->save();
        //             return $sent_path;
        //         }
        //     }
        // }
        // if (in_array($foldername, $TypeOfDedectionSub1_folders)) {

        //     if (in_array($filename, $TypeOfDedectionSub1_files)) {
        //         $typeofdeduc2 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('file_name', $filename)->first();
        //         if ($typeofdeduc2) {
        //             // $getprnt2 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('id', $typeofdeduc2->type_of_dedection_sub1_id)->first();
        //             $getprnt1 = PrNotDoc::where('construction_site_id', $id)->where('id', $typeofdeduc2->pr_not_doc_id)->first();
        //             $sent_path =  $getprnt1->folder_name;
        //             $file = $filename;
        //             $filename = $filename . '.pdf';
        //             $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);

        //             $typeofdeduc2->construction_site_id = $id;
        //             $typeofdeduc2->state =  $status;
        //             $typeofdeduc2->pr_not_doc_id = $getprnt1->id;
        //             $typeofdeduc2->file_path = $path;
        //             $typeofdeduc2->file_name = $file;
        //             $typeofdeduc2->updated_on = $updatedOn;
        //             $typeofdeduc2->updated_by = $updated_data->name;
        //             $typeofdeduc2->update();
        //             return $sent_path;
        //         }
        //     } else {
        //         $getprnt2 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //         if ($getprnt2) {
        //             $getprnt1 = PrNotDoc::where('id', $getprnt2->pr_not_doc_id)->first();
        //             $sent_path =  $getprnt1->folder_name;
        //             $file = $filename;
        //             $filename = $filename . '.pdf';
        //             $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);

        //             $newfile = new TypeOfDedectionSub2();
        //             $newfile->construction_site_id = $id;
        //             $newfile->state =  $status;
        //             $newfile->type_of_dedection_sub1_id = $getprnt2->id;
        //             $newfile->file_path = $path;
        //             $newfile->file_name = $file;
        //             $newfile->updated_on = $updatedOn;
        //             $newfile->updated_by = $updated_data->name;
        //             $newfile->save();
        //             return $sent_path;
        //         }
        //     }
        // }
        // if (in_array($foldername, $TypeOfDedectionSub2_folders)) {

        //     if (in_array($filename, $TypeOfDedectionSub2_files)) {
        //         $typeofdeducfile =  TypeOfDedectionSub2::where('construction_site_id', $id)->where('file_name', $filename)->first();
        //         if ($typeofdeducfile) {
        //             // $TypeOfDedectionSub2 = TypeOfDedectionSub2::where('construction_site_id', $id)->where('id', $typeofdeducfile->type_of_dedection_sub2_id)->first();
        //             $TypeOfDedectionSub1 = TypeOfDedectionSub1::where('id', $typeofdeducfile->type_of_dedection_sub1_id)->first();
        //             $get_prnt1 = PrNotDoc::where('id', $TypeOfDedectionSub1->pr_not_doc_id)->first();
        //             $sent_path = $get_prnt1->folder_name . '/' . $TypeOfDedectionSub1->folder_name;
        //             $file = $filename;
        //             $filename = $filename . '.pdf';
        //             $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //             // dd("7",$path);
        //             $typeofdeducfile->construction_site_id = $id;
        //             $typeofdeducfile->state =  $status;
        //             $typeofdeducfile->type_of_dedection_sub2_id = $typeofdeducfile->id;
        //             $typeofdeducfile->file_path = $path;
        //             $typeofdeducfile->file_name = $file;
        //             $typeofdeducfile->updated_on = $updatedOn;
        //             $typeofdeducfile->updated_by = $updated_data->name;
        //             $typeofdeducfile->update();
        //             return $sent_path;
        //         }
        //     }
        //     // else if (in_array($foldername, $TypeOfDedectionSub2_folders_confusing)) {

        //     //     if ('Visto di Conformita\' SALDO 50') {
        //     //         $foldername = 'Visto Di Conformita SALDO 50';
        //     //     } elseif ('Visto Di Conformita\'SALDO 110') {
        //     //         $foldername = 'Visto Di Conformita SALDO 110';
        //     //     } else if ('Visto di Conformita\' SALDO 65') {
        //     //         $foldername = 'Visto Di Conformita SALDO 65';
        //     //     } else if ('Visto di Conformita\' SALDO 90') {
        //     //         $foldername = 'Visto Di Conformita SALDO 90';
        //     //     }
        //     //     $typeofdeduc2 = TypeOfDedectionSub2::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //     //     if ($typeofdeduc2) {
        //     //         $get_prnt2 = TypeOfDedectionSub1::where('id', $typeofdeduc2->type_of_dedection_sub1_id)->first();
        //     //         $get_prnt1 = PrNotDoc::where('id', $get_prnt2->pr_not_doc_id)->first();
        //     //         $sent_path = $get_prnt1->folder_name . '/' . $get_prnt2->folder_name . '/' . $typeofdeduc2->folder_name;
        //     //         $newfile = new TypeOfDedectionFiles();
        //     //         $file = $filename;
        //     //         $filename = $filename . '.pdf';
        //     //         $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //     //         // dd("7",$path);
        //     //         $newfile->construction_site_id = $id;
        //     //         $newfile->state =  $status;

        //     //         $newfile->type_of_dedection_sub1_id = $get_prnt2->id;
        //     //         $newfile->file_path = $path;
        //     //         $newfile->file_name = $file;
        //     //         $newfile->updated_on = $updatedOn;
        //     //         $newfile->updated_by = $updated_data->name;
        //     //         $newfile->save();

        //     //         return $sent_path;
        //     //     }
        //     // } 
        //     else {
        //         $typeofdeduc2 = TypeOfDedectionSub2::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //         if ($typeofdeduc2) {
        //             $get_prnt2 = TypeOfDedectionSub1::where('id', $typeofdeduc2->type_of_dedection_sub1_id)->first();
        //             $get_prnt1 = PrNotDoc::where('id', $get_prnt2->pr_not_doc_id)->first();
        //             $sent_path = $get_prnt1->folder_name . '/' . $get_prnt2->folder_name . '/' . $typeofdeduc2->folder_name;
        //             $newfile = new TypeOfDedectionFiles();
        //             $file = $filename;
        //             $filename = $filename . '.pdf';
        //             $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //             // dd("7",$path);
        //             $newfile->construction_site_id = $id;
        //             $newfile->state =  $status;
        //             $newfile->type_of_dedection_sub2_id = $typeofdeduc2->id;
        //             $newfile->file_path = $path;
        //             $newfile->file_name = $file;
        //             $newfile->updated_on = $updatedOn;
        //             $newfile->updated_by = $updated_data->name;
        //             $newfile->save();
        //             return $sent_path;
        //         }
        //     }
        // }
        // if (in_array($foldername, $TypeOfDedectionFiles_folders)) {

        //     $TypeOfDedectionFiles = TypeOfDedectionFiles::where('construction_site_id', $id)->where('folder_name', $foldername)->first();
        //     if ($TypeOfDedectionFiles) {
        //         $TypeOfDedectionSub2 = TypeOfDedectionSub2::where('construction_site_id', $id)->where('id', $TypeOfDedectionFiles->type_of_dedection_sub2_id)->first();
        //         $TypeOfDedectionSub1 = TypeOfDedectionSub1::where('construction_site_id', $id)->where('id', $TypeOfDedectionSub2->type_of_dedection_sub1_id)->first();
        //         $get_prnt1 = PrNotDoc::where('id', $TypeOfDedectionSub1->pr_not_doc_id)->first();

        //         $sent_path = $get_prnt1->folder_name . '/' . $TypeOfDedectionSub1->folder_name . '/' . $TypeOfDedectionSub2->folder_name . '/' . $TypeOfDedectionFiles->folder_name;

        //         $newfile = new TypeOfDedectionFiles2();
        //         $file = $filename;
        //         $filename = $filename . '.pdf';
        //         $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //         // dd("8",$path);
        //         $newfile->construction_site_id = $id;
        //         $newfile->state =  $status;
        //         $newfile->type_of_dedection_file_id = $TypeOfDedectionFiles->id;
        //         $newfile->file_path =  $path;
        //         $newfile->file_name =  $file;
        //         $newfile->updated_on = $updatedOn;
        //         $newfile->updated_by = $updated_data->name;
        //         $newfile->save();
        //         return $sent_path;
        //     }
        // }
        // if (in_array($filename, $RegPracDoc)) {

        //     $regpracfile = RegPracDoc::where('construction_site_id', $id)->where('file_name', $filename)->first();
        //     if ($regpracfile) {
        //         $statusregprac = statusRegPrac::where('construction_site_id', $id)->where('id', $regpracfile->status_reg_prac_id)->first();
        //         $sent_path =  'Reg Practice File/';
        //         $file = $filename;
        //         $filename = $filename . '.pdf';

        //         $path = $this->upload_common_func($id, $filename, $status, $livePath, $sent_path, $file);
        //         // dd("9",$path);
        //         $regpracfile->construction_site_id = $id;
        //         $regpracfile->state =  $status;
        //         $regpracfile->status_reg_prac_id = $statusregprac->id;
        //         $regpracfile->file_path = $path;
        //         $regpracfile->file_name =  $file;
        //         // $data = RegPracDoc::create($arr);
        //         $regpracfile->updated_on = $updatedOn;
        //         $regpracfile->updated_by = $updated_data->name;
        //         $regpracfile->update();
        //         return $sent_path;
        //     }
        // } else {
        //     // $check_again = $this->checkfile($id, $updated_data, $foldername, $filename, $status, $livePath);
        //     // if ($check_again == null) {
        //     $path = $id . '/Missing-folder/' . $foldername;
        //     $directory = 'public/construction-assets/' . $path;
        //     // create directory
        //     if (!file_exists($directory)) {
        //         mkdir($directory, 0755, true);
        //     }

        //     try {
        //         $old_data = Http::get($livePath)->body();
        //     } catch (\Throwable $th) {
        //         // dd($th);
        //     }
        //     // return path

        //     if ($old_data != null) {
        //         // Upload the files to the new server
        //         // $new_filepath = $directory . '/' . basename($livePath);
        //         $new_filepath = $directory . '/' . basename(urldecode($livePath));
        //         file_put_contents($new_filepath, $old_data);
        //     }
        //     return $path;
        //     // }
        // }
    }
    // upload_common_func files
    public function upload_common_func($id, $filename, $status, $livePath, $sent_path, $file)
    {

        $constrct_id = $id;

        // move to directory

        if ($status == 1) {
            $path = $constrct_id . '/' . $sent_path . '/' . $filename;
            $directory = 'public/construction-assets/' . $constrct_id . '/' . $sent_path;
        } else {
            $path = $constrct_id . '/bin/' . $sent_path . '/' . $filename;
            $directory = 'public/construction-assets/' . $constrct_id . '/bin/' . $sent_path;
        }
        // create directory
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        try {
            $old_data = Http::get($livePath)->body();
        } catch (\Throwable $th) {
            // dd($th);
        }
        // return path

        if ($old_data != null) {
            // Upload the files to the new server
            // $new_filepath = $directory . '/' . basename($livePath);
            $new_filepath = $directory . '/' . basename(urldecode($livePath));
            file_put_contents($new_filepath, $old_data);
        }
        if ($old_data != null && $status == 1) {
            $path = $constrct_id . '/' . $sent_path . '/' . basename(urldecode($livePath));
        } else {
            $path = $constrct_id . '/bin/' . $sent_path . '/' . basename(urldecode($livePath));
        }

        return  $path;
    }
}

<?php

namespace App\Helper;

use File;
use ZipArchive;
use App\Mail\InviaEmail;
use App\Models\PrNotDoc;
use App\Mail\ExampleEmail;
use Illuminate\Support\Str;
use App\Models\PrNotDocFile;
use App\Models\StatusPrNoti;
use RecursiveIteratorIterator;
use App\Models\MatarialHistory;
use RecursiveDirectoryIterator;
use App\Mail\materialChangeMail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

trait BaseQuery
{
    /**
     * add new record
     */
    public function add($model, $data)
    {
        return $model->create($data);
    }

    /**
     * edit or update the record if exist by id
     */
    public function create_update($model, $data, $id)
    {

        return $model->updateOrCreate($id, $data);
    }
    /**
     * check id exist in model
     */
    public function check_exist()
    {
        $construction_id = $this->session_get('construction_site_id');

        if ($construction_id == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * get all record
     */
    public function get_all($model)
    {
        return $model->get();
    }

    /**
     * get all record
     */
    public function get_all_users($model, $relation = null)
    {
        if ($relation == null) {
            return $model->whereHas('roles', function ($q) {
                $q->where('name', '!=', 'admin');
            })->orderBy('created_at', 'DESC')->get();
        } else {
            return $model->with($relation)->whereHas('roles', function ($q) {
                $q->where('name', '!=', 'admin');
            })->orderBy('created_at', 'DESC')->get();
        }
    }

    /**
     * get record by its id
     */
    public function get_by_id($model, $id)
    {
 
        return $model->findOrFail($id);
    }

    /**
     * get count record by column
     */
    public function count_by_column($model, $column, $value)
    {
        return count($model->where($column, $value)->get());
    }
    /**
     * get record by column
     */
    public function get_by_column($model, $column, $value)
    {
        return $model->where($column, $value)->get();
    }

    /**
     * get single record by column
     */
    public function get_by_column_single($model, $column, $value)
    {
        return $model->where($column, $value)->first();
    }

    /**
     * delete record by its id
     */
    public function delete($model, $id)
    {
        // return $model->findOrFail($id)->delete();
        $data = $model->findOrFail($id);
        $data->status = 1;
        $data->save();
        return $data;
    }

    public function updateStateRecursive($model)
    {

        $baseData = [
            'updated_on' => '',
            'updated_by' => ''
        ];

        $model->update($baseData);
        // dd($model->PrNotDocFile);
        foreach ($model->PrNotDocFile as $sub1) {
        //  dd($sub1);
                if ($sub1->bydefault == 1) {
                    $this->softDelete($sub1);
                    $sub1->update($baseData);
                } else {
                    $this->permanentDelete($sub1);
                    $sub1->update($baseData);
                }
            
        }
    }

    public function updateStateRecursive1($model)
    {
        
        // Update the state of the current model
        $baseData = [
            'updated_on' => null,
            'updated_by' => null
        ];

        $model->update($baseData);


        // Traverse the relationships
        foreach ($model->TypeOfDedectionSub1 as $sub1) {
            
            if ($sub1->file_name != null && $sub1->folder_name == null ) {

                $sub1->bydefault == 1 ? $this->softDelete($sub1) : $this->permanentDelete($sub1);
               
            } else {
                $sub1->update($baseData);

                foreach ($sub1->TypeOfDedectionSub2 as $sub2) {

                    if ($sub2->file_name != null && $sub2->folder_name == null ) {

                        $sub2->bydefault == 1 ? $this->softDelete($sub2) : $this->permanentDelete($sub2);
                    
                    } else {
                        $sub2->update($baseData);

                        foreach ($sub2->TypeOfDedectionFiles as $files) {
                            if ($files->file_name != null && $files->folder_name == null) {

                                $files->bydefault == 1 ? $this->softDelete($files) : $this->permanentDelete($files);
                              
                            } else {
                                    $files->update($baseData);
                                foreach ($files->TypeOfDedectionFiles2 as $files2) {
                                    if ($files2->file_name != null && $files2->folder_name == null) {
                                        $files2->bydefault == 1 ? $this->softDelete($files2) : $this->permanentDelete($files2);
                                      
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    

    public function DeleteReliefFiles($model)
    {
// dd($model);
        $baseData = [
            'updated_on' => '',
            'updated_by' => ''
        ];

        $model->update($baseData);
        // dd($model->PrNotDocFile);
        foreach ($model->ReliefDocumentFile as $sub1) {
        //  dd($sub1);
                if ($sub1->bydefault == 1) {
                    $this->softDelete($sub1);
                    $sub1->update($baseData);
                } else {
                    $this->permanentDelete($sub1);
                    $sub1->update($baseData);
                }
            
        }
    }

    public function DeleteRecursiveReliefFolders($model)
    {
        
        // Update the state of the current model
        $baseData = [
            'updated_on' => '',
            'updated_by' => ''
        ];

        $model->update($baseData);

        foreach ($model->ReliefDocumentFile as $sub1) {
            
            if ($sub1->file_name != null )
            {

                if ($sub1->bydefault == 1)
                {
                    $this->softDelete($sub1);
                    $sub1->update($baseData);
                } else {
                    $this->permanentDelete($sub1);
                    $sub1->update($baseData);
                }
            }
             else {
                $sub1->update($baseData);
                // dd($sub1->RelifDocFileSub1);
               
                foreach ($sub1->RelifDocFileSub1 as $sub2) {

                    if ($sub2->file_name != null ) {

                        if ($sub2->bydefault == 1) {
                            $this->softDelete($sub2);
                            $sub2->update($baseData);
                        } else {
                            $this->permanentDelete($sub2);
                            $sub2->update($baseData);
                        }
                    } 
                }
      
             }
        }
    }

    public function DeleteRecursiveReliefsub1Folders($model)
    {
        
        // Update the state of the current model
        $baseData = [
            'updated_on' => '',
            'updated_by' => ''
        ];

        $model->update($baseData);

        foreach ($model->RelifDocFileSub1 as $sub1) {
            
            if ($sub1->file_name != null )
            {

                if ($sub1->bydefault == 1)
                {
                    $this->softDelete($sub1);
                    $sub1->update($baseData);
                } else {
                    $this->permanentDelete($sub1);
                    $sub1->update($baseData);
                }
            }
             
        }
    }


    public function softDelete($relatedModel)
    {

        $this->moveFileToTrash($relatedModel);

        $relatedModel->update([
            'file_path' =>  null,
            'updated_on' => null,
            'updated_by' => null,
        ]);

        return true;
    }
    public function permanentDelete($relatedModel)
    {

        $this->moveFileToTrash($relatedModel);

        $relatedModel->delete();

        return true;
    }
    /**
     * delete record by its id
     */
    public function destroyById($model, $id)
    {
        return $model->findOrFail($id)->delete();
    }

    /**
     * get all roles
     */
    public function all_roles()
    {
        return Role::pluck('name')->toArray();
    }

    /**
     * get user by role
     */

    public function user_by_role($model, $role, $relation = null)
    {
        if ($relation == null) {
            $user = $model->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            })->orderBy('created_at', 'DESC')->get();
        } else {
            $user = $model->with($relation)->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            })->orderBy('created_at', 'DESC')->get();
        }
        return $user;
    }

    /**
     * get user by role
     */
    public function business_user_by_role($model, $company_type)
    {
        $user = $model->whereHas('roles', function ($q) {
            $q->where('name', 'business');
        })->wherehas('business', function ($q_b) use ($company_type) {
            $q_b->where('company_type', $company_type);
        })->orderBy('created_at', 'DESC')->get();

        return $user;
    }

    /**
     * get user by id with role
     */
    public function user_by_id_with_role($model, $id, $role)
    {
        $user = $model->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        })->find($id);

        return $user;
    }

    /**
     * get user count by id with role
     */
    public function user_count_by_id_with_role($model, $id, $role)
    {
        $user = $model->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        })->count('id', $id);
        return $user;
    }

    /**
     * store value in session
     */
    public function session_store($key, $value)
    {
        Session::put($key, $value);
    }

    /**
     * store value in session
     */
    public function session_get($key)
    {
        return Session::get($key);
    }

    /**
     * store value in session
     */
    public function session_remove($key)
    {
        return Session::remove($key);
    }

    // =================status files route

    //increament page status in parent model
    public function update_page_status($model, $id, $stats)
    {
        $status = $model->find($id);
        $status->page_status = $stats;
        $status->save();
        return $status;
    }

    // upload_common_func files
    public function upload_common_func($fileName, $path, $file, $consId = null)
    {

        if ($consId != null) {
            $constrct_id  = $consId;
        } else {
            $constrct_id = $this->session_get("construction_id");
        }

        $path = $constrct_id . '/' . $path . '/' . $fileName;
        // Storage::disk('public_uploads')->put($path, File::get($file));


        $fileContent = File::get($file);

        // Encrypt the file content
        $encryptedContent = Crypt::encrypt($fileContent);
    
        // Store the encrypted content
        Storage::disk('public_uploads')->put($path, $encryptedContent);
        
        $publicPath = public_path();

        // Set the desired permissions (777 in this case)
        $permissions = 0777;

        // Recursively change permissions for files and directories within the public folder
        $this->recursiveChmod($publicPath, $permissions);

        return  $path;
    }

    // download for images
    public function download_common_func($path)
    {
        $constrct_id = $this->session_get("construction_id");
        return Storage::disk('public_uploads')->download($constrct_id . '/' . $path);
    }
    // download for files only
    public function download_common_func_for_files($path)
    {
       // Read the file content
        $fileContent = Storage::disk('public_uploads')->get($path);

        // Check if the file is encrypted
        if (Str::startsWith($fileContent, ['eyJpdiI6', 'eyJuYW1lIjoi'])) {
            // File is encrypted, decrypt it
            try {
                $decryptedContent = Crypt::decrypt($fileContent);
                $tempFilePath = tempnam(sys_get_temp_dir(), 'decrypted_file');
                file_put_contents($tempFilePath, $decryptedContent);
                return response()->download($tempFilePath, basename($path));
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return response()->json(['error' => 'Failed to decrypt file'], 500);
            }
        } else {
            // File is not encrypted, directly download it
            return Storage::disk('public_uploads')->download($path);
        }

     
    }

    // download files
    public function delete_files($path)
    {
        $constrct_id = $this->session_get("construction_id");
        return Storage::disk('public_uploads')->delete($constrct_id . '/' . $path);
    }

    // download files
    public function download_zip_files($model, $id, $path, $constrct_id = null)
    {
        if ($constrct_id) {
            $constrct_id = $constrct_id;
        } else {
            $constrct_id = $this->session_get("construction_id");
        }



        $fileName = $constrct_id . '_file.zip'; // Name of our archive to download

        $filePath = public_path($fileName);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        // dd($filePath);
        $zip = new ZipArchive(); // Initializing PHP class

        $var = $this->get_by_id($model, $id)->toArray();

        foreach ($var as $filevalue) {

            if ($zip->open(public_path($fileName), \ZipArchive::CREATE || \ZipArchive::OVERWRITE) === TRUE) {
                $filepath = public_path('construction-assets/' . $constrct_id . '/' . $path);
                $files = File::files($filepath);

                // loop the files result
                foreach ($files as $value) {
                    $relativeNameInZipFile = basename($value);
                    if ($relativeNameInZipFile == $filevalue['name'] && $filevalue['status'] == 1) {
                        $zip->addFile($value, $relativeNameInZipFile);
                    }
                }
            }
        }
        $zip->close();
        return redirect()->to(asset($fileName));
        // return response()->download(asset($fileName));
    }

    // download zip folder
    public function download_zip_folder($folder_name, $path, $consId = null)
    {

        $constrct_id = $consId != null ? $consId : $this->session_get("construction_id");

        if ($path == "all") {
            $add = 'construction-assets/' . $constrct_id;
        } else {
            $add = 'construction-assets/' . $constrct_id . '/' . $path;
        }





        $folderPath = public_path($add);
        // dd($folderPath );
        // Initialize an instance of ZipArchive
        $zip = new ZipArchive;
        // Create a new zip file and open it for writing
        $zipFileName = $folder_name . '.zip';
        // Check if the folder path exists
        if (File::exists($folderPath)) {
            $zip->open(public_path($zipFileName), ZipArchive::CREATE | ZipArchive::OVERWRITE);
            // Iterate through all the files in the folder
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folderPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $name => $file) {
                // Skip directories (they would be added automatically)
                if (!$file->isDir()) {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $substring = 'Releif document files';
                    if (preg_match("/$substring/", $filePath)) {
                        $relativePath = substr($filePath, strlen($folderPath));
                    } else {
                        $relativePath = substr($filePath, strlen($folderPath) + 1);
                    }
                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive will be created only after closing it
            $zip->close();
            // Download the zip file
            return response()->download(public_path($zipFileName))->deleteFileAfterSend(true);
        } else {

            return redirect()->back()->with('error', "La directory è vuota!!");
        }
    }



    public function DownloadSubFile($fol1, $fol2, $fol3, $fol4 = null)
    {
        // dd($fol1,$fol2,$fol3);
        if ($fol4 == null) {
            $folder_name = $fol3;
        } else {
            $folder_name = $fol4;
        }

        // dd($folder_name);
        if ($folder_name == "all") {
            $path = $fol1 . '/' . $fol2;
        } elseif ($fol4 == null) {
            $path = $fol1 . '/' . $fol2 . '/' . $fol3;
        } else {
            $path = $fol1 . '/' . $fol2 . '/' . $fol3 . '/' . $fol4;
        }

        // ---------------------------//
        return $this->download_zip_folder($folder_name, $path);
    }

    // download files
    public function get_from_storage($path)
    {
        $constrct_id = $this->session_get("construction_id");
        return Storage::disk('public_uploads')->download($constrct_id . '/' . $path);
    }

    // email sent against  missing files
    public function email_against_missing_files($to, $subject, $data, $path)
    {
        $fromName = 'PORTALE GREENGEN';
        $fromEmail = 'greengen@crm-labloid.com';

        Mail::send($path, $data, function ($message) use ($fromName, $fromEmail, $to, $subject) {
            $message->from($fromEmail, $fromName); // Set sender's name and email
            $message->to($to);
            $message->subject($subject);
        });

        return true;
            
    }

    //send material mail
    public function materialMail($to, $subject, $name, $msg, $construction_id = null)
    {
        
        return Mail::to($to)->send(new ExampleEmail($subject, $name, $msg, $construction_id));
    }

    public function InviaEmail($to, $subject, $name, $msg, $construction_id = null)
    {
        
        return Mail::to($to)->send(new InviaEmail($subject, $name, $msg, $construction_id));
    }

    public function MaterialDelete($model, $id){
        $data =  $model->findOrFail($id);
        $status =  $data->delete_status == 0 ? 1 : 0;
        $data->delete_status =  $status;
        $data->update();
        return true;
    }

    public function materialChangeMail($to, $subject, $name, $data , $construct_id, $delation = null)
    {
           
        return Mail::to($to)->send(new materialChangeMail($subject, $name, $data, $construct_id, $delation));
    }



    public function MaterialHistoryStore($data)
    {
        $count = count($data['construction_site_id_history']);
    
        for ($i = 0; $i < $count; $i++) {
            $materialHistoryData = [
                'construction_site_id' => $data['construction_site_id_history'][$i],
                'material_id' => $data['material_id_history'][$i],
                'changeBy' => $data['changeBy_history'][$i],
                'updated_field' => $data['updatedField_history'][$i],
                'Original' => $data['Original_history'][$i],
                'Updated_to' => $data['Updated_to_history'][$i],
                'reason' => $data['reason'][$i],
            ];
    
            // Define the conditions for update or create
            $conditions = [
                'material_id' => $data['material_id_history'][$i],
                'updated_field' => $data['updatedField_history'][$i],
                'Original' => $data['Original_history'][$i],
                'Updated_to' => $data['Updated_to_history'][$i],
            ];
    
            MatarialHistory::updateOrCreate($conditions, $materialHistoryData);
        }
    
        return true;
    }
    


    /**
     * Change state of status
     */
    public function change_status($modal, $id, $data, $page)
    {
        $currentStatus = $data['state'];

        $var = $this->get_by_id($modal, $id);

        $data['updated_on'] = date('Y-m-d');
        $data['updated_by'] = Auth()->user()->id;

        $var->update($data);

        $check_latest_status_func = new ConstuctionChiledStore;
        $check_latest_status_func->check_latest_status($page, $currentStatus);

        return true;
    }

    public function createAssistanceFolder($child_folder_name, $construct_id)
    {
        // dd("child_folder_name=>",$child_folder_name, "construct_id =>",$construct_id);
        $parent = "Documenti Assistenza";

        $pr_not_doc = PrNotDoc::where('construction_site_id', $this->session_get("construction_id"))->where('folder_name', $parent)->first();

        if ($pr_not_doc == null) {
            // dd($this->session_get("construction_id"));
            $status_prnoti = StatusPrNoti::where('construction_site_id', $this->session_get("construction_id"))->first();
            // dd($status_prnoti);
            $arr = [
                'status_pr_noti_id' => $status_prnoti->id,
                'folder_name' => $parent,
                'construction_site_id' => $construct_id,
                'allow' => 'admin,user',
                'state' => 1,
            ];

            $status_pre = PrNotDoc::create($arr);
            $pr_not_doc_id  = $status_pre->id;

            $prArr = [
                'pr_not_doc_id' => $pr_not_doc_id,
                'construction_site_id' => $construct_id,
                'folder_name' => $child_folder_name,
                'allow' => 'admin,user',
                'state' => 1,
            ];
            // $status_pre->TypeOfDedectionSub1()->updateOrCreate($prArr);
            $add_file = $status_pre->TypeOfDedectionSub1()->updateOrCreate($prArr);

            $fattura = "Fattura" . ' ' . $child_folder_name;
            $rapportino = "Rapportino" . ' ' . $child_folder_name;

            $sub_folder_1 = [
                'type_of_dedection_sub1_id' => $add_file->id,
                'construction_site_id' => $construct_id,
                'allow' => 'admin,user',
                'file_name' => $fattura,
                'bydefault' => '1',
                'state' => 1
            ];
            $sub_folder_2 = [
                'type_of_dedection_sub1_id' => $add_file->id,
                'construction_site_id' => $construct_id,
                'allow' => 'admin,user',
                'file_name' => $rapportino,
                'bydefault' => '1',
                'state' => 1
            ];
            $add_file->TypeOfDedectionSub2()->updateOrCreate($sub_folder_1);
            $add_file->TypeOfDedectionSub2()->updateOrCreate($sub_folder_2);
        } else {

            $prArr = [
                'pr_not_doc_id' => $pr_not_doc->id,
                'construction_site_id' => $construct_id,
                'folder_name' => $child_folder_name,
                'allow' => 'admin,user',
                'state' => 1,
            ];
            $add_file = $pr_not_doc->TypeOfDedectionSub1()->updateOrCreate($prArr);
            $fattura = "Fattura" . ' ' . $child_folder_name;
            $rapportino = "Rapportino" . ' ' . $child_folder_name;
            $sub_folder_1 = [
                'type_of_dedection_sub1_id' => $add_file->id,
                'construction_site_id' => $construct_id,
                'allow' => 'admin',
                'file_name' => $fattura,
                'bydefault' => 1,
                'state' => 1
            ];
            $sub_folder_2 = [
                'type_of_dedection_sub1_id' => $add_file->id,
                'construction_site_id' => $construct_id,
                'allow' => 'admin',
                'file_name' => $rapportino,
                'bydefault' => 1,
                'state' => 1
            ];
            $add_file->TypeOfDedectionSub2()->updateOrCreate($sub_folder_1);
            $add_file->TypeOfDedectionSub2()->updateOrCreate($sub_folder_2);
            // PrNotDocFile::create($prArr);
        }

        return true;
    }



    public function recursiveChmod($path, $permissions)
    {
        if (is_dir($path)) {
            // If it's a directory, change its permissions and iterate through its contents
            chmod($path, $permissions);
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $this->recursiveChmod($path . '/' . $file, $permissions);
                }
            }
        } elseif (is_file($path)) {
            // If it's a file, change its permissions
            chmod($path, $permissions);
        }
        return true;
    }

    //move file to trash or bin

    function moveFileToTrash($data)
    {

        $filePath = $data->file_path;
        $directory = dirname($filePath);

        $baseDirectory = public_path();

        $livePath = 'https://greengen.crm-labloid.com/construction-assets/' . $filePath;


        $oldPath = $baseDirectory . '/' . 'construction-assets' . '/' . $filePath;

        try {
            $oldData = Http::get($livePath)->body();
        } catch (\Throwable $th) {
            // Handle the error or exception as needed
        }

        $directory = 'construction-assets/' . $directory .  '/bin';

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($oldData != null) {
            $newFilePath = $directory . '/' . basename(urldecode($livePath));
            file_put_contents($newFilePath, $oldData);

            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
    }

    public function Deletesub1Recursive($model)
    {
        
        // Update the state of the current model
        $baseData = [
            'updated_on' => '',
            'updated_by' => ''
        ];
        // dd($model->TypeOfDedectionSub2 );
        $model->update($baseData);


        // Traverse the relationships
        foreach ($model->TypeOfDedectionSub2 as $sub1) {
            
            if ($sub1->file_name != null ) {

                if ($sub1->bydefault == 1) {
                    $this->softDelete($sub1);
                    $sub1->update($baseData);
                } else {
                    $this->permanentDelete($sub1);
                    $sub1->update($baseData);
                }
            } else {
                $sub1->update($baseData);
                // dd($sub1->TypeOfDedectionSub2);
                foreach ($sub1->TypeOfDedectionFiles as $sub2) {

                    if ($sub2->file_name != null ) {

                        if ($sub2->bydefault == 1) {
                            $this->softDelete($sub2);
                            $sub2->update($baseData);
                        } else {
                            $this->permanentDelete($sub2);
                            $sub2->update($baseData);
                        }
                    } else {
                        $sub2->update($baseData);

                        foreach ($sub2->TypeOfDedectionFiles2 as $files) {
                            if ($files->file_name != null) {


                                if ($files->bydefault == 1) {
                                    $this->softDelete($files);
                                    $files->update($baseData);
                                } else {
                                    $this->permanentDelete($files);
                                    $files->update($baseData);
                                    
                                }
                            } 
                        }
                    }
                }
            }
        }
    }

    public function Deletesub2Recursive($model)
    {

        $baseData = [
            'updated_on' => null,
            'updated_by' => null
        ];
        // dd($model->TypeOfDedectionSub2 );
        $model->update($baseData);


        // Traverse the relationships
        foreach ($model->TypeOfDedectionFiles as $sub1) {
            
            if ($sub1->file_name != null ) {

                if ($sub1->bydefault == 1) {
                    $this->softDelete($sub1);
                    $sub1->update($baseData);
                } else {
                    $this->permanentDelete($sub1);
                    $sub1->update($baseData);
                }
            } else {
                $sub1->update($baseData);
                // dd($sub1->TypeOfDedectionSub2);
                foreach ($sub1->TypeOfDedectionFiles2 as $sub2) {

                    if ($sub2->file_name != null ) {

                        if ($sub2->bydefault == 1) {
                            $this->softDelete($sub2);
                            $sub2->update($baseData);
                        } else {
                            $this->permanentDelete($sub2);
                            $sub2->update($baseData);
                        }
                    } 
                   
                }
            }
        }
    }



}

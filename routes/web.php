<?php

use App\Models\ConstructionSite;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\StatusTechnisan;
use App\Http\Controllers\ConstructionNote;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatusSALController;
use App\Http\Controllers\UploadCSVController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DocumentAndController;
use App\Http\Controllers\MaterialListController;

use App\Http\Controllers\PropertyDataController;
use App\Http\Controllers\StatusClosedController;
use App\Http\Controllers\StatusReliefController;
use App\Http\Controllers\ChiavettaFileController;
use App\Http\Controllers\StatusLegge10Controller;
use App\Http\Controllers\StatusPreNotiController;
use App\Http\Controllers\StatusRegPracController;
use App\Http\Controllers\MatarialHistoryController;
use App\Http\Controllers\ConstructionSiteController;
use App\Http\Controllers\TechincianDetailController;
use App\Http\Controllers\StatusComputationController;

use App\Http\Controllers\StatusEneaBalanceController;
use App\Http\Controllers\StatusPreAnalysisController;
use App\Http\Controllers\StatusWorkStartedController;
use App\Http\Controllers\MaterialsAsisstanceController;
use App\Http\Controllers\ConstructionLocationController;
use App\Http\Controllers\ConstructionMaterialController;
use App\Http\Controllers\ConstructionCondominiController;
use App\Http\Controllers\ConstructionSiteImageController;

use App\Http\Controllers\ConstructionSiteSettingController;
use App\Http\Controllers\ConstructionSiteShippingController;
use App\Http\Controllers\ConstructionSiteShippingListController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clear', function () {
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    return "Cleared";
});

Route::get('/down', function () {
    Artisan::call('down');
    return "site down successfully"; ; 
});


Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/signin', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/signin', [LoginController::class, 'login']);

Route::get('auth/google',  [LoginController::class, 'redirectToGoogle']);
Route::get('auth/google/callback',  [LoginController::class, 'handleGoogleCallback']);

Route::get('stampa/{id}', [ConstructionSiteController::class, 'stampa'])->name('construction_stampa');
Route::get('material/changing/{construct_id}', [MaterialListController::class, 'material_changing'])->name('material_changing');

Route::post('/sendInviaEmail', [ConstructionSiteController::class, 'sendInviaEmail'])->name('sendInviaEmail');

Auth::routes(['register' => false, 'login' => false]);

Route::group(
    ['prefix' => "material/", "controller" => MaterialListController::class],
    function () {
        Route::get('/all/list', 'index')->name('material_all');
        Route::get('create/form', 'create')->name('material_create');
        Route::post('store/form', 'store')->name('material_store');
        Route::post('get_mat_list_ajax/form', 'get_mat_list_ajax')->name('get_mat_list_ajax');
        Route::post('get/mat/list/ajax/form/report', 'get_mat_list_ajax_for_report')->name('get_mat_list_ajax_for_report');
    }
);

Route::resource('changingMaterial', MatarialHistoryController::class);
Route::post('storeMaterialHistory', [MatarialHistoryController::class, 'store_material_history'])->name('store_material_history');
Route::get('MaterialsHistories/{id}', [MatarialHistoryController::class, 'MaterialsHistories'])->name('MaterialsHistories');
Route::post('toggleMaterialHistory/{id}', [MatarialHistoryController::class, 'toggleMaterialHistory'])->name('toggleMaterialHistory');

Route::group(
    ['prefix' => "construction/material/", "controller" => ConstructionMaterialController::class],
    function () {
        Route::get('/all/list', 'index')->name('construction_material_all');
        Route::get('create/form', 'create')->name('construction_material_create');
        Route::post('add/store/{consId?}', 'store')->name('construction_material_store');
        Route::post('edit/update/{id}', 'update')->name('edit_materials_all');
        Route::post('edit/materials/update/{consId?}', 'edit_materials_state')->name('edit_materials_state');
        Route::post('delete/material', 'destroy')->name('delete_construction_material');
        Route::post('Delete/Popup/Body/{id}', 'deletePopupBodyGet')->name('deletePopupBodyGet');
        Route::get('fetch/data/for/create/contract/{id?}', 'fetch_data_for_create_contract')->name('fetch_data_for_create_contract');
        Route::post('get/mat/list/related/data', 'get_mat_list_related_data')->name('get_mat_list_related_data');
        Route::post('business/check', 'business_check')->name('business_check');
    }
);

Route::get('password/reset', function () {
    return view('password.reset');
})->name('admin_reset_password_form');

Route::get('password/change/{token}/{email}', function () {
    return view('password.change');
})->name('admin_reset_password_change');

Route::post('password/send/request', [UserController::class, 'passwordSendRequest'])->name('passwordSendRequest');
Route::get('/autocomplete-users', [UserController::class, 'autocomplete'])->name('users.autocomplete');
Route::post('status/changes', [ImportController::class, 'status_changes']);

Route::get('/db/down', [ImportController::class, 'db']);

Route::get('/system/down', [ImportController::class, 'down']);


Route::get('ReliefDocumentImport', [ImportController::class, 'ReliefDocumentImport']);

Route::get('ReliefDocumentPermission', [ImportController::class, 'ReliefDocumentPermission']);

Route::get('extraCondominio', [ImportController::class, 'extraCondominio']);

Route::get('DocumentiRilevanti', [ImportController::class, 'DocumentiRilevanti']);


Route::get('PrNotDocFile', [ImportController::class, 'PrNotDocFile']);

Route::get('metirialupdating', [ImportController::class, 'metirialupdating']);


Route::get('CantiereCondominio', [ImportController::class, 'CantiereCondominio']);
Route::get('RemoveCondomonioDublication', [ImportController::class, 'RemoveCondomonioDublication']);
Route::get('addNewRecords', [ImportController::class, 'addNewRecords']);
Route::get('closeStatus', [ImportController::class, 'CloseStatus']);

Route::get('condominiChildDelete', [ImportController::class, 'condominiChildDelete']);

Route::get('CommetApi', [ConstructionLocationController::class, 'CommetApi']);
Route::get('getCoordinates', [ImportController::class, 'getCoordinates']);


Route::post('material-assistance/add-assistance', [MaterialsAsisstanceController::class, 'store'])->name('add_assistance');
Route::post('add', [ConstructionSiteController::class, 'construction_pin_location'])->name('construction_pin_location');


/**
 * Secure Admin routes
 */
Route::group(
    ["middleware" => "auth"],
    function () {
        /**
         * dashboard route
         */
        Route::group(
            ["controller" => ConstructionSiteController::class],
            function () {
                Route::get('/', 'index')->name('home');
                Route::get('/auth', 'index')->name('welcome');
                Route::get('/dashboard', 'index')->name('homeDashboard');
                // Route::post('/sendInviaEmail', 'sendInviaEmail')->name('sendInviaEmail');
            }
        );

        /**
         * Send email notifications
         */
        Route::post('reminder/emails', [EmailController::class, 'reminder_emails'])->name('reminder_emails');
        Route::get('reminder/emails/assistensi', [EmailController::class, 'reminder_emails_assistensi'])->name('reminder_emails_assistensi');

        /**
         * Construction Site Shipping list route
         */
        Route::group(
            ['prefix' => "construction-site/shipping/list", "controller" => ConstructionSiteShippingListController::class],
            function () {
                Route::post('addConShippingList', 'store')->name('addConShippingList');
            }
        );

        /**
         * Construction Site Shipping route
         */
        Route::group(
            ['prefix' => "construction-site/shipping/", "controller" => ConstructionSiteShippingController::class],
            function () {
                Route::get('list', 'index')->name('centriList');
                Route::post('addCentrie', 'store')->name('addCentrie');
                Route::get('destroyCentrie/{id?}', 'destroy')->name('destroyCentrie');
                Route::get('print/', 'print_shipping')->name('print_shipping');
            }
        );

        Route::post('search/cons/search/role', [SearchController::class, 'construction_search_role'])->name('construction_search_role');
        Route::post('cons', [SearchController::class, 'index'])->name('construction_search');
       
        Route::get('construction-site/page/{id}/detail/{pagename}/{image?}', [ConstructionSiteController::class, 'show'])->whereIn('image', ['ante', 'durante', 'post', 'cantiere'])->name('construction_detail');

        // ReliefDocController
        Route::group(
            ['prefix' => "relief-doc/", "controller" => App\Http\Controllers\ReliefDocController::class],
            function () {
                Route::get('{id}', 'show')->name('show_relief_doc_file');
                Route::get('check-fotovoltac/{slug}/{consId?}', 'check_fotovoltac')->name('check_fotovoltac');
                Route::get('download-reliefdoc/{id}', 'download_reliefdoc')->name('download_reliefdoc');
                Route::get('download-reliefdoc/folder/{foldername}', 'download_reliefdoc_folder')->name('download_reliefdoc_folder');
                Route::get('delete-reliefdoc/{id}', 'destroy')->name('delete_reliefdoc');
                Route::get('delete-all-reliefdoc/{id}', 'DeleteAllReliefdoc')->name('DeleteAllReliefdoc');
            }
        );
        // ReliefDocumentFile
        Route::group(
            ['prefix' => "relief-doc-file/", "controller" => App\Http\Controllers\ReliefDocumentFile::class],
            function () {
                Route::post('upload-files', 'store')->name('upload_files');
                Route::post('replace-files', 'replace_file')->name('replace_rel_doc_files');
                Route::get('change-file-state/{id}', 'destroy')->name('change_file_state');
                Route::post('Rec-email', 'rec_email')->name('rec_email');
                Route::get('download-relief-file/{id}', 'download_file')->name('download_relief_file');
                Route::get('download-relief-folder/{foldname?}', 'download_folder')->name('download_relief_folder');
                Route::get('delete-relief-folder/{id}', 'delete_relief_folder')->name('delete_relief_folder');

                Route::post('delete-file', 'permanent_delete')->name('rec_file_delete');
                Route::post('relif-file-destroy', 'file_destroy')->name('rec_file_destroy');

                Route::get('relief-sub-file/{id}', 'relif_sub_file')->name('relif_sub_file');
            }
        );
        // RelifDocFileSub1Controller
        Route::group(
            ['prefix' => "reliefsub-file/", "controller" => App\Http\Controllers\RelifDocFileSub1Controller::class],
            function () {
                Route::post('sub1-files', 'create')->name('upload_sub1_files');
                Route::get('download-relief-sub-file/{id}', 'download_file')->name('download_relief_sub_file');
                Route::post('Rec-sub-email', 'rec_email')->name('rec_sub_email');
                Route::post('rel/doc-sub/delete-file', 'permanent_delete')->name('rel_doc_sub_file_delete');
            }
        );

        Route::group(
            ['prefix' => "status-leg10/", "controller" => StatusLegge10Controller::class],
            function () {
                Route::post('status/{id?}', 'status_leg10')->name('status_leg10');
            }
        );

        // Legge10 File Controller
        Route::group(
            ['prefix' => "leg10-file/", "controller" => App\Http\Controllers\Legge10FileController::class],
            function () {
                Route::get('legge10-doc/{id?}', 'show')->name('legge10_doc');
                Route::post('legge10-file-upload/{id?}', 'store')->name('legge10_file_upload');
                Route::post('legge10-multifile-upload', 'legge10_multifile_upload')->name('legge10_multifile_upload');
                Route::get('download-legg10/{id}', 'download_legg10_file')->name('download_legg10_file');
                Route::get('download/legg10/all/files', 'download_legg10_all_file')->name('download_legg10_all_file');
                Route::get('leg-email', 'legg10_email')->name('legg10_email');
                //
                Route::post('delete-file', 'permanent_delete')->name('leg10_file_delete');
                Route::post('relif-file-destroy', 'file_destroy')->name('leg10_file_destroy');
            }
        );

        Route::group(
            ['prefix' => "status-prenoti/", "controller" => StatusPreNotiController::class],
            function () {

                Route::post('status-prenoti/{id?}', 'status_prenoti')->name('status_prenoti');
                Route::get('download/prenoti-folder/{id?}', 'download_prenoti_folder')->name('download_prenoti_folder');
                Route::get('{id}', 'show')->name('show_preNoti_doc');
                Route::post('filter_documents', 'filter_documents')->name('show_filtered_documents');
            }
        );
        //
        Route::group(
            ['prefix' => "prenoti-doc/", "controller" => App\Http\Controllers\PriNotiDocController::class],
            function () {
                Route::get('{id}', 'show')->name('show_prenoti_doc_file');
                Route::get('check/{slug}/{consId?}', 'check_50_65_90_110')->name('check_50_65_90_110');
                Route::get('download/prenotidoc/{id}/{consId?}', 'download_prenotidoc')->name('download_prenotidoc');
                Route::get('download/prenotidoc/folder/{foldername}/{consId?}', 'download_prenotidoc_folder')->name('download_prenotidoc_folder');
                Route::get('delete-prinotdoc/{id}', 'destroy')->name('delete_prinotdoc');
                // Route::get('delete-sub1-prinotdoc/{id}', 'deleteAllSub1')->name('deleteAllSub1');
                Route::get('delete-all-prinotdoc/{id}', 'DeleteAllPrinotdoc')->name('DeleteAllPrinotdoc');
            }
        );
        // TypeOfDedectionSub1Controller
        Route::group(
            ['prefix' => "Type-Of-Deduction/", "controller" => App\Http\Controllers\TypeOfDedectionSub1Controller::class],
            function () {
                Route::get('{prnotid}/{id}/{docname}', 'sub2document')->name('type_of_deduc_sub1');

                Route::post('replace-file-sub1', 'replace_file')->name('replace_sub1_file');
                //
                Route::post('deduction1/destroy/sub1', 'file_destroy')->name('destroy_sub1');
                Route::post('deduction1/delete/sub1', 'permanent_delete')->name('delete_files_sub1');

                Route::post('upload-sub1-files', 'upload_file')->name('upload_sub1_main_file');
                Route::get('download-sub1file/{id}', 'download_sub1')->name('download_sub1');

                Route::get('download/sub1-folder/{fol1}/{fol2}', 'download_sub1_folder')->name('download_sub1_folder');
                Route::get('delete-all-sub1/{id}', 'DeleteAllsub1')->name('Delete_All_sub1');
                Route::post('email-sub2', 'email')->name('email_sub1');
            }
        );
        // TypeOfDedectionSub2Controller
        Route::group(
            ['prefix' => "Type-Of-Deduction-sub2/", "controller" => App\Http\Controllers\TypeOfDedectionSub2Controller::class],
            function () {

                Route::get('{prnotdocid}/{sub1id}/{id}/{docname}/{prntfname}', 'show_prnt_files')->name('type_of_deduc_sub2');
                Route::post('replace-sub2-file', 'replace_file')->name('replace_sub2_file');
                Route::post('upload-files', 'upload_file')->name('upload_sub2_main_file');
                Route::get('download-subfile/{id}', 'download_sub2')->name('download_sub2');
                Route::get('download-subfolder/{fol1}/{fol2}/{fol3}', 'download_sub2_folder')->name('download_sub2_folder');
                Route::get('delete-subfolder/{id}', 'delete_sub2_folder')->name('delete_sub2_folder');

                Route::get('download-subfolder/{id}', 'download_subfolder2')->name('download_subfolder2');

                Route::post('file-deduction2/destroy/sub2', 'file_destroy')->name('destroysub2');
                Route::post('file/deduction2/delete/sub2', 'permanent_delete')->name('deletefiles_sub2');
                Route::post('email-sub2', 'email')->name('email_sub2');
            }
        );
        // TypeOfDedectionFilesController
        Route::group(
            ['prefix' => "Deduction-sub2-files/", "controller" => App\Http\Controllers\TypeOfDedectionFilesController::class],
            function () {

                Route::get('{id}/{docname}/{prntfname}/{f1name}/{f2name}', 'show_prnt_files')->name('type_of_deduc_files1');
                Route::post('upload-sub-files', 'create')->name('upload_chiled_files');
                Route::get('download-sub2file/{id}', 'download_sub2_chiled_file')->name('download_sub2_chiled_file');
                Route::post('email-sub2', 'email')->name('email_sub2_file');
                Route::post('delete-sub2-file', 'file_destroy')->name('delete_sub2_file');
                Route::get('{id}', 'show')->name('type_of_deduc_files_sub2');
            }
        );
        // TypeOfDedectionFiles2Controller
        Route::group(
            ['prefix' => "Deduction-file/", "controller" => App\Http\Controllers\TypeOfDedectionFiles2Controller::class],
            function () {

                Route::get('{id},{docname},{prntfname},{f1name},{f2name}', 'show_prnt_files')->name('type_of_deduc_files2');
                Route::post('upload-sub-files', 'create')->name('upload_chiled_files2');
                Route::get('download-sub2file/{id}', 'download_sub2_chiled_file')->name('download_chiled_file2');
                Route::post('email-sub2', 'email')->name('email_file2');
                Route::post('delete-sub2-file', 'file_destroy')->name('delete_file2');
                Route::get('{id}', 'show')->name('type_of_deduc_files2_sub2');
            }
        );
        // PriNotiDocFileController
        Route::group(
            ['prefix' => "prenoti-doc-files/", "controller" => App\Http\Controllers\PriNotiDocFileController::class],
            function () {
                Route::post('pri-upload-files', 'store')->name('pri_upload_files');
                Route::post('pri-replace-file', 'replace_file')->name('replace_file');
                Route::get('download-prenoti-file/{id}', 'download_prenoti_file')->name('download_prenoti_file');
                //
                Route::post('prenoti-delete-file', 'permanent_delete')->name('prenoti_file_delete');
                Route::post('prenoti-file-destroy', 'file_destroy')->name('prenoti_file_destroy');
            }
        );
        //
        Route::group(
            ['prefix' => "status-regprac/", "controller" => StatusRegPracController::class],
            function () {
                Route::post('status-regprac/{id?}', 'status_regprac')->name('status_regprac');
                Route::get('regprac-files/{id?}', 'show')->name('regprac_prac');
            }
        );
        Route::group(
            ['prefix' => "status-regprac/", "controller" => App\Http\Controllers\RegPracDocController::class],
            function () {
                Route::post('regprac-file-upload/{id?}', 'store')->name('regprac_file_upload');
                Route::post('reg-multi-files/', 'reg_multi_files')->name('reg_multi_files');
                Route::get('download-regprac-files/{id}', 'download_regprac_files')->name('download_regprac_files');

                Route::get('download/all-files', 'download_regprac_all_files')->name('download_regprac_all_files');
                //
                Route::post('delete-regprac', 'permanent_delete')->name('regprac_file_delete');
                Route::post('destroy-regprac', 'file_destroy')->name('regprac_file_destroy');
            }
        );

        /**
         * Construction Images route
         */
        Route::group(
            ['prefix' => "construction-image/", "controller" => ConstructionSiteImageController::class],
            function () {
                Route::post('{image}/store', 'store')->name('construction_image_store');
                Route::get('{id}/download', 'download')->name('download_image');
                Route::get('{id}/destroy', 'destroy')->name('destroy_image');
                Route::get('image/email/{construction_id?}', 'emailalert')->name('emailalert');
                Route::post('destroy/images/delete', 'destroy_ajax')->name('destroy_image_ajax');
                Route::post('{image}/download', 'download_zip')->name('download_image_zip');
            }
        );
        // scripts
        Route::get('permissionIssue', [ImportController::class, 'permissionIssue']);
        Route::get('missingColumn', [ImportController::class, 'constructionMissingColumnData']);
        Route::get('missingDate', [ImportController::class, 'constructionMissingDate']);
        Route::get('infissi-issue', [ImportController::class, 'recreateInfissi']);
        Route::get('addFotovolticFiles', [ImportController::class, 'addFotovolticFiles']);
        Route::get('user-to-admin', [ImportController::class, 'changeUserRolesToAdmin']);

        Route::group(["middleware" => "role:admin|user", 'auth'], function () {
            /**
             * CSV upload route
             */

            //import script

            Route::group(
                ['prefix' => "files-dublication-of/", "controller" => App\Http\Controllers\ImportController::class],
                function () {
                    Route::get('DocumentiClienti', 'DocumentiClienti');
                    Route::get('PraticheComunali', 'PraticheComunali');
                    Route::get('PraticheComunaliProtocollocilas110', 'PraticheComunaliProtocollocilas110');
                    Route::get('EstrattoDiMappa', 'EstrattoDiMappa');
                    Route::get('NotificaPreliminare', 'NotificaPreliminare');
                    Route::get('RemoveNotificaPreliminare', 'RemoveNotificaPreliminare');


                }
            );


            Route::group(
                ['prefix' => "files-missing-of/", "controller" => App\Http\Controllers\ImportController::class],
                function () {
                    Route::get('saldo', 'add_data_into_chiled');
                }
            );






            Route::get('saldoFiles', [ImportController::class, 'addSaldoFile']);

            Route::get('DeductionSub2file', [ImportController::class, 'DeductionSub2file']);

            Route::get('DeductionSub2filepermission', [ImportController::class, 'DeductionSub2filepermission']);
            Route::get('DeductionSub2subfilepermission', [ImportController::class, 'DeductionSub2subfilepermission']);

            Route::get('ReliefDocPermission', [ImportController::class, 'ReliefDocPermission']);


            Route::get('importConstruction', [ImportController::class, 'import']);







            Route::get('/csv/user', [UploadCSVController::class, 'upload'])->name('csvUpload');


            // Route::get('/csv/material', [UploadCSVController::class, 'addMaterialList'])->name('csvaddMaterialList');
            Route::get('MaterialList', [ImportController::class, 'MaterialList']);

            Route::get('/csv/construction', [UploadCSVController::class, 'addConstructionSite'])->name('csvaddConstructionSite');

            Route::get('/csv/document', [UploadCSVController::class, 'document_and_contacts'])->name('csvdocument_and_contacts');
            Route::get('/csv/property', [UploadCSVController::class, 'property_data'])->name('csvproperty_data');

            //instead of document and property we are run constructionSites this script
            // Route::get('Document/property', [ImportController::class, 'constructionSites']);


            Route::get('/csv/construction_site', [UploadCSVController::class, 'construction_site_settings'])->name('csvconstruction_site_settings');

            // Route::get('/csv/construction_materials', [UploadCSVController::class, 'construction_materials'])->name('csvconstruction_materials');
            // Route::get('/csv/material_assistance', [UploadCSVController::class, 'material_assistance'])->name('csvmaterial_assistance');
            // Route::get('/csv/construction_notes', [UploadCSVController::class, 'construction_notes'])->name('csvconstruction_notes');
            Route::get('ConstructionMaterialScript', [ImportController::class, 'ConstructionMaterialScript']);
            Route::get('assistenzeScript', [ImportController::class, 'assistenzeScript']);
            Route::get('noteScript', [ImportController::class, 'noteScript']);


            Route::get('updatelateststatus', [ImportController::class, 'updatelateststatus']);


            Route::get('/csv/construction_job', [UploadCSVController::class, 'construction_job'])->name('csvconstruction_job');

            Route::get('/csv/filestructure', [UploadCSVController::class, 'filestructure'])->name('csvfilestructure');

            /**
             * All status csv data import
             */
            // Route::get('/csv/status_pre_analyses', [UploadCSVController::class, 'status_pre_analyses'])->name('csvstatus_pre_analyses');
            // Route::get('/csv/status_technicians', [UploadCSVController::class, 'status_technicians'])->name('csvstatus_technicians');
            // Route::get('/csv/status_reliefs', [UploadCSVController::class, 'status_reliefs'])->name('csvstatus_reliefs');
            // Route::get('/csv/status_leg10s', [UploadCSVController::class, 'status_leg10s'])->name('csvstatus_leg10s');
            // Route::get('/csv/status_computations', [UploadCSVController::class, 'status_computations'])->name('csvstatus_computations');
            // Route::get('/csv/status_pr_notis', [UploadCSVController::class, 'status_pr_notis'])->name('csvstatus_pr_notis');
            // Route::get('/csv/status_reg_pracs', [UploadCSVController::class, 'status_reg_pracs'])->name('csvstatus_reg_pracs');
            // Route::get('/csv/status_work_starteds', [UploadCSVController::class, 'status_work_starteds'])->name('csvstatus_work_starteds');
            // Route::get('/csv/status_work_closes', [UploadCSVController::class, 'status_work_closes'])->name('csvstatus_work_closes');
            // Route::get('/csv/status_s_a_l_s', [UploadCSVController::class, 'status_s_a_l_s'])->name('csvstatus_s_a_l_s');
            // Route::get('/csv/status_enea_balances', [UploadCSVController::class, 'status_enea_balances'])->name('csvstatus_enea_balances');
            Route::get('construction/status', [ImportController::class, 'ConstructionStatus']);



            // Route::get('/csv/construction_condominis', [UploadCSVController::class, 'construction_condominis'])->name('csvconstruction_condominis');
            Route::get('/construction_condominis', [ImportController::class, 'importDataToCunstructionCondomeni']);

            /**
             * Upload construction images
             */
            Route::get('/csv/uploadImages', [UploadCSVController::class, 'uploadImages'])->name('csvuploadImages');
            Route::get('/csv/uploadFolder', [UploadCSVController::class, 'uploadFolder'])->name('uploadFolder');


            Route::get('/delete/uploadImages', [UploadCSVController::class, 'deleteimges'])->name('deleteuploadImages');



            // new data

            Route::get('/MaterialsNewRecoards', [ImportController::class, 'MaterialsNewRecoards']);

            /**
             * User route
             */
            Route::group(
                ["controller" => UserController::class],
                function () {
                    // when we migrate on live then un commint this
                    // $roles = ['user'];
                    $roles = Role::pluck('name')->toArray();

                    Route::group(
                        ['prefix' => "{user}/", "controller" => UserController::class],
                        function () {
                            Route::get('create', 'create')->name('createUser');
                            Route::post('add', 'store')->name('addUser');
                        }
                    )->whereIn('user', $roles);

                    Route::get('users/{user?}', 'index')->whereIn('user', $roles)->name('allUsers');
                    Route::put('user-edit/{id}/{role?}', 'update')->whereNumber('id')->name('updateUser');

                    // Delete user route
                    Route::delete('user-delete/{id}', 'destroy')->whereNumber('id')->name('deleteUser');
                    Route::get('business/users', 'business_users')->name('business_users');
                }

            );

            Route::get('report/print', function () {
                return view('report_print');
            })->name('report_print');

            Route::get('reports', [ConstructionSiteController::class, 'all_constructions'])->name('allReports');

            /**
             * technician route
             */
            Route::group(
                ['prefix' => "technician/", "controller" => TechincianDetailController::class],
                function () {
                    Route::get('{id}/details/create', 'create')->whereNumber('id')->name('addTechDetails');
                    Route::post('store_techno_details', 'store')->name('store_techno_details');
                }
            );

            /**
             * Construction site route
             */
            Route::group(
                ['prefix' => "construction-site/page/", "controller" => ConstructionSiteController::class],
                function () {
                    Route::get('create', 'create')->name('shipyard_store');
                    Route::post('construction_site_store', 'store')->name('construction_store');
                    Route::get('construction_edit/{id}', 'edit')->name('construction_edit');
                    Route::get('{id}/set-archive/{archive}', 'set_archive')->name('set_archive');
                    Route::get('{id}/delete-archive', 'destroy')->name('delete_construction');
                    Route::put('{id}/construction_update', 'update')->name('construction_update');
                    Route::put('{id}/construction_update_building', 'update_building')->name('construction_update_building');
                    // get all assistance
                    Route::get('get-assistance', 'get_assistance')->name('get_assistance');
                    Route::get('print/material/{id}', 'print_material')->name('construction_material_print');
                    Route::get('print/assistance/{id}', 'assistance_print')->name('construction_assistance_print');
                    Route::get('assistance/document/{id}/{folder}', 'assistance_document')->name('assistance_document');
                    //Route::get('stampa/{id}', 'stampa')->name('construction_stampa');
                    Route::get('{id}/stampa/{page}', 'print_construction_stampa')->name('print_construction_stampa');
                    Route::post('material/stampa', 'print_construction_stampa_material')->name('print_construction_stampa_material');
                    // essential
                    Route::get('/essential/{id?}', 'essential')->name('essential');
                    Route::get('/essential/saldo/{id?}', 'essentialSaldo')->name('essentialSaldo');
                    //we change it
                    Route::get('/chiavetta/{id?}', 'chiavetta')->name('chiavetta');
                    // download zip
                    Route::get('/download/{slug}', 'zip_chiavetta_or_essential')->name('zip_chiavetta_or_essential');

                    //Route::post('add', 'construction_pin_location')->name('construction_pin_location');

                    //doc-filters
                    Route::get('/doc_fotovoltaico/{id?}', 'doc_fotovoltaico')->name('doc_fotovoltaico');
                    Route::get('/doc_tecnico/{id?}', 'doc_tecnico')->name('doc_tecnico');
                    Route::get('/doc_commercialista/{id?}', 'doc_commercialista')->name('doc_commercialista');
                    Route::get('/doc_chiavetta/{id?}', 'doc_chiavetta')->name('doc_chiavetta');
                    Route::get('/download/doc/{slug}', 'zip_doc_files')->name('zip_doc_files');
                }
            );

            /**
             * Chiavetta files route
             */
            Route::group(
                ['prefix' => "doc_chiavetta/", "controller" => ChiavettaFileController::class],
                function () {
                    Route::get('/{id}/{folder_id}', 'index')->name('show_chiavetta_files');
                    Route::post('upload', 'store')->name('upload_chiavetta_file');
                    Route::post('replace', 'replace_file')->name('replace_chiavetta_file');
                    Route::delete('file_delete', 'destroy')->name('delete_chiavetta_file');
                    Route::get('/download/{folder_id}/{consId}', 'zip_chiavetta_files')->name('download_chiavetta_files');
                    Route::get('delete-all-chiavetta/{folder_id}/{consId}', 'DeleteAllChiavettaFiles')->name('DeleteAllChiavettaFiles');
                }
            );

            /**
             * Construction Condomini route
             */
            Route::group(
                ['prefix' => "construction/condomini/", "controller" => ConstructionCondominiController::class],
                function () {
                    Route::post('getCondomini', 'getCondomini')->name('getCondomini');
                    Route::delete('condo_delete', 'destroy')->name('condo_delete');
                    Route::post('condo_store', 'store')->name('condo_store');
                }
            );

            /**
             * document and contacts route
             */
            Route::group(
                ['prefix' => "shipyard/document-contact/", "controller" => DocumentAndController::class],
                function () {
                    Route::get('create', 'create')->name('document_create');
                    Route::post('store', 'store')->name('document_store');
                }
            );

            /**
             * step3 property data route
             */
            Route::group(
                ['prefix' => "shipyard/property-data/", "controller" => PropertyDataController::class],
                function () {
                    Route::get('create', 'create')->name('property_data_create');
                    Route::post('store', 'store')->name('property_data_store');
                }
            );

            /**
             * step4 construction setting route
             */
            Route::group(
                ['prefix' => "shipyard/construction-setting/", "controller" => ConstructionSiteSettingController::class],
                function () {
                    Route::get('create', 'create')->name('construction_setting_data_create');
                    Route::post('store', 'store')->name('construction_setting_data_store');
                }
            );

            /**
             * Construction Note route
             */
            Route::group(
                ['prefix' => "notes/", "controller" => ConstructionNote::class],
                function () {
                    Route::get('create', 'create')->name('note_create');
                    Route::post('store', 'store')->name('note_store');
                    Route::get('start/{id}', 'click_on_start')->name('click_on_start');
                    Route::get('delete/{id}', 'destroy')->name('destroy');
                    Route::post('search', 'search')->name('search_note');
                }
            );
            // ===================change status
            /**
             * StatusPreAnalysisController  route
             */
            Route::group(
                ['prefix' => "pre-analysis/", "controller" => StatusPreAnalysisController::class],
                function () {
                    Route::post('pre-analysis/{id?}', 'pre_analysis')->name('pre_analysis');
                }
            );
            Route::group(
                ['prefix' => "status-technisan/", "controller" => StatusTechnisan::class],
                function () {
                    Route::post('status-technisan/{id?}', 'status_technisan')->name('status_technisan');
                }
            );
            Route::group(
                ['prefix' => "status-relief/", "controller" => StatusReliefController::class],
                function () {
                    Route::post('status-relief/{id?}', 'status_relief')->name('status_relief');
                    Route::get('{id}', 'show')->name('show_relief_doc');
                    Route::get('download-all-files', 'download_all_file')->name('download_all_file');
                    Route::get('download/relief-folder', 'download_relief_folder')->name('download_relief_folder');
                }
            );

            Route::group(
                ['prefix' => "status-computation/", "controller" => StatusComputationController::class],
                function () {
                    Route::post('status-computation/{id?}', 'status_computation')->name('status_computation');
                }
            );

            Route::group(
                ['prefix' => "status-workstarted/", "controller" => StatusWorkStartedController::class],
                function () {
                    Route::post('status-workstarted/{id?}', 'status_workstarted')->name('status_workstarted');
                }
            );
            Route::group(
                ['prefix' => "status-sal/", "controller" => StatusSALController::class],
                function () {
                    Route::post('status-sal/{id?}', 'status_sal')->name('status_sal');
                }
            );
            Route::group(
                ['prefix' => "status-eneablnc/", "controller" => StatusEneaBalanceController::class],
                function () {
                    Route::post('status-eneablnc/{id?}', 'status_eneablnc')->name('status_eneablnc');
                }
            );
            Route::group(
                ['prefix' => "status-workclose/", "controller" => StatusClosedController::class],
                function () {
                    Route::post('status-workclose/{id?}', 'status_workclose')->name('status_workclose');
                }
            );

            /**
             * Construction Note route
             */
            Route::group(
                ['prefix' => "search/", "controller" => SearchController::class],
                function () {
                    Route::post('centri/shipping', 'centri_search')->name('centri_search');
                    Route::post('get_model_column', 'get_model_column')->name('get_model_column');
                    Route::post('generateReport', 'generateReport')->name('generateReport');
                    Route::post('report/search', 'reportsSearch')->name('reportsSearch');
                    Route::post('get_job_reports', 'get_job_reports')->name('get_job_reports');
                }
            );

            /**
             * Assistances Note route
             */
            Route::get('/assistances', function () {
                return view('assistances');
            })->name('assistances');

            /**
             * Material route
             */


            /**
             * Construction Material route
             */

            // MaterialsAsisstance Controller
            Route::group(
                ['prefix' => "material-assistance", "controller" => App\Http\Controllers\MaterialsAsisstanceController::class],
                function () {
                    //Route::post('add-assistance', 'store')->name('add_assistance');
                    Route::post('delete-assistance', 'delete_assistance')->name('delete_assistance');
                    Route::post('update-assistance', 'update_assistance')->name('update_assistance');
                    // view all assistanse
                    Route::get('view-assistanse', 'show')->name('view_assistanse');

                    // change date
                    Route::post('change-date', 'change_date')->name('change_date');
                    Route::post('skip-this/year', 'skip_this_year')->name('skip_this_year');
                    Route::post('completed', 'completed')->name('completed');
                }
            );
        });
    }
);

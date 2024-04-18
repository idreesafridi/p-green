<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ConstructionSite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\ConstructionSiteImage;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


use Illuminate\Support\Facades\Http;


class UserController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = '';


    public function __construct(Request $request, ConstructionSiteImage $modal)
    {
        $this->_request = $request;

        $this->_modal = $modal;
    }


    /************* login*********************** */
    public function login(Request $request)
    {

        $request->validate([

            'email' => "required|email",
            'password' => "required",
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ' The provide cridentials are incorrect'
            ], 401);
        }

        $token = $user->createtoken('mytoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,

        ], 200);
    }

    /************* show all Active*********************** */
    public function index()
    {

        // $constructionsites = ConstructionSite::where('status', 1)->where('archive', 0)->orWhere('archive', null)->whereNotNull('name')->where('page_status', 4)->get();
        $constructionsites = ConstructionSite::where('status', 1)
            ->where(function ($query) {
                $query->where('archive', 0)
                    ->orWhereNull('archive');
            })
            ->whereNotNull('name')
            ->whereNotNull('surename')
            ->where('page_status', 4)
            ->orderByRaw("CASE
                WHEN surename LIKE 'A%' OR surename LIKE 'a%' THEN 1
                WHEN surename LIKE 'B%' OR surename LIKE 'b%' THEN 2
                WHEN surename LIKE 'C%' OR surename LIKE 'c%' THEN 3
                WHEN surename LIKE 'D%' OR surename LIKE 'd%' THEN 4
                WHEN surename LIKE 'E%' OR surename LIKE 'e%' THEN 5
                WHEN surename LIKE 'F%' OR surename LIKE 'f%' THEN 6
                WHEN surename LIKE 'G%' OR surename LIKE 'g%' THEN 7
                WHEN surename LIKE 'H%' OR surename LIKE 'h%' THEN 8
                WHEN surename LIKE 'I%' OR surename LIKE 'i%' THEN 9
                WHEN surename LIKE 'J%' OR surename LIKE 'j%' THEN 10
                WHEN surename LIKE 'K%' OR surename LIKE 'k%' THEN 11
                WHEN surename LIKE 'L%' OR surename LIKE 'l%' THEN 12
                WHEN surename LIKE 'M%' OR surename LIKE 'm%' THEN 13
                WHEN surename LIKE 'N%' OR surename LIKE 'n%' THEN 14
                WHEN surename LIKE 'O%' OR surename LIKE 'o%' THEN 15
                WHEN surename LIKE 'P%' OR surename LIKE 'p%' THEN 16
                WHEN surename LIKE 'Q%' OR surename LIKE 'q%' THEN 17
                WHEN surename LIKE 'R%' OR surename LIKE 'r%' THEN 18
                WHEN surename LIKE 'S%' OR surename LIKE 's%' THEN 19
                WHEN surename LIKE 'T%' OR surename LIKE 't%' THEN 20
                WHEN surename LIKE 'U%' OR surename LIKE 'u%' THEN 21
                WHEN surename LIKE 'V%' OR surename LIKE 'v%' THEN 22
                WHEN surename LIKE 'W%' OR surename LIKE 'w%' THEN 23
                WHEN surename LIKE 'X%' OR surename LIKE 'x%' THEN 24
                WHEN surename LIKE 'Y%' OR surename LIKE 'y%' THEN 25
                WHEN surename LIKE 'Z%' OR surename LIKE 'z%' THEN 26
                WHEN surename IS NULL THEN 27
                ELSE 28
            END")
            ->orderBy('surename', 'asc')
            ->get();

        return response(
            [
                'users' =>  $constructionsites,
                'message' => 'Show All User Where Status 1',
                'total User' => count($constructionsites)
            ],
            200
        );
    }

    /*************uploading-multi-images*********************** */

    public function uploadImages(Request $request)
    {

        $this->validate($this->_request, [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif',
            'folder' => 'required',
            'construction_site_id' => 'required|exists:construction_sites,id',
        ]);

        $data['folder'] = $this->_request->folder;


        $data['construction_site_id'] =  $request->construction_site_id;

        $data['uploaded_by'] = auth()->id();

        $images = $this->_request->file('images');

        if ($images) {
            foreach ($images as $image) {

                $dimensions = $this->getImageDimensionsFromRequest($image);

                $width = $dimensions['width'];
                $height = $dimensions['height'];

                $data['name'] = $image->getClientOriginalName();
                $path = 'images/' . $data['folder'];
                $data['path'] = $path . '/' . $data['name'];

                // $this->upload_common_func_api_image($data['construction_site_id'], $data['name'], $path, $image);

                $pathStore = $data['construction_site_id'] . '/' . $path . '/' . $data['name'];

                $thunmbnil = $data['construction_site_id'] . '/' . 'thumbnail' . '/' . $data['folder'] . '/' . $data['name'];

                // Create and store a thumbnail
                $thumbnailImage = Image::make($image)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode('jpg', 65);

                Storage::disk('public_uploads')->put($thunmbnil, $thumbnailImage);

                Storage::disk('public_uploads')->put($pathStore, File::get($image));

                $imageUploaded = $this->add($this->_modal, $data);
            }

            $publicPath = public_path();

            // Set the desired permissions (777 in this case)
            $permissions = 0777;

            // Recursively change permissions for files and directories within the public folder
            $this->recursiveChmod($publicPath, $permissions);
                
            return $imageUploaded;
            return response()->json([
                'message' => 'Images uploaded successfully',
            ], 201);

           

            
        } else {
            return response()->json([
                'error' => 'No images were found in the request',
            ], 400);
        }
    }


    private function getImageDimensionsFromRequest($image)
    {
        $tempImage = imagecreatefromstring(file_get_contents($image->getRealPath()));
        $originalWidth = imagesx($tempImage);
        $originalHeight = imagesy($tempImage);

        $targetWidth = 250; // Set your desired width here
        $targetHeight = intval($targetWidth * $originalHeight / $originalWidth);

        imagedestroy($tempImage);

        return ['width' => $targetWidth, 'height' => $targetHeight];
    }

    /*************Logout*********************** */

    public function logout()

    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'successfully logged Out',
        ]);
    }

    /*************delete Image*********************** */

    public function deleteImage($id)
    {
        // Find the image by its ID 
        $image = ConstructionSiteImage::where('id', $id)->first();

        if (!$image) {
            return response()->json([
                'error' => 'Image not found for the given id',
            ], 404);
        }

        Storage::delete($image->path);

        $image->delete();

        return response()->json(
            [
                'message' => 'Image deleted successfully',
            ],
            200
        );
    }

    /*************select folder for images*********************** */

    public function show($id)
    {

        $data = ConstructionSite::findOrfail($id);
        if ($data != null) {
            $imageFolder = $data->ConstructionImagesFolderApi->where('folder', $this->_request->folderName)->where('status', 1);
            return response()->json(['imageFolder' => $imageFolder, 'imageFolderName' => $this->_request->folderName]);
        } else {
            return response()->json(['Sorry no recoard find!']);
        }
    }

    // public function searching()
    //     {
    //         $search_keyword =  $this->_request->SearchKeyword;
    //        $constructionsites = ConstructionSite::where(function ($query) use ($search_keyword) {
    //         $query->where('name', 'LIKE', '%' . $search_keyword . '%')
    //               ->orWhere('surename', 'LIKE', '%' . $search_keyword . '%');
    //     })
    //     ->where('status', 1)
    //     ->where('archive', 0)
    //     ->whereNotNull('name')
    //     ->where('page_status', 4)
    //     ->get();
    //         return response(
    //             [
    //                 'total Results' =>  $constructionsites != null ? count($constructionsites) : '',
    //                 'users' =>  $constructionsites,

    //             ],
    //             200
    //         );
    //     }



    public function deleteMultiImage()
    {

        $imagesId =  $this->_request->ImageId;

        if ($imagesId) {
            foreach ($imagesId as $ImageId) {
                $image = ConstructionSiteImage::findOrfail($ImageId);
                if ($image) {
                    $image->delete();
                }
            }
            return response()->json(
                [
                    'message' => 'All selected Images have been deleted successfully',
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'error' => 'Kindly select at least one Images',
                ],
                400
            );
        }
    }

    public function searching()
    {
        $search_keyword =  $this->_request->SearchKeyword;
        $constructionsites = ConstructionSite::where(function ($query) use ($search_keyword) {
            $query->where('name', 'LIKE', '%' . $search_keyword . '%')
                ->orWhere('surename', 'LIKE', '%' . $search_keyword . '%');
        })
            ->where('status', 1)
            ->where('archive', 0)
            ->whereNotNull('name')
            ->where('page_status', 4)
            ->get();
        return response(
            [
                'total Results' =>  $constructionsites != null ? count($constructionsites) : '',
                'users' =>  $constructionsites,

            ],
            200
        );
    }
}

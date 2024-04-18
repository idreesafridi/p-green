<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConstructionSiteImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;
use Intervention\Image\Facades\Image;

class ConstructionSiteImageController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ConstructionSiteImage $modal)
    {
        $this->_request = $request;
        $this->_modal = $modal;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all = $this->get_all($this->_modal);

        return view('{{ routeName }}', compact('all'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view({{ view_name }});
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate($this->_request, [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        $images = $this->_request->images;
        $data['folder'] = $this->_request->route()->image;

        if ($this->_request->construction_id) {

            $data['construction_site_id'] = $this->_request->construction_id;
        } else {
            $data['construction_site_id'] = $this->session_get("construction_id");
        }

        $data['uploaded_by'] = auth()->id();

        foreach ($images as $image) {

            $dimensions = $this->getImageDimensionsFromRequest($image);
            $width = $dimensions['width'];
            $height = $dimensions['height'];

            $data['name'] = $image->getClientOriginalName();
            $path = 'images/' . $data['folder'];

            $data['path'] = $path . '/' . $data['name'];

            // $this->upload_common_func($data['name'], $path, $image);

            if ($this->_request->construction_id) {
                $constrct_id = $this->_request->construction_id;
            } else {
                $constrct_id = $this->session_get("construction_id");
            }



            $path = $constrct_id . '/' . $path . '/' . $data['name'];
            $thunmbnil = $constrct_id . '/' . 'thumbnail' . '/' . $data['folder'] . '/' . $data['name'];
            // $thumbnailImage = Image::make($image)->fit(250)->encode('jpg', 65);


            // Create and store a thumbnail
            $thumbnailImage = Image::make($image)->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('jpg', 65);

            Storage::disk('public_uploads')->put($thunmbnil, $thumbnailImage);
            Storage::disk('public_uploads')->put($path, File::get($image));

            $imageUploaded = $this->add($this->_modal, $data);
        }

        $name = Auth()->user()->name;
        $cantriName = $imageUploaded->ConstructionSite->name . ' ' . $imageUploaded->ConstructionSite->surename;

        $to = 'gabriele.greengen@gmail.com';
        $subject = 'FOTO CARICATE | ' . $name . ' |' . $cantriName;
        $msg = $name . ' ha caricato delle foto per il cantiere. Accedi al portale per vederle.';

        $this->materialMail($to, $subject, $name, $msg, $data['construction_site_id']);

        $to = 'assistenza.greengen@gmail.com';
        $subject = 'FOTO CARICATE |  ' . $name . ' | ' . $cantriName;
        $msg = $name . ' ha caricato delle foto per il cantiere .' .$cantriName.'. Accedi al portale per vederle.';

        $this->materialMail($to, $subject, $name, $msg,  $data['construction_site_id']);

        $publicPath = public_path();

        // Set the desired permissions (777 in this case)
        $permissions = 0777;

        // Recursively change permissions for files and directories within the public folder
        $this->recursiveChmod($publicPath, $permissions);


        return redirect()->route('construction_detail', ['id' => $data['construction_site_id'], 'pagename' => 'Immagini', 'image' => $data['folder']])->with('success', 'Immagine caricata');
    }


    // Function to calculate image dimensions from image object
    private function getImageDimensionsFromRequest($image) {
        // Suppressing the warning
        error_reporting(E_ERROR | E_PARSE);
    
        $tempImage = imagecreatefromstring(file_get_contents($image->getRealPath()));
    
        // Restoring error reporting
        error_reporting(E_ALL);
    
        $originalWidth = imagesx($tempImage);
        $originalHeight = imagesy($tempImage);
    
        $targetWidth = 250; // Set your desired width here
        $targetHeight = intval($targetWidth * $originalHeight / $originalWidth);
    
        imagedestroy($tempImage);
    
        return ['width' => $targetWidth, 'height' => $targetHeight];
    }
    

    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        return view('{{view_name}}', compact('data'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function download()
    {

        $id = $this->_request->route()->id;
        $data = $this->get_by_id($this->_modal, $id);

        return $this->download_common_func($data->path);
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
        return view('{{view_name}}', compact('data'));
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
        $this->validate($this->_request, [
            'name' => 'required',
        ]);

        $data = $this->_request->except('_token', '_method');

        $data = $this->get_by_id($this->_modal, $id)->update($data);

        return redirect()->route('{{routeName}}.index');
    }

    /**
     * Delete files
     */
    private function delete_file($id, $req = null)
    {
        $data = $this->get_by_id($this->_modal, $id);

        if ($req == 'ajax') {
            foreach ($data as $image) {
                $return['construction_site_id'] = $image->construction_site_id;
                $return['folder'] = $image->folder;

                $image->status = 0;
                $image->update();

                // $this->delete_files($image->path);

                // $image->delete();
            }
        } else {
            $return['construction_site_id'] = $data['construction_site_id'];
            $return['folder'] = $data['folder'];

            $data->status = 0;
            $data->update();

            // $this->delete_files($data->path);

            // $this->_modal->find($id)->delete();
        }

        return $return;
    }

    /**
     * download multiple images in zip file
     */
    public function download_zip()
    {

        $construction_id =    $this->_request->construction_id;

        $data = $this->_request->except('_token');

        $path = 'images/' . request()->route()->image;

        return $this->download_zip_files($this->_modal, $data['image'], $path, $construction_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  {{ model }}  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ok = $this->delete_file($id);

        return redirect()->route('construction_detail', ['id' => $ok['construction_site_id'], 'pagename' => 'Immagini', 'image' => $ok['folder']])->with('success', 'Immagine cancellata');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  {{ model }}  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy_ajax()
    {
        $id = $this->_request->except('_token');

        $this->delete_file($id['data'], 'ajax');

        return response()->json('true');
    }

    public function emailalert($construct_id){
        // if (Auth::check()) {
        //     // User is logged in, redirect to the specified route
            return redirect()->route('home');
              
        // } else {
        //     return redirect()->route('home');
        // }
    }
}

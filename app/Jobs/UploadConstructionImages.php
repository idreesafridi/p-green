<?php

namespace App\Jobs;

use App\Models\ConstructionSite;
use App\Models\ConstructionSiteImage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

use Intervention\Image\Facades\Image;


class UploadConstructionImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $imageContruction = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($imageContruction)
    {
        $this->imageContruction = $imageContruction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $old_image = null;
        $old_image_thumbnail = null;

        $name = $this->imageContruction[2];
        $folder = $this->imageContruction[3];

        $imageContructionPath = str_replace(' _', '_', $this->imageContruction[6]);;

        $id  = ConstructionSite::where('oldid', $this->imageContruction[0])->first();
        $user = User::where('id', (int)$this->imageContruction[1])->first();

        if ($id != null) {
            if ($this->imageContruction[4] == 0) {
                $status = 0;
                $binPathReplace = str_replace('../images_bin/', '', $imageContructionPath);
                $livePath = 'https://greengen.crisaloid.com/images_bin/' . $binPathReplace;
                $response = Http::get($livePath);

                if ($response->successful()) {
                    $directory = 'public/construction-assets/' . $id->id . "/images_bin/" . $folder;
                    $thumnail_directory = 'public/construction-assets/' . $id->id . "/images_bin/" . $folder . "/thumbnail";

                    $path = "images_bin/" . $folder . '/' . $name;
                } else {
                    $binPathReplace = str_replace('../images_bin/', '', $this->imageContruction[6]);
                    $livePath = 'https://greengen.crisaloid.com/images_bin/' . $binPathReplace;
                    $response = Http::get($livePath);

                    if ($response->successful()) {
                        $directory = 'public/construction-assets/' . $id->id . "/images_bin/" . $folder;
                        $thumnail_directory = 'public/construction-assets/' . $id->id . "/images_bin/" . $folder . "/thumbnail";
                        $path = "images_bin/" . $folder . '/' . $name;
                    } else {
                        return false;
                    }
                }
                $livePathThumbnail = null;
                $directoryThumbnail = null;
                $pathThumbnail = null;
            } else {
                $status = 1;
                $livePath = 'https://greengen.crisaloid.com/user_images/' . $imageContructionPath;

                $response = Http::get($livePath);
                if ($response->successful()) {
                    $directory = 'public/construction-assets/' . $id->id . "/images/" . $folder;
                    $thumnail_directory = 'public/construction-assets/' . $id->id . "/thumbnail/" . $folder ;
                    $path = "images/" . $folder . '/' . $name;
                } else {
                    $livePath = 'https://greengen.crisaloid.com/user_images/' . $this->imageContruction[6];
                    $response = Http::get($livePath);
                    if ($response->successful()) {
                        $directory = 'public/construction-assets/' . $id->id . "/images/" . $folder;
                        $thumnail_directory = 'public/construction-assets/' . $id->id . "/thumbnail/" . $folder;
                        $path = "images/" . $folder . '/' . $name;
                    } else {
                        $livePath = 'https://greengen.crisaloid.com/user_images_thumbnail/' . $imageContructionPath;
                        $response = Http::get($livePath);
                        if ($response->successful()) {
                            $directory = 'public/construction-assets/' . $id->id . "/images/" . $folder;
                            $thumnail_directory = 'public/construction-assets/' . $id->id . "/thumbnail/" . $folder;
                            $path = "images/" . $folder . '/' . $name;
                        } else {
                            $livePath = 'https://greengen.crisaloid.com/user_images_thumbnail/' . $this->imageContruction[6];
                            $directory = 'public/construction-assets/' . $id->id . "/images/" . $folder;
                            $thumnail_directory = 'public/construction-assets/' . $id->id . "/thumbnail/" . $folder;
                            $path = "images/" . $folder . '/' . $name;
                        }
                    }

                    // $livePath = 'https://greengen.crisaloid.com/user_images_thumbnail/' . $imageContructionPath;
                    // $directory = 'public/construction-assets/' . $id->id . "/thumbnail/" . $folder;
                    // $path = "thumbnail/" . $folder . '/' . $name;
                }

                // $livePathThumbnail = 'https://greengen.crisaloid.com/user_images_thumbnail/' . $this->imageContruction[6];


                // $old_image = Http::get($livePathThumbnail)->body();
                // dd($old_image);


                // $directoryThumbnail = 'public/construction-assets/' . $id->id . "/thumbnail/images" . $folder;
                // $pathThumbnail = "thumbnail/images/" . $folder . '/' . $name;
            }

          

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            if (!file_exists($thumnail_directory)) {
                mkdir($thumnail_directory, 0755, true);
            }

            try {
                $old_image = Http::get($livePath)->body();
            } catch (\Throwable $th) {
                // dd($th);
            }

            if ($old_image != null) {
                // Upload the image to the new server
                try {
                    // $compressedImage = Image::make($old_image)->encode('jpg', 65); // Adjust the format and quality as needed
                    // dd($compressedImage);
                    $new_filepath = $directory . '/' . basename($livePath);
                    file_put_contents($new_filepath, $old_image);



                    

                    // Create and store a thumbnail
                    // $thumbnail = Image::make($old_image)->encode('jpg', 50); // Adjust the format and quality as needed
                    // $thumbnail = Image::make($old_image)->fit(100, 100)->encode('jpg', 50);
                    // $thumbnail = Image::make($old_image)->fit(100, 100)->encode('jpg', 65); // Adjust the dimensions as needed

                    // // $thumbnail = Image::make($old_image)->fit(100, 100); // Adjust the thumbnail size as needed
                    // $thumbnail_filepath = $thumnail_directory . '/' . basename($livePath);
                    // file_put_contents($thumbnail_filepath, $thumbnail);


                    // Calculate the height based on a fixed width of 250 pixels
                    // $width = 250;
                    // $height = intval(250 * imagesy(imagecreatefromstring($old_image)) / imagesx(imagecreatefromstring($old_image)));

                    // // Create and store a thumbnail with a fixed width of 250 pixels
                    // $thumbnail = Image::make($old_image)->fit($width, $height)->encode('jpg', 65);
                    $width = 250;
                    $height = intval(250 * imagesy(imagecreatefromstring($old_image)) / imagesx(imagecreatefromstring($old_image)));
                    
                    // Create and store a thumbnail with a fixed width of 250 pixels and higher quality
                    $thumbnail = Image::make($old_image)->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->encode('jpg', 65); // Adjust quality (e.g., 90 for higher quality)
                    // Specify the thumbnail file path
                    $thumbnail_filepath = $thumnail_directory . '/' . basename($livePath);

                    // Save the thumbnail to the specified file path
                    file_put_contents($thumbnail_filepath, $thumbnail);










                } catch (\Throwable $th) {
                    dd($th);
                }

                $arr = [
                    'construction_site_id' => $id == null ? null : $id->id,
                    'uploaded_by' => $user == null ? null : $user->id,
                    'name' => basename($livePath) == null ? $name : basename($livePath),
                    'folder'  => $folder,
                    'status' => $status,
                    'version' => (int)$this->imageContruction[5],
                    'path' => $path,
                    'uploaded_on' => $this->imageContruction[7]
                ];

                ConstructionSiteImage::create($arr);
            }

            // ------------------------------------ //
            // if ($directoryThumbnail =! null) {
            //     if (!file_exists($directoryThumbnail)) {
            //         mkdir($directoryThumbnail, 0755, true);
            //     }

            //     try {
            //         // Download the image from the old server
            //         // $old_imasssge = file_get_contents($livePath);

            //         // Download the image from the URL
            //         $old_image_thumbnail = Http::get($livePathThumbnail)->body();
            //     } catch (\Throwable $th) {
            //         // dd($th);
            //     }

            //     if ($old_image_thumbnail != null) {
            //         // Upload the image to the new server
            //         $new_filepath_thumbnail = $directoryThumbnail . '/' . basename($livePathThumbnail);
            //         file_put_contents($new_filepath_thumbnail, $old_image_thumbnail);
            //     }
            // }

            // dd($new_filepath, $old_image);
            // dd('ok', $directory);

        }
    }
}

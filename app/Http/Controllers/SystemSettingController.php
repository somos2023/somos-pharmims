<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\SystemSettingResouce;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SystemSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response([
            'data' => $this->systemInfo()
        ]); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $vdata = $request->validate([
            'name' => 'required|string',
            'title' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        if (isset($request['icon_url'])) {
            $systemSetting = SystemSetting::where('meta_field', 'icon_url')->first();
            $oldValue = $systemSetting->meta_value;
            $relativePath = $this->saveImage($request['icon_url']);
            $systemSetting->meta_value = $relativePath;
           
            if($oldValue){
                $absolutePath = public_path($oldValue);
                File::delete($absolutePath);
            }
            $systemSetting->save();
        } 


        if (isset($request['logo_url'])) {
            $systemSetting = SystemSetting::where('meta_field', 'logo_url')->first();
            $oldValue = $systemSetting->meta_value;
            $relativePath = $this->saveImage($request['logo_url']);
            $systemSetting->meta_value = $relativePath;
           
            if($oldValue){
                $absolutePath = public_path($oldValue);
                File::delete($absolutePath);
            }
            $systemSetting->save();
        } 

        if (isset($request['logo_lg_url'])) {
            $systemSetting = SystemSetting::where('meta_field', 'logo_lg_url')->first();
            $oldValue = $systemSetting->meta_value;
            $relativePath = $this->saveImage($request['logo_lg_url']);
            $systemSetting->meta_value = $relativePath;
           
            if($oldValue){
                $absolutePath = public_path($oldValue);
                File::delete($absolutePath);
            }
            $systemSetting->save();
        } 

        if (isset($request['icon_url'])) {
            $systemSetting = SystemSetting::where('meta_field', 'icon_url')->first();
            $oldValue = $systemSetting->meta_value;
            $relativePath = $this->saveImage($request['icon_url']);
            $systemSetting->meta_value = $relativePath;
           
            if($oldValue){
                $absolutePath = public_path($oldValue);
                File::delete($absolutePath);
            }
            $systemSetting->save();
        } 

      
        $systemInfo = SystemSetting::all();

        foreach ($systemInfo as $item) {
            $metaField = $item->meta_field;

            if (array_key_exists($metaField, $vdata)) {
                $item->meta_value = $vdata[$metaField];

                $item->save();
            }
        }

        return response([
            'message' => 'Save successfully!',
            'data' => $this->systemInfo()
        ], 200);
    }


    private function systemInfo(){
        $data = SystemSetting::all();

        $systemInfo = [];

        foreach ($data as $item) {
            if($item->meta_field == 'logo_url'){
                $item['meta_value'] = $item->meta_value ? URL::to($item->meta_value) : URL::to("defaults/logo.svg");
                $item->meta_field = 'logo';
            } else if ($item->meta_field == 'logo_lg_url'){
                $item['meta_value'] = $item->meta_value ? URL::to($item->meta_value) : URL::to("defaults/pharmims-logo-lg.png");
                $item->meta_field = 'logo_lg';
            } else if ($item->meta_field == 'cover_url'){
                $item['meta_value'] = $item->meta_value ? URL::to($item->meta_value) : URL::to("defaults/cover.svg");
                $item->meta_field = 'cover';
            } else if ($item->meta_field == 'icon_url'){
                $item['meta_value'] = $item->meta_value;
                $item->meta_field = 'icon';
            }
            $systemInfo[$item->meta_field] = $item->meta_value;
        }

        return $systemInfo;
    }

  // foreach ($vdata as $metaField => $value) {
  //           $systemSetting = SystemSetting::where('meta_field', $metaField)->first();
  //           $oldValue = $systemSetting->meta_value;

  //           if (!$systemSetting) {
  //               // If the record does not exist, create a new one
  //               $systemSetting = new SystemSetting();
  //               $systemSetting->meta_field = $metaField;
  //           }

            
  //           $systemSetting->meta_value = $value;
           

  //           // Save the changes or create a new record
  //           $systemSetting->save();

  //       }

    

     // Update or insert system settings
    

    /**
     * Display the specified resource.
     */
    public function show(SystemSetting $systemSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemSetting $systemSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemSetting $systemSetting)
    {
        //
    }

    private function saveImage($image)
    {

        // Check if image is a valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take out the base64 encoded text without the MIME type
            $image = substr($image, strpos($image, ',') + 1);
            // Get file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // Check if the file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'svg', 'ico', 'png'])) {
                throw new \Exception('Invalid image type');
            }

            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }

        } else {
            throw new \Exception('Did not match data URI with image data');
        }


        $dir = 'images/system/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    // private function saveIcon($image)
    // {

    //     // Check if image is a valid base64 string
    //     if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
    //         // Take out the base64 encoded text without the MIME type
    //         $image = substr($image, strpos($image, ',') + 1);
    //         // Get file extension
    //         $type = strtolower($type[1]); // jpg, png, gif

    //         // Check if the file is an image
    //         if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
    //             throw new \Exception('Invalid image type');
    //         }

    //         $image = str_replace(' ', '+', $image);
    //         $image = base64_decode($image);

    //         if ($image === false) {
    //             throw new \Exception('base64_decode failed');
    //         }

    //     } else {
    //         throw new \Exception('Did not match data URI with image data');
    //     }


    //     $dir = 'images/system/';
    //     $file = Str::random() . '.' . $type;
    //     $absolutePath = public_path($dir);
    //     $relativePath = $dir . $file;
    //     if (!File::exists($absolutePath)) {
    //         File::makeDirectory($absolutePath, 0755, true);
    //     }
    //     file_put_contents($relativePath, $image);

    //     return $relativePath;
    // }

    
}

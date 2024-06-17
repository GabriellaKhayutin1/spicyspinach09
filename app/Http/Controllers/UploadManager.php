<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadManager extends Controller
{
    function upload()
    {
        return view("mains.index");
    }

    public function uploadPost(Request $request)
    {
        $destinationPath = "geopackages";
        $files = glob($destinationPath . '/*'); 
        foreach ($files as $file) { 
            if (is_file($file))
                unlink($file); 
        }
        $file = $request->file("file");
    
        $FileName = $file->getClientOriginalName();

        if ($file->move($destinationPath, $FileName)) {
            return redirect()->back();
        } else {
            echo "Error uploading file";
        }
    }

    public function display()
    {
        return view('mains.index');
    }


    public function fetch()
    {
        $directory = public_path('geopackages');
        $files = File::files($directory);
        $fileName = count($files) > 0 ? $files[0]->getFilename() : null;

        return response()->json(['file' => $fileName]);
    }
}

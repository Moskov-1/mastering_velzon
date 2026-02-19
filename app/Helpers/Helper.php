<?php

use App\Models\QR;
use Carbon\Carbon;
use App\Models\Location;
use Illuminate\Support\Str;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCodeBuilder;
	
    function getDurationType(?string $data): ?string {
        if (!$data) {
            return null;
        }
        $allowed = [
        'hour' => 'hour', 
        'hora' => 'hour', 
        'horas' => 'hour', 
        'day' => 'day', 
        'dias' => 'day',
        ];
        $parts = explode(' ', trim($data));

        if (count($parts) < 2) {
            return null;
        }

        $unit = strtolower($parts[count($parts) - 1]);

        if ($unit === 'hours' || $unit === 'hour') {
            $unit = 'horas';
        } elseif ($unit === 'days' || $unit === 'day') {
            $unit = 'dias';
        }

        return $allowed[$unit];
    }

    function getDuration(?string $data): ?int {
        if (!$data) {
            return null;
        }

        $parts = explode(' ', trim($data));

        if (empty($parts)) {
            return null;
        }

        $number = $parts[0];

        if (!is_numeric($number)) {
            return null;
        }

        return (int) $number;
    }
    
    function getExpiryDate($date, $start_time, $duration) {
        $datetime = new DateTime($date . ' ' . $start_time);
        Log::info('Initial datetime: ', [$datetime]); 
        
        // Parse duration: e.g., "1 hour", "2 hours", "1 hora", "2 horas", "1 day", "2 dias"
        // Updated regex to match both English and Spanish, singular and plural
        if (preg_match('/^(\d+)\s+(hour|hours|hora|horas|day|days|dia|dias)$/i', trim($duration), $matches)) {
            $amount = (int)$matches[1];
            $unit = strtolower($matches[2]);
            Log::info('Duration amount: ' . $amount . ', unit: ' . $unit);
            
            // Add the interval
            if (in_array($unit, ['hour', 'hours', 'hora', 'horas'])) {
                Log::info('Adding hours: ' . $amount);
                $datetime->add(new DateInterval("PT{$amount}H"));
            } elseif (in_array($unit, ['day', 'days', 'dia', 'dias'])) {
                Log::info('Adding days: ' . $amount);
                $datetime->add(new DateInterval("P{$amount}D"));
            }
            
            Log::info('New datetime after addition: ', [$datetime]);
        } else {
            Log::warning('Invalid duration format: ' . $duration);
            return null; // or throw an exception
        }

        return $datetime->format('Y-m-d H:i:s');
    }
	function formatOnTimezone($dateTime, $timezone) {
        if (!$dateTime) return '';

        $dt = Carbon::parse($dateTime);
        $tz = new DateTimeZone($timezone);
        $dt->setTimezone($tz);

        // Format date
        $formattedDate = $dt->format('M j, Y, g:i A'); // e.g., Jan 11, 2026, 12:11 AM

        // Get offset in seconds
        $offset = $tz->getOffset($dt);
        $hours = intval($offset / 3600);
        $minutes = abs(($offset % 3600) / 60);

        // Build GMT±X or GMT±X:30
        if ($minutes === 0) {
            $gmt = "GMT" . ($hours >= 0 ? '+' : '') . $hours;
        } else {
            $sign = $hours >= 0 ? '+' : '-';
            $absHours = abs($hours);
            $gmt = "GMT{$sign}{$absHours}:";
            $gmt .= $minutes < 10 ? "0{$minutes}" : $minutes;
        }

        return "{$formattedDate} {$gmt}";
    }

    function getValidationType(): string{
        return "validationError";
    }
    function getErrorHeader(): string{
        return "errorType";  // regularError, authError
    }

    function storage_raw_path(string $path): string
    {
        // Decode URL in case it's encoded
        $path = urldecode($path);

        // Remove scheme + domain if present
        $path = preg_replace('#^https?://[^/]+/#', '', $path);

        // Remove "storage/" or "public/" prefix if present
        $path = preg_replace('#^(storage|public)/#', '', $path);

        return ltrim($path, '/');
    }

    function closestRegion($lat, $lng, $listing){
        $closestRegion = Location::where('type', 'region')
            ->select('*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$lat, $lng, $lat]
            )
            ->orderBy('distance')
            ->first();

        if ($closestRegion) {
            $listing->region()->sync([$closestRegion->id]);
        }
    }
    
    function getLists($query, $limit = 3){
        return $query->with([
                'cover:listing_id,media_url',
                'category:id,name',
                'regionOne:name,slug',
                'favorite' => function ($query) {
                    $query->where('user_id', auth()->id())
                          ->select('listing_id', 'is_fav');
                }
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->select(['id','title','category_id','base_price'])
            ->limit($limit)
            ->get()
            ->map(function ($listing) {
                $listing->is_fav = (bool)($listing->favorite->is_fav ?? false);
                unset($listing->favorite);

                $listing->region_one = $listing->regionOne->first() ?? null;
                unset($listing->regionOne);

                $listing->category_name = $listing->category->name ?? null;
                unset($listing->category);

                return $listing;
            });
    }
    function uploadImage($file, $folder, $name): string
    {
        $imageName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('public_uploads/' . $folder), $imageName);
        $path = 'public_uploads/' . $folder . $imageName;
        return $path;
    }
    
    function Base64Img($logoPath){
        
        // Check if path is empty or null
        if (empty($logoPath)) {
            Log::warning('Base64Img: Empty logo path provided');
            return null;
        }
        
        // Check if it's a directory
        if (is_dir($logoPath)) {
            Log::error('Base64Img: Path is a directory, not a file', ['path' => $logoPath]);
            return null;
        }
        
        // Check if file exists
        if (!file_exists($logoPath)) {
            Log::error('Base64Img: File does not exist', ['path' => $logoPath]);
            return null;
        }
        
        try {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        } catch (\Exception $e) {
            Log::error('Base64Img: Failed to read file', [
                'path' => $logoPath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    function carbonParse($date){
        return Carbon::parse($date);
    }
    function format_currency($amount) {
        return '$' . number_format($amount, 2);
    }

    // Get status color
    function status_color($status) {
        return match($status) {
            'confirmed' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }
    
    function showQr($qrId){
        $qr = QR::findOrFail($qrId);

        // Generate a QR image (SVG by default)
        $svg = QrCode::size(300)->generate($qr->QR);

        // Return image response
        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml');
    }

    function DBDateFormatter($date, ?string $givenFormat = 'd-m-Y', ?bool $isDate = true){
        if (empty($date)) {
            return null;
        }

        try {
            $carbon = Carbon::createFromFormat($givenFormat, $date);

            return $isDate
                ? $carbon->format('Y-m-d')
                : $carbon->format('Y-m-d H:i:s');

        } catch (\Exception $e) {
            return null; // Invalid date format
        }
    }

    function parseDate($date, ?string $format = 'd-m-Y' ){
        // return Carbon::createFromFormat('d-m-Y', $date);
        if (empty($date)) {
            return null;
        }
         try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return null; // Handle invalid date input gracefully
        }
    }
    function FetchDate($date, string $format='d-m-Y'){
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $date)->format($format);
        } catch (\Exception $e) {
            return null;
        }
    }

    function isLinkedStorage(){
        return env('APP_LINKED_LOCAL_STORAGE', false);
    }
    //! File or Image Upload
    function removeSpaces($string) {
        return str_replace(' ', '', $string);
    }
    function getStatusHTML($data, $backgroundColor, $sliderTranslateX){
        $sliderStyles     = "position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: transform 0.3s ease; transform: translateX($sliderTranslateX);";
        $status = '<div class="form-check form-switch" style="margin-left:40px; position: relative; width: 50px; height: 24px; background-color: ' . $backgroundColor . '; border-radius: 12px; transition: background-color 0.3s ease; cursor: pointer;">';
        $status .= '<input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status" style="position: absolute; width: 100%; height: 100%; opacity: 0; z-index: 2; cursor: pointer;">';
        $status .= '<span style="' . $sliderStyles . '"></span>';
        $status .= '<label for="customSwitch' . $data->id . '" class="form-check-label" style="margin-left: 10px;"></label>';
        $status .= '</div>';

        return $status;

    }
    
    function getPageStatus(string $url , $text=null){
        if($text)
        return Route::is($url) ? $text : '';
        return Route::is($url) ? 'active' : '';
    }

    function public_fileUpdate($file, string $folder, ?string $old = null, $option = null){
        if($old){
            fileDelete($old);
        }
        return fileUpload($file,  $folder, $option);
    }
     function public_fileUpload($file, string $folder, ?string $option = null): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Generate clean unique filename
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slugName     = Str::slug($originalName);
        $imageName    = $slugName . '-' . uniqid() . '.' . $file->extension();

        // Define storage path
        $uploadPath = public_path('public_uploads/' . $folder);
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Full file path
        $filePath = $uploadPath . '/' . $imageName;

        // Resize / process image
        $img = Image::make($file)
            ->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

        // Optionally apply other operations
        if ($option === 'thumb') {
            $img->resize(100, 100);
        }

        $img->save($filePath, 90);

        // Return relative path (useful for DB & display)
        return 'public_uploads/' . $folder . '/' . $imageName;
    }
    function public_fileDelete(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    function fileDelete($path){
        app(ImageService::class)->delete($path);
    }
    function fileUpdate($file, string $folder, ?string $oldPath, $disk='public', ?string $option = null): ?string {
       
        if($oldPath ) {
            return app(ImageService::class)->update($file, $folder, $oldPath);
        }

        return fileUpload($file, $folder,$disk, option: $option);
    }   
    
    function fileUpload($file, string $folder, $disk = 'public', ?string $option = null)  {

        return app(ImageService::class)->upload($file, $folder);
        
    }
        
    function fileUpload_working($file, string $folder, $disk = 'public', ?string $option = null): ?string {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Generate clean unique filename
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slugName     = Str::slug($originalName);
        $extension    = $file->extension();
        $fileName     = $slugName . '-' . uniqid() . '.' . $extension;

        // Detect if it's an image
        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        // Check if storage is linked
        $isLinked = isLinkedStorage();

        $finalPath = $folder . '/' . $fileName;

        if ($isImage) {
            // Process image with Intervention
            $img = Image::make($file)->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            if ($option === 'thumb') {
                $img->resize(100, 100);
            }

            // Encode image (90% quality)
            $imageData = (string) $img->encode(null, 90);

            // Store using Storage facade (no UploadedFile wrapping needed)
            if ($isLinked) {
                Storage::disk($disk)->put($finalPath, $imageData);
                return 'storage/' . $finalPath;
            } else {
                // Fallback: save to public/uploads
                $uploadPath = public_path('uploads/' . $folder);
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                file_put_contents($uploadPath . '/' . $fileName, $imageData);
                return 'uploads/' . $folder . '/' . $fileName;
            }
        } else {
            // Non-image: store directly
            if ($isLinked) {
                $path = $file->storeAs($folder, $fileName, $disk);
                return 'storage/' . $path;
            } else {
                $uploadPath = public_path('uploads/' . $folder);
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $file->move($uploadPath, $fileName);
                return 'uploads/' . $folder . '/' . $fileName;
            }
        }
    }

    
    //! File or Image Delete
    function fileDelete_working(?string $path): void {
        if (!$path) return;

        // Normalize slashes just in case
        $path = str_replace('\\', '/', $path);

        // If it’s a storage file (e.g. "storage/profile/...") 
        if (str_starts_with($path, 'storage/')) {
            $storagePath = str_replace('storage/', '', $path);
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }
        } 
        // If it’s a public/uploads file
        elseif (str_starts_with($path, 'uploads/')) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    function fileUpload2($file, string $folder, ?string $option = null): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Generate clean unique filename
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slugName     = Str::slug($originalName);
        $imageName    = $slugName . '-' . uniqid() . '.' . $file->extension();

        // Define storage path
        $uploadPath = public_path('public_uploads/' . $folder);
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Full file path
        $filePath = $uploadPath . '/' . $imageName;

        // Resize / process image
        $img = Image::make($file)
            ->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

        // Optionally apply other operations
        if ($option === 'thumb') {
            $img->resize(100, 100);
        }

        $img->save($filePath, 90);

        // Return relative path (useful for DB & display)
        return 'public_uploads/' . $folder . '/' . $imageName;
    }



    //! Generate Slug
    function makeSlug($model, string $title): string
    {
        $slug = Str::slug($title);
        while ($model::where('slug', $slug)->exists()) {
            $randomString = Str::random(5);
            $slug         = Str::slug($title) . '-' . $randomString;
        }
        return $slug;
    }

    //! JSON Response
    function jsonResponse(bool $status, string $message, int $code, $data = null, bool $paginate = false, $paginateData = null): JsonResponse
    {
        $response = [
            'status'  => $status,
            'message' => $message,
            'code'    => $code,
        ];

        if ($paginate && !empty($paginateData)) {
            $response['data'] = $data;
            $response['pagination'] = [
                'current_page' => $paginateData->currentPage(),
                'last_page' => $paginateData->lastPage(),
                'per_page' => $paginateData->perPage(),
                'total' => $paginateData->total(),
                'first_page_url' => $paginateData->url(1),
                'last_page_url' => $paginateData->url($paginateData->lastPage()),
                'next_page_url' => $paginateData->nextPageUrl(),
                'prev_page_url' => $paginateData->previousPageUrl(),
                'from' => $paginateData->firstItem(),
                'to' => $paginateData->lastItem(),
                'path' => $paginateData->path(),
            ];
        } elseif ($paginate && !empty($data)) {
            $response['data'] = $data->items();
            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'first_page_url' => $data->url(1),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'path' => $data->path(),
            ];
        } elseif ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    function jsonErrorResponse(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        $response = [
            'status'  => false,
            'message' => $message,
            'code'    => $code,
            'errors'  => $errors,
        ];
        return response()->json($response, $code);
    }

    // Add this method in your ChatController
    function validationError($errors)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors'  => $errors,
        ], 422); // 422 is HTTP status for Unprocessable Entity
    }

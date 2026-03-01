<?php

use App\Models\Location;
use App\Models\QR;
use App\Services\ImageServiceV2;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
	
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
    

    // Get status color
    function status_color($status) {
        return match($status) {
            'confirmed' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }
    

    function timeFormatter($date, ?string $givenFormat = 'd-m-Y', ?bool $onlyDate = true){
        if (empty($date)) {
            return null;
        }

        try {
            $carbon = Carbon::createFromFormat($givenFormat, $date);

            return $onlyDate
                ? $carbon->format('Y-m-d')
                : $carbon->format('Y-m-d H:i:s');

        } catch (\Exception $e) {
            return null; // Invalid date format
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

    

    function fileDelete($path){
        app(ImageServiceV2::class)->delete($path);
    }
    function fileUpdate($file, string $folder, ?string $oldPath, $disk='public', ?string $option = null): ?string {
       
        if($oldPath ) {
            return app(ImageServiceV2::class)->update($file, $folder, $oldPath);
        }

        return fileUpload($file, $folder,$disk, option: $option);
    }   
    
    function fileUpload($file, string $folder, $disk = 'public', ?string $option = null)  {

        return app(ImageServiceV2::class)->upload($file, $folder);
        
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
    function jsonResponse(bool $success, string $message, int $code, $data = null, bool $paginate = false, $paginateData = null): JsonResponse
    {
        $response = [
            'success'  => $success,
            'message' => $message,
            'status_code'    => $code,
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
            'success'  => false,
            'message' => $message,
            'status_code'    => $code,
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    use LogsActivity;

    /**
     * Display media gallery page.
     */
    public function index(Request $request)
    {
        // Automatically sync files on disk with media_items DB
        $this->syncUploadsFolder();

        $folder = $request->input('folder', 'all');
        $search = $request->input('search');

        $query = MediaItem::orderBy('created_at', 'desc');

        if ($search) {
            $query->where('filename', 'like', "%{$search}%");
        }

        switch ($folder) {
            case 'product-images':
                $query->where('file_path', 'like', '%uploads/products/%')->where('file_type', 'image');
                break;
            case 'sliders':
                $query->where('file_path', 'like', '%uploads/sliders/%')->where('file_type', 'image');
                break;
            case 'reviews':
                $query->where('file_path', 'like', '%uploads/reviews/%')->where('file_type', 'image');
                break;
            case 'settings':
                $query->where('file_path', 'like', '%uploads/settings/%')->where('file_type', 'image');
                break;
            case 'videos':
                $query->where('file_type', 'video');
                break;
            case 'unsplash':
                $query->whereNotNull('url');
                break;
            case 'trash':
                $query->where('id', '=', 0); // Mock empty trash
                break;
        }

        $mediaItems = $query->paginate(18)->withQueryString();
        
        $totalSizeBytes = MediaItem::sum('file_size') ?: 0;
        $totalSizeFormatted = $this->formatSize($totalSizeBytes);

        $counts = [
            'all' => MediaItem::count(),
            'product-images' => MediaItem::where('file_path', 'like', '%uploads/products/%')->where('file_type', 'image')->count(),
            'sliders' => MediaItem::where('file_path', 'like', '%uploads/sliders/%')->where('file_type', 'image')->count(),
            'reviews' => MediaItem::where('file_path', 'like', '%uploads/reviews/%')->where('file_type', 'image')->count(),
            'settings' => MediaItem::where('file_path', 'like', '%uploads/settings/%')->where('file_type', 'image')->count(),
            'videos' => MediaItem::where('file_type', 'video')->count(),
            'unsplash' => MediaItem::whereNotNull('url')->count(),
            'trash' => 0
        ];

        return view('admin.media.index', compact('mediaItems', 'totalSizeFormatted', 'folder', 'counts', 'search'));
    }

    /**
     * Scan disk and sync missing uploads with database items.
     */
    private function syncUploadsFolder()
    {
        $uploadsPath = public_path('uploads');
        if (!file_exists($uploadsPath)) {
            return;
        }

        try {
            $directoryIterator = new \RecursiveDirectoryIterator($uploadsPath, \RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($directoryIterator);

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $filePath = $file->getPathname();
                    $relativePath = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $filePath);
                    $relativePath = str_replace('\\', '/', $relativePath);

                    // Check if already exists in DB
                    $exists = MediaItem::where('file_path', $relativePath)
                        ->orWhere('file_path', '/' . $relativePath)
                        ->exists();

                    if (!$exists) {
                        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        $fileType = in_array($extension, ['mp4', 'webm', 'mov', 'avi']) ? 'video' : 'image';
                        
                        MediaItem::create([
                            'filename' => basename($filePath),
                            'file_path' => $relativePath,
                            'file_size' => $file->getSize(),
                            'file_type' => $fileType,
                            'url' => null
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Silence exceptions in case directories cannot be iterated due to permissions
        }
    }

    /**
     * Helper to format bytes to human readable sizes.
     */
    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }


    /**
     * Store manual file upload.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            
            $fileType = in_array($extension, ['mp4', 'mov', 'avi', 'webm', 'mkv']) ? 'video' : 'image';
            
            $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            
            $targetFolder = $request->input('target_folder');
            if ($fileType === 'video') {
                $folder = 'uploads/videos';
            } else {
                if ($targetFolder === 'sliders') {
                    $folder = 'uploads/sliders';
                } elseif ($targetFolder === 'reviews') {
                    $folder = 'uploads/reviews';
                } elseif ($targetFolder === 'settings') {
                    $folder = 'uploads/settings';
                } else {
                    // Check referrer header to auto-route uploads from specific pages
                    $referer = $request->header('referer', '');
                    if (str_contains($referer, '/admin/homepage')) {
                        $folder = 'uploads/sliders';
                    } elseif (str_contains($referer, '/admin/settings')) {
                        $folder = 'uploads/settings';
                    } elseif (str_contains($referer, '/admin/reviews')) {
                        $folder = 'uploads/reviews';
                    } else {
                        $folder = 'uploads/products';
                    }
                }
            }
            
            // Ensure folder directory exists physically
            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0777, true);
            }
            
            $file->move(public_path($folder), $filename);
            
            // Compress image in-place if supported
            if ($fileType === 'image') {
                \App\Services\ImageOptimizerService::optimize(public_path($folder . '/' . $filename));
            }
            
            $media = MediaItem::create([
                'filename' => $originalName,
                'file_path' => '/' . $folder . '/' . $filename,
                'file_type' => $fileType,
                'file_size' => filesize(public_path($folder . '/' . $filename)),
            ]);

            self::logActivity('media_upload', "Uploaded media file {$originalName}");

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'media' => $media
                ]);
            }

            return back()->with('success', 'Media file uploaded successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fetch media items via AJAX for picker modal.
     */
    public function listAjax(Request $request)
    {
        $type = $request->input('type'); // 'image' or 'video' or null
        
        $query = MediaItem::orderBy('created_at', 'desc');
        if ($type) {
            $query->where('file_type', $type);
        }
        
        $media = $query->paginate(12);

        // Append a full_url field so the JS picker can use absolute URLs for <img src>
        // regardless of which admin page the picker is opened from.
        $items = collect($media->items())->map(function ($item) {
            $data = $item->toArray();
            // Use the external URL if stored, otherwise build from APP_URL + file_path
            if (!empty($item->url) && filter_var($item->url, FILTER_VALIDATE_URL)) {
                $data['full_url'] = $item->url;
            } else {
                $cleanPath = ltrim($item->file_path, '/');
                $data['full_url'] = asset($cleanPath);
            }
            return $data;
        })->values()->all();

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'next_page_url' => $media->nextPageUrl()
        ]);
    }

    /**
     * Import media file from external URL.
     */
    public function storeFromUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->input('url');

        try {
            // Get file contents using Laravel HTTP client to spoof User-Agent (avoids blocks by Cloudinary/CDNs)
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->get($url);

            if (!$response->successful()) {
                throw new \Exception("Could not retrieve file from URL. Server responded with status " . $response->status());
            }

            $contentType = $response->header('Content-Type') ?? '';
            if (is_array($contentType)) {
                $contentType = end($contentType);
            }

            $isImg = str_contains($contentType, 'image');
            $isVid = str_contains($contentType, 'video');

            if (!$isImg && !$isVid) {
                // fallback extension check if headers do not specify content type
                $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                $isImg = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif']);
                $isVid = in_array($extension, ['mp4', 'mov', 'webm', 'avi']);
            }

            if (!$isImg && !$isVid) {
                throw new \Exception("Provided URL does not link to a valid image or video file.");
            }

            $fileType = $isVid ? 'video' : 'image';
            
            // Get contents
            $contents = $response->body();
            if (empty($contents)) {
                throw new \Exception("File content retrieved from URL is empty.");
            }

            // Extract filename from URL
            $urlPath = parse_url($url, PHP_URL_PATH);
            $originalName = basename($urlPath) ?: 'imported_file_' . time();
            $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: ($fileType === 'video' ? 'mp4' : 'jpg');
            
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
            $folder = $fileType === 'video' ? 'uploads/videos' : 'uploads/products';
            
            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0755, true);
            }

            file_put_contents(public_path($folder . '/' . $filename), $contents);

            $media = MediaItem::create([
                'filename' => $originalName,
                'file_path' => '/' . $folder . '/' . $filename,
                'file_type' => $fileType,
                'file_size' => filesize(public_path($folder . '/' . $filename)),
                'url' => $url
            ]);

            self::logActivity('media_url_import', "Imported media from URL: {$url}");

            return response()->json([
                'status' => 'success',
                'media' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete media item.
     */
    public function destroy($id)
    {
        $media = MediaItem::findOrFail($id);

        try {
            if ($media->file_path && file_exists(public_path($media->file_path))) {
                @unlink(public_path($media->file_path));
            }

            $filename = $media->filename;
            $media->delete();

            self::logActivity('media_delete', "Deleted media file {$filename}");

            return back()->with('success', 'Media item deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete media item: ' . $e->getMessage());
        }
    }

    /**
     * Compress all existing images in the media library.
     */
    public function compressAll()
    {
        $images = MediaItem::where('file_type', 'image')->get();
        $count = 0;
        $totalSavedBytes = 0;

        foreach ($images as $media) {
            $path = public_path(ltrim($media->file_path, '/'));
            if (file_exists($path)) {
                $oldSize = filesize($path);
                
                // Compress image in-place
                $optimized = \App\Services\ImageOptimizerService::optimize($path);
                
                if ($optimized) {
                    clearstatcache();
                    $newSize = filesize($path);
                    $saved = $oldSize - $newSize;
                    if ($saved > 0) {
                        $totalSavedBytes += $saved;
                    }
                    
                    // Update database record
                    $media->update([
                        'file_size' => $newSize
                    ]);
                    $count++;
                }
            }
        }

        $savedSizeFormatted = $this->formatSize($totalSavedBytes);

        if ($count > 0 && $totalSavedBytes > 0) {
            self::logActivity('media_compress_all', "Compressed {$count} images, saving {$savedSizeFormatted}");
            return back()->with('success', "Success! Compressed {$count} images and saved {$savedSizeFormatted} of disk space.");
        }
        
        if (!extension_loaded('gd')) {
            return back()->with('warning', "No images compressed because PHP GD library extension is not enabled in this environment. Once you upload this to Hostinger, it will work perfectly!");
        }

        return back()->with('info', "All images are already fully compressed and optimized!");
    }

    /**
     * Compress selected media items via AJAX.
     */
    public function bulkCompress(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No items selected.'], 400);
        }

        $mediaItems = MediaItem::whereIn('id', $ids)->where('file_type', 'image')->get();
        $count = 0;
        $totalSavedBytes = 0;

        foreach ($mediaItems as $media) {
            $path = public_path(ltrim($media->file_path, '/'));
            if (file_exists($path)) {
                $oldSize = filesize($path);
                
                // Compress image in-place
                $optimized = \App\Services\ImageOptimizerService::optimize($path);
                
                if ($optimized) {
                    clearstatcache();
                    $newSize = filesize($path);
                    $saved = $oldSize - $newSize;
                    if ($saved > 0) {
                        $totalSavedBytes += $saved;
                    }
                    
                    // Update database record
                    $media->update([
                        'file_size' => $newSize
                    ]);
                    $count++;
                }
            }
        }

        $savedSizeFormatted = $this->formatSize($totalSavedBytes);

        if ($count > 0 && $totalSavedBytes > 0) {
            self::logActivity('media_bulk_compress', "Compressed {$count} selected images, saving {$savedSizeFormatted}");
            return response()->json([
                'status' => 'success',
                'message' => "Success! Compressed {$count} selected images and saved {$savedSizeFormatted}."
            ]);
        }

        if (!extension_loaded('gd')) {
            return response()->json([
                'status' => 'error',
                'message' => "PHP GD extension is not loaded in this local environment. Upload to Hostinger to activate compression."
            ], 400);
        }

        return response()->json([
            'status' => 'info',
            'message' => "Selected images are already fully optimized!"
        ]);
    }

    /**
     * Delete selected media items via AJAX.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No items selected.'], 400);
        }

        $mediaItems = MediaItem::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($mediaItems as $media) {
            if ($media->file_path && file_exists(public_path($media->file_path))) {
                @unlink(public_path($media->file_path));
            }
            $media->delete();
            $count++;
        }

        if ($count > 0) {
            self::logActivity('media_bulk_delete', "Deleted {$count} media items");
            return response()->json([
                'status' => 'success',
                'message' => "Successfully deleted {$count} media items."
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to delete selected items.'], 400);
    }

    /**
     * Compress a single image item via AJAX.
     */
    public function singleCompress($id)
    {
        $media = MediaItem::findOrFail($id);
        $path = public_path(ltrim($media->file_path, '/'));
        
        if (!file_exists($path)) {
            return response()->json(['status' => 'error', 'message' => 'File not found on disk.'], 404);
        }
        
        if ($media->file_type !== 'image') {
            return response()->json(['status' => 'error', 'message' => 'Compression only supported for images.'], 400);
        }

        $oldSize = filesize($path);
        
        // Optimise image
        $optimized = \App\Services\ImageOptimizerService::optimize($path);
        
        if ($optimized) {
            clearstatcache();
            $newSize = filesize($path);
            $savedBytes = $oldSize - $newSize;
            $percent = $oldSize > 0 ? round(($savedBytes / $oldSize) * 100) : 0;
            
            // Update database record
            $media->update([
                'file_size' => $newSize
            ]);

            return response()->json([
                'status' => 'success',
                'old_size' => $this->formatSize($oldSize),
                'new_size' => $this->formatSize($newSize),
                'saved_percent' => $percent,
                'saved_formatted' => $this->formatSize($savedBytes)
            ]);
        }

        return response()->json([
            'status' => 'info',
            'message' => 'Image is already optimized.'
        ]);
    }
}


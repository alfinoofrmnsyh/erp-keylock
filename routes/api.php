<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AboutpageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TestimonialController;

/*
|--------------------------------------------------------------------------
| Public API Routes (Throttled for DoS & Scraping protection - A04:2021)
|--------------------------------------------------------------------------
*/
Route::middleware(['throttle:60,1'])->group(function () {

    // Safe Image Serving with Path Traversal Protection (A01:2021)
    Route::get('/image/{filename}', function (string $filename) {
        $safeName = basename($filename);

        // Disallow path traversal characters
        if (empty($safeName) || $safeName !== $filename) {
            abort(400, 'Invalid filename format.');
        }

        $allowedFolders = ['products', 'galleries', 'categories', 'partners'];

        foreach ($allowedFolders as $folder) {
            $filePath = storage_path("app/public/{$folder}/{$safeName}");
            if (is_file($filePath)) {
                return response()->file($filePath);
            }
        }

        abort(404, 'File tidak ditemukan');
    });

    // Content & Catalog Endpoints
    Route::get('/about', [AboutpageController::class, 'index']);
    Route::get('/contact', [ContactController::class, 'index']);
    Route::get('/partners', [PartnerController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{slug}', [ProductController::class, 'show']);
    });

    Route::prefix('galleries')->group(function () {
        Route::get('/', [GalleryController::class, 'index']);
        Route::get('/{id}', [GalleryController::class, 'show']);
    });

    Route::prefix('testimonials')->group(function () {
        Route::get('/', [TestimonialController::class, 'index']);
        Route::get('/{id}', [TestimonialController::class, 'show']);
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated User Route
|--------------------------------------------------------------------------
*/
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
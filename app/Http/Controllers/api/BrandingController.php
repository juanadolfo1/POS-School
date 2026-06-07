<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Models\SchoolConfig;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    /**
     * Endpoint público: devuelve la configuración de branding de la escuela.
     */
    public function index()
    {
        try {
            $config = SchoolConfig::first();

            if (!$config) {
                return response()
                    ->json(ApiResponse::success('No branding configured', [
                        'school_name' => 'Escuela',
                        'favicon_url' => null,
                        'logo_url' => null,
                    ]))->setStatusCode(200);
            }

            return response()
                ->json(ApiResponse::success('Branding retrieved', [
                    'school_name' => $config->school_name,
                    'favicon_url' => $config->favicon_path ? asset('storage/' . $config->favicon_path) : null,
                    'logo_url' => $config->logo_path ? asset('storage/' . $config->logo_path) : null,
                ]))->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching branding: ' . $e->getMessage());
            return response()
                ->json(ApiResponse::internalError('Failed to fetch branding', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    /**
     * Actualiza la configuración de branding (nombre, favicon, logo).
     */
    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'nullable|string|max:128',
            'favicon' => 'nullable|file|mimes:ico,png|max:512',
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        try {
            $config = SchoolConfig::firstOrCreate([], ['school_name' => 'Escuela']);

            if ($request->school_name) {
                $config->school_name = $request->school_name;
            }

            if ($request->hasFile('favicon')) {
                if ($config->favicon_path) {
                    Storage::disk('public')->delete($config->favicon_path);
                }
                $config->favicon_path = $request->file('favicon')->store('branding', 'public');
            }

            if ($request->hasFile('logo')) {
                if ($config->logo_path) {
                    Storage::disk('public')->delete($config->logo_path);
                }
                $config->logo_path = $request->file('logo')->store('branding', 'public');
            }

            $config->save();

            return response()
                ->json(ApiResponse::success('Branding updated', [
                    'school_name' => $config->school_name,
                    'favicon_url' => $config->favicon_path ? asset('storage/' . $config->favicon_path) : null,
                    'logo_url' => $config->logo_path ? asset('storage/' . $config->logo_path) : null,
                ]))->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error updating branding: ' . $e->getMessage());
            return response()
                ->json(ApiResponse::internalError('Failed to update branding', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }
}

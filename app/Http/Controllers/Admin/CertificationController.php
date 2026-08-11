<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificationController extends Controller
{
    /**
     * Display a listing of certifications.
     */
    public function index()
    {
        $certifications = Certification::orderBy('sort_order', 'asc')->get();
        return view('admin.certifications.index', compact('certifications'));
    }

    /**
     * Show the form for creating a new certification.
     */
    public function create()
    {
        return view('admin.certifications.create');
    }

    /**
     * Store a newly created certification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'certificate_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'nullable|integer',
        ]);

        // ── Logo: file upload > logo_path (Media Picker / URL) > media URL > manual URL ──
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = time() . '_logo_' . Str::slug($request->name) . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('uploads/certifications'), $logoName);
            $logoPath = 'uploads/certifications/' . $logoName;
        } elseif ($request->filled('logo_path')) {
            $logoPath = $request->logo_path;
        } elseif ($request->filled('logo_media_url')) {
            $logoPath = ltrim(parse_url($request->logo_media_url, PHP_URL_PATH), '/');
        } elseif ($request->filled('logo_url')) {
            $logoPath = $request->logo_url; // external URL stored as-is
        }

        // ── Cert Images: file upload > cert_media_urls > media library JSON > newline URLs ──
        $certImages = [];
        if ($request->hasFile('certificate_images')) {
            foreach ($request->file('certificate_images') as $key => $file) {
                $imgName = time() . '_' . $key . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/certifications'), $imgName);
                $certImages[] = 'uploads/certifications/' . $imgName;
            }
        } elseif ($request->filled('cert_media_urls')) {
            $raw = $request->cert_media_urls;
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $certImages = array_map(fn($url) => ltrim(parse_url($url, PHP_URL_PATH), '/'), $decoded);
            } else {
                $certImages = array_filter(array_map('trim', explode(',', $raw)));
            }
        } elseif ($request->filled('cert_url_list')) {
            $certImages = array_filter(array_map('trim', explode("\n", $request->cert_url_list)));
        }

        Certification::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'certificate_number' => $request->certificate_number,
            'logo_path' => $logoPath,
            'certificate_images' => $certImages,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.certifications.index')->with('success', 'Certification / License added successfully.');
    }

    /**
     * Show the form for editing the specified certification.
     */
    public function edit($id)
    {
        $certification = Certification::findOrFail($id);
        return view('admin.certifications.edit', compact('certification'));
    }

    /**
     * Update the specified certification.
     */
    public function update(Request $request, $id)
    {
        $certification = Certification::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'certificate_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'nullable|integer',
        ]);

        // ── Logo: file > logo_path > media URL > manual URL > keep existing ──
        $logoPath = $certification->logo_path;
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = time() . '_logo_' . Str::slug($request->name) . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('uploads/certifications'), $logoName);
            $logoPath = 'uploads/certifications/' . $logoName;
        } elseif ($request->filled('logo_path')) {
            $logoPath = $request->logo_path;
        } elseif ($request->filled('logo_media_url')) {
            $logoPath = ltrim(parse_url($request->logo_media_url, PHP_URL_PATH), '/');
        } elseif ($request->filled('logo_url')) {
            $logoPath = $request->logo_url;
        }

        // ── Cert Images: file > cert_media_urls > media JSON > URL list > keep existing ──
        $certImages = $certification->certificate_images ?? [];
        if ($request->hasFile('certificate_images')) {
            $newImages = [];
            foreach ($request->file('certificate_images') as $key => $file) {
                $imgName = time() . '_' . $key . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/certifications'), $imgName);
                $newImages[] = 'uploads/certifications/' . $imgName;
            }
            $certImages = $request->has('replace_images') ? $newImages : array_merge($certImages, $newImages);
        } elseif ($request->filled('cert_media_urls')) {
            $raw = $request->cert_media_urls;
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $newImages = array_map(fn($url) => ltrim(parse_url($url, PHP_URL_PATH), '/'), $decoded);
            } else {
                $newImages = array_filter(array_map('trim', explode(',', $raw)));
            }
            $certImages = $request->has('replace_images') ? $newImages : array_merge($certImages, $newImages);
        } elseif ($request->filled('cert_url_list')) {
            $newImages = array_values(array_filter(array_map('trim', explode("\n", $request->cert_url_list))));
            $certImages = $request->has('replace_images') ? $newImages : array_merge($certImages, $newImages);
        }

        $certification->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'certificate_number' => $request->certificate_number,
            'logo_path' => $logoPath,
            'certificate_images' => $certImages,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.certifications.index')->with('success', 'Certification updated successfully.');
    }

    /**
     * Remove the specified certification.
     */
    public function destroy($id)
    {
        $certification = Certification::findOrFail($id);
        $certification->delete();

        return redirect()->route('admin.certifications.index')->with('success', 'Certification deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageKitService;

class PengaturanController extends Controller
{
    protected $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }

    public function index()
    {
        $landing = DB::table('hero_section')->first();
        return view('pengaturan.index', compact('landing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hero_images.*' => 'nullable|image|mimes:webp,jpeg,png,jpg|max:2048',
        ]);

        $heroImages = [];

        if ($request->hasFile('hero_images')) {
            foreach ($request->file('hero_images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $upload = $this->imageKitService->upload($image, $imageName, 'hero');
                
                if (isset($upload->result) && $upload->result) {
                    $heroImages[] = [
                        'url' => $upload->result->url,
                        'file_id' => $upload->result->fileId
                    ];
                }
            }
        }

        DB::table('hero_section')->insert([
            'hero_images' => json_encode($heroImages),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('pengaturan_web.hero.index')->with('success', 'Hero Section berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hero_images.*' => 'nullable|image|mimes:webp,jpeg,png,jpg|max:2048',
            'delete_images.*' => 'nullable|string'
        ]);

        $landing = DB::table('hero_section')->where('id', $id)->first();
        
        // Load existing images
        $heroImages = [];
        if ($landing->hero_images) {
            $heroImages = json_decode($landing->hero_images, true) ?? [];
        } else {
            // Carry over old columns if present (legacy support)
            if ($landing->hero_image_1) $heroImages[] = ['url' => asset('storage/'.$landing->hero_image_1), 'path' => $landing->hero_image_1];
            if ($landing->hero_image_2) $heroImages[] = ['url' => asset('storage/'.$landing->hero_image_2), 'path' => $landing->hero_image_2];
        }

        // Handle deletions
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageToDelete) {
                foreach ($heroImages as $key => $imgObj) {
                    if (isset($imgObj['file_id']) && $imgObj['file_id'] === $imageToDelete) {
                        $this->imageKitService->delete($imgObj['file_id']);
                        unset($heroImages[$key]);
                        break;
                    } elseif (isset($imgObj['path']) && $imgObj['path'] === $imageToDelete) {
                        Storage::disk('public')->delete($imgObj['path']);
                        unset($heroImages[$key]);
                        break;
                    }
                }
            }
            $heroImages = array_values($heroImages);
        }

        // Add new images
        if ($request->hasFile('hero_images')) {
            foreach ($request->file('hero_images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $upload = $this->imageKitService->upload($image, $imageName, 'hero');
                
                if (isset($upload->result) && $upload->result) {
                    $heroImages[] = [
                        'url' => $upload->result->url,
                        'file_id' => $upload->result->fileId
                    ];
                }
            }
        }

        DB::table('hero_section')->where('id', $id)->update([
            'hero_images' => json_encode($heroImages),
            'updated_at' => now()
        ]);

        return redirect()->route('pengaturan_web.hero.index')->with('success', 'Hero Section berhasil diupdate!');
    }
}

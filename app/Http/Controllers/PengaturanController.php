<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $landing = DB::table('hero_section')->first();
        return view('pengaturan.index', compact('landing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hero_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'hero_image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        $data = [];

        if ($request->hasFile('hero_image_1')) {
            $data['hero_image_1'] = $request->file('hero_image_1')->store('hero', 'public');
        }
        if ($request->hasFile('hero_image_2')) {
            $data['hero_image_2'] = $request->file('hero_image_2')->store('hero', 'public');
        }

        DB::table('hero_section')->insert($data);

        return redirect()->route('pengaturan_web.hero.index')->with('success', 'Hero Section berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hero_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'hero_image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        $landing = DB::table('hero_section')->where('id', $id)->first();
        $data = [];

        if ($request->hasFile('hero_image_1')) {
            // hapus file lama kalau ada
            if ($landing && $landing->hero_image_1) {
                Storage::disk('public')->delete($landing->hero_image_1);
            }
            $data['hero_image_1'] = $request->file('hero_image_1')->store('hero', 'public');
        }

        if ($request->hasFile('hero_image_2')) {
            if ($landing && $landing->hero_image_2) {
                Storage::disk('public')->delete($landing->hero_image_2);
            }
            $data['hero_image_2'] = $request->file('hero_image_2')->store('hero', 'public');
        }

        DB::table('hero_section')->where('id', $id)->update($data);

        return redirect()->route('pengaturan_web.hero.index')->with('success', 'Hero Section berhasil diupdate!');
    }
}

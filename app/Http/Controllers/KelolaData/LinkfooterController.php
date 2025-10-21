<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class LinkfooterController extends Controller
{
    public function index()
    {
        $count = DB::table('social_links')->count();
        return view('pengaturan_web.link_social.index', compact('count'));
    }

    public function load(Request $request)
    {
        $query = DB::table('social_links')
            ->select(['id', 'facebook', 'instagram', 'twitter', 'tiktok', 'telegram', 'google_maps', 'youtube']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('facebook', 'like', "%{$search}%")
                    ->orWhere('instagram', 'like', "%{$search}%")
                    ->orWhere('twitter', 'like', "%{$search}%")
                    ->orWhere('tiktok', 'like', "%{$search}%")
                    ->orWhere('telegram', 'like', "%{$search}%")
                    ->orWhere('google_maps', 'like', "%{$search}%")
                    ->orWhere('youtube', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('pengaturan_web.link_social.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('pengaturan_web.link_social.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create()
    {
        return view('pengaturan_web.link_social.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'telegram' => 'nullable|url|max:255',
            'google_maps' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
        ]);

        DB::table('social_links')->insert($request->only([
            'facebook',
            'instagram',
            'twitter',
            'tiktok',
            'telegram',
            'google_maps',
            'youtube'
        ]));

        return redirect()->route('pengaturan_web.link_social.index')
            ->with(['message' => 'Link social media berhasil ditambahkan', 'message_type' => 'success', 'message_title' => 'Sukses']);
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $social = DB::table('social_links')->find($decryptedId);

        return view('pengaturan_web.link_social.create', compact('social'));
    }


    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $request->validate([
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'telegram' => 'nullable|url|max:255',
            'google_maps' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
        ]);

        DB::table('social_links')
            ->where('id', $decryptedId)
            ->update($request->only([
                'facebook',
                'instagram',
                'twitter',
                'tiktok',
                'telegram',
                'google_maps',
                'youtube'
            ]));

        return redirect()->route('pengaturan_web.link_social.index')
            ->with(['message' => 'Link social media berhasil diperbarui', 'message_type' => 'success', 'message_title' => 'Sukses']);
    }


    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $kertas = DB::table('social_links')->find($decryptedId);

            if ($kertas) {
                DB::table('social_links')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Link berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus link_social.'
            ], 500);
        }
    }
}

<?php
namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ImageKitService;

class BlogController extends Controller
{
    protected $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }

    public function index()
    {
        $count = DB::table('articles')->count();
        return view('pengaturan_web.blog.index', compact('count'));
    }

    public function load(Request $request)
    {
        $query = DB::table('articles')
            ->select(['id', 'title', 'category', 'author', 'image', 'is_featured', 'created_at']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($row) {
                if ($row->image) {
                    $imageData = json_decode($row->image, true);
                    $url = (is_array($imageData) && isset($imageData['url'])) ? $imageData['url'] : asset('storage/blog/' . $row->image);
                    return '<img src="' . $url . '" class="logo-img" style="max-width: 100px; max-height: 60px; border-radius: 5px;" alt="Image">';
                }
                return '<span class="badge bg-secondary">Tidak ada</span>';
            })
            ->addColumn('is_featured', function ($row) {
                return $row->is_featured ? '<span class="badge bg-success">Featured</span>' : '<span class="badge bg-secondary">Normal</span>';
            })
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('pengaturan_web.blog.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('pengaturan_web.blog.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['image', 'is_featured', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('pengaturan_web.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100',
            'read_time' => 'nullable|string|max:50',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:webp|max:2048',
            'is_featured' => 'nullable|boolean'
        ], [
            'image.mimes' => 'Format gambar yang diperbolehkan: webp',
        ]);

        $imageData = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $upload = $this->imageKitService->upload($image, $imageName, 'blog');
            
            if (isset($upload->result) && $upload->result) {
                $imageData = json_encode([
                    'url' => $upload->result->url,
                    'file_id' => $upload->result->fileId
                ]);
            }
        }

        DB::table('articles')->insert([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category' => $request->category,
            'author' => $request->author,
            'read_time' => $request->read_time,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image' => $imageData,
            'is_featured' => $request->has('is_featured') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pengaturan_web.blog.index')
            ->with(['message' => 'Artikel berhasil ditambahkan', 'message_type' => 'success', 'message_title' => 'Sukses']);
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $article = DB::table('articles')->find($decryptedId);

        return view('pengaturan_web.blog.create', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100',
            'read_time' => 'nullable|string|max:50',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:webp|max:2048',
            'is_featured' => 'nullable|boolean'
        ], [
            'image.mimes' => 'Format gambar yang diperbolehkan: webp',
        ]);

        $existing = DB::table('articles')->find($decryptedId);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category' => $request->category,
            'author' => $request->author,
            'read_time' => $request->read_time,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'is_featured' => $request->has('is_featured') ? 1 : 0,
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            // Delete old image from ImageKit
            if ($existing->image) {
                $oldImageData = json_decode($existing->image, true);
                if (is_array($oldImageData) && isset($oldImageData['file_id'])) {
                    $this->imageKitService->delete($oldImageData['file_id']);
                } else {
                    Storage::delete('public/blog/' . $existing->image);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $upload = $this->imageKitService->upload($image, $imageName, 'blog');
            
            if (isset($upload->result) && $upload->result) {
                $data['image'] = json_encode([
                    'url' => $upload->result->url,
                    'file_id' => $upload->result->fileId
                ]);
            }
        }

        DB::table('articles')->where('id', $decryptedId)->update($data);

        return redirect()->route('pengaturan_web.blog.index')
            ->with(['message' => 'Artikel berhasil diperbarui', 'message_type' => 'success', 'message_title' => 'Sukses']);
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $article = DB::table('articles')->find($decryptedId);

            if ($article) {
                // Delete image from ImageKit
                if ($article->image) {
                    $imageData = json_decode($article->image, true);
                    if (is_array($imageData) && isset($imageData['file_id'])) {
                        $this->imageKitService->delete($imageData['file_id']);
                    } else {
                        Storage::delete('public/blog/' . $article->image);
                    }
                }

                DB::table('articles')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Artikel berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus artikel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSosmed()
    {
        // This was in the route but might not be needed for blog
        return response()->json([]);
    }
}

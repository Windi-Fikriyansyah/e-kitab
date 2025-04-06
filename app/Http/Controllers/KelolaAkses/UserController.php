<?php

namespace App\Http\Controllers\KelolaAkses;

use App\Http\Controllers\Controller;
use App\Http\Requests\KelolaAkses\UserRequest;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kelola_akses.user.index');
    }

    public function load(Request $request)
    {
        // Page Length
        // $pageNumber = ($request->start / $request->length) + 1;
        // $pageLength = $request->length;
        // $skip       = ($pageNumber - 1) * $pageLength;

        // // Page Order
        // $orderColumnIndex = $request->order[0]['column'] ?? '0';
        // $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = DB::table('users')
            ->get();

        // Search
        // $search = $request->search;
        // $query = $query->where(function ($query) use ($search) {
        //     $query->orWhere('name', 'like', "%" . $search . "%");
        // });

        // $orderByName = 'name';
        // switch ($orderColumnIndex) {
        //     case '0':
        //         $orderByName = 'name';
        //         break;
        // }
        // $query = $query->orderBy($orderByName, $orderBy);
        // $recordsFiltered = $recordsTotal = $query->count();
        // $users = $query->skip($skip)->take($pageLength)->get();

        return Datatables::of($query)
            ->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route("user.edit", Crypt::encrypt($row->id)) . '" class="btn btn-md btn-warning" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';
                $btn .= '<a onclick="hapus(\'' . $row->id . '\')" class="btn btn-md btn-danger"><span class="fa-fw select-all fas"></span></a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


        $daftar_peran = Role::all();

        return view('kelola_akses.user.create', compact('daftar_peran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'tipe' => $request->tipe,
                'status_aktif' => $request->status_aktif,
                'role' => (int)$request->role,
                'jabatan' => $request->jabatan,
            ]);

            $user->syncRoles((int)$request->role);

            DB::commit();
            return redirect()
                ->route('user.index')
                ->with('message', 'User berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('user.create')->withInput()->with('message', 'Data gagal disimpan' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);



        $data = User::find($id);

        $daftar_peran = Role::all();

        return view('kelola_akses.user.edit', compact('daftar_peran', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);

            $user
                ->update([
                    'name' => $request->name,
                    'username' => $request->username,
                    // 'password' => Hash::make($request->password),
                    'tipe' => $request->tipe,
                    'status_aktif' => $request->status_aktif,
                    'role' => (int)$request->role,
                    'jabatan' => $request->jabatan,
                ]);

            $user->syncRoles((int)$request->role);

            DB::commit();
            return redirect()
                ->route('user.index')
                ->with('message', 'User berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            User::find($id)
                ->delete();

            DB::commit();
            return response()->json([
                'status' => true,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back();
        }
    }

    // GANTI SKPD



    // UBAH PASSWORD
    public function ubahPassword()
    {
        $daftarSkpd = DB::connection('simakda')
            ->table('ms_skpd')
            ->select('kd_skpd', 'nm_skpd')
            ->groupBy('kd_skpd', 'nm_skpd')
            ->get();

        return view('ubahpassword.index', compact('daftarSkpd'));
    }

    public function simpanUbahPassword(Request $request)
    {
        $id = Auth::user()->id;

        $validated = $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'confirmation_password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(), 'same:new_password'],
        ]);

        $password_lama = DB::table('users')
            ->where(['id' => Auth::user()->id])
            ->first()
            ->password;

        if (!Hash::check($request->old_password, $password_lama)) {
            return redirect()->route('ubah_password.index')->withInput()->with('error', 'Pasword lama tidak sesuai');
        }

        DB::beginTransaction();
        try {
            $user = User::find($id);

            $user
                ->update([
                    'password' => Hash::make($request->new_password)
                ]);

            DB::commit();
            return redirect()
                ->route('ubah_password.index')
                ->with('message', 'Password berhasil diubah');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('ubah_password.index')->withInput()->with('error', 'Ubah passsword gagal');
        }
    }
}

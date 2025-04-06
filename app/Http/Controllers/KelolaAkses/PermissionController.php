<?php

namespace App\Http\Controllers\KelolaAkses;

use App\Http\Controllers\Controller;
use App\Http\Requests\KelolaAkses\PermissionRequest;
use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function routeList()
    {
        $routeList1 = Route::getRoutes();

        $routeList = [];
        foreach ($routeList1 as $value) {
            $routeList[] = $value->getName();
        }

        return $routeList;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kelola_akses.akses.index');
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
        $query = DB::table('permissions')
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
                $btn = '<a href="' . route("akses.edit", Crypt::encrypt($row->id)) . '" class="btn btn-md btn-warning" style="margin-right:4px"><span class="fa-fw select-all fas"></span></a>';
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
        $permissions = Permission::where('parent', '')->get();

        return view('kelola_akses.akses.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request)
    {
        $routeList = $this->routeList();
        // dd($request->validated());
        if ($request->link && !in_array($request->link, $routeList)) {
            return redirect()->route('akses.create')->withInput()->with('message', 'Link belum ada, Silahkan hubungi Administrator');
        }

        DB::beginTransaction();
        try {
            Permission::create([
                'name' => $request->name,
                'tipe' => $request->tipe,
                'link' => $request->tipe == '1' ? '' : $request->link,
                'parent' => $request->parent == '-' ? '' : $request->parent,
            ]);

            DB::commit();
            return redirect()->route('akses.index')->with('message', 'Akses berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
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

        $permissions = Permission::where('parent', '')->get();

        $data = Permission::findById($id);

        return view('kelola_akses.akses.edit', compact('permissions', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, string $id)
    {
        $routeList = $this->routeList();

        if ($request->link && !in_array($request->link, $routeList)) {
            return redirect()->route('akses.edit', Crypt::encrypt($id))->withInput()->with('message', 'Link belum ada, silahkan hubungi Administrator');
        }

        DB::beginTransaction();
        try {
            Permission::findById($id)
                ->update([
                    'name' => $request->name,
                    'tipe' => $request->tipe,
                    'link' => $request->tipe == '1' ? '' : $request->link,
                    'parent' => $request->parent == '-' ? '' : $request->parent,
                ]);

            DB::commit();
            return redirect()->route('akses.index')->with('message', 'Akses berhasil diperbaharui!');
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
            $rolePermissions = DB::table('role_has_permissions')
                ->where('role_has_permissions.permission_id', $id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->count();

            if ($rolePermissions > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akses telah digunakan di Peran!'
                ], 200);
            }

            Permission::findById($id)
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
}

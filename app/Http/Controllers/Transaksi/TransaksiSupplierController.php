<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use PDF;

if (!function_exists('toNumber')) {
    function toNumber($rupiah)
    {
        return intval(preg_replace('/[^0-9]/', '', $rupiah));
    }
}


class TransaksiSupplierController extends Controller
{
    public function index()
    {
        $supplier = DB::table('supplier')->get();
        return view('transaksi.transaksi_supplier.index', compact('supplier'));
    }
    public function load(Request $request)
    {
        $query = DB::table('transaksi_supplier as t')
            ->leftJoin('supplier as s', 't.id_supplier', '=', 's.id')
            ->select([
                't.id',
                't.kode_transaksi',
                't.resi',
                't.total',
                't.fee',
                't.id_supplier',
                's.nama_supplier',
                't.created_at'
            ]);

        if ($request->filter_supplier) {
            $query->where('t.id_supplier', $request->filter_supplier);
        }

        return DataTables::of($query)

            ->filter(function ($q) use ($request) {
                if ($request->search['value']) {
                    $search = strtolower($request->search['value']);

                    $q->where(function ($w) use ($search) {
                        $w->whereRaw('LOWER(t.kode_transaksi) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(s.nama_supplier) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(t.total) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(t.resi) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(t.fee) LIKE ?', ["%$search%"]);
                    });
                }
            })

            ->addColumn('aksi', function ($row) {
                return '
        <button class="btn btn-info btn-sm detail-btn"
            data-id="' . $row->id . '">
            <i class="bx bx-search"></i> Detail
        </button>
    ';
            })


            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create()
    {
        $supplier = DB::table('supplier')->select('id', 'nama_supplier')->get();

        return view('transaksi.transaksi_supplier.create', compact('supplier'));
    }


    public function getProduk(Request $request)
    {
        $produk = DB::table('produk')
            ->where('supplier', $request->id_supplier)
            ->select('id', 'judul', 'harga_modal')
            ->get();

        return response()->json($produk);
    }

    public function getProdukDT(Request $request)
    {
        $query = DB::table('produk as p')
            ->where('p.supplier', $request->id_supplier)
            ->select(['p.id', 'p.judul', 'p.harga_modal']);

        return DataTables::of($query)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="choose-product" data-id="' . $row->id . '" data-judul="' . $row->judul . '" data-harga="' . $row->harga_modal . '">';
            })
            ->rawColumns(['checkbox'])
            ->make(true);
    }



    private function generateKodeTransaksi()
    {
        $tanggal = date('Ymd');

        // cek kode terakhir hari ini
        $last = DB::table('transaksi_supplier')
            ->whereDate('created_at', date('Y-m-d'))
            ->orderBy('id', 'desc')
            ->first();

        if (!$last) {
            $urut = 1;
        } else {
            // ambil nomor urut dari kode terakhir (format TS-YYYYMMDD-0001)
            $lastKode = explode('-', $last->kode_transaksi);
            $urut = intval($lastKode[2]) + 1;
        }

        return 'TS-' . $tanggal . '-' . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }


    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $id_supplier = $request->id_supplier;

            $total = $request->total;

            $resi  = $request->resi;
            $fee   = $request->fee;

            // Produk JSON
            $produk = json_decode($request->produk, true);

            if (!$produk || count($produk) == 0) {
                return back()->with('message', 'Produk belum dipilih!');
            }

            // Generate kode
            $kodeTransaksi = $this->generateKodeTransaksi();

            // SIMPAN HEADER
            $id_transaksi = DB::table('transaksi_supplier')->insertGetId([
                'kode_transaksi' => $kodeTransaksi,
                'id_supplier'    => $id_supplier,
                'total'          => $total,
                'resi'           => $resi,
                'fee'            => $fee,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // SIMPAN DETAIL
            foreach ($produk as $p) {

                DB::table('transaksi_supplier_detail')->insert([
                    'id_transaksi' => $id_transaksi,
                    'id_produk'    => $p['id'],
                    'harga'        => $p['harga'],
                    'qty'          => $p['qty'],
                    'total'        => $p['total'],
                    'created_at'   => now(),
                ]);

                // Kurangi stok
                DB::table('produk')
                    ->where('id', $p['id'])
                    ->decrement('stok', $p['qty']);
            }

            DB::commit();

            return redirect()->route('transaksi.transaksi_supplier.index')
                ->with('message', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('message', 'Error: ' . $e->getMessage());
        }
    }



    public function detail($id)
    {
        // Ambil header transaksi
        $transaksi = DB::table('transaksi_supplier as t')
            ->leftJoin('supplier as s', 't.id_supplier', '=', 's.id')
            ->where('t.id', $id)
            ->select(
                't.*',
                's.nama_supplier'
            )
            ->first();

        // Ambil detail produk
        $detail = DB::table('transaksi_supplier_detail as d')
            ->leftJoin('produk as p', 'd.id_produk', '=', 'p.id')
            ->where('d.id_transaksi', $id)
            ->select(
                'p.judul',
                'd.harga',
                'd.qty'
            )
            ->get();

        return response()->json([
            'transaksi' => $transaksi,
            'detail' => $detail
        ]);
    }

    public function exportPDF(Request $request)
    {
        $supplier = $request->supplier;

        // AMBIL HEADER TRANSAKSI
        $transaksi = DB::table('transaksi_supplier as t')
            ->leftJoin('supplier as s', 't.id_supplier', '=', 's.id')
            ->select('t.*', 's.nama_supplier')
            ->when($supplier, function ($q) use ($supplier) {
                $q->where('t.id_supplier', $supplier);
            })
            ->orderBy('t.id', 'desc')
            ->get();

        // AMBIL DETAIL PER TRANSAKSI
        $detail = DB::table('transaksi_supplier_detail as d')
            ->leftJoin('produk as p', 'd.id_produk', '=', 'p.id')
            ->select(
                'd.id_transaksi',
                'p.judul',
                'd.harga',
                'd.qty',
                DB::raw('(d.harga * d.qty) as total')
            )
            ->get();

        $pdf = PDF::loadView('transaksi.transaksi_supplier.pdf', [
            'transaksi' => $transaksi,
            'detail' => $detail,
            'nama_supplier' => $supplier
                ? DB::table('supplier')->where('id', $supplier)->value('nama_supplier')
                : 'Semua Supplier'
        ]);

        return $pdf->setPaper('A4', 'portrait')->stream('Laporan-Transaksi-Supplier.pdf');
    }
}

<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Exports\LaporanSupplierExport;
use Maatwebsite\Excel\Facades\Excel;


class LaporanSupplierController extends Controller
{
    public function index()
    {
        $suppliers = DB::table('supplier')->get();
        $products = DB::table('produk')->get();

        return view('laporan.supplier.index', compact('suppliers', 'products'));
    }


    public function load(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $tanggalAwal  = $request->tanggal_awal ?: date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?: date('Y-m-d');
        $supplier     = $request->supplier;
        $produk       = $request->produk;

        $start = Carbon::parse($tanggalAwal)->startOfDay();
        $end   = Carbon::parse($tanggalAkhir)->endOfDay();

        // ===== Base filter (dipakai ulang untuk total) =====
        $base = DB::table('transaksi_items as ti')
            ->join('transaksi as t', 'ti.id_transaksi', '=', 't.id')
            ->join('produk as p', 'ti.kd_produk', '=', 'p.kd_produk')
            ->join('supplier as s', 'p.supplier', '=', 's.id')
            ->whereBetween('t.created_at', [$start, $end]);

        if (!empty($supplier)) {
            $base->where('s.id', $supplier);
        }
        if (!empty($produk)) {
            $base->where('p.kd_produk', $produk);
        }

        // ===== Query utama untuk datatable (group per produk + tanggal) =====
        $query = (clone $base)
            ->selectRaw("
            p.kd_produk,
            p.judul,
            s.nama_supplier,
            p.harga_modal,
            SUM(ti.quantity) as qty_terjual,
            SUM(ti.quantity * p.harga_modal) as total_terjual,
            DATE(t.created_at) as tanggal_transaksi
        ")
            ->groupBy(
                'p.kd_produk',
                'p.judul',
                's.nama_supplier',
                'p.harga_modal',
                DB::raw('DATE(t.created_at)')
            );

        // ===== Total qty & nilai (jangan ambil dari hasil group datatable, biar akurat) =====
        $totals = (clone $base)
            ->selectRaw("
            COALESCE(SUM(ti.quantity),0) as total_qty,
            COALESCE(SUM(ti.quantity * p.harga_modal),0) as total_nilai
        ")
            ->first();

        // ===== Total fee+resi berdasarkan transaksi_supplier (tanpa dobel akibat join items) =====
        // Versi aman: DISTINCT transaksi_supplier.id lalu SUM(resi+fee)
        $subTs = DB::table('transaksi as t')
            ->join('transaksi_supplier as ts', 't.id_transaksi_supplier', '=', 'ts.id')
            ->whereBetween('t.created_at', [$start, $end])
            ->select('ts.id', 'ts.resi', 'ts.fee')
            ->distinct();

        // Kalau transaksi_supplier punya kolom supplier_id / id_supplier, filter di sini:
        if (!empty($supplier)) {
            $subTs->where('ts.id_supplier', $supplier); // <-- ganti sesuai kolom tabelmu
        }

        $totalFeeResi = DB::query()
            ->fromSub($subTs, 'x')
            ->selectRaw('COALESCE(SUM(x.resi + x.fee),0) as total_fee_resi')
            ->value('total_fee_resi');

        $totalTagihan = (int) $totals->total_nilai - (int) $totalFeeResi;

        return DataTables::of($query)
            ->addIndexColumn()
            ->with([
                'total_qty'       => (int) $totals->total_qty,
                'total_nilai'     => (int) $totals->total_nilai,
                'total_fee_resi'  => (int) $totalFeeResi,
                'total_tagihan'   => (int) $totalTagihan,
            ])
            ->make(true);
    }



    public function exportExcel(Request $request)
    {
        $tanggalAwal  = $request->tanggal_awal ?: date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?: date('Y-m-d');
        $supplier     = $request->supplier;
        $produk       = $request->produk;

        $start = Carbon::parse($tanggalAwal)->startOfDay();
        $end   = Carbon::parse($tanggalAkhir)->endOfDay();

        // ===== Base transaksi (sama seperti load) =====
        $base = DB::table('transaksi_items as ti')
            ->join('transaksi as t', 'ti.id_transaksi', '=', 't.id')
            ->join('produk as p', 'ti.kd_produk', '=', 'p.kd_produk')
            ->join('supplier as s', 'p.supplier', '=', 's.id')
            ->whereBetween('t.created_at', [$start, $end]);

        if (!empty($supplier)) $base->where('s.id', $supplier);
        if (!empty($produk))   $base->where('p.kd_produk', $produk);

        // ===== TABEL ATAS: Rekap transaksi =====
        $rowsTransaksi = (clone $base)
            ->selectRaw("
            p.kd_produk,
            p.judul,
            s.nama_supplier,
            p.harga_modal,
            SUM(ti.quantity) as qty_terjual,
            SUM(ti.quantity * p.harga_modal) as total_terjual,
            DATE(t.created_at) as tanggal_transaksi
        ")
            ->groupBy(
                'p.kd_produk',
                'p.judul',
                's.nama_supplier',
                'p.harga_modal',
                DB::raw('DATE(t.created_at)')
            )
            ->orderBy(DB::raw('DATE(t.created_at)'), 'asc')
            ->orderBy('p.kd_produk', 'asc')
            ->get();

        $totalQty   = (int) $rowsTransaksi->sum('qty_terjual');
        $totalNilai = (int) $rowsTransaksi->sum('total_terjual');

        // ===== Ambil ID transaksi_supplier yang TERPAKAI (biar konsisten jika filter produk dipakai) =====
        $tsIds = (clone $base)
            ->whereNotNull('t.id_transaksi_supplier')
            ->distinct()
            ->pluck('t.id_transaksi_supplier')
            ->values()
            ->all();

        // ===== TABEL BAWAH: transaksi_supplier (FEE+RESI) =====
        $rowsTransaksiSupplier = DB::table('transaksi as t')
            ->join('transaksi_supplier as ts', 't.id_transaksi_supplier', '=', 'ts.id')
            ->join('supplier as sup', 'ts.id_supplier', '=', 'sup.id') // <-- join supplier
            ->whereBetween('t.created_at', [$start, $end])
            ->when(!empty($supplier), function ($q) use ($supplier) {
                $q->where('ts.id_supplier', $supplier);
            })
            ->when(!empty($produk), function ($q) use ($tsIds) {
                $q->whereIn('ts.id', $tsIds);
            })
            ->selectRaw("
        sup.nama_supplier as nama_supplier,
        DATE(t.created_at) as tanggal_transaksi,
        COALESCE(ts.resi,0) as resi,
        COALESCE(ts.fee,0) as fee,
        (COALESCE(ts.resi,0) + COALESCE(ts.fee,0)) as total_fee_resi
    ")
            ->distinct()
            ->orderBy(DB::raw('DATE(t.created_at)'), 'asc')
            ->orderBy('sup.nama_supplier', 'asc')
            ->get();

        $totalFeeResi = (int) $rowsTransaksiSupplier->sum('total_fee_resi');

        $totalTagihan = $totalNilai - $totalFeeResi;

        // Label header
        $supplierLabel = 'Semua Supplier';
        if (!empty($supplier)) {
            $supplierLabel = DB::table('supplier')->where('id', $supplier)->value('nama_supplier') ?? 'Supplier dipilih';
        }
        $produkLabel = 'Semua Produk';
        if (!empty($produk)) {
            $produkLabel = DB::table('produk')->where('kd_produk', $produk)->value('judul') ?? 'Produk dipilih';
        }

        $meta = [
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir,
            'supplier_label' => $supplierLabel,
            'produk_label' => $produkLabel,
            'total_qty' => $totalQty,
            'total_nilai' => $totalNilai,
            'total_fee_resi' => $totalFeeResi,
            'total_tagihan' => $totalTagihan,
        ];

        $filename = "laporan-supplier-{$tanggalAwal}-{$tanggalAkhir}.xlsx";

        return Excel::download(
            new LaporanSupplierExport($rowsTransaksi, $rowsTransaksiSupplier, $meta),
            $filename
        );
    }
}

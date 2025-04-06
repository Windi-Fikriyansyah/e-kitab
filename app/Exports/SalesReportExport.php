<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithFormatting;

class SalesReportExport implements FromCollection, WithHeadings
{
    protected $start_date;
    protected $end_date;
    protected $product_id;

    // Constructor to pass filter parameters
    public function __construct($start_date, $end_date, $product_id)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->product_id = $product_id;
    }

    public function collection()
    {
        // Query with filters
        $query = DB::table('transaksidetails')
            ->join('products', 'transaksidetails.ProductId', '=', 'products.id')
            ->join('transaksi', 'transaksidetails.TransactionId', '=', 'transaksi.Id')
            ->join('users', 'transaksi.user_id', '=', 'users.id')
            ->select(
                'transaksidetails.created_at',
                'users.name as nama_kasir',
                'products.barcode',
                'products.name as nama_produk',
                'products.category',
                'transaksidetails.selling_price',
                'transaksidetails.Quantity',
                DB::raw('transaksidetails.selling_price * transaksidetails.Quantity as total')
            );

        // Apply filters
        if ($this->start_date) {
            $query->whereDate('transaksidetails.created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->whereDate('transaksidetails.created_at', '<=', $this->end_date);
        }
        if ($this->product_id) {
            $query->where('transaksidetails.ProductId', '=', $this->product_id);  // Assuming 'product_id' is the column for product filter
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kasir',
            'Barcode',
            'Nama Produk',
            'Kategori',
            'Harga Jual',
            'Quantity',
            'Total'
        ];
    }

    // Optional: If you want to apply formatting (e.g., date format)
    public function styles($sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
    }
}

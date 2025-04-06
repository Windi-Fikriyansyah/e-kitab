<?php

namespace App\Http\Requests\KelolaData\BPKB;

use Illuminate\Foundation\Http\FormRequest;

class TambahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $this->redirect = 'kelola_data/bpkb/create';

        return [
            'kodeSkpd' => 'required',
            'nomorRegister' => 'nullable|max:255|unique:masterBpkb,nomorRegister',
            'nomorBpkb' => 'required|max:255|unique:masterBpkb,nomorBpkb',
            'nomorPolisi' => 'required|max:255|unique:masterBpkb,nomorPolisi',
            'namaPemilik' => 'required|max:255',
            'jenis' => 'required|max:255',
            'merk' => 'required|max:255',
            'tipe' => 'required|max:255',
            'model' => 'required|max:255',
            'tahunPembuatan' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'tahunPerakitan' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'isiSilinder' => 'required|max:255',
            'warna' => 'required|max:255',
            'alamat' => 'required|max:1000',
            'nomorRangka' => 'required|max:255|unique:masterBpkb,nomorRangka',
            'nomorMesin' => 'required|max:255|unique:masterBpkb,nomorMesin',
            'keterangan' => 'required|max:3000',
            'nomorPolisiLama' => 'nullable|max:255|unique:masterBpkb,nomorPolisiLama',
            'nomorBpkbLama' => 'nullable|max:255|unique:masterBpkb,nomorBpkbLama',
            'Nibbar' => 'required|max:255',
            'namapenerimakendaraan' => 'required|max:255',
            'filesuratpenunjukan' => 'required|mimes:pdf|max:2048',
            'fileba' => 'required|mimes:pdf|max:2048',
            'filepaktaintegritas' => 'required|mimes:pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'kodeSkpd.required' => 'SKPD wajib dipilih',
            'nomorRegister.required' => 'Nomor Register wajib diisi',
            'nomorRegister.max' => 'Nomor Register hanya bisa diisi 255 abjad',
            'nomorBpkb.required' => 'Nomor BPKB wajib diisi',
            'nomorBpkb.max' => 'Nomor BPKB hanya bisa diisi 255 abjad',
            'nomorPolisi.required' => 'Nomor Polisi wajib diisi',
            'nomorPolisi.max' => 'Nomor Polisi hanya bisa diisi 255 abjad',
            'namaPemilik.required' => 'Nama Pemilik wajib diisi',
            'namaPemilik.max' => 'Nama Pemilik hanya bisa diisi 255 abjad',
            'jenis.required' => 'Jenis wajib diisi',
            'jenis.max' => 'Jenis hanya bisa diisi 255 abjad',
            'merk.required' => 'Merk wajib diisi',
            'merk.max' => 'Merk hanya bisa diisi 255 abjad',
            'tipe.required' => 'Tipe wajib diisi',
            'tipe.max' => 'Tipe hanya bisa diisi 255 abjad',
            'model.required' => 'Model wajib diisi',
            'model.max' => 'Model hanya bisa diisi 255 abjad',
            'tahunPembuatan.required' => 'Tahun Pembuatan wajib diisi',
            'tahunPembuatan.min' => 'Tahun Pembuatan wajib diisi minimal tahun 1900',
            'tahunPembuatan.max' => 'Tahun Pembuatan wajib diisi maksimal tahun ' . date('Y') + 1,
            'tahunPerakitan.required' => 'Tahun Perakitan wajib diisi',
            'tahunPerakitan.min' => 'Tahun Perakitan wajib diisi minimal tahun 1900',
            'tahunPerakitan.max' => 'Tahun Perakitan wajib diisi maksimal tahun ' . date('Y') + 1,
            'isiSilinder.required' => 'Isi Silinder wajib diisi',
            'isiSilinder.max' => 'Isi Silinder hanya bisa diisi 255 abjad',
            'warna.required' => 'Warna wajib diisi',
            'warna.max' => 'Warna hanya bisa diisi 255 abjad',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.max' => 'Alamat hanya bisa diisi 1000 abjad',
            'nomorRangka.required' => 'Nomor Rangka wajib diisi',
            'nomorRangka.max' => 'Nomor Rangka hanya bisa diisi 255 abjad',
            'nomorMesin.required' => 'Nomor Mesin wajib diisi',
            'nomorMesin.max' => 'Nomor Mesin hanya bisa diisi 255 abjad',
            'keterangan.required' => 'Keterangan wajib diisi',
            'keterangan.max' => 'Keterangan hanya bisa diisi 3000 abjad',
            'nomorPolisiLama.required' => 'Nomor Polisi Lama wajib diisi',
            'nomorPolisiLama.max' => 'Nomor Polisi Lama hanya bisa diisi 255 abjad',
            'nomorBpkbLama.required' => 'Nomor BPKB Lama wajib diisi',
            'nomorBpkbLama.max' => 'Nomor BPKB Lama hanya bisa diisi 255 abjad',
            'Nibbar.required' => 'Nibbar wajib diisi',
            'Nibbar.max' => 'Nibbar hanya bisa diisi 255 abjad',
            'namapenerimakendaraan.required' => 'nama penerima kendaraan wajib diisi',
            'namapenerimakendaraan.max' => 'nama penerima kendaraan hanya bisa diisi 255 abjad',
            'filesuratpenunjukan.required' => 'file surat penunjukan wajib diisi',
            'fileba.required' => 'file ba wajib diisi',
            'filepaktaintegritas.required' => 'file pakta integritas wajib diisi',
        ];
    }
}

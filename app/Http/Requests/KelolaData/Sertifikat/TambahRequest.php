<?php

namespace App\Http\Requests\KelolaData\Sertifikat;

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
        $this->redirect = 'kelola_data/sertifikat/create';

        return [
            'kodeSkpd' => 'required',
            'nomorRegister' => 'nullable|max:255|unique:masterSertifikat,nomorRegister',
            'nib' => 'required|max:255|unique:masterSertifikat,nib',
            'nomorSertifikat' => 'required|max:255|unique:masterSertifikat,nomorSertifikat',
            'tanggalSertifikat' => 'required',
            'luas' => 'required|max:255',
            'hak' => 'required|max:255',
            'pemegangHak' => 'required|max:255',
            'asalUsul' => 'required|max:255',
            'alamat' => 'required|max:1000',
            'sertifikatAsli' => 'required|max:255',
            'balikNama' => 'required|max:255',
            'penggunaan' => 'required|max:255',
            'keterangan' => 'required|max:3000',
            'Nibbar' => 'required|max:3000',
            'file' => 'required|mimes:pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'kodeSkpd.required' => 'SKPD wajib dipilih',
            'nomorRegister.required' => 'Nomor Register wajib diisi',
            'nomorRegister.max' => 'Nomor Register hanya bisa diisi 255 abjad',
            'nib.required' => 'Nib wajib diisi',
            'nib.max' => 'Nib hanya bisa diisi 255 abjad',
            'nomorSertifikat.required' => 'Nomor Sertifikat wajib diisi',
            'nomorSertifikat.max' => 'Nomor Sertifikat hanya bisa diisi 255 abjad',
            'tanggalSertifikat.required' => 'tanggalSertifikat wajib diisi',
            'luas.required' => 'luas wajib diisi',
            'hak.required' => 'hak wajib diisi',
            'pemegangHak.required' => 'Pemegang Hak wajib diisi',
            'pemegangHak.max' => 'Pemegang Hak hanya bisa diisi 255 abjad',
            'asalUsul.required' => 'Asal-Usul wajib diisi',
            'asalUsul.max' => 'Asal-Usul hanya bisa diisi 255 abjad',
            'alamat.required' => 'alamat wajib diisi',
            'alamat.max' => 'alamat hanya bisa diisi 255 abjad',
            'sertifikatAsli.required' => 'Sertifikat Asli Pembuatan wajib diisi',
            'sertifikatAsli.max' => 'Sertifikat Asli hanya bisa diisi 255 abjad',
            'balikNama.required' => 'Balik Nama wajib diisi',
            'balikNama.max' => 'Balik Nama hanya bisa diisi 255 abjad',
            'penggunaan.required' => 'Penggunaan wajib diisi',
            'penggunaan.max' => 'Penggunaan hanya bisa diisi 255 abjad',
            'keterangan.required' => 'Keterangan wajib diisi',
            'keterangan.max' => 'Keterangan hanya bisa diisi 3000 abjad',
            'Nibbar.max' => 'Nibbar hanya bisa diisi 3000 abjad',
        ];
    }
}

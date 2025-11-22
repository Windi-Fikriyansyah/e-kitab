<?php

namespace App\Http\Requests\KelolaAkses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
        if (request()->isMethod('post')) {
            $this->redirect = 'akses/create';
        } elseif (request()->isMethod('put')) {
            $this->redirect = 'akses/' . Crypt::encrypt($this->akse) . '/edit';
        }

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('permissions', 'name')->ignore($this->route('akse'), 'id')
            ],
            'parent' => 'required',
            'tipe' => 'required',
            'link' => 'string|nullable|required_unless:tipe,1|required_unless:parent,-',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.string' => 'Nama wajib bertipe string',
            'parent.required' => 'Parent wajib dipilih',
            'tipe.required' => 'Tipe wajib dipilih',
            'link.string' => 'Link wajib bertipe string',
            'link.required_unless' => 'Link wajib diisi jika tipe nya Ada Link',
        ];
    }
}

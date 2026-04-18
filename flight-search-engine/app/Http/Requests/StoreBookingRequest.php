<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => trim((string) $this->input('full_name')),
            'nik' => trim((string) $this->input('nik')),
            'seat_class' => strtolower(trim((string) $this->input('seat_class'))),
            'back_to_detail' => trim((string) $this->input('back_to_detail')),
            'back_to_results' => trim((string) $this->input('back_to_results')),
            'back_to_form' => trim((string) $this->input('back_to_form')),
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:3', 'max:120'],
            'nik' => ['required', 'digits_between:16,20'],
            'seat_class' => ['required', Rule::in(['economy', 'business', 'first_class'])],
            'passenger_count' => ['required', 'integer', 'min:1', 'max:7'],
            'departure_slots' => ['nullable', 'array'],
            'departure_slots.*' => [Rule::in(['dawn', 'morning', 'afternoon', 'evening'])],
            'arrival_slots' => ['nullable', 'array'],
            'arrival_slots.*' => [Rule::in(['dawn', 'morning', 'afternoon', 'evening'])],
            'ancillary_services' => ['nullable', 'array'],
            'ancillary_services.*' => [Rule::in(['travel_insurance', 'extra_baggage'])],
            'back_to_detail' => ['nullable', 'url'],
            'back_to_results' => ['nullable', 'url'],
            'back_to_form' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Full Name wajib diisi sesuai identitas penumpang.',
            'full_name.min' => 'Full Name minimal harus terdiri dari 3 karakter.',
            'full_name.max' => 'Full Name maksimal 120 karakter.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits_between' => 'NIK harus terdiri dari minimal 16 digit angka.',
            'seat_class.required' => 'Kelas penerbangan wajib dipilih.',
            'seat_class.in' => 'Kelas penerbangan tidak valid. Pilih Economy, Business, atau First Class.',
            'passenger_count.required' => 'Jumlah penumpang wajib diisi.',
            'passenger_count.integer' => 'Jumlah penumpang harus berupa angka bulat.',
            'passenger_count.min' => 'Jumlah penumpang minimal 1.',
            'passenger_count.max' => 'Jumlah penumpang maksimal 7.',
            'departure_slots.array' => 'Format filter waktu keberangkatan tidak valid.',
            'departure_slots.*.in' => 'Pilihan filter waktu keberangkatan tidak valid.',
            'arrival_slots.array' => 'Format filter waktu kedatangan tidak valid.',
            'arrival_slots.*.in' => 'Pilihan filter waktu kedatangan tidak valid.',
            'ancillary_services.array' => 'Format layanan tambahan tidak valid.',
            'ancillary_services.*.in' => 'Pilihan layanan tambahan tidak valid.',
            'back_to_detail.url' => 'URL kembali ke detail tidak valid.',
            'back_to_results.url' => 'URL kembali ke hasil pencarian tidak valid.',
            'back_to_form.url' => 'URL kembali ke formulir tidak valid.',
        ];
    }
}

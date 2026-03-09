<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GenerateVoucherRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:100',
            'id' => 'required|string|max:50',
            'flightNumber' => 'required|string|max:20',
            'date' => 'required|date',
            'aircraft' => 'required|in:ATR,Airbus 320,Boeing 737 Max'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Crew name is required',
            'id.required' => 'Crew ID is required',
            'flightNumber.required' => 'Flight number is required',
            'date.required' => 'Flight date is required',
            'date.date' => 'Flight date must be a valid date',
            'aircraft.required' => 'Aircraft type is required',
            'aircraft.in' => 'Aircraft type is invalid'
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422)); // Ganti status code jika perlu
    }
}

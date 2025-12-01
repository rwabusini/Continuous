<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeaconRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'geo_confidence' => ['nullable', 'numeric', 'between:0,1'],
            'missed' => ['required', 'boolean'],
        ];
    }
}

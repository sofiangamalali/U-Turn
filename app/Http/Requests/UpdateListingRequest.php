<?php

namespace App\Http\Requests;

use App\Enums\ListingType;
use App\Support\ListingRuleResolver;
use Illuminate\Foundation\Http\FormRequest;

class UpdateListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $baseRules = [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
        ];
        $type = $this->input('type', $this->route('listing')->type ?? null);
        if ($type && in_array($type, ListingType::values())) {
            $typeRules = ListingRuleResolver::resolve($type, $this->all());
            return array_merge($baseRules, $typeRules);
        }
        return $baseRules;
    }
}

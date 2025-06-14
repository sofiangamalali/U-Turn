<?php

namespace App\Http\Requests;

use App\Enums\ListingType;
use App\Support\ListingRuleResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreListingRequest extends FormRequest
{
    public function rules(): array
    {
        return array_merge([
            'user_id' => 'required|exists:users,id',
            'type' => ['required', Rule::in(ListingType::values())],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'location' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], ListingRuleResolver::resolve($this->input('type'), $this->all()));
    }
    public function authorize(): bool
    {
        return true;
    }
}

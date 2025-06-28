<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
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
            'listing_id' => 'required_without:chat_id|nullable|exists:listings,id',
            'chat_id' => 'required_without:listing_id|nullable|exists:chats,id',
            'type' => 'required|in:text,image,voice',
            'message' => 'required_if:type,text|string',
            'image' => 'required_if:type,image|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'voice' => 'required_if:type,voice|file|mimes:mp3,wav|max:2048',
        ];
    }
}

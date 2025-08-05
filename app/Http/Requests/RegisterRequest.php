<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'motdepasse' => 'required|string|min:6|confirmed',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:100',
            'date_naissance' => 'nullable|date|before:today'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'motdepasse.required' => 'Le mot de passe est obligatoire',
            'motdepasse.min' => 'Le mot de passe doit contenir au moins 6 caractères',
            'motdepasse.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;

class ProjectRequest extends FormRequest
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
        $projectId = $this->route('id'); // Per l'update, escludi il progetto corrente
        $isUpdate = !is_null($projectId); // Determina se è un aggiornamento
        $isDraftMode = $this->input('form_submit_mode') === 'draft';
        $existingProject = $isUpdate ? Project::query()->find($projectId) : null;
        $incomingTitle = trim((string) $this->input('title', ''));
        $existingTitle = trim((string) ($existingProject?->title ?? ''));
        $isTitleChanged = !$isUpdate || $incomingTitle !== $existingTitle;

        if ($isDraftMode) {
            $draftTitleRules = [
                'nullable',
                'string',
                'max:255',
            ];

            if ($incomingTitle !== '' && $isTitleChanged) {
                $draftTitleRules[] = \Illuminate\Validation\Rule::unique('projects', 'title')->ignore($projectId);
            }

            $draftRules = [
                'title' => $draftTitleRules,
                'category_id' => 'nullable|exists:categories,id',
                'association_id' => 'nullable|exists:associations,id',
                'user_id' => 'required|exists:users,id',
                'requested_people' => 'nullable|integer|min:0|max:999',
                'location' => 'nullable|string|max:255',
                'sum_description' => 'nullable|string|max:500',
                'full_description' => 'nullable|string',
                'requirements' => 'nullable|string',
                'travel_conditions' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'expire_date' => 'nullable|date|before:start_date',
                'image_path' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
                'form_submit_mode' => 'nullable|in:publish,draft',
                'completion_confirmed' => 'nullable|boolean',
            ];

            if ($isUpdate) {
                $draftRules['status'] = 'nullable|in:draft,published,completed';
            } else {
                $draftRules['status'] = 'nullable|in:draft,published';
            }

            return $draftRules;
        }
        
        $titleRules = [
            'required',
            'string',
            'min:5',
            'max:255',
        ];

        if ($isTitleChanged) {
            $titleRules[] = \Illuminate\Validation\Rule::unique('projects', 'title')->ignore($projectId);
        }

        $rules = [
            'title' => $titleRules,
            'category_id' => 'required|exists:categories,id',
            'association_id' => 'required|exists:associations,id',
            'user_id' => 'required|exists:users,id',
            'requested_people' => 'required|integer|min:1|max:999',
            'location' => 'required|string|min:2|max:255',
            'sum_description' => 'required|string|min:20|max:500',
            'full_description' => 'required|string|min:50',
            'requirements' => 'required|string|min:10',
            'travel_conditions' => 'required|string|min:10',
            'form_submit_mode' => 'nullable|in:publish,draft',
            'completion_confirmed' => 'nullable|boolean',
        ];

        // Regole per le date - bilanciate tra usabilità e correttezza
        if ($isUpdate) {
            // Durante l'aggiornamento, le date possono essere nel passato ma devono essere coerenti
            $rules['start_date'] = 'required|date';
            $rules['end_date'] = 'required|date|after:start_date';
            $rules['expire_date'] = 'required|date|before:start_date';
        } else {
            // Durante la creazione, le date devono essere future e coerenti
            $rules['start_date'] = 'required|date|after_or_equal:today';
            $rules['end_date'] = 'required|date|after:start_date';
            $rules['expire_date'] = 'required|date|after_or_equal:today|before:start_date';
        }

        // Regole per il campo immagine
        if ($isUpdate) {
            // Durante l'aggiornamento, l'immagine è opzionale
            $rules['image_path'] = 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048';
            // Durante l'aggiornamento, tutti gli stati sono permessi
            $rules['status'] = 'required|in:draft,published,completed';
        } else {
            // Durante la creazione, l'immagine è obbligatoria
            $rules['image_path'] = 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048';
            // Durante la creazione, solo draft e published sono permessi
            $rules['status'] = 'required|in:draft,published';
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Il titolo è obbligatorio.',
            'title.min' => 'Il titolo deve essere di almeno 5 caratteri.',
            'title.max' => 'Il titolo non può superare i 255 caratteri.',
            'title.unique' => 'Esiste già un progetto con questo titolo. Scegli un titolo diverso.',
            
            'category_id.required' => 'La categoria è obbligatoria.',
            'category_id.exists' => 'La categoria selezionata non è valida.',
            
            'association_id.required' => 'L\'associazione è obbligatoria.',
            'association_id.exists' => 'L\'associazione selezionata non è valida.',
            
            'user_id.required' => 'L\'autore del progetto è obbligatorio.',
            'user_id.exists' => 'L\'autore selezionato non è valido.',
            
            'image_path.required' => 'L\'immagine del progetto è obbligatoria.',
            'image_path.image' => 'Il file deve essere un\'immagine.',
            'image_path.mimes' => 'L\'immagine deve essere in formato JPEG, JPG, PNG, GIF o WEBP.',
            'image_path.max' => 'L\'immagine non può superare i 2MB.',
            
            'status.required' => 'Lo stato è obbligatorio.',
            'status.in' => 'Lo stato selezionato non è valido.',
            
            'requested_people.required' => 'Il numero di persone richieste è obbligatorio.',
            'requested_people.integer' => 'Il numero di persone richieste deve essere un numero intero.',
            'requested_people.min' => 'Il numero di persone richieste deve essere almeno 1.',
            'requested_people.max' => 'Il numero di persone richieste non può superare 999.',
            
            'location.required' => 'L\'ubicazione è obbligatoria.',
            'location.min' => 'L\'ubicazione deve essere di almeno 2 caratteri.',
            'location.max' => 'L\'ubicazione non può superare i 255 caratteri.',
            
            'start_date.required' => 'La data di inizio è obbligatoria.',
            'start_date.date' => 'La data di inizio deve essere una data valida.',
            'start_date.after_or_equal' => 'La data di inizio non può essere nel passato.',
            
            'end_date.required' => 'La data di fine è obbligatoria.',
            'end_date.date' => 'La data di fine deve essere una data valida.',
            'end_date.after' => 'La data di fine deve essere successiva alla data di inizio.',
            
            'expire_date.required' => 'La scadenza delle candidature è obbligatoria.',
            'expire_date.date' => 'La scadenza delle candidature deve essere una data valida.',
            'expire_date.after_or_equal' => 'La scadenza delle candidature non può essere nel passato.',
            'expire_date.before' => 'La scadenza delle candidature deve essere prima della data di inizio.',
            
            'sum_description.required' => 'La descrizione riassuntiva è obbligatoria.',
            'sum_description.min' => 'La descrizione riassuntiva deve essere di almeno 20 caratteri.',
            'sum_description.max' => 'La descrizione riassuntiva non può superare i 500 caratteri.',
            
            'full_description.required' => 'La descrizione completa è obbligatoria.',
            'full_description.min' => 'La descrizione completa deve essere di almeno 50 caratteri.',
            
            'requirements.required' => 'I requisiti sono obbligatori.',
            'requirements.min' => 'I requisiti devono essere di almeno 10 caratteri.',
            
            'travel_conditions.required' => 'Le condizioni di viaggio sono obbligatorie.',
            'travel_conditions.min' => 'Le condizioni di viaggio devono essere di almeno 10 caratteri.',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\CustomField;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntriesExport implements FromArray, WithHeadings
{
    private readonly Collection $customFields;

    /** @param array<string, mixed> $filters */
    public function __construct(
        private readonly array $filters,
        private readonly User $user,
    ) {
        $this->customFields = CustomField::active()->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Nb personnes',
            'Déjà venu',
            'Durée (min)',
            'Lieu',
            'Commune',
            'Support',
            'Thème',
            'Difficulté',
            'Genre',
            'Âge',
            'Statut',
            'Créé par',
            ...$this->customFields->pluck('label')->all(),
        ];
    }

    public function array(): array
    {
        $query = $this->user->is_admin
            ? Entry::with(['user', 'location', 'commune', 'theme', 'difficulty', 'age', 'statut', 'persons.age', 'persons.statut', 'customValues'])
            : $this->user->entries()->with(['location', 'commune', 'theme', 'difficulty', 'age', 'statut', 'persons.age', 'persons.statut', 'customValues']);

        if (! empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }
        if (! empty($this->filters['theme_id'])) {
            $query->where('theme_id', $this->filters['theme_id']);
        }
        if (! empty($this->filters['commune_id'])) {
            $query->where('commune_id', $this->filters['commune_id']);
        }
        if (! empty($this->filters['date_from'])) {
            $query->where('date', '>=', $this->filters['date_from']);
        }
        if (! empty($this->filters['date_to'])) {
            $query->where('date', '<=', $this->filters['date_to']);
        }

        $typeLabels = ['rdv' => 'RDV', 'collective' => 'Collectif', 'evenement' => 'Événement'];
        $materialLabels = ['notebook' => 'Carnet', 'phone' => 'Téléphone', 'tablet' => 'Tablette', 'mix' => 'Mix'];

        $rows = [];

        foreach ($query->orderByDesc('date')->get() as $entry) {
            $base = [
                $entry->date?->format('d/m/Y'),
                $typeLabels[$entry->type] ?? $entry->type,
                $entry->count,
                null, // was_here_before — filled per row below
                $entry->duration,
                $entry->location?->value,
                $entry->commune?->value,
                $materialLabels[$entry->material] ?? $entry->material,
                $entry->theme?->value,
                $entry->difficulty?->value,
                null, // genre
                null, // âge
                null, // statut
                $entry->user?->name ?? $this->user->name,
            ];

            $valuesByFieldId = $entry->customValues->keyBy('custom_field_id');
            $customColumns = $this->customFields->map(
                fn (CustomField $field) => $valuesByFieldId->get($field->id)?->value,
            )->all();

            if ($entry->type === 'collective' && $entry->persons->isNotEmpty()) {
                foreach ($entry->persons as $person) {
                    $row = $base;
                    $row[3] = $person->was_here_before ? 'Oui' : 'Non';
                    $row[10] = $person->gender ? ucfirst($person->gender) : null;
                    $row[11] = $person->age?->value;
                    $row[12] = $person->statut?->value;
                    $rows[] = array_merge($row, $customColumns);
                }
            } else {
                $row = $base;
                $row[3] = $entry->was_here_before ? 'Oui' : 'Non';
                $row[10] = $entry->gender ? ucfirst($entry->gender) : null;
                $row[11] = $entry->age?->value;
                $row[12] = $entry->statut?->value;
                $rows[] = array_merge($row, $customColumns);
            }
        }

        return $rows;
    }
}

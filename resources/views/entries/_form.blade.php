@php
    $isEdit = $entry !== null;
    $old = fn(string $key, $default = null) => old($key, $isEdit ? data_get($entry, $key) : $default);
    $materialLabels = ['notebook' => 'Carnet', 'phone' => 'Téléphone', 'tablet' => 'Tablette', 'mix' => 'Mix'];
    $typeLabels = ['rdv' => 'RDV', 'collective' => 'Collectif', 'evenement' => 'Événement'];
    $genderLabels = ['homme' => 'Homme', 'femme' => 'Femme', 'autre' => 'Autre'];
@endphp

<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('entries.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Retour</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Modifier la saisie' : 'Nouvelle saisie' }}</h1>
    </div>

    @if($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form
        method="POST"
        action="{{ $isEdit ? route('entries.update', $entry) : route('entries.store') }}"
        id="entry-form"
        class="space-y-6"
    >
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        {{-- Section 1: Date & Type --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-5">
            <h2 class="font-semibold text-gray-800">Informations générales</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date de l'intervention <span class="text-red-500">*</span></label>
                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ $old('date', date('Y-m-d')) }}"
                        required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                    <div class="flex gap-2 flex-wrap" id="type-chips">
                        @foreach($typeLabels as $val => $label)
                            <button
                                type="button"
                                data-chip-group="type"
                                data-chip-value="{{ $val }}"
                                class="chip-btn px-4 py-2 rounded-lg border-2 text-sm font-medium transition-all
                                    {{ $old('type') === $val ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}"
                            >{{ $label }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="type" id="input-type" value="{{ $old('type', 'rdv') }}">
                </div>
            </div>
        </div>

        {{-- Section 2: Count, Was here before, Duration --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-5">
            <h2 class="font-semibold text-gray-800">Détails de l'intervention</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                <div>
                    <label for="count" class="block text-sm font-medium text-gray-700 mb-1">Nb de personnes <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        id="count"
                        name="count"
                        value="{{ $old('count', 1) }}"
                        min="1"
                        required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    >
                </div>

                <div id="was-here-before-field">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Déjà venu ?</label>
                    <div class="flex gap-2">
                        <button type="button" data-chip-group="was_here_before" data-chip-value="1"
                            class="chip-btn px-4 py-2 rounded-lg border-2 text-sm font-medium transition-all
                                {{ $old('was_here_before') ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 bg-white text-gray-700 hover:border-green-300' }}">
                            Oui
                        </button>
                        <button type="button" data-chip-group="was_here_before" data-chip-value="0"
                            class="chip-btn px-4 py-2 rounded-lg border-2 text-sm font-medium transition-all
                                {{ !$old('was_here_before') ? 'border-gray-400 bg-gray-50 text-gray-700' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-400' }}">
                            Non
                        </button>
                    </div>
                    <input type="hidden" name="was_here_before" id="input-was_here_before" value="{{ $old('was_here_before', '0') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durée</label>
                    @php
                        $durationPresets = [30 => '30 min', 45 => '45 min', 60 => '1h', 90 => '1h30', 120 => '2h', 150 => '2h30', 180 => '3h'];
                        $currentDuration = $old('duration', $isEdit ? $entry->duration : '');
                        $isCustom = $currentDuration && !array_key_exists((int) $currentDuration, $durationPresets);
                    @endphp
                    <div class="flex flex-wrap gap-2" id="duration-chips">
                        @foreach($durationPresets as $minutes => $label)
                            <button type="button" data-chip-group="duration" data-chip-value="{{ $minutes }}"
                                class="chip-btn px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all
                                    {{ (int) $currentDuration === $minutes ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                        <button type="button" id="duration-plus-btn"
                            class="chip-btn px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all {{ $isCustom ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}"
                            data-chip-group="duration" data-chip-value="custom">
                            + (autre)
                        </button>
                    </div>
                    <input type="hidden" name="duration" id="input-duration" value="{{ $currentDuration }}">
                    <div id="duration-custom-form" class="{{ $isCustom ? '' : 'hidden' }} mt-2 flex gap-2 items-center">
                        <input type="number" id="duration-custom-value" min="1" placeholder="Minutes"
                            value="{{ $isCustom ? $currentDuration : '' }}"
                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm w-32 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <span class="text-sm text-gray-500">minutes</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Location, Commune, Material --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-5">
            <h2 class="font-semibold text-gray-800">Lieu & Support</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lieu</label>
                <div class="flex flex-wrap gap-2" id="location-chips" data-quick-add-type="location">
                    @foreach($locations as $loc)
                        <button type="button" data-chip-group="location_id" data-chip-value="{{ $loc->id }}"
                            class="chip-btn px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all
                                {{ $old('location_id') == $loc->id ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}">
                            {{ $loc->value }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="location_id" id="input-location_id" value="{{ $old('location_id', $isEdit ? $entry->location_id : '') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Commune</label>
                <div class="flex flex-wrap gap-2" id="commune-chips" data-quick-add-type="commune">
                    @foreach($communes as $commune)
                        <button type="button" data-chip-group="commune_id" data-chip-value="{{ $commune->id }}"
                            class="chip-btn px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all
                                {{ $old('commune_id') == $commune->id ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}">
                            {{ $commune->value }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="commune_id" id="input-commune_id" value="{{ $old('commune_id', $isEdit ? $entry->commune_id : '') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Support utilisé</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($materialLabels as $val => $label)
                        <button type="button" data-chip-group="material" data-chip-value="{{ $val }}"
                            class="chip-btn px-4 py-2 rounded-lg border-2 text-sm font-medium transition-all
                                {{ $old('material') === $val ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="material" id="input-material" value="{{ $old('material', $isEdit ? $entry->material : '') }}">
            </div>
        </div>

        {{-- Section 4: Theme, Difficulty --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-5">
            <h2 class="font-semibold text-gray-800">Thème & Autonomie</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Thème</label>
                <div class="flex flex-wrap gap-2" data-quick-add-type="theme">
                    @foreach($themes as $theme)
                        <button type="button" data-chip-group="theme_id" data-chip-value="{{ $theme->id }}"
                            class="chip-btn px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all
                                {{ $old('theme_id') == $theme->id ? 'border-2 text-white' : 'border-gray-200 bg-white text-gray-700 hover:opacity-80' }}"
                            @if($theme->color)
                                style="{{ $old('theme_id') == $theme->id ? 'background-color:' . $theme->color . ';border-color:' . $theme->color : 'hover-color:' . $theme->color }}"
                                data-color="{{ $theme->color }}"
                            @endif>
                            {{ $theme->value }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="theme_id" id="input-theme_id" value="{{ $old('theme_id', $isEdit ? $entry->theme_id : '') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau d'autonomie</label>
                <div class="flex flex-wrap gap-2" data-quick-add-type="difficulty">
                    @foreach($difficulties as $diff)
                        <button type="button" data-chip-group="difficulty_id" data-chip-value="{{ $diff->id }}"
                            class="chip-btn px-4 py-2 rounded-lg border-2 text-sm font-medium transition-all
                                {{ $old('difficulty_id') == $diff->id ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}"
                            @if($diff->color) data-color="{{ $diff->color }}" @endif>
                            {{ $diff->value }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="difficulty_id" id="input-difficulty_id" value="{{ $old('difficulty_id', $isEdit ? $entry->difficulty_id : '') }}">
            </div>
        </div>

        {{-- Section 5a: Person info (non-collectif) --}}
        <div id="single-person-section" class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-5">
            <h2 class="font-semibold text-gray-800">Informations sur la personne</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Genre</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($genderLabels as $val => $label)
                        <button type="button" data-chip-group="gender" data-chip-value="{{ $val }}"
                            class="chip-btn px-4 py-2 rounded-lg border-2 text-sm font-medium transition-all
                                {{ $old('gender') === $val ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="gender" id="input-gender" value="{{ $old('gender', $isEdit ? $entry->gender : '') }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tranche d'âge</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ages as $age)
                            <button type="button" data-chip-group="age_id" data-chip-value="{{ $age->id }}"
                                class="chip-btn px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all
                                    {{ $old('age_id') == $age->id ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}">
                                {{ $age->value }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="age_id" id="input-age_id" value="{{ $old('age_id', $isEdit ? $entry->age_id : '') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <div class="flex flex-wrap gap-2" data-quick-add-type="statut">
                        @foreach($statuts as $statut)
                            <button type="button" data-chip-group="statut_id" data-chip-value="{{ $statut->id }}"
                                class="chip-btn px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all
                                    {{ $old('statut_id') == $statut->id ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}">
                                {{ $statut->value }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="statut_id" id="input-statut_id" value="{{ $old('statut_id', $isEdit ? $entry->statut_id : '') }}">
                </div>
            </div>
        </div>

        {{-- Section 5b: Collectif persons --}}
        <div id="collective-persons-section" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
            <h2 class="font-semibold text-gray-800">Personnes du groupe</h2>
            <p class="text-sm text-gray-500">Renseignez les informations pour chaque personne du groupe.</p>
            <div id="persons-container" class="space-y-4">
                {{-- Persons will be generated by JS --}}
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('entries.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors shadow-sm">
                {{ $isEdit ? 'Mettre à jour' : 'Enregistrer la saisie' }}
            </button>
        </div>

    </form>
</div>

@php
    $existingPersons = $isEdit ? $entry->persons->map(fn($p) => ['gender' => $p->gender, 'was_here_before' => $p->was_here_before ? '1' : '0', 'age_id' => $p->age_id, 'statut_id' => $p->statut_id])->toArray() : [];
    $agesJson = $ages->map(fn($a) => ['id' => $a->id, 'value' => $a->value])->toJson();
    $statutsJson = $statuts->map(fn($s) => ['id' => $s->id, 'value' => $s->value])->toJson();
    $gendersJson = json_encode($genderLabels);
@endphp

<script>
(function() {
    // Chip selector logic
    function initChips() {
        document.querySelectorAll('.chip-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const group = this.dataset.chipGroup;
                const value = this.dataset.chipValue;
                selectChip(group, value, this);
            });
        });
    }

    function selectChip(group, value, btn) {
        if (btn && btn.textContent && btn.textContent.trim().toLowerCase().includes('bomboclat') && window.activateBomboclat) {
            window.activateBomboclat();
        }
        // Deselect all in group
        document.querySelectorAll('[data-chip-group="' + group + '"]').forEach(function(b) {
            b.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700', 'border-green-500', 'bg-green-50', 'text-green-700', 'border-gray-400', 'bg-gray-50');
            b.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
            if (b.dataset.color) {
                b.style.backgroundColor = '';
                b.style.borderColor = '';
                b.style.color = '';
            }
        });

        // Select clicked
        btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
        if (btn.dataset.color && (group === 'theme_id' || group === 'difficulty_id')) {
            btn.style.backgroundColor = btn.dataset.color;
            btn.style.borderColor = btn.dataset.color;
            btn.style.color = '#fff';
        } else if (group === 'was_here_before' && value === '1') {
            btn.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
        } else if (group === 'was_here_before' && value === '0') {
            btn.classList.add('border-gray-400', 'bg-gray-50', 'text-gray-700');
        } else {
            btn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
        }

        const hidden = document.getElementById('input-' + group);
        if (hidden) {
            hidden.value = value;
        }

        // If type changed, update person sections
        if (group === 'type') {
            updatePersonSections(value);
        }

        // If count changed for collective, regenerate persons
        if (group === 'was_here_before') {
            // nothing extra needed
        }
    }

    // Type-based sections
    function updatePersonSections(type) {
        const single = document.getElementById('single-person-section');
        const collective = document.getElementById('collective-persons-section');
        const wasHereField = document.getElementById('was-here-before-field');
        if (type === 'collective') {
            single.classList.add('hidden');
            collective.classList.remove('hidden');
            wasHereField.classList.add('hidden');
            regeneratePersons();
        } else {
            single.classList.remove('hidden');
            collective.classList.add('hidden');
            wasHereField.classList.remove('hidden');
        }
    }

    // Persons for collective
    const ages = @json($ages->map(fn($a) => ['id' => $a->id, 'value' => $a->value]));
    const statuts = @json($statuts->map(fn($s) => ['id' => $s->id, 'value' => $s->value]));
    const genders = [{value: 'homme', label: 'Homme'}, {value: 'femme', label: 'Femme'}, {value: 'autre', label: 'Autre'}];
    const existingPersons = @json($existingPersons);

    function regeneratePersons() {
        const count = parseInt(document.getElementById('count').value) || 1;
        const container = document.getElementById('persons-container');
        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const existing = existingPersons[i] || null;
            container.appendChild(buildPersonCard(i, existing));
        }
        // Re-init chips in new persons
        container.querySelectorAll('.chip-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                personChipSelect(this);
            });
        });
    }

    function personChipSelect(btn) {
        if (btn.textContent && btn.textContent.trim().toLowerCase().includes('bomboclat') && window.activateBomboclat) {
            window.activateBomboclat();
        }
        const group = btn.dataset.chipGroup;
        // Deselect siblings
        btn.closest('.person-chip-group').querySelectorAll('[data-chip-group="' + group + '"]').forEach(function(b) {
            b.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
            b.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
        });
        btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
        btn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');

        const hiddenName = btn.dataset.hiddenName;
        const hidden = btn.closest('.person-chip-group').querySelector('[name="' + hiddenName + '"]');
        if (hidden) { hidden.value = btn.dataset.chipValue; }
    }

    function buildPersonCard(index, existing) {
        const card = document.createElement('div');
        card.className = 'border border-gray-200 rounded-xl p-4 space-y-3 bg-gray-50';
        card.innerHTML = '<h3 class="text-sm font-semibold text-gray-700">Personne ' + (index + 1) + '</h3>';

        // Was here before
        const wasHereGroup = buildChipGroup('Déjà venu·e ?', [
            {value: '1', label: 'Oui'},
            {value: '0', label: 'Non'}
        ], 'persons[' + index + '][was_here_before]', existing ? String(existing.was_here_before ?? '0') : '0');
        card.appendChild(wasHereGroup);

        // Gender
        const genderGroup = buildChipGroup('Genre', [
            {value: 'homme', label: 'Homme'},
            {value: 'femme', label: 'Femme'},
            {value: 'autre', label: 'Autre'}
        ], 'persons[' + index + '][gender]', existing ? existing.gender : '');
        card.appendChild(genderGroup);

        // Age
        const ageGroup = buildChipGroup('Tranche d\'âge', ages.map(a => ({value: String(a.id), label: a.value})), 'persons[' + index + '][age_id]', existing && existing.age_id ? String(existing.age_id) : '');
        card.appendChild(ageGroup);

        // Statut
        const statutGroup = buildChipGroup('Statut', statuts.map(s => ({value: String(s.id), label: s.value})), 'persons[' + index + '][statut_id]', existing && existing.statut_id ? String(existing.statut_id) : '');
        card.appendChild(statutGroup);

        return card;
    }

    function buildChipGroup(label, options, hiddenName, selectedValue) {
        const div = document.createElement('div');
        div.className = 'person-chip-group';
        const lbl = document.createElement('p');
        lbl.className = 'text-xs font-medium text-gray-600 mb-1.5';
        lbl.textContent = label;
        div.appendChild(lbl);

        const chips = document.createElement('div');
        chips.className = 'flex flex-wrap gap-1.5';

        options.forEach(function(opt) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'chip-btn px-3 py-1 rounded-full border-2 text-xs font-medium transition-all ' +
                (String(selectedValue) === String(opt.value) ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300');
            btn.textContent = opt.label;
            btn.dataset.chipGroup = hiddenName.replace(/\[/g, '_').replace(/\]/g, '');
            btn.dataset.chipValue = opt.value;
            btn.dataset.hiddenName = hiddenName;
            chips.appendChild(btn);
        });

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = hiddenName;
        hidden.value = selectedValue;

        div.appendChild(chips);
        div.appendChild(hidden);
        return div;
    }

    // Duration preset chips + custom input
    (function() {
        const customForm = document.getElementById('duration-custom-form');
        const customInput = document.getElementById('duration-custom-value');
        const hidden = document.getElementById('input-duration');
        const plusBtn = document.getElementById('duration-plus-btn');

        document.querySelectorAll('[data-chip-group="duration"]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('[data-chip-group="duration"]').forEach(function(b) {
                    b.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
                    b.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
                });
                btn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
                btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');

                if (btn.dataset.chipValue === 'custom') {
                    customForm.classList.remove('hidden');
                    hidden.value = customInput.value || '';
                    customInput.focus();
                } else {
                    customForm.classList.add('hidden');
                    hidden.value = btn.dataset.chipValue;
                }
            });
        });

        customInput.addEventListener('input', function() {
            hidden.value = this.value;
        });
    })();

    // Count input triggers person regeneration for collective
    document.getElementById('count').addEventListener('input', function() {
        const type = document.getElementById('input-type').value;
        if (type === 'collective') {
            regeneratePersons();
        }
    });

    // Generic quick-add for any option chip group
    const typeToHiddenInput = {
        location: 'location_id',
        commune: 'commune_id',
        theme: 'theme_id',
        difficulty: 'difficulty_id',
        statut: 'statut_id',
    };

    function attachQuickAdd(container) {
        const optionType = container.dataset.quickAddType;
        const hiddenInputName = typeToHiddenInput[optionType];
        if (!hiddenInputName) { return; }

        const plusBtn = document.createElement('button');
        plusBtn.type = 'button';
        plusBtn.className = 'quick-add-plus px-2 py-1.5 rounded-full border-2 border-dashed border-gray-300 text-sm font-medium text-gray-400 hover:border-indigo-400 hover:text-indigo-500 transition-all leading-none';
        plusBtn.textContent = '+';
        container.appendChild(plusBtn);

        const inlineForm = document.createElement('div');
        inlineForm.className = 'hidden mt-2 flex gap-2 items-center';
        inlineForm.innerHTML =
            '<input type="text" placeholder="Nouveau..." class="quick-add-input rounded-lg border border-gray-300 px-3 py-1.5 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-indigo-400">' +
            '<button type="button" class="quick-add-save bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-indigo-700 transition-colors">OK</button>' +
            '<button type="button" class="quick-add-cancel text-gray-400 text-sm px-1 hover:text-gray-600">✕</button>';
        container.parentElement.appendChild(inlineForm);

        const input = inlineForm.querySelector('.quick-add-input');
        const saveBtn = inlineForm.querySelector('.quick-add-save');
        const cancelBtn = inlineForm.querySelector('.quick-add-cancel');

        plusBtn.addEventListener('click', function() {
            inlineForm.classList.remove('hidden');
            plusBtn.classList.add('hidden');
            input.focus();
        });

        cancelBtn.addEventListener('click', function() {
            inlineForm.classList.add('hidden');
            plusBtn.classList.remove('hidden');
            input.value = '';
        });

        function doSave() {
            const value = input.value.trim();
            if (!value) { return; }

            // Check if already exists in the chip list
            const existingChip = Array.from(container.querySelectorAll('.chip-btn')).find(function(b) {
                return b.textContent.trim().toLowerCase() === value.toLowerCase();
            });
            if (existingChip) {
                selectChip(hiddenInputName, existingChip.dataset.chipValue, existingChip);
                inlineForm.classList.add('hidden');
                plusBtn.classList.remove('hidden');
                input.value = '';
                if (value.toLowerCase().includes('bomboclat') && window.activateBomboclat) {
                    window.activateBomboclat();
                }
                return;
            }

            // Also trigger bomboclat if the new value itself contains it
            if (value.toLowerCase().includes('bomboclat') && window.activateBomboclat) {
                window.activateBomboclat();
            }

            saveBtn.disabled = true;
            saveBtn.textContent = '…';

            fetch('{{ route('options.quick-add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({type: optionType, value: value})
            })
            .then(function(r) { return r.ok ? r.json() : {id: 0, value: value}; })
            .then(function(data) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'chip-btn px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all border-indigo-500 bg-indigo-50 text-indigo-700';
                btn.dataset.chipGroup = hiddenInputName;
                btn.dataset.chipValue = data.id;
                btn.textContent = data.value;
                btn.addEventListener('click', function() { selectChip(hiddenInputName, data.id, btn); });
                container.insertBefore(btn, plusBtn);
                selectChip(hiddenInputName, data.id, btn);
                inlineForm.classList.add('hidden');
                plusBtn.classList.remove('hidden');
                input.value = '';
                saveBtn.disabled = false;
                saveBtn.textContent = 'OK';
            })
            .catch(function() {
                saveBtn.disabled = false;
                saveBtn.textContent = 'OK';
            });
        }

        saveBtn.addEventListener('click', doSave);
        input.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); doSave(); } });
    }

    document.querySelectorAll('[data-quick-add-type]').forEach(attachQuickAdd);

    // Init
    initChips();

    // Initial state
    const initialType = document.getElementById('input-type').value;
    updatePersonSections(initialType);

    // Ensure the currently selected type chip is visually active on load
    const activeTypeBtn = document.querySelector('[data-chip-group="type"][data-chip-value="' + initialType + '"]');
    if (activeTypeBtn) {
        activeTypeBtn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
        activeTypeBtn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
    }

})();
</script>

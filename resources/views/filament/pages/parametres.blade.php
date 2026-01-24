<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-4">
        <x-filament::input
            label="Nom de l'école"
            wire:model.defer="data.nom_ecole"
        />

        <x-filament::input
            label="Email de contact"
            wire:model.defer="data.email_contact"
        />

        <x-filament::input
            label="Téléphone"
            wire:model.defer="data.telephone"
        />

        <x-filament::button type="submit">
            Enregistrer
        </x-filament::button>
    </form>
</x-filament::page>

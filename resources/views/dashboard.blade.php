<x-excel-importer::layout>
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Excel Import</h1>
        <p class="mt-2 text-gray-600">Upload, preview, and import Excel/CSV files with progress tracking and error handling.</p>
    </div>

    <livewire:excel-importer :type="$type ?? null" />
</x-excel-importer::layout>

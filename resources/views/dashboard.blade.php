<x-excel-importer::layout>
    <div class="ei-card" style="margin-bottom: 16px;">
        <h2 class="ei-title">Excel Importer</h2>
        <p class="ei-subtitle">Use this dashboard to upload, preview, and manage imports.</p>
        <div class="ei-muted">Route: <strong>{{ route('excel-importer.dashboard') }}</strong></div>
    </div>

    <livewire:excel-importer :type="$type ?? null" />
</x-excel-importer::layout>

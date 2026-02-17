<div>
    <div class="ei-card">
        <div class="ei-row" style="justify-content: space-between;">
            <div>
                <h2 class="ei-title">Import Dashboard</h2>
                <p class="ei-subtitle">Upload, preview, import, and manage error files.</p>
            </div>
            <span class="ei-badge">Excel Importer</span>
        </div>

        @if(empty($importTypes))
            <div class="ei-error" style="margin-top: 16px;">
                No import types are configured. Add at least one type in config/excel-importer.php.
            </div>
        @endif

        <div class="ei-section">
            <label class="ei-label" for="importType">Import Type</label>
            <select id="importType" class="ei-select" wire:model="importType">
                @foreach($importTypes as $type => $class)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>

        @if(!$showPreview && !$importing)
            <div class="ei-section">
                <label class="ei-label">Select File</label>
                <input type="file" wire:model="file" class="ei-input" accept=".xlsx,.xls,.csv">
                @if($file)
                    <div class="ei-muted" style="margin-top: 6px;">Selected: {{ $file->getClientOriginalName() }}</div>
                @endif

                @error('file')
                    <div class="ei-error" style="margin-top: 10px;">{{ $message }}</div>
                @enderror

                <div wire:loading wire:target="file" class="ei-muted" style="margin-top: 10px;">Loading preview...</div>
            </div>
        @endif

        @if($showPreview && !$importing)
            <div class="ei-section">
                <div class="ei-row" style="justify-content: space-between; margin-bottom: 10px;">
                    <div>
                        <strong>Preview Data</strong>
                        <div class="ei-muted">Total rows: {{ $previewData['total'] }}</div>
                    </div>
                    <div class="ei-row">
                        <button type="button" class="ei-button secondary" wire:click="cancelImport">Cancel</button>
                        <button type="button" class="ei-button" wire:click="startImport">Start Import</button>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="ei-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                @foreach($previewData['headers'] as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewData['rows'] as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    @foreach($row as $cell)
                                        <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($importing && $import)
            <div class="ei-section" @if(!$import->isCompleted()) wire:poll.2s="checkProgress" @endif>
                <div class="ei-row" style="justify-content: space-between;">
                    <div>
                        <strong>
                            @if($import->isCompleted())
                                @if($import->isFailed())
                                    Import Failed
                                @else
                                    Import Completed
                                @endif
                            @else
                                Processing Import...
                            @endif
                        </strong>
                        <div class="ei-muted">{{ $import->original_filename }}</div>
                    </div>
                    <span class="ei-badge">{{ strtoupper($import->status) }}</span>
                </div>

                @if(!$import->isCompleted())
                    <div style="margin-top: 16px;">
                        <div class="ei-row" style="justify-content: space-between;">
                            <span class="ei-muted">Progress</span>
                            <strong>{{ $import->progress_percentage }}%</strong>
                        </div>
                        <div class="ei-progress" style="margin-top: 6px;">
                            <div style="width: {{ $import->progress_percentage }}%"></div>
                        </div>
                    </div>
                @endif

                <div class="ei-grid" style="margin-top: 16px;">
                    <div class="ei-card" style="box-shadow: none;">
                        <div><strong>{{ number_format($import->total_rows) }}</strong></div>
                        <div class="ei-muted">Total Rows</div>
                    </div>
                    <div class="ei-card" style="box-shadow: none;">
                        <div><strong>{{ number_format($import->processed_rows) }}</strong></div>
                        <div class="ei-muted">Processed</div>
                    </div>
                    <div class="ei-card" style="box-shadow: none;">
                        <div><strong>{{ number_format($import->successful_rows) }}</strong></div>
                        <div class="ei-muted">Successful</div>
                    </div>
                    <div class="ei-card" style="box-shadow: none;">
                        <div><strong>{{ number_format($import->failed_rows) }}</strong></div>
                        <div class="ei-muted">Failed</div>
                    </div>
                </div>

                @if($import->isFailed() && $import->error_message)
                    <div class="ei-error" style="margin-top: 12px;">{{ $import->error_message }}</div>
                @endif

                @if($import->isCompleted())
                    <div class="ei-row" style="margin-top: 16px;">
                        @if($import->error_path)
                            <button type="button" class="ei-button danger" wire:click="downloadErrors">Download Error Report</button>
                        @endif
                        <button type="button" class="ei-button secondary" wire:click="cancelImport">Import Another File</button>
                        <button type="button" class="ei-button" wire:click="clearImport">Clear Import Files</button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="ei-card ei-section">
        <h3 class="ei-title" style="font-size: 18px;">Recent Imports</h3>
        @if($recentImports->isEmpty())
            <div class="ei-muted">No imports yet. Start by selecting a file above.</div>
        @else
            <div style="overflow-x: auto;">
                <table class="ei-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Processed</th>
                            <th>Failed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentImports as $recent)
                            <tr>
                                <td>#{{ $recent->id }}</td>
                                <td>{{ $recent->import_type }}</td>
                                <td>{{ $recent->original_filename }}</td>
                                <td>{{ $recent->status }}</td>
                                <td>{{ $recent->processed_rows }}</td>
                                <td>{{ $recent->failed_rows }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<div class="space-y-6">
    <!-- Upload Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Import Data</h2>
                <p class="mt-1 text-sm text-gray-600">Upload, preview, and import Excel/CSV files</p>
            </div>
            <span class="inline-block px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Excel Importer</span>
        </div>

        @if(empty($importTypes))
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-red-800">
                <p class="font-medium">No import types configured</p>
                <p class="text-sm mt-1">Add at least one type in <code class="bg-red-100 px-2 py-1 rounded text-xs">config/excel-importer.php</code></p>
            </div>
        @else
            <div class="space-y-6">
                <!-- Import Type Select -->
                <div>
                    <label for="importType" class="block text-sm font-medium text-gray-700 mb-2">Import Type</label>
                    <select id="importType" wire:model="importType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($importTypes as $type => $class)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>

                @if(!$showPreview && !$importing)
                    <!-- File Upload -->
                    <div>
                        <label for="fileInput" class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition cursor-pointer" onclick="document.getElementById('fileInput').click()">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Click to select or drag and drop</p>
                            <p class="text-xs text-gray-500 mt-1">XLSX, XLS, or CSV (max 10MB)</p>
                        </div>
                        <input type="file" id="fileInput" wire:model="file" class="hidden" accept=".xlsx,.xls,.csv">

                        @if($file)
                            <div class="mt-3 flex items-center text-sm text-green-700 bg-green-50 p-3 rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $file->getClientOriginalName() }}</span>
                            </div>
                        @endif

                        @error('file')
                            <div class="mt-3 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800 text-sm">{{ $message }}</div>
                        @enderror

                        <div wire:loading wire:target="file" class="mt-3 text-sm text-blue-600">
                            <svg class="animate-spin h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading preview...
                        </div>
                    </div>
                @endif

                @if($showPreview && !$importing)
                    <!-- Preview Section -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">Preview Data</h3>
                                <p class="text-sm text-gray-600">Total rows: <span class="font-medium">{{ $previewData['total'] }}</span></p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" wire:click="cancelImport" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">Cancel</button>
                                <button type="button" wire:click="startImport" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-sm font-medium transition">Start Import</button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-900">#</th>
                                        @foreach($previewData['headers'] as $header)
                                            <th class="px-4 py-3 text-left font-medium text-gray-900">{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($previewData['rows'] as $index => $row)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                                            @foreach($row as $cell)
                                                <td class="px-4 py-3 text-gray-900">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if($importing && $import)
                    <!-- Progress Section -->
                    <div @if(!$import->isCompleted()) wire:poll.2s="checkProgress" @endif>
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        @if($import->isCompleted())
                                            @if($import->isFailed())
                                                <span class="text-red-600">❌ Import Failed</span>
                                            @else
                                                <span class="text-green-600">✓ Import Completed</span>
                                            @endif
                                        @else
                                            <span class="text-blue-600">⏳ Processing Import...</span>
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $import->original_filename }}</p>
                                </div>
                                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
                                    @if($import->status === 'completed')
                                        bg-green-100 text-green-800
                                    @elseif($import->status === 'failed')
                                        bg-red-100 text-red-800
                                    @elseif($import->status === 'processing')
                                        bg-blue-100 text-blue-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">{{ strtoupper($import->status) }}</span>
                            </div>

                            @if(!$import->isCompleted())
                                <div class="mt-6">
                                    <div class="flex items-center justify-between text-sm mb-2">
                                        <span class="text-gray-600">Progress</span>
                                        <span class="font-semibold text-gray-900">{{ $import->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $import->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="text-2xl font-bold text-gray-900">{{ number_format($import->total_rows) }}</div>
                                    <div class="text-xs text-gray-600 mt-1">Total Rows</div>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <div class="text-2xl font-bold text-blue-600">{{ number_format($import->processed_rows) }}</div>
                                    <div class="text-xs text-gray-600 mt-1">Processed</div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($import->successful_rows) }}</div>
                                    <div class="text-xs text-gray-600 mt-1">Successful</div>
                                </div>
                                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                    <div class="text-2xl font-bold text-red-600">{{ number_format($import->failed_rows) }}</div>
                                    <div class="text-xs text-gray-600 mt-1">Failed</div>
                                </div>
                            </div>

                            @if($import->isFailed() && $import->error_message)
                                <div class="mt-6 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800 text-sm">
                                    <p class="font-medium">Error:</p>
                                    <p class="mt-1">{{ $import->error_message }}</p>
                                </div>
                            @endif

                            @if($import->isCompleted())
                                <div class="mt-6 flex flex-wrap gap-3">
                                    @if($import->error_path)
                                    <button type="button" wire:click="downloadErrors" class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg text-sm font-medium transition inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download Errors
                                    </button>
                                    @endif
                                    <button type="button" wire:click="cancelImport" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-sm font-medium transition">Import Another</button>
                                    <button type="button" wire:click="clearImport" class="px-4 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-lg text-sm font-medium transition">Clear Files & Records</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Recent Imports -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Imports</h3>
        @if($recentImports->isEmpty())
            <p class="text-gray-600 text-sm">No imports yet. Upload a file above to get started.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">ID</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">Type</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">File</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">Processed</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">Failed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentImports as $recent)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">#{{ $recent->id }}</td>
                                <td class="px-4 py-3"><span class="inline-block px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">{{ ucfirst($recent->import_type) }}</span></td>
                                <td class="px-4 py-3 text-gray-900">{{ $recent->original_filename }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded
                                        @if($recent->status === 'completed')
                                            bg-green-100 text-green-800
                                        @elseif($recent->status === 'failed')
                                            bg-red-100 text-red-800
                                        @else
                                            bg-blue-100 text-blue-800
                                        @endif
                                    ">{{ ucfirst($recent->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-900">{{ $recent->processed_rows }}</td>
                                <td class="px-4 py-3">
                                    @if($recent->failed_rows > 0)
                                        <span class="text-red-600 font-medium">{{ $recent->failed_rows }}</span>
                                    @else
                                        <span class="text-gray-600">{{ $recent->failed_rows }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 rounded-lg border border-blue-200 p-8">
        <h3 class="text-lg font-bold text-blue-900 mb-4">📋 File Format Requirements</h3>
        <ul class="space-y-3 text-sm text-blue-800">
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>Supported formats:</strong> XLSX, XLS, CSV</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>Max file size:</strong> 10MB</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>First row:</strong> Column headers (name, email, etc.)</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>Validation:</strong> Errors shown in preview and error report</span>
            </li>
        </ul>

        <div class="mt-6 pt-6 border-t border-blue-300">
            <p class="text-blue-900 font-medium text-sm mb-3">📥 Sample Files:</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ asset('sample-import.csv') }}" download class="inline-flex items-center px-3 py-2 bg-white text-blue-700 hover:bg-blue-100 rounded-lg text-sm font-medium transition border border-blue-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Sample Data
                </a>
                <a href="{{ asset('sample-10k.csv') }}" download class="inline-flex items-center px-3 py-2 bg-white text-blue-700 hover:bg-blue-100 rounded-lg text-sm font-medium transition border border-blue-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Large Dataset (10K Rows)
                </a>
                <a href="{{ asset('sample-with-errors.csv') }}" download class="inline-flex items-center px-3 py-2 bg-white text-blue-700 hover:bg-blue-100 rounded-lg text-sm font-medium transition border border-blue-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Test Errors
                </a>
            </div>
        </div>
    </div>
</div>

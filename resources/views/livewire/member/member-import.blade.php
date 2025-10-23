<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MembersImport;
use App\Exports\MembersTemplateExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

new class extends Component {
    use WithFileUploads;

    public $file;
    public $importing = false;
    public $importFinished = false;
    public $errorMessages = [];

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.mimes' => 'File harus berformat xlsx, xls, atau csv.',
            'file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $this->importing = true;
        $this->errorMessages = [];
        $this->importFinished = false;

        try {
            Excel::import(new MembersImport, $this->file->getRealPath());
            
            $this->importFinished = true;
            $this->file = null;
            
            session()->flash('success', 'Data berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            foreach ($failures as $failure) {
                $this->errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
        } catch (\Exception $e) {
            $this->errorMessages[] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        $this->importing = false;
    }

    public function downloadTemplate()
    {
        return Excel::download(new MembersTemplateExport, 'template_members.xlsx');
    }

    public function resetImport()
    {
        $this->file = null;
        $this->importing = false;
        $this->importFinished = false;
        $this->errorMessages = [];
    }
}; ?>

<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Import Data Members</h2>
            
            {{-- Download Template Button --}}
            <button 
                wire:click="downloadTemplate"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition duration-200"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Template
            </button>
        </div>

        {{-- Success Message --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start">
                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Error Messages --}}
        @if (!empty($errorMessages))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <div class="flex items-start mb-2">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">Error saat import:</span>
                </div>
                <ul class="ml-7 list-disc space-y-1">
                    @foreach ($errorMessages as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form wire:submit.prevent="import">
            {{-- File Input --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih File Excel
                </label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                            </p>
                            <p class="text-xs text-gray-500">XLSX, XLS, atau CSV (Max. 2MB)</p>
                        </div>
                        <input type="file" wire:model="file" class="hidden" accept=".xlsx,.xls,.csv" />
                    </label>
                </div>
                
                @if ($file)
                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-blue-800">{{ $file->getClientOriginalName() }}</span>
                        </div>
                        <button type="button" wire:click="resetImport" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Loading Indicator --}}
            <div wire:loading wire:target="file" class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="animate-spin h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">Mengupload file...</span>
                </div>
            </div>

            {{-- Submit Button --}}
            <button 
                type="submit" 
                wire:loading.attr="disabled"
                wire:target="import"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                @if(!$file) disabled @endif
            >
                <span wire:loading.remove wire:target="import">
                    <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Import Data
                </span>
                <span wire:loading wire:target="import" class="flex items-center">
                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengimport...
                </span>
            </button>
        </form>

        {{-- Template Information --}}
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-start mb-3">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">Format Excel Template:</p>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li><code class="bg-gray-200 px-2 py-1 rounded">order</code> - Nomor urut (angka)</li>
                            <li><code class="bg-gray-200 px-2 py-1 rounded">name</code> - Nama (teks)</li>
                            <li><code class="bg-gray-200 px-2 py-1 rounded">gender</code> - Jenis kelamin (1=Laki-laki, 2=Perempuan)</li>
                            <li><code class="bg-gray-200 px-2 py-1 rounded">phone</code> - Nomor telepon (teks)</li>
                        </ul>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        💡 Download template di atas untuk memastikan format yang benar
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
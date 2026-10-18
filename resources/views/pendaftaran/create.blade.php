@extends('layouts.app')

@section('title', 'Tambah Pendaftaran OSIS')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-sm text-slate-500 gap-2">
        <a href="{{ route('pendaftaran.index') }}" class="hover:text-indigo-600 transition">Daftar Pendaftar</a>
        <span>/</span>
        <span class="text-slate-800 font-medium">Tambah Pendaftaran Baru</span>
    </nav>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Formulir Pendaftaran OSIS</h1>
            <p class="text-sm text-slate-500 mt-1">
                Data akan divalidasi dan dikirim langsung ke REST API OSIS.
            </p>
        </div>

        <form action="{{ route('pendaftaran.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Dropdown Siswa (FR-2) -->
            <div>
                <label for="siswa_id" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Pilih Siswa <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="siswa_id" id="siswa_id" 
                            class="w-full px-3.5 py-2.5 rounded-xl border bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition @error('siswa_id') border-rose-400 bg-rose-50/20 text-rose-900 @else border-slate-300 @enderror">
                        <option value="">-- Pilih Siswa Calon Pengurus --</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s['id'] }}" {{ old('siswa_id') == $s['id'] ? 'selected' : '' }}>
                                {{ $s['nama'] }} 
                                @if (isset($s['kelas']['nama_kelas']))
                                    ({{ $s['kelas']['nama_kelas'] }})
                                @endif
                                @if (isset($s['nis']))
                                    - NIS: {{ $s['nis'] }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="text-xs text-slate-500 mt-1.5">
                    Data siswa ditarik dari endpoint <code class="font-mono text-indigo-600">GET /api/siswa</code>. Hanya siswa yang belum mendaftar yang dapat didaftarkan.
                </p>
                @error('siswa_id')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Pilihan Bidang (FR-2) -->
            <div>
                <label for="pilihan_bidang" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Pilihan Bidang <span class="text-rose-500">*</span>
                </label>
                <select name="pilihan_bidang" id="pilihan_bidang" 
                        class="w-full px-3.5 py-2.5 rounded-xl border bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition @error('pilihan_bidang') border-rose-400 bg-rose-50/20 text-rose-900 @else border-slate-300 @enderror">
                    <option value="">-- Pilih Bidang OSIS --</option>
                    <option value="kepemimpinan" {{ old('pilihan_bidang') == 'kepemimpinan' ? 'selected' : '' }}>Kepemimpinan (Ketua, Wakil, Sekretaris, Bendahara)</option>
                    <option value="keagamaan" {{ old('pilihan_bidang') == 'keagamaan' ? 'selected' : '' }}>Keagamaan & Ketaqwaan</option>
                    <option value="olahraga" {{ old('pilihan_bidang') == 'olahraga' ? 'selected' : '' }}>Olahraga & Kesehatan</option>
                    <option value="seni" {{ old('pilihan_bidang') == 'seni' ? 'selected' : '' }}>Seni & Budaya</option>
                </select>
                <p class="text-xs text-slate-500 mt-1.5">
                    Pilihan yang diterima API: <span class="font-mono text-slate-700">kepemimpinan, keagamaan, olahraga, seni</span>.
                </p>
                @error('pilihan_bidang')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Motivasi (FR-2) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="motivasi" class="block text-sm font-semibold text-slate-700">
                        Motivasi Mendaftar <span class="text-rose-500">*</span>
                    </label>
                    <span id="charCount" class="text-xs text-slate-400 font-mono">0 / 300 karakter (min. 20)</span>
                </div>
                <textarea name="motivasi" id="motivasi" rows="4" 
                          placeholder="Jelaskan alasan dan motivasimu mendaftar pengurus OSIS (minimal 20 karakter)..."
                          class="w-full px-3.5 py-2.5 rounded-xl border bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition @error('motivasi') border-rose-400 bg-rose-50/20 text-rose-900 @else border-slate-300 @enderror">{{ old('motivasi') }}</textarea>
                <p class="text-xs text-slate-500 mt-1.5">
                    Panjang motivasi wajib antara 20 sampai 300 karakter sesuai aturan validasi API OSIS.
                </p>
                @error('motivasi')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('pendaftaran.index') }}" 
                   class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Pendaftaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const textarea = document.getElementById('motivasi');
        const charCount = document.getElementById('charCount');

        function updateCount() {
            const length = textarea.value.length;
            charCount.textContent = `${length} / 300 karakter (min. 20)`;
            if (length < 20 || length > 300) {
                charCount.classList.add('text-amber-600');
                charCount.classList.remove('text-slate-400', 'text-emerald-600');
            } else {
                charCount.classList.add('text-emerald-600');
                charCount.classList.remove('text-amber-600', 'text-slate-400');
            }
        }

        textarea.addEventListener('input', updateCount);
        updateCount();
    });
</script>
@endpush

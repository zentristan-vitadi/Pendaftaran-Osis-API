@extends('layouts.app')

@section('title', 'Daftar Pendaftaran OSIS')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Pendaftaran OSIS</h1>
            <p class="text-sm text-slate-500 mt-1">
                Data diperoleh langsung secara real-time dari <strong>REST API OSIS (Project A)</strong>.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pendaftaran.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pendaftaran
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Pendaftar</p>
                <p class="text-2xl font-bold text-slate-900">{{ count($pendaftaran) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Endpoint API</p>
                <p class="text-sm font-semibold font-mono text-slate-700">/api/pendaftaran-osis</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Arsitektur</p>
                <p class="text-sm font-semibold text-slate-700">Laravel Client (Port 8001)</p>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800 text-base">Tabel Calon Pengurus</h2>
            <span class="text-xs font-medium text-slate-500">Menampilkan {{ count($pendaftaran) }} data</span>
        </div>

        @if (count($pendaftaran) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 w-12 text-center">#</th>
                            <th scope="col" class="px-6 py-3.5">Siswa</th>
                            <th scope="col" class="px-6 py-3.5">Kelas</th>
                            <th scope="col" class="px-6 py-3.5">Jenis Kelamin</th>
                            <th scope="col" class="px-6 py-3.5">Pilihan Bidang</th>
                            <th scope="col" class="px-6 py-3.5">Motivasi</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pendaftaran as $index => $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 text-center font-medium text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">
                                        {{ $item['siswa']['nama'] ?? 'Siswa ID: ' . ($item['siswa_id'] ?? '-') }}
                                    </div>
                                    @if (isset($item['siswa']['nis']))
                                        <div class="text-xs text-slate-400 font-mono">NIS: {{ $item['siswa']['nis'] }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $item['siswa']['kelas']['nama_kelas'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-600">
                                        {{ $item['siswa']['jenis_kelamin']['nama'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $bidang = strtolower($item['pilihan_bidang'] ?? '');
                                        $badgeColor = match($bidang) {
                                            'kepemimpinan' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'keagamaan' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'olahraga' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'seni' => 'bg-purple-100 text-purple-700 border-purple-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeColor }}">
                                        {{ ucfirst($item['pilihan_bidang'] ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <p class="text-slate-700 truncate" title="{{ $item['motivasi'] ?? '' }}">
                                        {{ $item['motivasi'] ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Button -->
                                        <a href="{{ route('pendaftaran.edit', $item['id']) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition"
                                           title="Edit Data">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            Edit
                                        </a>

                                        <!-- Delete Form -->
                                        <form action="{{ route('pendaftaran.destroy', $item['id']) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pendaftaran untuk siswa \'{{ addslashes($item['siswa']['nama'] ?? 'ini') }}\'? Data pendaftaran akan dihapus dari API.');"
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 transition"
                                                    title="Hapus Data">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty State (FR-1) -->
            <div class="py-16 px-4 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-800">Belum ada pendaftaran</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">
                    Saat ini belum ada data calon pengurus OSIS yang tersimpan di sistem API.
                </p>
                <div class="mt-5">
                    <a href="{{ route('pendaftaran.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Pendaftaran Pertama
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

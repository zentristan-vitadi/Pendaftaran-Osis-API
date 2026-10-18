<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    /**
     * Mendapatkan base URL API OSIS dari konfigurasi services.
     */
    protected function apiUrl(): string
    {
        return rtrim(config('services.osis.api_url', 'http://127.0.0.1:8000/api'), '/');
    }

    /**
     * Menampilkan daftar pendaftaran OSIS (FR-1).
     */
    public function index(): View
    {
        $pendaftaran = [];
        $apiError = null;

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->get($this->apiUrl() . '/pendaftaran-osis');

            if ($response->successful()) {
                $pendaftaran = $response->json('data') ?? [];
            } else {
                $apiError = 'Gagal mengambil data pendaftaran: ' . ($response->json('message') ?? 'Status HTTP ' . $response->status());
            }
        } catch (ConnectionException $e) {
            $apiError = 'Tidak dapat terhubung ke Server API OSIS (' . $this->apiUrl() . '). Pastikan Project A sudah dijalankan pada port 8000 (php artisan serve --port=8000).';
        } catch (\Exception $e) {
            $apiError = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }

        return view('pendaftaran.index', compact('pendaftaran', 'apiError'));
    }

    /**
     * Menampilkan form pendaftaran OSIS baru (FR-2).
     */
    public function create(): View|RedirectResponse
    {
        $siswa = [];
        $apiError = null;

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->get($this->apiUrl() . '/siswa');

            if ($response->successful()) {
                $siswa = $response->json('data') ?? [];
            } else {
                $apiError = 'Gagal mengambil data master siswa dari API: ' . ($response->json('message') ?? 'Status ' . $response->status());
            }
        } catch (ConnectionException $e) {
            $apiError = 'Tidak dapat terhubung ke Server API OSIS (' . $this->apiUrl() . ').';
        } catch (\Exception $e) {
            $apiError = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        if ($apiError && empty($siswa)) {
            return redirect()->route('pendaftaran.index')->with('error', $apiError);
        }

        return view('pendaftaran.create', compact('siswa', 'apiError'));
    }

    /**
     * Menyimpan data pendaftaran baru ke API OSIS (FR-2).
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $payload = [
                'siswa_id' => $request->input('siswa_id'),
                'pilihan_bidang' => $request->input('pilihan_bidang'),
                'motivasi' => $request->input('motivasi'),
            ];

            $response = Http::timeout(5)
                ->acceptJson()
                ->post($this->apiUrl() . '/pendaftaran-osis', $payload);

            // Berhasil dibuat (201 Created atau 200 OK)
            if ($response->status() === 201 || $response->status() === 200) {
                return redirect()->route('pendaftaran.index')
                    ->with('success', 'Pendaftaran OSIS berhasil disimpan.');
            }

            // Validasi gagal dari API (422 Unprocessable Entity)
            if ($response->status() === 422) {
                $errors = $response->json('errors') ?? [];
                $message = $response->json('message') ?? 'Terdapat kesalahan pada isian form.';
                return back()->withErrors($errors)->withInput()->with('error', $message);
            }

            // Status error lainnya dari API
            $message = $response->json('message') ?? 'Gagal membuat pendaftaran (HTTP ' . $response->status() . ').';
            return back()->withInput()->with('error', $message);

        } catch (ConnectionException $e) {
            return back()->withInput()->with('error', 'Koneksi ke API OSIS terputus. Pastikan server API di port 8000 aktif.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit pendaftaran OSIS (FR-3).
     */
    public function edit(string|int $id): View|RedirectResponse
    {
        try {
            // Ambil detail pendaftaran
            $pendaftaranRes = Http::timeout(5)
                ->acceptJson()
                ->get($this->apiUrl() . '/pendaftaran-osis/' . $id);

            if ($pendaftaranRes->status() === 404) {
                return redirect()->route('pendaftaran.index')
                    ->with('error', 'Data pendaftaran dengan ID ' . $id . ' tidak ditemukan.');
            }

            if (!$pendaftaranRes->successful()) {
                return redirect()->route('pendaftaran.index')
                    ->with('error', 'Gagal mengambil detail pendaftaran: ' . ($pendaftaranRes->json('message') ?? 'HTTP ' . $pendaftaranRes->status()));
            }

            $pendaftaran = $pendaftaranRes->json('data');

            // Ambil daftar master siswa untuk opsi dropdown
            $siswaRes = Http::timeout(5)
                ->acceptJson()
                ->get($this->apiUrl() . '/siswa');

            $siswa = $siswaRes->successful() ? ($siswaRes->json('data') ?? []) : [];

            return view('pendaftaran.edit', compact('pendaftaran', 'siswa'));

        } catch (ConnectionException $e) {
            return redirect()->route('pendaftaran.index')
                ->with('error', 'Tidak dapat terhubung ke Server API OSIS (' . $this->apiUrl() . ').');
        } catch (\Exception $e) {
            return redirect()->route('pendaftaran.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data pendaftaran OSIS ke API (FR-3).
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        try {
            $payload = [
                'siswa_id' => $request->input('siswa_id'),
                'pilihan_bidang' => $request->input('pilihan_bidang'),
                'motivasi' => $request->input('motivasi'),
            ];

            $response = Http::timeout(5)
                ->acceptJson()
                ->put($this->apiUrl() . '/pendaftaran-osis/' . $id, $payload);

            // Berhasil diperbarui (200 OK)
            if ($response->status() === 200) {
                return redirect()->route('pendaftaran.index')
                    ->with('success', 'Data pendaftaran OSIS berhasil diperbarui.');
            }

            // Validasi gagal dari API (422 Unprocessable Entity)
            if ($response->status() === 422) {
                $errors = $response->json('errors') ?? [];
                $message = $response->json('message') ?? 'Terdapat kesalahan pada input pembaruan.';
                return back()->withErrors($errors)->withInput()->with('error', $message);
            }

            if ($response->status() === 404) {
                return redirect()->route('pendaftaran.index')
                    ->with('error', 'Data pendaftaran tidak ditemukan di API.');
            }

            $message = $response->json('message') ?? 'Gagal memperbarui pendaftaran (HTTP ' . $response->status() . ').';
            return back()->withInput()->with('error', $message);

        } catch (ConnectionException $e) {
            return back()->withInput()->with('error', 'Koneksi ke API OSIS terputus.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data pendaftaran OSIS dari API (FR-4).
     */
    public function destroy(string|int $id): RedirectResponse
    {
        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->delete($this->apiUrl() . '/pendaftaran-osis/' . $id);

            // Tangani respons 204 No Content (tanpa body/tanpa ->json()) atau 200 OK
            if ($response->status() === 204 || $response->status() === 200) {
                return redirect()->route('pendaftaran.index')
                    ->with('success', 'Pendaftaran OSIS berhasil dihapus.');
            }

            if ($response->status() === 404) {
                return redirect()->route('pendaftaran.index')
                    ->with('error', 'Data pendaftaran tidak ditemukan atau sudah dihapus sebelumnya.');
            }

            $message = $response->json('message') ?? 'Gagal menghapus pendaftaran (HTTP ' . $response->status() . ').';
            return redirect()->route('pendaftaran.index')->with('error', $message);

        } catch (ConnectionException $e) {
            return redirect()->route('pendaftaran.index')
                ->with('error', 'Koneksi ke API OSIS terputus saat mencoba menghapus data.');
        } catch (\Exception $e) {
            return redirect()->route('pendaftaran.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

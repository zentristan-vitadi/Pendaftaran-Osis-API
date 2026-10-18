<?php

use Illuminate\Support\Facades\Http;

test('root url redirects to /pendaftaran', function () {
    $response = $this->get('/');
    $response->assertRedirect('/pendaftaran');
});

test('index displays pendaftaran list from api', function () {
    Http::fake([
        '*/api/pendaftaran-osis' => Http::response([
            'data' => [
                [
                    'id' => 1,
                    'siswa' => [
                        'id' => 1,
                        'nis' => '1001',
                        'nama' => 'Budi Pratama',
                        'kelas' => ['id' => 1, 'nama_kelas' => 'XI RPL 1'],
                        'jenis_kelamin' => ['id' => 1, 'nama' => 'Laki-laki'],
                    ],
                    'pilihan_bidang' => 'kepemimpinan',
                    'motivasi' => 'Ingin belajar memimpin organisasi dengan dedikasi tinggi.',
                ],
            ],
        ], 200),
    ]);

    $response = $this->get('/pendaftaran');

    $response->assertStatus(200);
    $response->assertSee('Budi Pratama');
    $response->assertSee('XI RPL 1');
    $response->assertSee('Kepemimpinan');
});

test('create page fetches siswa list from api', function () {
    Http::fake([
        '*/api/siswa' => Http::response([
            'data' => [
                [
                    'id' => 2,
                    'nis' => '1002',
                    'nama' => 'Siti Aisyah',
                    'kelas' => ['id' => 1, 'nama_kelas' => 'XI RPL 1'],
                    'jenis_kelamin' => ['id' => 2, 'nama' => 'Perempuan'],
                ],
            ],
        ], 200),
    ]);

    $response = $this->get('/pendaftaran/create');

    $response->assertStatus(200);
    $response->assertSee('Siti Aisyah');
});

test('store sends POST to api and redirects on success', function () {
    Http::fake([
        '*/api/pendaftaran-osis' => Http::response([
            'message' => 'Pendaftaran berhasil dibuat.',
            'data' => [
                'id' => 2,
                'pilihan_bidang' => 'keagamaan',
                'motivasi' => 'Ingin meningkatkan kegiatan keagamaan di lingkungan sekolah.',
            ],
        ], 201),
    ]);

    $response = $this->post('/pendaftaran', [
        'siswa_id' => 2,
        'pilihan_bidang' => 'keagamaan',
        'motivasi' => 'Ingin meningkatkan kegiatan keagamaan di lingkungan sekolah.',
    ]);

    $response->assertRedirect('/pendaftaran');
    $response->assertSessionHas('success');
});

test('store handles 422 validation error from api', function () {
    Http::fake([
        '*/api/pendaftaran-osis' => Http::response([
            'message' => 'The given data was invalid.',
            'errors' => [
                'motivasi' => ['Motivasi wajib memiliki panjang minimal 20 karakter.'],
            ],
        ], 422),
    ]);

    $response = $this->from('/pendaftaran/create')->post('/pendaftaran', [
        'siswa_id' => 2,
        'pilihan_bidang' => 'keagamaan',
        'motivasi' => 'Pendek',
    ]);

    $response->assertRedirect('/pendaftaran/create');
    $response->assertSessionHasErrors(['motivasi']);
});

test('edit page fetches detail and siswa list', function () {
    Http::fake([
        '*/api/pendaftaran-osis/1' => Http::response([
            'data' => [
                'id' => 1,
                'siswa' => [
                    'id' => 1,
                    'nis' => '1001',
                    'nama' => 'Budi Pratama',
                    'kelas' => ['id' => 1, 'nama_kelas' => 'XI RPL 1'],
                    'jenis_kelamin' => ['id' => 1, 'nama' => 'Laki-laki'],
                ],
                'pilihan_bidang' => 'kepemimpinan',
                'motivasi' => 'Ingin belajar memimpin organisasi dengan dedikasi tinggi.',
            ],
        ], 200),
        '*/api/siswa' => Http::response([
            'data' => [
                [
                    'id' => 1,
                    'nis' => '1001',
                    'nama' => 'Budi Pratama',
                    'kelas' => ['id' => 1, 'nama_kelas' => 'XI RPL 1'],
                    'jenis_kelamin' => ['id' => 1, 'nama' => 'Laki-laki'],
                ],
            ],
        ], 200),
    ]);

    $response = $this->get('/pendaftaran/1/edit');

    $response->assertStatus(200);
    $response->assertSee('Budi Pratama');
    $response->assertSee('Ingin belajar memimpin organisasi dengan dedikasi tinggi.');
});

test('update sends PUT to api and redirects on success', function () {
    Http::fake([
        '*/api/pendaftaran-osis/1' => Http::response([
            'message' => 'Pendaftaran berhasil diperbarui.',
            'data' => [
                'id' => 1,
                'pilihan_bidang' => 'olahraga',
                'motivasi' => 'Ingin memajukan bidang olahraga dan turnamen antarkelas sekolah.',
            ],
        ], 200),
    ]);

    $response = $this->put('/pendaftaran/1', [
        'siswa_id' => 1,
        'pilihan_bidang' => 'olahraga',
        'motivasi' => 'Ingin memajukan bidang olahraga dan turnamen antarkelas sekolah.',
    ]);

    $response->assertRedirect('/pendaftaran');
    $response->assertSessionHas('success');
});

test('destroy sends DELETE to api and handles 204 No Content', function () {
    Http::fake([
        '*/api/pendaftaran-osis/1' => Http::response(null, 204),
    ]);

    $response = $this->delete('/pendaftaran/1');

    $response->assertRedirect('/pendaftaran');
    $response->assertSessionHas('success');
});

test('index handles api connection exception gracefully', function () {
    Http::fake([
        '*/api/pendaftaran-osis' => function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
        },
    ]);

    $response = $this->get('/pendaftaran');

    $response->assertStatus(200);
    $response->assertSee('Tidak dapat terhubung ke Server API OSIS');
});

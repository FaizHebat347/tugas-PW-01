<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

Route::get('/form-mahasiswa', function () {
    return view('form-mahasiswa');
});

Route::post('/form-mahasiswa', function (Request $request) {
    $dataBersih = [
        'nim' => strip_tags(trim((string) $request->input('nim'))),
        'nama' => strip_tags(trim((string) $request->input('nama'))),
        'email' => filter_var(
            (string) $request->input('email'),
            FILTER_SANITIZE_EMAIL
        ),
        'usia' => trim((string) $request->input('usia')),
    ];

    $validator = Validator::make($dataBersih, [
        'nim' => ['required', 'min:8', 'max:12'],
        'nama' => ['required', 'min:3', 'max:50'],
        'email' => ['required', 'email'],
        'usia' => ['required', 'integer', 'min:17', 'max:60'],
    ], [
        'nim.required' => 'Nim wajib diisi.',
        'nim.min' => 'Nim minimal 8 karakter.',
        'nama.required' => 'Nama wajib diisi.',
        'nama.min' => 'Nama minimal 3 karakter.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'usia.required' => 'Usia wajib diisi.',
        'usia.integer' => 'Usia harus berupa angka.',
        'usia.min' => 'Usia minimal 17 tahun.',
        'usia.max' => 'Usia maksimal 60 tahun.',
    ]);

    if ($validator->fails()) {
        return redirect('/form-mahasiswa')
            ->withErrors($validator)
            ->withInput();
    }

    $data = $validator->validated();
    $data['usia'] = (int) $data['usia'];

    return view('hasil-form', ['data' => $data]);
});

Route::get('/mahasiswa/{nim?}', function (?string $nim = null) {
    try {
        $pdo = DB::connection()->getPdo();
 
        $sql = 'SELECT m.nim, m.nama, m.email, m.usia,
                       p.nama_prodi
                FROM mahasiswa AS m
                JOIN program_studi AS p
                    ON p.id = m.program_studi_id';
 
        if ($nim !== null) {
            $sql .= ' WHERE m.nim = :nim';
        }
 
        $sql .= ' ORDER BY m.nim';
 
        $statement = $pdo->prepare($sql);
        $statement->execute($nim !== null ? ['nim' => $nim] : []);
 
        $daftarMahasiswa = $statement->fetchAll(\PDO::FETCH_ASSOC);
 
        return view('mahasiswa', compact('daftarMahasiswa', 'nim'));
    } catch (\Throwable $error) {
        report($error);
 
        return response(
            'Koneksi atau query basis data gagal. Periksa file .env dan layanan MySQL.',
            500
        );
    }
});


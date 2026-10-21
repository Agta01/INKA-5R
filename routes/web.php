<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Menampilkan halaman form laporan 5R
Route::get('/', function () {
    $riwayat = DB::table('laporan_5r')->orderBy('id', 'desc')->limit(5)->get();
    return view('lapor', compact('riwayat'));
});

// Menyimpan laporan ke database Supabase
Route::post('/simpan-laporan', function (Request $request) {
    // 1. Simpan ke tabel laporan_5r
    $laporanId = DB::table('laporan_5r')->insertGetId([
        'tanggal' => $request->tanggal,
        'status_tim' => $request->status_tim,
        'hasil' => $request->hasil,
        'created_at' => now(),
    ]);

    // 2. Simpan setiap baris temuan & tindakan ke tabel detail_temuan
    if ($request->has('temuan')) {
        foreach ($request->temuan as $index => $isiTemuan) {
            DB::table('detail_temuan')->insert([
                'laporan_id' => $laporanId,
                'deskripsi_temuan' => $isiTemuan,
                'tindakan_dilakukan' => $request->tindakan[$index] ?? '-',
                'created_at' => now(),
            ]);
        }
    }

    return back()->with('sukses', 'Laporan berhasil disimpan ke Supabase!');
});
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('lapor');
});

Route::get('/riwayat', function (Request $request) {
    $query = DB::table('laporan_5r')->orderBy('id', 'desc');

    // Filter Pencarian
    if ($request->filled('q')) {
        $q = trim($request->q);
        $query->where(function ($sub) use ($q) {
            $sub->where('nama_penulis', 'like', "%{$q}%")
                ->orWhere('nama_tim', 'like', "%{$q}%")
                ->orWhere('hasil', 'like', "%{$q}%")
                ->orWhere('tanggal', 'like', "%{$q}%");
        });
    }

    // Filter Status Tim
    if ($request->filled('status') && in_array($request->status, ['Lengkap', 'Tidak Lengkap'])) {
        $query->where('status_tim', $request->status);
    }

    $riwayat = $query->limit(50)->get();

    // Ambil detail temuan untuk masing-masing laporan
    foreach ($riwayat as $item) {
        $item->detail = DB::table('detail_temuan')->where('laporan_id', $item->id)->get();
    }

    // Statistik ringkas
    $totalLaporan = DB::table('laporan_5r')->count();
    $timLengkap = DB::table('laporan_5r')->where('status_tim', 'Lengkap')->count();
    $timTidakLengkap = DB::table('laporan_5r')->where('status_tim', 'Tidak Lengkap')->count();

    return view('riwayat', compact('riwayat', 'totalLaporan', 'timLengkap', 'timTidakLengkap'));
});


Route::post('/simpan-laporan', function (Request $request) {
    // 1. Simpan ke tabel laporan_5r (termasuk nama_penulis & nama_tim opsional)
    $laporanId = DB::table('laporan_5r')->insertGetId([
        'tanggal' => $request->tanggal,
        'status_tim' => $request->status_tim,
        'nama_tim' => $request->nama_tim, // Opsional
        'nama_penulis' => $request->nama_penulis, // Hanya untuk sistem/riwayat, tidak masuk WA
        'hasil' => $request->hasil,
        'created_at' => now(),
    ]);

    // 2. Simpan detail temuan, tindakan, dan foto (Base64 yang sudah dikompres)
    if ($request->has('temuan')) {
        foreach ($request->temuan as $index => $isiTemuan) {
            DB::table('detail_temuan')->insert([
                'laporan_id' => $laporanId,
                'deskripsi_temuan' => $isiTemuan,
                'tindakan_dilakukan' => $request->tindakan[$index] ?? '-',
                'foto_url' => $request->foto_base64 ?? null, // Menyimpan gambar kolase ke foto_url
                'created_at' => now(),
            ]);
        }
    }

    return back()->with('sukses', 'Laporan & Foto berhasil disimpan ke Supabase!');
});
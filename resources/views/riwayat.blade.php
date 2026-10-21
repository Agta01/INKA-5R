<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan 5R</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-4 md:p-8">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- HEADER / TOMBOL KEMBALI KE FORM (RAMAH HP) -->
        <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-md">
            <div>
                <h1 class="text-lg md:text-xl font-bold text-blue-700 flex items-center gap-1.5">
                    <span>📂</span>
                    <span>Riwayat Laporan 5R</span>
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Daftar arsip laporan yang tersimpan di database</p>
            </div>
            <a href="/"
                class="bg-green-600 hover:bg-green-700 text-white text-xs md:text-sm font-bold py-2 px-3.5 rounded-lg shadow flex items-center gap-1.5 transition-all whitespace-nowrap">
                <span>✏️</span>
                <span>+ Buat Laporan</span>
            </a>
        </div>

        <!-- ALERT SUKSES JIKA ADA -->
        @if(session('sukses'))
            <div class="bg-green-100 text-green-800 p-3 rounded-xl text-sm font-medium flex justify-between items-center shadow-sm">
                <span>✅ {{ session('sukses') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-900 font-bold ml-2">✕</button>
            </div>
        @endif

        <!-- KARTU STATISTIK RINGKAS -->
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white p-3 md:p-4 rounded-xl shadow-md text-center">
                <div class="text-lg md:text-2xl font-bold text-gray-800">{{ $totalLaporan ?? count($riwayat) }}</div>
                <div class="text-[11px] md:text-xs text-gray-500">Total Laporan</div>
            </div>
            <div class="bg-white p-3 md:p-4 rounded-xl shadow-md text-center">
                <div class="text-lg md:text-2xl font-bold text-green-600">{{ $timLengkap ?? 0 }}</div>
                <div class="text-[11px] md:text-xs text-gray-500">Tim Lengkap</div>
            </div>
            <div class="bg-white p-3 md:p-4 rounded-xl shadow-md text-center">
                <div class="text-lg md:text-2xl font-bold text-amber-500">{{ $timTidakLengkap ?? 0 }}</div>
                <div class="text-[11px] md:text-xs text-gray-500">Tim Kurang</div>
            </div>
        </div>

        <!-- FORM FILTER & PENCARIAN -->
        <div class="bg-white p-4 rounded-xl shadow-md">
            <form action="/riwayat" method="GET" class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="Cari penulis, tim, tanggal, atau temuan..."
                    class="w-full border rounded-lg p-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="flex gap-2">
                    <select name="status" class="border rounded-lg p-2 text-sm bg-white">
                        <option value="">Semua Status</option>
                        <option value="Lengkap" {{ request('status') == 'Lengkap' ? 'selected' : '' }}>Lengkap</option>
                        <option value="Tidak Lengkap" {{ request('status') == 'Tidak Lengkap' ? 'selected' : '' }}>Tidak Lengkap</option>
                    </select>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg text-sm shadow">
                        Cari
                    </button>
                    @if(request('q') || request('status'))
                        <a href="/riwayat"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-3 py-2 rounded-lg text-sm flex items-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABEL RIWAYAT LAPORAN -->
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm md:text-base font-bold text-gray-800">📋 Arsip Laporan 5R</h2>
                <span class="text-xs text-gray-500">Total: {{ count($riwayat) }} data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs md:text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b text-gray-700">
                            <th class="p-3 whitespace-nowrap">Tanggal</th>
                            <th class="p-3 whitespace-nowrap">Penulis</th>
                            <th class="p-3 whitespace-nowrap">Tim 5R</th>
                            <th class="p-3 text-center">Foto</th>
                            <th class="p-3 min-w-[220px]">Temuan & Tindakan</th>
                            <th class="p-3 min-w-[160px]">Hasil</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @if(isset($riwayat) && count($riwayat) > 0)
                            @foreach($riwayat as $lap)
                                @php
                                    $fotoPertama = null;
                                    if (isset($lap->detail) && count($lap->detail) > 0) {
                                        foreach ($lap->detail as $item) {
                                            if (!empty($item->foto_url)) {
                                                $fotoPertama = $item->foto_url;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 align-top">
                                    <!-- Tanggal -->
                                    <td class="p-3 whitespace-nowrap">
                                        <div class="font-bold text-gray-800">{{ $lap->tanggal ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $lap->created_at ?? '' }}</div>
                                    </td>

                                    <!-- Penulis -->
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">
                                            👤 {{ !empty($lap->nama_penulis) ? $lap->nama_penulis : 'Anonim' }}
                                        </span>
                                    </td>

                                    <!-- Tim -->
                                    <td class="p-3 whitespace-nowrap">
                                        <div class="font-semibold {{ $lap->status_tim === 'Lengkap' ? 'text-green-700' : 'text-amber-600' }}">
                                            {{ $lap->status_tim ?? '-' }}
                                        </div>
                                        @if(!empty($lap->nama_tim))
                                            <div class="text-[11px] text-gray-500">{{ $lap->nama_tim }}</div>
                                        @endif
                                    </td>

                                    <!-- Foto -->
                                    <td class="p-3 text-center whitespace-nowrap">
                                        @if(!empty($fotoPertama))
                                            <img src="{{ $fotoPertama }}"
                                                class="w-20 h-14 object-cover rounded border cursor-pointer hover:opacity-80 transition inline-block shadow-sm"
                                                onclick="bukaModalFoto('{{ $fotoPertama }}', '{{ $lap->tanggal }}')"
                                                title="Klik untuk memperbesar">
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tanpa foto</span>
                                        @endif
                                    </td>

                                    <!-- Temuan & Tindakan -->
                                    <td class="p-3">
                                        @if(isset($lap->detail) && count($lap->detail) > 0)
                                            <ul class="space-y-2">
                                                @foreach($lap->detail as $idx => $dt)
                                                    <li class="bg-gray-50 p-2 rounded border border-gray-100">
                                                        <div class="font-medium text-red-600">
                                                            ⚠️ {{ $dt->deskripsi_temuan ?? '-' }}
                                                        </div>
                                                        <div class="text-green-700 text-xs mt-0.5">
                                                            ✔️ {{ $dt->tindakan_dilakukan ?? '-' }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Hasil -->
                                    <td class="p-3 text-gray-700">{{ $lap->hasil ?? '-' }}</td>

                                    <!-- Aksi Salin WA -->
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <button type="button"
                                            onclick="salinUlangWA(this)"
                                            data-tanggal="{{ $lap->tanggal }}"
                                            data-status-tim="{{ $lap->status_tim }}"
                                            data-nama-tim="{{ $lap->nama_tim ?? '' }}"
                                            data-hasil="{{ $lap->hasil }}"
                                            data-detail="{{ json_encode($lap->detail) }}"
                                            class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 font-semibold text-xs px-2.5 py-1.5 rounded shadow-sm">
                                            📋 Copy WA
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-400">
                                    {{ request('q') || request('status') ? 'Tidak ada data laporan yang sesuai pencarian.' : 'Belum ada riwayat laporan.' }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL FOTO ZOOM -->
    <div id="modalFoto" class="fixed inset-0 z-50 bg-black/80 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full overflow-hidden shadow-2xl">
            <div class="p-3 border-b flex items-center justify-between">
                <span id="modalJudul" class="font-bold text-sm text-gray-800">Foto Dokumentasi</span>
                <button onclick="tutupModalFoto()" class="text-gray-500 hover:text-gray-800 font-bold text-lg px-2">✕</button>
            </div>
            <div class="p-2 bg-black flex justify-center">
                <img id="modalImg" src="" class="max-h-[70vh] w-auto object-contain rounded">
            </div>
            <div class="p-3 bg-gray-50 border-t flex justify-end">
                <button onclick="tutupModalFoto()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 text-xs font-bold px-4 py-1.5 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function bukaModalFoto(src, tanggal) {
            document.getElementById('modalImg').src = src;
            document.getElementById('modalJudul').innerText = 'Foto Dokumentasi - Tanggal: ' + tanggal;
            document.getElementById('modalFoto').classList.remove('hidden');
        }

        function tutupModalFoto() {
            document.getElementById('modalFoto').classList.add('hidden');
            document.getElementById('modalImg').src = '';
        }

        document.getElementById('modalFoto').addEventListener('click', function(e) {
            if (e.target === this) tutupModalFoto();
        });

        function formatTanggalIndo(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }

        function salinUlangWA(btn) {
            const tanggal = formatTanggalIndo(btn.dataset.tanggal);
            const statusTim = btn.dataset.statusTim;
            const namaTim = btn.dataset.namaTim;
            const hasil = btn.dataset.hasil;
            const details = JSON.parse(btn.dataset.detail || '[]');

            let listTemuan = '';
            let listTindakan = '';

            details.forEach(item => {
                if (item.deskripsi_temuan) {
                    listTemuan += ` • ${item.deskripsi_temuan}\n\n`;
                }
                if (item.tindakan_dilakukan) {
                    listTindakan += ` • ${item.tindakan_dilakukan}\n\n`;
                }
            });

            let teksTim = `*Tim 5R* : ${statusTim}`;
            if (namaTim && namaTim.trim() !== '') {
                teksTim += `\n*Nama Tim* : ${namaTim}`;
            }

            const teksWA = `*Laporan 5R ${tanggal}*\n\n${teksTim}\n\n*Temuan* :\n${listTemuan || ' • -\n\n'}*Tindakan yg dilakukan* :\n${listTindakan || ' • -\n\n'}*Hasil* :\n • ${hasil}`;

            navigator.clipboard.writeText(teksWA).then(() => {
                const ori = btn.innerText;
                btn.innerText = '✅ Tersalin!';
                setTimeout(() => { btn.innerText = ori; }, 2000);
            }).catch(() => {
                alert('Format WA berhasil disalin manual!');
            });
        }
    </script>

</body>

</html>

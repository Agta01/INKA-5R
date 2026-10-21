<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Laporan 5R</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-4 md:p-8">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- TOMBOL NAVIGASI / SUBMENU MENUJU RIWAYAT (RAMAH HP) -->
        <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-md">
            <h1 class="text-lg md:text-xl font-bold text-green-700 flex items-center gap-1.5">
                <span>📝</span>
                <span>Input Laporan 5R</span>
            </h1>
            <a href="/riwayat"
                class="bg-blue-600 hover:bg-blue-700 text-white text-xs md:text-sm font-bold py-2 px-3.5 rounded-lg shadow flex items-center gap-1.5 transition-all">
                <span>📂</span>
                <span>Lihat Riwayat</span>
            </a>
        </div>

        <!-- FORM & LIVE PREVIEW ALA WHATSAPP -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- KOLOM KIRI: FORM INPUT -->
            <div class="bg-white p-6 rounded-xl shadow-md">

                @if(session('sukses'))
                    <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-sm font-medium flex justify-between items-center">
                        <span>✅ {{ session('sukses') }}</span>
                        <a href="/riwayat" class="underline font-bold text-xs ml-2 text-green-900">Lihat Riwayat →</a>
                    </div>
                @endif

                <form action="/simpan-laporan" method="POST" id="form5R">
                    @csrf

                    <!-- Nama Penulis / Pelapor (Diisi Manual setiap kali lapor, TIDAK masuk WA) -->
                    <div class="mb-4 bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <label class="block text-sm font-bold text-blue-800 mb-1">
                            👤 Nama Penulis Laporan <span class="text-xs font-normal text-blue-600">(Diisi manual, tidak
                                masuk format WA)</span>
                        </label>
                        <input type="text" name="nama_penulis" id="inputPenulis" required
                            placeholder="Masukkan nama penulis laporan sekarang..."
                            class="w-full border rounded-lg p-2 text-sm bg-white">
                    </div>

                    <!-- Tanggal -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal" id="inputTanggal" required
                            class="w-full border rounded-lg p-2 text-sm" onchange="updatePreview()">
                    </div>

                    <!-- Status Tim & Nama Anggota Tim (Opsional) -->
                    <div class="mb-4 p-3 bg-gray-50 border rounded-lg space-y-3">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Status Tim 5R</label>
                            <select name="status_tim" id="inputTim"
                                class="w-full border rounded-lg p-2 text-sm bg-white" onchange="updatePreview()">
                                <option value="Lengkap">Lengkap</option>
                                <option value="Tidak Lengkap">Tidak Lengkap</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                Nama Tim / Anggota <span class="text-gray-400 font-normal">(Opsional / Tidak Wajib
                                    Diisi)</span>
                            </label>
                            <input type="text" name="nama_tim" id="inputNamaTim"
                                placeholder="Contoh: Tim A / Budi, Andi (Boleh dikosongkan)"
                                class="w-full border rounded-lg p-2 text-sm bg-white" oninput="updatePreview()">
                        </div>
                    </div>

                    <!-- Upload Foto Dokumentasi -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Foto Dokumentasi (Otomatis digabung jadi 1
                            kolase)</label>
                        <input type="file" id="inputFoto" multiple accept="image/*"
                            class="w-full text-sm border rounded-lg p-1.5" onchange="prosesKolaseFoto()">
                        <!-- Input tersembunyi untuk mengirim data gambar ke Supabase -->
                        <input type="hidden" name="foto_base64" id="inputFotoBase64">
                    </div>

                    <!-- Daftar Temuan & Tindakan Dinamis -->
                    <div id="containerTemuan" class="space-y-4 mb-4">
                        <div class="p-3 bg-gray-50 border rounded-lg item-temuan">
                            <label class="block text-xs font-bold text-gray-600 uppercase">Temuan 1</label>
                            <textarea name="temuan[]" rows="2" required
                                placeholder="Contoh: Terdapat papan triplek yang tidak digunakan tergeletak di bawah tangga office gedung M."
                                class="w-full border rounded p-2 text-sm mt-1 input-temuan"
                                oninput="updatePreview()"></textarea>

                            <label class="block text-xs font-bold text-gray-600 uppercase mt-2">Tindakan yg dilakukan
                                1</label>
                            <textarea name="tindakan[]" rows="2" required
                                placeholder="Contoh: Papan triplek diletakkan di utara gedung M."
                                class="w-full border rounded p-2 text-sm mt-1 input-tindakan"
                                oninput="updatePreview()"></textarea>
                        </div>
                    </div>

                    <button type="button" onclick="tambahBaris()"
                        class="w-full py-2 mb-4 bg-blue-50 text-blue-600 font-semibold text-sm rounded-lg border border-blue-200 hover:bg-blue-100">
                        + Tambah Temuan & Tindakan Lainnya
                    </button>

                    <!-- Hasil -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-1">Hasil</label>
                        <textarea name="hasil" id="inputHasil" rows="2" required
                            class="w-full border rounded-lg p-2 text-sm"
                            oninput="updatePreview()">5R telah dilaksanakan di area gedung M dan Office gedung M.</textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700 shadow">
                        💾 Simpan Laporan ke Database
                    </button>
                </form>
            </div>

            <!-- KOLOM KANAN: LIVE PREVIEW ALA WHATSAPP -->
            <div>
                <div class="bg-[#e5ddd5] p-4 rounded-xl shadow-md md:sticky md:top-4">
                    <h2 class="text-sm font-bold text-gray-700 mb-2">📱 Live Preview Format WhatsApp</h2>

                    <!-- Bubble Chat WA -->
                    <div class="bg-white p-3 rounded-lg shadow-sm text-sm text-gray-800">
                        <!-- Canvas Kolase Foto -->
                        <div id="previewFotoWrapper" class="mb-2 hidden">
                            <canvas id="canvasKolase" class="w-full rounded border hidden"></canvas>
                            <img id="hasilKolaseImg" class="w-full rounded border" alt="Kolase Foto 5R">
                        </div>

                        <!-- Teks Laporan -->
                        <div id="waTextPreview" class="whitespace-pre-line font-sans leading-relaxed"></div>
                    </div>

                    <!-- Tombol Aksi Copy Foto & Teks -->
                    <div class="mt-4 space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="copyGambarKeWA()" id="btnCopyFoto"
                                class="bg-blue-600 text-white font-bold py-2.5 px-3 rounded-lg shadow hover:bg-blue-700 text-xs flex justify-center items-center gap-1 opacity-50 cursor-not-allowed"
                                disabled>
                                🖼️ 1. Copy Foto
                            </button>
                            <button type="button" onclick="copyTeksKeWA()"
                                class="bg-[#25D366] text-white font-bold py-2.5 px-3 rounded-lg shadow hover:bg-green-600 text-xs flex justify-center items-center gap-1">
                                📋 2. Copy Teks
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-600 text-center">
                            💡 <b>Cara kirim ke WA:</b> Klik <b>1. Copy Foto</b> lalu Paste (Ctrl+V) di chat WA,
                            kemudian klik <b>2. Copy Teks</b> dan Paste (Ctrl+V) sebagai caption-nya!
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        // Set tanggal hari ini secara otomatis
        document.getElementById('inputTanggal').valueAsDate = new Date();

        function tambahBaris() {
            const container = document.getElementById('containerTemuan');
            const index = container.children.length + 1;
            const div = document.createElement('div');
            div.className = 'p-3 bg-gray-50 border rounded-lg item-temuan relative';
            div.innerHTML = `
            <button type="button" onclick="this.parentElement.remove(); updatePreview();" class="absolute top-2 right-2 text-red-500 text-xs font-bold">✕ Hapus</button>
            <label class="block text-xs font-bold text-gray-600 uppercase">Temuan ${index}</label>
            <textarea name="temuan[]" rows="2" required placeholder="Deskripsi temuan..." class="w-full border rounded p-2 text-sm mt-1 input-temuan" oninput="updatePreview()"></textarea>
            <label class="block text-xs font-bold text-gray-600 uppercase mt-2">Tindakan yg dilakukan ${index}</label>
            <textarea name="tindakan[]" rows="2" required placeholder="Tindakan yang dilakukan..." class="w-full border rounded p-2 text-sm mt-1 input-tindakan" oninput="updatePreview()"></textarea>
        `;
            container.appendChild(div);
        }

        function formatTanggalIndo(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }

        function updatePreview() {
            const tgl = formatTanggalIndo(document.getElementById('inputTanggal').value);
            const tim = document.getElementById('inputTim').value;
            const namaTim = document.getElementById('inputNamaTim').value.trim();
            const hasil = document.getElementById('inputHasil').value;

            const temuanInputs = document.querySelectorAll('.input-temuan');
            const tindakanInputs = document.querySelectorAll('.input-tindakan');

            let listTemuan = '';
            temuanInputs.forEach(el => {
                if (el.value.trim() !== '') listTemuan += ` • ${el.value.trim()}\n\n`;
            });

            let listTindakan = '';
            tindakanInputs.forEach(el => {
                if (el.value.trim() !== '') listTindakan += ` • ${el.value.trim()}\n\n`;
            });

            // Jika nama tim diisi (opsional), tampilkan di bawah status tim
            let teksTim = `*Tim 5R* : ${tim}`;
            if (namaTim !== '') {
                teksTim += `\n*Nama Tim* : ${namaTim}`;
            }

            // Nama penulis TIDAK dimasukkan ke dalam teksWA
            const teksWA = `*Laporan 5R ${tgl}*\n\n${teksTim}\n\n*Temuan* :\n${listTemuan || ' • -\n\n'}*Tindakan yg dilakukan* :\n${listTindakan || ' • -\n\n'}*Hasil* :\n • ${hasil}`;

            document.getElementById('waTextPreview').innerText = teksWA;
        }

        function prosesKolaseFoto() {
            const files = Array.from(document.getElementById('inputFoto').files);
            const wrapper = document.getElementById('previewFotoWrapper');
            const canvas = document.getElementById('canvasKolase');
            const ctx = canvas.getContext('2d');
            const imgPreview = document.getElementById('hasilKolaseImg');
            const inputBase64 = document.getElementById('inputFotoBase64');
            const btnCopyFoto = document.getElementById('btnCopyFoto');

            if (files.length === 0) {
                wrapper.classList.add('hidden');
                inputBase64.value = '';
                btnCopyFoto.disabled = true;
                btnCopyFoto.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            const selectedFiles = files.slice(0, 4);
            const imagePromises = selectedFiles.map(file => {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => resolve(img);
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            });

            Promise.all(imagePromises).then(images => {
                const count = images.length;
                const targetWidth = 800;
                const targetHeight = 500;
                canvas.width = targetWidth;
                canvas.height = targetHeight;

                ctx.fillStyle = "#ffffff";
                ctx.fillRect(0, 0, targetWidth, targetHeight);

                const colWidth = targetWidth / count;
                images.forEach((img, idx) => {
                    const scale = Math.max(colWidth / img.width, targetHeight / img.height);
                    const x = (colWidth / 2) - (img.width / 2) * scale + (idx * colWidth);
                    const y = (targetHeight / 2) - (img.height / 2) * scale;

                    ctx.save();
                    ctx.beginPath();
                    ctx.rect(idx * colWidth, 0, colWidth - (idx < count - 1 ? 4 : 0), targetHeight);
                    ctx.clip();
                    ctx.drawImage(img, x, y, img.width * scale, img.height * scale);
                    ctx.restore();
                });

                const dataUrlJpeg = canvas.toDataURL('image/jpeg', 0.7);
                imgPreview.src = dataUrlJpeg;
                inputBase64.value = dataUrlJpeg;

                wrapper.classList.remove('hidden');
                btnCopyFoto.disabled = false;
                btnCopyFoto.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }

        function copyGambarKeWA() {
            const canvas = document.getElementById('canvasKolase');
            canvas.toBlob(async (blob) => {
                try {
                    await navigator.clipboard.write([
                        new ClipboardItem({ 'image/png': blob })
                    ]);
                    alert('✅ Foto Kolase berhasil disalin! Silakan tekan Paste (Ctrl+V) di ruang chat WhatsApp Anda.');
                } catch (err) {
                    alert('Browser Anda tidak mendukung copy gambar langsung. Silakan klik kanan pada gambar preview lalu pilih "Copy Image".');
                }
            }, 'image/png');
        }

        function copyTeksKeWA() {
            const teks = document.getElementById('waTextPreview').innerText;
            navigator.clipboard.writeText(teks);
            alert('✅ Format teks WhatsApp berhasil disalin! Silakan Paste (Ctrl+V) di grup WA Anda.');
        }

        updatePreview();
    </script>

</body>

</html>
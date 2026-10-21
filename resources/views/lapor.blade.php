<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otomatisasi Laporan 5R</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 md:p-8">

<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- KOLOM KIRI: FORM INPUT -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h1 class="text-xl font-bold text-green-700 mb-4">📝 Input Laporan 5R</h1>

        @if(session('sukses'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-sm font-medium">
                ✅ {{ session('sukses') }}
            </div>
        @endif

        <form action="/simpan-laporan" method="POST" id="form5R">
            @csrf
            
            <!-- Tanggal -->
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" id="inputTanggal" required
                       class="w-full border rounded-lg p-2 text-sm" onchange="updatePreview()">
            </div>

            <!-- Status Tim -->
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Tim 5R</label>
                <select name="status_tim" id="inputTim" class="w-full border rounded-lg p-2 text-sm" onchange="updatePreview()">
                    <option value="Lengkap">Lengkap</option>
                    <option value="Tidak Lengkap">Tidak Lengkap</option>
                </select>
            </div>

            <!-- Upload Foto Dokumentasi -->
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Foto Dokumentasi (Bisa pilih 2 foto)</label>
                <input type="file" id="inputFoto" multiple accept="image/*" 
                       class="w-full text-sm border rounded-lg p-1.5" onchange="previewImages()">
            </div>

            <!-- Daftar Temuan & Tindakan Dinamis -->
            <div id="containerTemuan" class="space-y-4 mb-4">
                <div class="p-3 bg-gray-50 border rounded-lg item-temuan">
                    <label class="block text-xs font-bold text-gray-600 uppercase">Temuan 1</label>
                    <textarea name="temuan[]" rows="2" required placeholder="Contoh: Terdapat papan triplek yang tidak digunakan tergeletak di bawah tangga office gedung M."
                              class="w-full border rounded p-2 text-sm mt-1 input-temuan" oninput="updatePreview()"></textarea>
                    
                    <label class="block text-xs font-bold text-gray-600 uppercase mt-2">Tindakan yg dilakukan 1</label>
                    <textarea name="tindakan[]" rows="2" required placeholder="Contoh: Papan triplek diletakkan di utara gedung M."
                              class="w-full border rounded p-2 text-sm mt-1 input-tindakan" oninput="updatePreview()"></textarea>
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
                          class="w-full border rounded-lg p-2 text-sm" oninput="updatePreview()">5R telah dilaksanakan di area gedung M dan Office gedung M.</textarea>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700">
                💾 Simpan ke Database Supabase
            </button>
        </form>
    </div>

    <!-- KOLOM KANAN: LIVE PREVIEW ALA WHATSAPP -->
    <div>
        <div class="bg-[#e5ddd5] p-4 rounded-xl shadow-md">
            <h2 class="text-sm font-bold text-gray-700 mb-2">📱 Live Preview Format WhatsApp</h2>
            
            <!-- Bubble Chat WA -->
            <div class="bg-white p-3 rounded-lg shadow-sm text-sm text-gray-800">
                <!-- Kolase Foto -->
                <div id="previewFotoContainer" class="grid grid-cols-2 gap-1 mb-2 hidden"></div>
                
                <!-- Teks Laporan -->
                <div id="waTextPreview" class="whitespace-pre-line font-sans leading-relaxed"></div>
            </div>

            <button type="button" onclick="copyKeWA()" 
                    class="mt-4 w-full bg-[#25D366] text-white font-bold py-2.5 rounded-lg shadow hover:bg-green-600 flex justify-center items-center gap-2">
                📋 Salin Teks untuk WhatsApp
            </button>
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

        const teksWA = `*Laporan 5R ${tgl}*\n\n*Tim 5R* : ${tim}\n\n*Temuan* :\n${listTemuan || ' • -\n\n'}*Tindakan yg dilakukan* :\n${listTindakan || ' • -\n\n'}*Hasil* :\n • ${hasil}`;

        document.getElementById('waTextPreview').innerText = teksWA;
    }

    function previewImages() {
        const container = document.getElementById('previewFotoContainer');
        container.innerHTML = '';
        const files = document.getElementById('inputFoto').files;
        
        if (files.length > 0) {
            container.classList.remove('hidden');
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-40 object-cover rounded';
                    container.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        } else {
            container.classList.add('hidden');
        }
    }

    function copyKeWA() {
        const teks = document.getElementById('waTextPreview').innerText;
        navigator.clipboard.writeText(teks);
        alert('Format teks WhatsApp berhasil disalin! Silakan Paste di grup WA Anda.');
    }

    // Jalankan preview saat pertama kali halaman dibuka
    updatePreview();
</script>

</body>
</html>
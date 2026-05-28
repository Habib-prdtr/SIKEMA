<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="p-6 bg-gray-50 min-h-screen font-sans">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Penerimaan</h1>
        <p class="text-sm text-gray-500">Laporan transaksi penerimaan dengan filter dan export</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Filter Laporan</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                <input type="date" value="2026-05-01" class="w-full border border-gray-200 rounded-lg p-2 text-sm focus:outline-none focus:ring-1 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                <input type="date" value="2026-05-18" class="w-full border border-gray-200 rounded-lg p-2 text-sm focus:outline-none focus:ring-1 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Metode Pembayaran</label>
                <select class="w-full border border-gray-200 rounded-lg p-2 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500">
                    <option>Semua Metode</option>
                    <option>Transfer Bank</option>
                    <option>Tunai</option>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1 transition">
                🔍 Terapkan Filter
            </button>
            <button id="btn-export-pdf" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1 transition">
                📄 Export PDF
            </button>
            <button id="btn-export-excel" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1 transition">
                📊 Export Excel
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Total Penerimaan</p>
                <h3 class="text-2xl font-bold text-gray-800">Rp 2.300.000</h3>
                <p class="text-xs text-gray-400 mt-1">Periode yang dipilih</p>
            </div>
            <div class="bg-emerald-50 text-emerald-600 p-3 rounded-lg text-xl">📈</div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Total Transaksi</p>
                <h3 class="text-2xl font-bold text-gray-800">8</h3>
                <p class="text-xs text-gray-400 mt-1">Transaksi tercatat</p>
            </div>
            <div class="bg-blue-50 text-blue-600 p-3 rounded-lg text-xl">📅</div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Rata-rata per Transaksi</p>
                <h3 class="text-2xl font-bold text-gray-800">Rp 287.500</h3>
                <p class="text-xs text-gray-400 mt-1">Per transaksi</p>
            </div>
            <div class="bg-purple-50 text-purple-600 p-3 rounded-lg text-xl">📊</div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnExportPDF = document.getElementById("btn-export-pdf");
    const btnExportExcel = document.getElementById("btn-export-excel");

    function getFilterData() {
        const inputs = document.querySelectorAll('input[type="date"]');
        const tglMulai = inputs[0]?.value || "2026-05-01";
        const textTglMulai = tglMulai.split('-').reverse().join('/');
        const tglSelesai = inputs[1]?.value || "2026-05-18";
        const textTglSelesai = tglSelesai.split('-').reverse().join('/');
        const metode = document.querySelector('select')?.value || "Semua Metode";

        return { textTglMulai, textTglSelesai, metode };
    }

    // --- PROSES EXPORT EXCEL ---
    if (btnExportExcel) {
        btnExportExcel.addEventListener("click", function (e) {
            e.preventDefault();
            const filter = getFilterData();

            const dataLaporan = [
                ["LAPORAN PENERIMAAN SIKEMA"],
                ["Periode:", `${filter.textTglMulai} s/d ${filter.textTglSelesai}`],
                ["Metode Pembayaran:", filter.metode],
                [], 
                ["Ringkasan Transaksi", "Nilai"],
                ["Total Penerimaan", "Rp 2.300.000"], 
                ["Total Transaksi", "8 Transaksi"],
                ["Rata-rata per Transaksi", "Rp 287.500"]
            ];

            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.aoa_to_sheet(dataLaporan);
            XLSX.utils.book_append_sheet(workbook, worksheet, "Laporan Penerimaan");
            XLSX.writeFile(workbook, `Laporan_Penerimaan_${filter.textTglMulai.replace(/\//g, '-')}.xlsx`);
        });
    }

    // --- PROSES EXPORT PDF ---
    if (btnExportPDF) {
        btnExportPDF.addEventListener("click", function (e) {
            e.preventDefault();
            const filter = getFilterData();
            
            const element = document.createElement("div");
            element.style.padding = "20px";
            element.style.fontFamily = "Arial, sans-serif";
            
            element.innerHTML = `
                <h2 style="text-align: center; color: #0d9488; margin-bottom: 5px;">LAPORAN PENERIMAAN SIKEMA</h2>
                <p style="text-align: center; font-size: 12px; color: #666; margin-top: 0;">Sistem Informasi Keuangan Madrasah</p>
                <hr style="border: 1px solid #ddd; margin: 15px 0;"/>
                <table style="width: 100%; margin-bottom: 20px; font-size: 14px;">
                    <tr><td><strong>Tanggal Mulai:</strong> ${filter.textTglMulai}</td></tr>
                    <tr><td><strong>Tanggal Selesai:</strong> ${filter.textTglSelesai}</td></tr>
                    <tr><td><strong>Metode Pembayaran:</strong> ${filter.metode}</td></tr>
                </table>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #f3f4f6; text-align: left;">
                            <th style="border: 1px solid #ddd; padding: 10px;">Metrik Transaksi</th>
                            <th style="border: 1px solid #ddd; padding: 10px;">Jumlah / Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 10px;">Total Penerimaan</td>
                            <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold; color: #0d9488;">Rp 2.300.000</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 10px;">Total Transaksi</td>
                            <td style="border: 1px solid #ddd; padding: 10px;">8 Transaksi</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 10px;">Rata-rata per Transaksi</td>
                            <td style="border: 1px solid #ddd; padding: 10px;">Rp 287.500</td>
                        </tr>
                    </tbody>
                </table>
                <p style="margin-top: 40px; text-align: right; font-size: 12px;">Dicetak otomatis oleh SIKEMA pada: ${new Date().toLocaleDateString('id-ID')}</p>
            `;

            const options = {
                margin:       10,
                filename:     `Laporan_Penerimaan_${filter.textTglMulai.replace(/\//g, '-')}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(options).from(element).save();
        });
    }
});
</script>

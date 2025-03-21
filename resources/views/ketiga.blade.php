<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan Obat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script>
        let savedMedicine = "Paracetamol";
        function selectSavedMedicine() {
            document.querySelectorAll(".medicine-row").forEach(row => {
                if (row.dataset.name === savedMedicine) {
                    row.querySelector(".add-btn").click();
                }
            });
        }
        window.onload = selectSavedMedicine;
    </script>
</head>
<body class="bg-gray-100 p-6">
    
    <!-- Navbar -->
    <nav class="bg-green-700 text-white p-4 rounded-xl mb-4">
        <h1 class="text-xl font-bold">Penjualan Obat</h1>
    </nav>

    <div class="grid grid-cols-2 gap-6">
        <!-- Daftar Obat -->
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-lg font-bold mb-2">Daftar Obat</h2>
            <input type="text" placeholder="Cari obat..." class="w-full p-2 border rounded mb-3">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Nama Obat</th>
                        <th class="p-2">Harga</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="medicine-row" data-name="Paracetamol">
                        <td class="p-2">Paracetamol</td>
                        <td class="p-2">Rp 7.500</td>
                        <td class="p-2"><button class="add-btn bg-blue-500 text-white px-3 py-1 rounded">Tambah</button></td>
                    </tr>
                    <tr class="medicine-row" data-name="Amoxicillin">
                        <td class="p-2">Amoxicillin</td>
                        <td class="p-2">Rp 15.000</td>
                        <td class="p-2"><button class="add-btn bg-blue-500 text-white px-3 py-1 rounded">Tambah</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Keranjang Belanja -->
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-lg font-bold mb-2">Keranjang Belanja</h2>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Nama Obat</th>
                        <th class="p-2">Jumlah</th>
                        <th class="p-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-2">Paracetamol</td>
                        <td class="p-2">2</td>
                        <td class="p-2">Rp 15.000</td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4 text-right">
                <strong>Total: Rp 15.000</strong>
            </div>
            <button class="mt-4 w-full bg-green-500 text-white p-2 rounded">Checkout</button>
        </div>
    </div>

    <!-- Riwayat Penjualan -->
    <div class="bg-white p-4 rounded-xl shadow mt-6">
        <h2 class="text-lg font-bold mb-2">Riwayat Penjualan</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Tanggal</th>
                    <th class="p-2">Nama Obat</th>
                    <th class="p-2">Jumlah</th>
                    <th class="p-2">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="p-2">18-02-2025</td>
                    <td class="p-2">Paracetamol</td>
                    <td class="p-2">2</td>
                    <td class="p-2">Rp 15.000</td>
                </tr>
            </tbody>
        </table>
    </div>
    
</body>
</html>
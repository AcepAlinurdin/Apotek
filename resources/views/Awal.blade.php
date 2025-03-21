<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Apotek</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">
    
    <!-- Navbar -->
    <nav class="bg-blue-700 text-white p-4 rounded-xl mb-4">
        <h1 class="text-xl font-bold">Dashboard Apotek</h1>
    </nav>

    <!-- Statistik & Menu -->
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-lg font-bold">Total Obat</h2>
            <p class="text-2xl font-semibold">120</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-lg font-bold">Penjualan Hari Ini</h2>
            <p class="text-2xl font-semibold">Rp 2.500.000</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-lg font-bold">Obat Hampir Habis</h2>
            <p class="text-2xl font-semibold">5 Item</p>
        </div>
    </div>

    <!-- Navigasi Cepat -->
    <div class="grid grid-cols-2 gap-6 mt-6">
        <a href="stok_obat.html" class="bg-green-500 text-white p-4 rounded-xl text-center font-bold">Kelola Stok Obat</a>
        <a href="penjualan_obat.html" class="bg-blue-500 text-white p-4 rounded-xl text-center font-bold">Penjualan Obat</a>
    </div>
</body>
</html>

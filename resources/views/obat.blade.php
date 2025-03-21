<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Obat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Obat</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-blue-500 text-white">
                        <th class="border border-gray-300 px-4 py-2">No</th>
                        <th class="border border-gray-300 px-4 py-2">Nama Obat</th>
                        <th class="border border-gray-300 px-4 py-2">Harga</th>
                        <th class="border border-gray-300 px-4 py-2">Stok</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($obat as $item)
                        <tr class="text-gray-700 odd:bg-gray-100 even:bg-white hover:bg-gray-200">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $item->nama_obat }}</td>
                            <td class="border border-gray-300 px-4 py-2">Rp{{ number_format($item->harga, 2, ',', '.') }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->stok }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center"><button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Edit</button>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
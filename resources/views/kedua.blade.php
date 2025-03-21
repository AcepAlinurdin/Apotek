<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelolaan Obat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Pengelolaan Obat</h2>

    <!-- Form Tambah Obat -->
    <form id="formObat" class="mb-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <input id="nama_obat" name="nama_obat" type="text" placeholder="Nama Obat" class="border p-2 rounded">
            <input id="kategori" name="kategori" type="text" placeholder="Kategori" class="border p-2 rounded">
            <input id="stok" name="stok" type="number" placeholder="Stok" class="border p-2 rounded">
            <input id="harga" name="harga" type="number" placeholder="Harga" class="border p-2 rounded">
        </div>
        <button type="submit" class="mt-4 w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
            Tambah Obat
        </button>
    </form>

    <!-- Tabel Daftar Obat -->
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">No</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Kategori</th>
                <th class="border p-2">Stok</th>
                <th class="border p-2">Harga</th>
            </tr>
        </thead>
        <tbody id="tabelObat">
        @foreach ($obat as $item)
                <tr>
                    <td class="border p-2">{{ $index + 1 }}</td>
                    <td class="border p-2">{{ $item->nama_obat }}</td>
                    <td class="border p-2">{{ $item->kategori }}</td>
                    <td class="border p-2">{{ $item->stok }}</td>
                    <td class="border p-2">Rp{{ number_format($item->harga, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $("#formObat").submit(function(event) {
            event.preventDefault();

            let formData = {
                _token: "{{ csrf_token() }}",
                nama_obat: $("#nama_obat").val(),
                kategori: $("#kategori").val(),
                stok: $("#stok").val(),
                harga: $("#harga").val()
            };

            $.ajax({
                url: "{{ route('obat.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        let nomorBaru = $("#tabelObat tr").length + 1;
                        let newRow = `
                            <tr>
                                <td class="border p-2">${nomorBaru}</td>
                                <td class="border p-2">${formData.nama_obat}</td>
                                <td class="border p-2">${formData.kategori}</td>
                                <td class="border p-2">${formData.stok}</td>
                                <td class="border p-2">Rp ${new Intl.NumberFormat('id-ID').format(formData.harga)}</td>
                            </tr>
                        `;
                        $("#tabelObat").append(newRow);
                        $("#formObat")[0].reset();
                    } else {
                        alert("Gagal menambahkan data!");
                    }
                },
                error: function(xhr) 

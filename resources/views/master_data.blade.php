<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Penting untuk AJAX --}}
    <title>Pengelolaan Obat - Master Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    
    </style>
</head>
<body class="bg-gray-100 p-8">

<nav class="bg-green-700 text-white p-4 rounded-xl mb-4 flex justify-between items-center shadow-md">
    <h1 class="text-xl font-bold">Apotek Parakan Muncang</h1>
    <div class="space-x-4">
        {{-- PERBAIKAN: Menggunakan URL langsung untuk semua link navigasi --}}
        <a href="/master_data" class="font-bold underline ">Master Data</a>
        <a href="/perhitungan" class="hover:underline">Pembelian</a>
        <a href="/penjualan" class="hover:underline">Kasir</a>
    </div>
</nav>

<div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-4">Pengelolaan Data Obat</h1>

    <!-- Form Tambah / Edit Obat -->
    <div class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Formulir Data Obat</h2>
        <form id="formObat">
            <input type="hidden" id="obat_id" name="obat_id">
            <div class="grid md:grid-cols-4 grid-cols-2 gap-4">
                <div class="md:col-span-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input id="tanggal" name="tanggal" type="date" class="mt-1 border p-2 rounded-md w-full" required>
                </div>
                <div class="md:col-span-1">
                    <label for="kode_obat" class="block text-sm font-medium text-gray-700">Kode Obat</label>
                    <input id="kode_obat" name="kode_obat" type="text" placeholder="Contoh: PM001" class="mt-1 border p-2 rounded-md w-full" required>
                </div>
                <div class="md:col-span-2">
                    <label for="nama_obat" class="block text-sm font-medium text-gray-700">Nama Obat</label>
                    <input id="nama_obat" name="nama_obat" type="text" placeholder="Nama Lengkap Obat" class="mt-1 border p-2 rounded-md w-full" required>
                </div>
                <!-- <div class="md:col-span-2">
                     <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <input id="kategori" name="kategori" type="text" placeholder="Contoh: Tablet" class="mt-1 border p-2 rounded-md w-full" required>
                </div> -->
     <div class="md:col-span-2">
                     <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                     <select id="kategori" name="kategori" class="mt-1 border p-2 rounded-md w-full bg-white" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="Obat Bebas">Obat Bebas</option>
                        <option value="Obat Bebas Terbatas">Obat Bebas Terbatas</option>
                        <option value="Obat Keras">Obat Keras</option>
                        <!-- <option value="Tablet">Tablet</option>
                        <option value="Sirup">Sirup</option>
                        <option value="Kapsul">Kapsul</option>
                        <option value="Salep">Salep</option> -->
                        <option value="Alat Kesehatan">Alat Kesehatan</option>
                     </select>
                </div>

                 <div class="md:col-span-2">
                     <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                    <input id="supplier" name="supplier" type="text" placeholder="Nama Supplier" class="mt-1 border p-2 rounded-md w-full">
                </div>
                <div class="md:col-span-1">
                    <label for="stok" class="block text-sm font-medium text-gray-700">Stok (qty)</label>
                    <input id="stok" name="stok" type="number" placeholder="Jumlah Stok" class="mt-1 border p-2 rounded-md w-full" required>
                </div>
                <div class="md:col-span-1">
                    <label for="harga_satuan" class="block text-sm font-medium text-gray-700">Harga Satuan</label>
                    <input id="harga_satuan" name="harga_satuan" type="number" placeholder="Contoh: 1500" class="mt-1 border p-2 rounded-md w-full" required>
                </div>
                 <div class="md:col-span-2">
                    <label for="harga_box" class="block text-sm font-medium text-gray-700">Harga per Box</label>
                    <input id="harga_box" name="harga_box" type="number" placeholder="Contoh: 15000" class="mt-1 border p-2 rounded-md w-full" required>
                </div>
            </div>
            <div class="flex space-x-4 mt-6">
                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 font-semibold transition-colors">Simpan</button>
                <button type="button" id="btn-clear" class="w-1/3 bg-gray-300 text-gray-700 p-2 rounded-md hover:bg-gray-400 font-semibold transition-colors">Batal</button>
            </div>
        </form>
        <div id="form-error" class="text-red-500 mt-2"></div>
    </div>

    <!-- Tabel Daftar Obat -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Tanggal</th>
                    <th class="py-3 px-6 text-left">Kode</th>
                    <th class="py-3 px-6 text-left">Nama Obat</th>
                    <th class="py-3 px-6 text-left">Kategori</th>
                    <th class="py-3 px-6 text-left">Supplier</th>
                    <th class="py-3 px-6 text-center">Stok</th>
                    <th class="py-3 px-6 text-right">Harga Satuan</th>
                    <th class="py-3 px-6 text-right">Harga Box</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelObat" class="text-gray-700 text-sm">
                @forelse($data_obats as $obat)
                    <tr id="row-{{ $obat->id }}" class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($obat->tanggal)->format('d/m/Y') }}</td>
                        <td class="py-3 px-6 text-left font-mono">{{ $obat->kode_obat }}</td>
                        <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                        <td class="py-3 px-6 text-left">{{ $obat->kategori }}</td>
                        <td class="py-3 px-6 text-left">{{ $obat->supplier }}</td>
                        <td class="py-3 px-6 text-center font-bold">{{ $obat->qty }}</td>
                        <td class="py-3 px-6 text-right">Rp {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
                        <td class="py-3 px-6 text-right">Rp {{ number_format($obat->harga_box, 0, ',', '.') }}</td>
                        <td class="py-3 px-6 text-center">
                            <button class="edit-btn bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600 text-xs" data-id="{{ $obat->id }}">Edit</button>
                            <button class="delete-btn bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 text-xs" data-id="{{ $obat->id }}">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center p-4">Belum ada data obat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    function resetForm() {
        $('#formObat')[0].reset();
        $('#obat_id').val('');
        $('#formObat button[type="submit"]').text('Simpan').removeClass('bg-green-500 hover:bg-green-600').addClass('bg-blue-500 hover:bg-blue-600');
    }

    $('#btn-clear').on('click', function() {
        resetForm();
    });

    $('#formObat').submit(function(e) {
        e.preventDefault();
        let id = $('#obat_id').val();
        let url = id ? `/master_data/${id}` : "{{ route('obat.master.store') }}";
        let method = id ? 'PUT' : 'POST';
        let formData = $(this).serialize();

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                if(response.success){
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                 let errorMsg = "Terjadi kesalahan:\n";
                 if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function(key, value){
                        errorMsg += `- ${value[0]}\n`;
                    });
                 } else {
                    errorMsg += "Silakan periksa kembali data Anda atau hubungi administrator.";
                 }
                 alert(errorMsg);
            }
        });
    });

    $('#tabelObat').on('click', '.edit-btn', function() {
        let row = $(this).closest('tr');
        let id = $(this).data('id');

        let tanggal = new Date(row.find('td:eq(0)').text().split('/').reverse().join('-')).toISOString().split('T')[0];

        $('#obat_id').val(id);
        $('#tanggal').val(tanggal);
        $('#kode_obat').val(row.find('td:eq(1)').text());
        $('#nama_obat').val(row.find('td:eq(2)').text());
        $('#kategori').val(row.find('td:eq(3)').text());
        $('#supplier').val(row.find('td:eq(4)').text());
        $('#stok').val(row.find('td:eq(5)').text());
        $('#harga_satuan').val(row.find('td:eq(6)').text().replace(/[^0-9]/g, ''));
        $('#harga_box').val(row.find('td:eq(7)').text().replace(/[^0-9]/g, ''));
        
        $('#formObat button[type="submit"]').text('Update').removeClass('bg-blue-500 hover:bg-blue-600').addClass('bg-green-500 hover:bg-green-600');
        
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    $('#tabelObat').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: `/master_data/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if(response.success){
                        alert(response.message);
                        $(`#row-${id}`).remove();
                    }
                },
                error: function(xhr) {
                    alert('Gagal menghapus data.');
                }
            });
        }
    });
});
</script>

</body>
</html>

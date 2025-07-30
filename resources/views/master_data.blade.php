<x-app-layout>
    {{-- Slot untuk judul halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengelolaan Data Obat') }}
        </h2>
    </x-slot>

    {{-- Konten utama halaman --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Form Tambah / Edit Obat --}}
                <div class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Formulir Data Obat</h2>
                    <form id="formObat">
                        <input type="hidden" id="obat_id" name="obat_id">
                        <div class="grid md:grid-cols-4 grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="nama_obat" class="block text-sm font-medium text-gray-700">Nama Obat</label>
                                <input id="nama_obat" name="nama_obat" type="text" placeholder="Nama Lengkap Obat" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>
                            <div class="md:col-span-2">
                                <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select id="kategori" name="kategori" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    <option value="Obat Luar">Obat Luar</option>
                                    <option value="Obat Makan/Minum">Obat Makan/Minum</option>
                                    <option value="Alat Kesehatan">Alat Kesehatan</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                                <input id="supplier" name="supplier" type="text" placeholder="Nama Supplier" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>
                            <div class="md:col-span-1">
                                <label for="stok" class="block text-sm font-medium text-gray-700">Stok (qty)</label>
                                <input id="stok" name="stok" type="number" placeholder="Jumlah Stok" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>
                            <div class="md:col-span-1">
                                <label for="harga_satuan" class="block text-sm font-medium text-gray-700">Harga Satuan</label>
                                <input id="harga_satuan" name="harga_satuan" type="number" placeholder="Contoh: 1500" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>
                            <div class="md:col-span-2">
                                <label for="harga_box" class="block text-sm font-medium text-gray-700">Harga per Box</label>
                                <input id="harga_box" name="harga_box" type="number" placeholder="Contoh: 15000" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>
                        </div>
                        <div class="flex space-x-4 mt-6">
                            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 font-semibold transition-colors">Simpan</button>
                            <button type="button" id="btn-clear" class="w-1/3 bg-gray-300 text-gray-700 p-2 rounded-md hover:bg-gray-400 font-semibold transition-colors">Batal</button>
                        </div>
                    </form>
                </div>

                {{-- Form Pencarian --}}
                <div class="mb-4">
                    <form action="{{ route('obat.master.index') }}" method="GET" class="flex items-center space-x-2">
                        <input type="text" name="search" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Cari berdasarkan Nama Obat..." value="{{ $searchTerm ?? '' }}">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Cari
                        </button>
                        <a href="{{ route('obat.master.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Reset
                        </a>
                    </form>
                </div>

                {{-- Tabel Daftar Obat --}}
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Tanggal Dibuat</th>
                                <th class="py-3 px-6 text-left">Nama Obat</th>
                                <th class="py-3 px-6 text-left">Kategori</th>
                                <th class="py-3 px-6 text-left">Supplier</th>
                                <th class="py-3 px-6 text-center">Stok</th>
                                <th class="py-3 px-6 text-right">Harga Satuan</th>
                                <th class="py-3 px-6 text-right">Harga Box</th>
                                <th class="py-3 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelObat" class="text-gray-700 text-sm ">
                            @forelse($data_obats as $obat)
                                <tr id="row-{{ $obat->id }}" class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $obat->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                                    <td class="py-3 px-6 text-left">{{ $obat->kategori }}</td>
                                    <td class="py-3 px-6 text-left">{{ $obat->supplier->nama_supplier ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-center font-bold">{{ $obat->stok }}</td>
                                    <td class="py-3 px-6 text-right">Rp {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-right">Rp {{ number_format($obat->harga_box, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <button class="edit-btn bg-yellow-500 text-black py-1 px-3 rounded hover:bg-yellow-600 text-xs" data-id="{{ $obat->id }}">Edit</button>
                                        <button class="delete-btn bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 text-xs" data-id="{{ $obat->id }}">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center p-4">Belum ada data obat atau data tidak ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@livewireScripts
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

            $('#btn-clear').on('click', resetForm);

            // [DEBUG] Script AJAX diperbarui dengan console.log untuk debugging
            $('#formObat').submit(function(e) {
                e.preventDefault();
                console.log('Form submission triggered.'); // 1. Cek apakah event terpicu

                let id = $('#obat_id').val();
                let url = id ? `/master_data/${id}` : "{{ route('obat.master.store') }}";
                let method = id ? 'PUT' : 'POST';
                let formData = $(this).serialize();

                console.log('Sending AJAX request...'); // 2. Cek sebelum mengirim
                console.log('URL:', url);
                console.log('Method:', method);
                console.log('Data:', formData);

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        // 3. Log response sukses dari server
                        console.log('AJAX Success:', response); 
                        if(response.success){
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Terjadi kesalahan: ' + (response.message || 'Tidak ada pesan error spesifik.'));
                        }
                    },
                    error: function(xhr, status, error) {
                        // 4. Log seluruh objek error untuk debug mendalam
                        console.error('AJAX Error:', xhr);
                        console.error('Status:', status);
                        console.error('Error Thrown:', error);

                        let errorMsg = "Terjadi kesalahan:\n";
                        if (xhr.status === 422) {
                            console.log('Validation Errors:', xhr.responseJSON.errors);
                            $.each(xhr.responseJSON.errors, function(key, value){
                                errorMsg += `- ${value[0]}\n`;
                            });
                        } else {
                            errorMsg += `Gagal menyimpan data (Status: ${xhr.status}). Silakan hubungi administrator.`;
                            if(xhr.responseText) {
                                console.error('Server Response:', xhr.responseText);
                            }
                        }
                        alert(errorMsg);
                    }
                });
            });

            $('#tabelObat').on('click', '.edit-btn', function() {
                let row = $(this).closest('tr');
                let id = $(this).data('id');
                
                $('#obat_id').val(id);
                $('#nama_obat').val(row.find('td:eq(1)').text());
                $('#kategori').val(row.find('td:eq(2)').text());
                $('#supplier').val(row.find('td:eq(3)').text());
                $('#stok').val(row.find('td:eq(4)').text());
                $('#harga_satuan').val(row.find('td:eq(5)').text().replace(/[^0-9]/g, ''));
                $('#harga_box').val(row.find('td:eq(6)').text().replace(/[^0-9]/g, ''));
                
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
    @endpush
 @stack('scripts') 
</x-app-layout>

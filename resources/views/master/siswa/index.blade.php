<x-layouts.app title="Data Siswa">
    <x-slot:pageTitle>Master Data / Siswa</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Siswa</h1>
                <p class="text-gray-500 text-sm mt-0.5">Kelola data induk siswa madrasah</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('master.siswa.import.form') }}" class="btn-secondary">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Import Excel
                </a>
                <a href="{{ route('master.siswa.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Siswa
                </a>
            </div>
        </div>

        {{-- Filter / Pencarian --}}
        <form method="GET" action="{{ route('master.siswa.index') }}" class="card p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="cari" value="{{ request('cari') }}"
                        class="form-input" placeholder="Cari nama atau nomor induk...">
                </div>
                <div class="sm:w-44">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="lulus"    {{ request('status') === 'lulus'    ? 'selected' : '' }}>Lulus</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Cari</button>
                @if(request('cari') || request('status'))
                    <a href="{{ route('master.siswa.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="card">
            @if($siswa->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="text-sm">Tidak ada data siswa ditemukan.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Induk</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Asrama</th>
                                <th>JK</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $s)
                                <tr>
                                    <td class="font-mono text-xs font-medium">{{ $s->no_induk }}</td>
                                    <td class="font-medium text-gray-900">{{ $s->nama }}</td>
                                    <td>{{ $s->kelas }}</td>
                                    <td class="text-gray-500">{{ $s->asrama ?? '-' }}</td>
                                    <td>
                                        <span class="{{ $s->jenis_kelamin === 'L' ? 'badge-blue' : 'badge badge-pink' }}">
                                            {{ $s->jenis_kelamin === 'L' ? 'L' : 'P' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php $statusMap = ['aktif' => 'badge-green', 'nonaktif' => 'badge-gray', 'lulus' => 'badge-blue']; @endphp
                                        <span class="{{ $statusMap[$s->status] ?? 'badge-gray' }}">
                                            {{ ucfirst($s->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('master.siswa.edit', $s) }}"
                                                class="btn-secondary btn-sm">Edit</a>
                                            <form id="del-siswa-{{ $s->id }}" method="POST"
                                                action="{{ route('master.siswa.destroy', $s) }}">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="Yakin hapus siswa {{ $s->nama }}?"
                                                data-form-id="del-siswa-{{ $s->id }}"
                                                class="btn-danger btn-sm">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($siswa->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $siswa->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>

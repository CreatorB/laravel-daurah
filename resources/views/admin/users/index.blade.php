@extends('layouts.app')

@section('title', 'Kelola User - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3"><i class="fas fa-users me-2"></i>Kelola User</h2>
    
    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, no HP, lembaga..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
            </div>
            <div class="col-12 col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary gradient-bg btn-sm">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>Reset
                    </a>
                </div>
            </div>
        </div>
    </form>
    
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.users.export', array_merge(request()->all(), ['export_csv' => 1])) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.users.export-excel', array_merge(request()->all(), ['export_excel' => 1])) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
            </ul>
        </div>
        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="d-inline">
            @csrf
            <input type="file" name="csv_file" id="csv_file" class="d-none" accept=".csv" onchange="this.form.submit()">
            <label for="csv_file" class="btn btn-outline-primary btn-sm mb-0">
                <i class="fas fa-upload me-1"></i><span class="d-none d-sm-inline">Import CSV</span>
            </label>
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn btn-primary gradient-bg btn-sm">
            <i class="fas fa-user-plus me-1"></i><span class="d-none d-sm-inline">User</span> Baru
        </a>
    </div>
    
    @if(request()->has('search') || request()->has('start_date') || request()->has('end_date'))
    <div class="alert alert-info py-2">
        <i class="fas fa-info-circle me-1"></i> Menampilkan {{ $users->count() }} hasil filter
    </div>
    @endif
    
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        <div class="card-body p-0">
            @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="cursor: pointer;" onclick="sortTable('nama')">
                                Nama
                                @if(request('sort') == 'nama')
                                    <i class="fas fa-sort-up"></i>
                                @elseif(request('sort') == 'nama_desc')
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            </th>
                            <th>No HP</th>
                            <th style="cursor: pointer;" onclick="sortTable('lembaga')">
                                Lembaga
                                @if(request('sort') == 'lembaga')
                                    <i class="fas fa-sort-up"></i>
                                @elseif(request('sort') == 'lembaga_desc')
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            </th>
                            <th>Domisili</th>
                            <th>Menginap</th>
                            <th style="cursor: pointer;" onclick="sortTable('created_at')">
                                Tanggal Daftar
                                @if(request('sort') == 'created_at')
                                    <i class="fas fa-sort-up"></i>
                                @elseif(request('sort') == 'created_at_desc')
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            </th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="#" class="text-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#userDetailModal{{ $user->id }}">
                                    {{ $user->nama }}
                                </a>
                            </td>
                            <td>+{{ $user->nohp }}</td>
                            <td>{{ $user->lembaga }}</td>
                            <td>{{ $user->domisili }}</td>
                            <td>
                                @if($user->menginap == 'ya')
                                <span class="badge gradient-bg">Ya</span>
                                @else
                                <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn btn-primary gradient-bg">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users-times fa-3x mb-3"></i>
                <p>Belum ada user</p>
            </div>
            @endif
        </div>
    </div>
</div>

@foreach($users as $user)
<div class="modal fade" id="userDetailModal{{ $user->id }}" tabindex="-1" aria-labelledby="userDetailModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailModalLabel{{ $user->id }}">
                    <i class="fas fa-user me-2"></i>Detail User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 140px;">Nama</td>
                                <td><strong>{{ $user->nama }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">No HP</td>
                                <td>+{{ $user->nohp }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $user->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Lembaga</td>
                                <td>{{ $user->lembaga }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat</td>
                                <td>{{ $user->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Domisili</td>
                                <td>{{ $user->domisili ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Menginap</td>
                                <td>
                                    @if($user->menginap == 'ya')
                                    <span class="badge gradient-bg">Ya</span>
                                    @else
                                    <span class="badge bg-secondary">Tidak</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Daftar</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted">Bukti Undangan</label>
                            @if($user->bukti_undangan)
                                @php
                                    $extension = pathinfo($user->bukti_undangan, PATHINFO_EXTENSION);
                                @endphp
                                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $user->bukti_undangan) }}" alt="Bukti Undangan" class="img-fluid rounded" style="max-height: 300px;">
                                    </div>
                                    <a href="{{ asset('storage/' . $user->bukti_undangan) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                                    </a>
                                @else
                                    <div class="mt-2">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-file-pdf me-2"></i>{{ basename($user->bukti_undangan) }}
                                        </div>
                                        <a href="{{ asset('storage/' . $user->bukti_undangan) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-download me-1"></i>Download PDF
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-muted fst-italic">Tidak ada file</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary gradient-bg">
                    <i class="fas fa-edit me-1"></i>Edit User
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
function sortTable(field) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort');
    
    if (currentSort === field) {
        url.searchParams.set('sort', field + '_desc');
    } else {
        url.searchParams.set('sort', field);
    }
    
    window.location.href = url.toString();
}
</script>
@endsection

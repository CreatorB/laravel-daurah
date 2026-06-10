@extends('layouts.app')

@section('title', 'Kelola User - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3"><i class="fas fa-users me-2"></i>Kelola User</h2>
    
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('admin.users.export') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-download me-1"></i><span class="d-none d-sm-inline">Export</span>
        </a>
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
                            <th>No</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Lembaga</th>
                            <th>Domisili</th>
                            <th>Menginap</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->nama }}</td>
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
@endsection

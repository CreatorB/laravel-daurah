@extends('layouts.app')

@section('title', 'Recycle Bin - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3"><i class="fas fa-recycle me-2"></i>Recycle Bin</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'active' ? 'active fw-semibold' : '' }}" href="{{ route('admin.recycle-bin.index', ['tab' => 'active'] + $activeFilters) }}">
                Aktif <span class="badge bg-danger ms-1">{{ $counts['active'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'restored' ? 'active fw-semibold' : '' }}" href="{{ route('admin.recycle-bin.index', ['tab' => 'restored'] + $activeFilters) }}">
                Sudah Direstore <span class="badge bg-success ms-1">{{ $counts['restored'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'permanent' ? 'active fw-semibold' : '' }}" href="{{ route('admin.recycle-bin.index', ['tab' => 'permanent'] + $activeFilters) }}">
                Dihapus Permanen <span class="badge bg-secondary ms-1">{{ $counts['permanent'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'all' ? 'active fw-semibold' : '' }}" href="{{ route('admin.recycle-bin.index', ['tab' => 'all'] + $activeFilters) }}">
                Semua <span class="badge bg-light text-dark ms-1">{{ $counts['all'] }}</span>
            </a>
        </li>
    </ul>

    <form method="GET" class="card mb-3">
        <div class="card-body">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="row g-2">
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Tipe</label>
                    <select name="entity_type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="App\Models\User" {{ $activeFilters['entity_type'] === 'App\\Models\\User' ? 'selected' : '' }}>User</option>
                        <option value="App\Models\Event" {{ $activeFilters['entity_type'] === 'App\\Models\\Event' ? 'selected' : '' }}>Event</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Dari</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $activeFilters['start_date'] }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Sampai</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $activeFilters['end_date'] }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Cari</label>
                    <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Label / nama penghapus" value="{{ $activeFilters['keyword'] }}">
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('admin.recycle-bin.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            @if($items->count() > 0)
            @php $modals = []; @endphp
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>Label / Identitas</th>
                            <th>Dihapus Oleh</th>
                            <th>Tanggal Hapus</th>
                            <th>Status</th>
                            <th style="width: 240px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        @php
                            $typeShort = class_basename($item->entity_type);
                        @endphp
                        <tr>
                            <td>
                                @if($typeShort === 'User')
                                <span class="badge bg-primary"><i class="fas fa-user me-1"></i>User</span>
                                @elseif($typeShort === 'Event')
                                <span class="badge bg-info"><i class="fas fa-calendar me-1"></i>Event</span>
                                @else
                                <span class="badge bg-secondary">{{ $typeShort }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->label ?: '-' }}</strong>
                                <div class="small text-muted">ID #{{ $item->entity_id }}</div>
                            </td>
                            <td>{{ $item->deleted_by_name ?: '-' }}</td>
                            <td>
                                <div>{{ $item->deleted_at->format('d/m/Y H:i') }}</div>
                                <div class="small text-muted">{{ $item->deleted_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($item->permanently_deleted_at)
                                <span class="badge bg-secondary">Permanen</span>
                                <div class="small text-muted">{{ $item->permanently_deleted_at->format('d/m/Y H:i') }}</div>
                                @elseif($item->restored_at)
                                <span class="badge bg-success">Direstore</span>
                                <div class="small text-muted">oleh {{ $item->restored_by_name }} • {{ $item->restored_at->format('d/m/Y H:i') }}</div>
                                @else
                                <span class="badge bg-warning text-dark">Di Recycle Bin</span>
                                @endif
                            </td>
                            <td>
                                @if(!$item->restored_at && !$item->permanently_deleted_at)
                                <form action="{{ route('admin.recycle-bin.restore', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Restore item ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                                @php
                                    $typeShort = class_basename($item->entity_type);
                                    $confirmMsg = "PERMANEN! Hapus '" . ($item->label ?: 'item ini') . "'?";
                                    if ($typeShort === 'Event') {
                                        $confirmMsg .= "\n\nSemua sesi, pendaftaran, absensi, dan materi terkait juga akan ikut terhapus permanen.";
                                    } elseif ($typeShort === 'User') {
                                        $confirmMsg .= "\n\nSemua pendaftaran & absensi user ini juga akan ikut terhapus permanen.";
                                    }
                                    $confirmMsg .= "\n\nTindakan ini tidak dapat dibatalkan.";
                                @endphp
                                <form action="{{ route('admin.recycle-bin.force', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm(&quot;{{ $confirmMsg }}&quot;)">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                        <i class="fas fa-trash"></i> Permanen
                                    </button>
                                </form>
                                @elseif($item->restored_at)
                                <form action="{{ route('admin.recycle-bin.purge', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus catatan ini dari recycle bin? (Data asli tetap aman)')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hapus catatan">
                                        <i class="fas fa-eraser"></i> Hapus Catatan
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.recycle-bin.purge', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus catatan ini dari recycle bin?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hapus catatan">
                                        <i class="fas fa-eraser"></i> Hapus Catatan
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('admin.recycle-bin.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Detail Snapshot">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $items->links() }}
            </div>

            @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-recycle fa-3x mb-3"></i>
                @if($tab === 'active')
                <p>Recycle bin kosong. Tidak ada item yang perlu direstore.</p>
                @elseif($tab === 'restored')
                <p>Belum ada item yang direstore.</p>
                @elseif($tab === 'permanent')
                <p>Belum ada item yang dihapus permanen.</p>
                @else
                <p>Belum ada catatan recycle bin.</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Detail Recycle Bin - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3"><i class="fas fa-recycle me-2"></i>Detail Recycle Bin</h2>

    <div class="card">
        <div class="card-header">Metadata</div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr>
                    <td class="text-muted" style="width: 180px;">Tipe</td>
                    <td><strong>{{ class_basename($item->entity_type) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Label</td>
                    <td>{{ $item->label ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">ID Asli</td>
                    <td>#{{ $item->entity_id }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Dihapus Oleh</td>
                    <td>{{ $item->deleted_by_name ?: '-' }} pada {{ $item->deleted_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Status</td>
                    <td>
                        @if($item->permanently_deleted_at)
                        <span class="badge bg-secondary">Dihapus Permanen</span> oleh {{ $item->permanently_deleted_by_name }} pada {{ $item->permanently_deleted_at->format('d/m/Y H:i:s') }}
                        @elseif($item->restored_at)
                        <span class="badge bg-success">Direstore</span> oleh {{ $item->restored_by_name }} pada {{ $item->restored_at->format('d/m/Y H:i:s') }}
                        @else
                        <span class="badge bg-warning text-dark">Aktif di Recycle Bin</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Record Saat Ini</td>
                    <td>
                        @if($entity)
                        <span class="text-success">Record masih ada di database (deleted_at: {{ optional($entity->deleted_at)->format('d/m/Y H:i:s') ?? 'kosong' }})</span>
                        @else
                        <span class="text-danger">Record sudah tidak ada di database (force-deleted).</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Snapshot</div>
        <div class="card-body">
            <pre class="bg-light p-3 rounded small" style="max-height: 500px; overflow:auto;">{{ json_encode($item->snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.recycle-bin.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
        @if(!$item->restored_at && !$item->permanently_deleted_at)
        <form action="{{ route('admin.recycle-bin.restore', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Restore item ini?')">
            @csrf
            <button type="submit" class="btn btn-success"><i class="fas fa-undo me-1"></i>Restore</button>
        </form>
        @endif
    </div>
</div>
@endsection

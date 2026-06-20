@extends('layouts.app')

@section('title', 'Database Status - Daurah Syariyyah')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="gradient-text"><i class="fas fa-database me-2"></i>Database Status</h2>
        <a href="{{ route('admin.maintenance.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card">
        <div class="card-header gradient-bg">
            <i class="fas fa-table me-2"></i>Tables ({{ count($tableInfo) }})
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Table Name</th>
                            <th class="text-end">Rows</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tableInfo as $table)
                        <tr>
                            <td>{{ $table['name'] }}</td>
                            <td class="text-end">{{ number_format($table['rows']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

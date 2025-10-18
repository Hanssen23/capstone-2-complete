@extends('layouts.admin')

@section('title', 'Auto-Deletion Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-history"></i> Auto-Deletion Logs</h1>
                <div>
                    <a href="{{ route('admin.auto-deletion.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Settings
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-filter"></i> Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.auto-deletion.logs') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="type" class="form-label">Deletion Type</label>
                                <select class="form-select" name="type" id="type">
                                    <option value="">All Types</option>
                                    <option value="auto" {{ request('type') === 'auto' ? 'selected' : '' }}>Automatic</option>
                                    <option value="manual" {{ request('type') === 'manual' ? 'selected' : '' }}>Manual</option>
                                    <option value="admin" {{ request('type') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="from_date" class="form-label">From Date</label>
                                <input type="date" class="form-control" name="from_date" id="from_date" 
                                       value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="to_date" class="form-label">To Date</label>
                                <input type="date" class="form-control" name="to_date" id="to_date" 
                                       value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Deletion Logs ({{ $logs->total() }} total)</h5>
                </div>
                <div class="card-body">
                    @if($logs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Member</th>
                                        <th>Type</th>
                                        <th>Reason</th>
                                        <th>Admin</th>
                                        <th>Warnings</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                        <tr>
                                            <td>
                                                <small>{{ $log->created_at->format('M j, Y') }}</small><br>
                                                <small class="text-muted">{{ $log->created_at->format('g:i A') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $log->member_name }}</strong><br>
                                                <small class="text-muted">{{ $log->member_email }}</small><br>
                                                <small class="badge bg-secondary">{{ $log->member_number }}</small>
                                            </td>
                                            <td>
                                                @switch($log->deletion_type)
                                                    @case('auto')
                                                        <span class="badge bg-warning">Automatic</span>
                                                        @break
                                                    @case('manual')
                                                        <span class="badge bg-info">Manual</span>
                                                        @break
                                                    @case('admin')
                                                        <span class="badge bg-danger">Admin</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($log->deletion_type) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small>{{ $log->deletion_reason }}</small>
                                                @if($log->deletion_criteria)
                                                    <br><small class="text-muted">
                                                        Criteria: {{ implode(', ', json_decode($log->deletion_criteria, true) ?? []) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->admin)
                                                    {{ $log->admin->first_name }} {{ $log->admin->last_name }}
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->first_warning_sent_at)
                                                    <small class="badge bg-info">1st: {{ $log->first_warning_sent_at->format('M j') }}</small><br>
                                                @endif
                                                @if($log->final_warning_sent_at)
                                                    <small class="badge bg-warning">Final: {{ $log->final_warning_sent_at->format('M j') }}</small>
                                                @endif
                                                @if(!$log->first_warning_sent_at && !$log->final_warning_sent_at)
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->is_restored)
                                                    <span class="badge bg-success">Restored</span><br>
                                                    <small class="text-muted">{{ $log->restored_at->format('M j, Y') }}</small>
                                                @elseif($log->member_reactivated_before_deletion)
                                                    <span class="badge bg-primary">Reactivated</span>
                                                @else
                                                    <span class="badge bg-dark">Deleted</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                                    <i class="fas fa-eye"></i> Details
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Log Details Modal -->
                                        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Deletion Log Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6>Member Information</h6>
                                                                <p><strong>Name:</strong> {{ $log->member_name }}</p>
                                                                <p><strong>Email:</strong> {{ $log->member_email }}</p>
                                                                <p><strong>Member Number:</strong> {{ $log->member_number }}</p>
                                                                <p><strong>Status:</strong> {{ ucfirst($log->member_status) }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Deletion Details</h6>
                                                                <p><strong>Type:</strong> {{ ucfirst($log->deletion_type) }}</p>
                                                                <p><strong>Date:</strong> {{ $log->created_at->format('F j, Y \a\t g:i A') }}</p>
                                                                <p><strong>Reason:</strong> {{ $log->deletion_reason }}</p>
                                                                @if($log->admin)
                                                                    <p><strong>Admin:</strong> {{ $log->admin->first_name }} {{ $log->admin->last_name }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        
                                                        @if($log->deletion_criteria)
                                                            <h6>Deletion Criteria</h6>
                                                            <ul>
                                                                @foreach(json_decode($log->deletion_criteria, true) ?? [] as $criterion)
                                                                    <li>{{ $criterion }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif

                                                        @if($log->first_warning_sent_at || $log->final_warning_sent_at)
                                                            <h6>Warning History</h6>
                                                            @if($log->first_warning_sent_at)
                                                                <p><strong>First Warning:</strong> {{ $log->first_warning_sent_at->format('F j, Y \a\t g:i A') }}</p>
                                                            @endif
                                                            @if($log->final_warning_sent_at)
                                                                <p><strong>Final Warning:</strong> {{ $log->final_warning_sent_at->format('F j, Y \a\t g:i A') }}</p>
                                                            @endif
                                                        @endif

                                                        @if($log->is_restored)
                                                            <h6>Restoration Details</h6>
                                                            <p><strong>Restored At:</strong> {{ $log->restored_at->format('F j, Y \a\t g:i A') }}</p>
                                                            <p><strong>Restored By:</strong> {{ $log->restoredByAdmin->first_name ?? 'Unknown' }} {{ $log->restoredByAdmin->last_name ?? '' }}</p>
                                                            @if($log->restoration_reason)
                                                                <p><strong>Reason:</strong> {{ $log->restoration_reason }}</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No deletion logs found</h5>
                            <p class="text-muted">No members have been deleted yet, or your filters didn't match any records.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

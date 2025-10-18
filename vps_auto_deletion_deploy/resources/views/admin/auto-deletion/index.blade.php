@extends('layouts.admin')

@section('title', 'Auto-Deletion Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-trash-alt"></i> Auto-Deletion Settings</h1>
                <div>
                    <a href="{{ route('admin.auto-deletion.logs') }}" class="btn btn-outline-info">
                        <i class="fas fa-history"></i> View Logs
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('command_output'))
                <div class="alert alert-info alert-dismissible fade show">
                    <h6><i class="fas fa-terminal"></i> Command Output:</h6>
                    <pre class="mb-0" style="white-space: pre-wrap;">{{ session('command_output') }}</pre>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['eligible_for_deletion'] }}</h3>
                            <small>Eligible for Deletion</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['first_warning_sent'] }}</h3>
                            <small>First Warning Sent</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['final_warning_sent'] }}</h3>
                            <small>Final Warning Sent</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['excluded_members'] }}</h3>
                            <small>Excluded Members</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['recently_deleted'] }}</h3>
                            <small>Deleted (30 days)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6>Last Run</h6>
                            <small>{{ $stats['last_run'] ? $stats['last_run']->format('M j, Y') : 'Never' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Settings Form -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-cog"></i> Configuration Settings</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.auto-deletion.update-settings') }}">
                                @csrf
                                
                                <!-- Feature Control -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_enabled" 
                                                   id="is_enabled" value="1" {{ $settings->is_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_enabled">
                                                <strong>Enable Auto-Deletion</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted">Master switch for the auto-deletion feature</small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="dry_run_mode" 
                                                   id="dry_run_mode" value="1" {{ $settings->dry_run_mode ? 'checked' : '' }}>
                                            <label class="form-check-label" for="dry_run_mode">
                                                <strong>Dry Run Mode</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted">Test mode - no actual deletions will occur</small>
                                    </div>
                                </div>

                                <!-- Inactivity Thresholds -->
                                <h6><i class="fas fa-clock"></i> Inactivity Thresholds</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="no_login_threshold_days" class="form-label">No Login Threshold (days)</label>
                                        <input type="number" class="form-control @error('no_login_threshold_days') is-invalid @enderror" 
                                               name="no_login_threshold_days" id="no_login_threshold_days" 
                                               value="{{ old('no_login_threshold_days', $settings->no_login_threshold_days) }}" 
                                               min="30" max="1095" required>
                                        @error('no_login_threshold_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="expired_membership_grace_days" class="form-label">Expired Membership Grace (days)</label>
                                        <input type="number" class="form-control @error('expired_membership_grace_days') is-invalid @enderror" 
                                               name="expired_membership_grace_days" id="expired_membership_grace_days" 
                                               value="{{ old('expired_membership_grace_days', $settings->expired_membership_grace_days) }}" 
                                               min="7" max="365" required>
                                        @error('expired_membership_grace_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="unverified_email_threshold_days" class="form-label">Unverified Email Threshold (days)</label>
                                        <input type="number" class="form-control @error('unverified_email_threshold_days') is-invalid @enderror" 
                                               name="unverified_email_threshold_days" id="unverified_email_threshold_days" 
                                               value="{{ old('unverified_email_threshold_days', $settings->unverified_email_threshold_days) }}" 
                                               min="1" max="90" required>
                                        @error('unverified_email_threshold_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="inactive_status_threshold_days" class="form-label">Inactive Status Threshold (days)</label>
                                        <input type="number" class="form-control @error('inactive_status_threshold_days') is-invalid @enderror" 
                                               name="inactive_status_threshold_days" id="inactive_status_threshold_days" 
                                               value="{{ old('inactive_status_threshold_days', $settings->inactive_status_threshold_days) }}" 
                                               min="30" max="730" required>
                                        @error('inactive_status_threshold_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Warning Schedule -->
                                <h6><i class="fas fa-envelope"></i> Warning Schedule</h6>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="first_warning_days" class="form-label">First Warning (days before)</label>
                                        <input type="number" class="form-control @error('first_warning_days') is-invalid @enderror" 
                                               name="first_warning_days" id="first_warning_days" 
                                               value="{{ old('first_warning_days', $settings->first_warning_days) }}" 
                                               min="1" max="90" required>
                                        @error('first_warning_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="final_warning_days" class="form-label">Final Warning (days before)</label>
                                        <input type="number" class="form-control @error('final_warning_days') is-invalid @enderror" 
                                               name="final_warning_days" id="final_warning_days" 
                                               value="{{ old('final_warning_days', $settings->final_warning_days) }}" 
                                               min="1" max="30" required>
                                        @error('final_warning_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" name="send_warning_emails" 
                                                   id="send_warning_emails" value="1" {{ $settings->send_warning_emails ? 'checked' : '' }}>
                                            <label class="form-check-label" for="send_warning_emails">
                                                Send Warning Emails
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Exclusion Rules -->
                                <h6><i class="fas fa-shield-alt"></i> Exclusion Rules</h6>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exclude_vip_members" 
                                                   id="exclude_vip_members" value="1" {{ $settings->exclude_vip_members ? 'checked' : '' }}>
                                            <label class="form-check-label" for="exclude_vip_members">
                                                Exclude VIP Members
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exclude_members_with_payments" 
                                                   id="exclude_members_with_payments" value="1" {{ $settings->exclude_members_with_payments ? 'checked' : '' }}>
                                            <label class="form-check-label" for="exclude_members_with_payments">
                                                Exclude Members with Payments
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exclude_recent_activity" 
                                                   id="exclude_recent_activity" value="1" {{ $settings->exclude_recent_activity ? 'checked' : '' }}>
                                            <label class="form-check-label" for="exclude_recent_activity">
                                                Exclude Recent RFID Activity
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="recent_activity_threshold_days" class="form-label">Recent Activity Threshold (days)</label>
                                        <input type="number" class="form-control @error('recent_activity_threshold_days') is-invalid @enderror" 
                                               name="recent_activity_threshold_days" id="recent_activity_threshold_days" 
                                               value="{{ old('recent_activity_threshold_days', $settings->recent_activity_threshold_days) }}" 
                                               min="1" max="90" required>
                                        @error('recent_activity_threshold_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="schedule_time" class="form-label">Daily Run Time</label>
                                        <input type="time" class="form-control @error('schedule_time') is-invalid @enderror" 
                                               name="schedule_time" id="schedule_time" 
                                               value="{{ old('schedule_time', $settings->schedule_time) }}" required>
                                        @error('schedule_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Manual Actions -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-play"></i> Manual Actions</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Run the deletion process manually for testing or immediate execution.</p>
                            
                            <form method="POST" action="{{ route('admin.auto-deletion.run-process') }}" class="mb-3">
                                @csrf
                                <input type="hidden" name="dry_run" value="1">
                                <button type="submit" class="btn btn-outline-info w-100">
                                    <i class="fas fa-vial"></i> Run Dry Run Test
                                </button>
                            </form>

                            @if($settings->is_enabled && !$settings->dry_run_mode)
                                <form method="POST" action="{{ route('admin.auto-deletion.run-process') }}" 
                                      onsubmit="return confirm('Are you sure you want to run the actual deletion process? This cannot be undone.')">
                                    @csrf
                                    <input type="hidden" name="dry_run" value="0">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> Run Actual Process
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-lock"></i> Actual Process Disabled
                                </button>
                                <small class="text-muted">Enable auto-deletion and disable dry-run mode to run actual process.</small>
                            @endif
                        </div>
                    </div>

                    <!-- Last Run Stats -->
                    @if($stats['last_run'])
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6><i class="fas fa-chart-bar"></i> Last Run Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h5>{{ $stats['last_run_stats']['processed'] }}</h5>
                                        <small>Processed</small>
                                    </div>
                                    <div class="col-4">
                                        <h5>{{ $stats['last_run_stats']['warned'] }}</h5>
                                        <small>Warned</small>
                                    </div>
                                    <div class="col-4">
                                        <h5>{{ $stats['last_run_stats']['deleted'] }}</h5>
                                        <small>Deleted</small>
                                    </div>
                                </div>
                                <hr>
                                <small class="text-muted">
                                    Last run: {{ $stats['last_run']->format('F j, Y \a\t g:i A') }}
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

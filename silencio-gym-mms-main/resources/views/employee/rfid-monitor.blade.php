<x-layout>
    <x-nav-employee></x-nav-employee>
    <div class="flex-1 bg-white">
        <x-topbar>RFID Monitor</x-topbar>

        <div class="bg-white p-6">
            <!-- Metrics Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h2 class="text-2xl font-bold mb-6" style="color: #1E40AF;">Today's Metrics</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white border rounded-lg p-6" style="border-color: #059669; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium mb-2" style="color: #6B7280;">Today's Check-ins</p>
                                    <p class="text-3xl font-bold" style="color: #059669;" id="today-checkins">-</p>
                                </div>
                                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: #059669;">
                                    <span class="text-2xl text-white">✅</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white border rounded-lg p-6" style="border-color: #DC2626; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium mb-2" style="color: #6B7280;">Failed Attempts</p>
                                    <p class="text-3xl font-bold" style="color: #DC2626;" id="failed-attempts">-</p>
                                </div>
                                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: #DC2626;">
                                    <span class="text-2xl text-white">❌</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white border rounded-lg p-6" style="border-color: #8B5CF6; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium mb-2" style="color: #6B7280;">Unknown Cards</p>
                                    <p class="text-3xl font-bold" style="color: #8B5CF6;" id="unknown-cards">-</p>
                                </div>
                                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: #8B5CF6;">
                                    <span class="text-2xl text-white">❓</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RFID Control Panel -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold" style="color: #1E40AF;">RFID Control</h2>
                        <div class="flex items-center gap-3">
                            <div id="rfid-control-status-indicator" class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span id="rfid-control-status-text" class="text-sm font-medium" style="color: #DC2626;">System Offline</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-center gap-6">
                        <button onclick="startRfidSystem()" id="start-rfid-control-btn" 
                                class="px-8 py-4 text-white text-lg rounded-lg transition-colors flex items-center gap-3" 
                                style="background-color: #059669;" 
                                onmouseover="this.style.backgroundColor='#047857'" 
                                onmouseout="this.style.backgroundColor='#059669'">
                            <span class="text-xl">▶️</span>
                            Start RFID
                        </button>
                        
                        <button onclick="stopRfidSystem()" id="stop-rfid-control-btn" 
                                class="px-8 py-4 text-white text-lg rounded-lg transition-colors flex items-center gap-3" 
                                style="background-color: #DC2626;" 
                                onmouseover="this.style.backgroundColor='#B91C1C'" 
                                onmouseout="this.style.backgroundColor='#DC2626'">
                            <span class="text-xl">⏹️</span>
                            Stop RFID
                        </button>
                    </div>
                </div>
            </div>

            <!-- Currently Active Members -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold" style="color: #1E40AF;">Currently Active Members</h2>
                        <button onclick="refreshActiveMembers()" 
                                class="px-4 py-2 text-white text-sm rounded-lg transition-colors flex items-center gap-2" 
                                style="background-color: #2563EB;" 
                                onmouseover="this.style.backgroundColor='#1D4ED8'" 
                                onmouseout="this.style.backgroundColor='#2563EB'">
                            <span class="text-lg">🔄</span>
                            Refresh
                        </button>
                    </div>
                    
                    <div id="active-members-list" class="space-y-3">
                        <div class="text-center py-8" style="color: #6B7280;">
                            <p>Loading active members...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent RFID Activity -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold" style="color: #1E40AF;">Recent RFID Activity</h2>
                        <div class="flex items-center gap-4">
                            <select id="log-filter" 
                                    class="px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                    style="border-color: #E5E7EB;">
                                <option value="">All Actions</option>
                                <option value="check_in">Check-ins</option>
                                <option value="check_out">Check-outs</option>
                                <option value="unknown_card">Unknown Cards</option>
                                <option value="expired_membership">Expired Memberships</option>
                            </select>
                            <button onclick="refreshLogs()" 
                                    class="px-4 py-2 text-white text-sm rounded-lg transition-colors flex items-center gap-2" 
                                    style="background-color: #2563EB;" 
                                    onmouseover="this.style.backgroundColor='#1D4ED8'" 
                                    onmouseout="this.style.backgroundColor='#2563EB'">
                                <span class="text-lg">🔄</span>
                                Refresh
                            </button>
                        </div>
                    </div>
                    
                    <div id="rfid-logs-list" class="space-y-2">
                        <div class="text-center py-8" style="color: #6B7280;">
                            <p>Loading RFID logs...</p>
                        </div>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div id="rfid-logs-pagination" class="mt-6 flex items-center justify-between">
                        <div class="text-sm" style="color: #6B7280;">
                            <span id="logs-info">Showing 0 to 0 of 0 results</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button id="prev-page" onclick="changePage(-1)" 
                                    class="px-4 py-2 text-sm font-medium border rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                                    style="border-color: #E5E7EB; color: #6B7280;" 
                                    onmouseover="this.style.backgroundColor='#F3F4F6'" 
                                    onmouseout="this.style.backgroundColor='transparent'">
                                Previous
                            </button>
                            <span id="page-info" class="px-4 py-2 text-sm" style="color: #6B7280;">Page 1</span>
                            <button id="next-page" onclick="changePage(1)" 
                                    class="px-4 py-2 text-sm font-medium border rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                                    style="border-color: #E5E7EB; color: #6B7280;" 
                                    onmouseover="this.style.backgroundColor='#F3F4F6'" 
                                    onmouseout="this.style.backgroundColor='transparent'">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pagination variables
        let currentPage = 1;
        let totalPages = 1;
        let totalRecords = 0;
        let currentFilter = '';

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('RFID Monitor initialized');
            
            // Check RFID system status on load
            checkRfidStatus();
            
            // Load initial data
            loadDashboardStats();
            loadActiveMembers();
            loadRfidLogs();
            
            // Auto-refresh every 1 second for real-time updates
            setInterval(function() {
                console.log('Auto-refreshing RFID Monitor data...');
                loadDashboardStats();
                loadActiveMembers();
                loadRfidLogs(currentPage); // Maintain current page during auto-refresh
            }, 1000);
            
            // Check RFID status every 10 seconds
            setInterval(function() {
                checkRfidStatus();
            }, 10000);
            
        });

        // Load dashboard statistics
        function loadDashboardStats() {
            console.log('🔄 Loading dashboard stats...');
            fetch('{{ route("employee.dashboard.stats") }}')
                .then(response => {
                    console.log('📡 Dashboard stats response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Dashboard stats loaded:', data);
                    document.getElementById('current-active-count').textContent = data.current_active_members;
                    document.getElementById('today-checkins').textContent = data.today_attendance;
                    document.getElementById('failed-attempts').textContent = data.failed_rfid_today;
                })
                .catch(error => {
                    console.error('Error loading dashboard stats:', error);
                    // Show error indicator
                    document.getElementById('current-active-count').textContent = 'Error';
                    document.getElementById('today-checkins').textContent = 'Error';
                    document.getElementById('failed-attempts').textContent = 'Error';
                });
        }

        // Load active members
        function loadActiveMembers() {
            console.log('🔄 Loading active members...');
            fetch('{{ route("rfid.active-members") }}')
                .then(response => {
                    console.log('📡 Active members response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Active members data received:', data);
                    const container = document.getElementById('active-members-list');
                    
                    if (!data.success) {
                        throw new Error('API returned error: ' + (data.message || 'Unknown error'));
                    }
                    
                    console.log('👥 Active members count:', data.count);
                    console.log('👥 Active members list:', data.active_members);
                    
                    if (data.active_members.length === 0) {
                        console.log('📭 No active members, showing empty message');
                        container.innerHTML = '<div class="text-center py-8" style="color: #6B7280;"><p>No active members currently</p></div>';
                        return;
                    }
                    
                    container.innerHTML = data.active_members.map(member => `
                        <div class="flex items-center justify-between p-4 rounded-lg border" style="background-color: #F9FAFB; border-color: #E5E7EB;">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #059669;">
                                    <span class="text-xl text-white">✅</span>
                                </div>
                                <div>
                                    <p class="font-semibold" style="color: #000000;">${member.name}</p>
                                    <p class="text-sm" style="color: #6B7280;">${member.membership_plan} • Checked in at ${formatTime(member.check_in_time)}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium" style="color: #059669;">${member.session_duration}</p>
                                <p class="text-xs" style="color: #6B7280;">Session duration</p>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading active members:', error);
                    document.getElementById('active-members-list').innerHTML = '<div class="text-center py-8" style="color: #DC2626;"><p>Error loading active members: ' + error.message + '</p></div>';
                });
        }

        // Load RFID logs
        function loadRfidLogs(page = 1) {
            console.log('🔄 Loading RFID logs, page:', page);
            const filter = document.getElementById('log-filter').value;
            const url = new URL('{{ route("rfid.logs") }}', window.location.origin);
            
            if (filter) {
                url.searchParams.set('action', filter);
            }
            if (page > 1) {
                url.searchParams.set('page', page);
            }
            
            console.log('📡 RFID logs URL:', url.toString());
            
            fetch(url)
                .then(response => {
                    console.log('📡 RFID logs response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📊 RFID logs data received:', data);
                    const container = document.getElementById('rfid-logs-list');
                    
                    if (!data.success) {
                        throw new Error('API returned error: ' + (data.message || 'Unknown error'));
                    }
                    
                    console.log('📋 RFID logs count:', data.logs.data.length);
                    
                    if (data.logs.data.length === 0) {
                        container.innerHTML = '<div class="text-center py-8" style="color: #6B7280;"><p>No RFID logs found</p></div>';
                        updatePaginationInfo(data.logs);
                        return;
                    }
                    
                    container.innerHTML = data.logs.data.map((log, index) => {
                        const logConfig = getLogConfig(log.action, log.status);
                        const timestamp = new Date(log.timestamp);
                        const formattedTime = formatTimeWithDate(timestamp);
                        
                        return `
                            <div class="flex items-center p-4 rounded-lg border-l-4" 
                                 style="background-color: ${index % 2 === 0 ? '#FFFFFF' : '#F9FAFB'}; border-left-color: ${logConfig.borderColor}; border-color: #E5E7EB;">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: ${logConfig.bgColor};">
                                        <span class="text-xl text-white">${logConfig.icon}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="font-semibold" style="color: #000000;">${logConfig.memberName || 'Unknown Member'}</p>
                                            <span class="text-sm px-2 py-1 rounded-full text-white" style="background-color: ${logConfig.badgeColor};">
                                                ${logConfig.actionText}
                                            </span>
                                        </div>
                                        <p class="text-sm" style="color: #6B7280;">${logConfig.description}</p>
                                        <p class="text-xs" style="color: #6B7280;">Card: ${log.card_uid}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium" style="color: #000000;">${formattedTime}</p>
                                </div>
                            </div>
                        `;
                    }).join('');
                    
                    updatePaginationInfo(data.logs);
                })
                .catch(error => {
                    console.error('Error loading RFID logs:', error);
                    document.getElementById('rfid-logs-list').innerHTML = '<div class="text-center py-8" style="color: #DC2626;"><p>Error loading RFID logs: ' + error.message + '</p></div>';
                });
        }

        // Get log configuration based on action and status
        function getLogConfig(action, status) {
            const configs = {
                'check_in': {
                    icon: '✅',
                    borderColor: '#059669',
                    bgColor: '#059669',
                    badgeColor: '#059669',
                    actionText: 'Checked in successfully',
                    description: 'Member successfully checked in',
                    memberName: null // Will be extracted from log message
                },
                'check_out': {
                    icon: '🔄',
                    borderColor: '#D97706',
                    bgColor: '#D97706',
                    badgeColor: '#D97706',
                    actionText: 'Checked out',
                    description: 'Member checked out',
                    memberName: null
                },
                'unknown_card': {
                    icon: '❓',
                    borderColor: '#8B5CF6',
                    bgColor: '#8B5CF6',
                    badgeColor: '#8B5CF6',
                    actionText: 'Unknown Member',
                    description: 'Unknown card detected',
                    memberName: 'Unknown Member'
                },
                'expired_membership': {
                    icon: '❌',
                    borderColor: '#DC2626',
                    bgColor: '#DC2626',
                    badgeColor: '#DC2626',
                    actionText: 'Expired Membership',
                    description: 'Membership has expired',
                    memberName: null
                }
            };
            
            return configs[action] || {
                icon: '❓',
                borderColor: '#8B5CF6',
                bgColor: '#8B5CF6',
                badgeColor: '#8B5CF6',
                actionText: 'Unknown Action',
                description: 'Unknown action detected',
                memberName: 'Unknown Member'
            };
        }

        // Format time to 12-hour format with AM/PM
        function formatTime(timeString) {
            if (!timeString) return 'N/A';
            const date = new Date(timeString);
            return date.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        }

        // Format time with full date
        function formatTimeWithDate(date) {
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric',
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
                });
        }

        // Play feedback sound
        function playFeedbackSound(type) {
            // Create audio context for sound feedback
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                if (type === 'success') {
                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                    oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
                } else if (type === 'error') {
                    oscillator.frequency.setValueAtTime(400, audioContext.currentTime);
                    oscillator.frequency.setValueAtTime(200, audioContext.currentTime + 0.1);
                } else if (type === 'warning') {
                    oscillator.frequency.setValueAtTime(600, audioContext.currentTime);
                }
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.2);
            } catch (e) {
                console.log('Audio not supported');
            }
        }

        // Refresh functions
        function refreshActiveMembers() {
            loadActiveMembers();
        }

        function refreshLogs() {
            currentPage = 1; // Reset to first page when manually refreshing
            loadRfidLogs();
        }

        // Update pagination information
        function updatePaginationInfo(logs) {
            currentPage = logs.current_page;
            totalPages = logs.last_page;
            totalRecords = logs.total;
            
            const startRecord = logs.from || 0;
            const endRecord = logs.to || 0;
            
            document.getElementById('logs-info').textContent = `Showing ${startRecord} to ${endRecord} of ${totalRecords} results`;
            document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
            
            // Update button states
            document.getElementById('prev-page').disabled = currentPage <= 1;
            document.getElementById('next-page').disabled = currentPage >= totalPages;
        }
        
        // Change page
        function changePage(direction) {
            const newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                loadRfidLogs(newPage);
            }
        }
        
        // Filter logs when selection changes
        document.getElementById('log-filter').addEventListener('change', function() {
            currentPage = 1; // Reset to first page when filter changes
            loadRfidLogs();
        });

        // RFID System Management Functions
        function startRfidSystem() {
            console.log('🚀 Starting RFID system...');
            const button = document.getElementById('start-rfid-control-btn');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Starting...';
            button.disabled = true;
            
            fetch('{{ route("employee.rfid.start") }}')
                .then(response => response.json())
                .then(data => {
                    console.log('📊 RFID start response:', data);
                    
                    if (data.success) {
                        console.log('✅ RFID system started successfully');
                        // Immediately update status to online
                        updateRfidStatus(true, data.message);
                        
                        // Refresh data after starting
                        setTimeout(() => {
                            loadDashboardStats();
                            loadActiveMembers();
                            loadRfidLogs();
                        }, 2000);
                    } else {
                        console.log('❌ Failed to start RFID system:', data.message);
                        updateRfidStatus(false, data.message);
                    }
                })
                .catch(error => {
                    console.error('❌ Error starting RFID system:', error);
                    updateRfidStatus(false, 'Error starting RFID system');
                })
                .finally(() => {
                    // Restore button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        }

        
        function checkRfidStatus() {
            fetch('{{ route("employee.rfid.status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateRfidStatus(data.rfid_reader_running, data.message);
                    }
                })
                .catch(error => {
                    console.error('Error checking RFID status:', error);
                    updateRfidStatus(false, 'Error checking status');
                });
        }
        
        function stopRfidSystem() {
            console.log('🛑 Stopping RFID system...');
            const button = document.getElementById('stop-rfid-control-btn');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Stopping...';
            button.disabled = true;
            
            // Stop Python processes
            fetch('{{ route("employee.rfid.stop") }}')
                .then(response => response.json())
                .then(data => {
                    console.log('📊 RFID stop response:', data);
                    
                    if (data.success) {
                        console.log('✅ RFID system stopped successfully');
                        updateRfidStatus(false, data.message);
                    } else {
                        console.log('❌ Failed to stop RFID system:', data.message);
                        updateRfidStatus(true, data.message);
                    }
                })
                .catch(error => {
                    console.error('❌ Error stopping RFID system:', error);
                    updateRfidStatus(false, 'Error stopping RFID system');
                })
                .finally(() => {
                    // Restore button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        }
        
        function updateRfidStatus(isRunning, message) {
            const controlIndicator = document.getElementById('rfid-control-status-indicator');
            const controlStatusText = document.getElementById('rfid-control-status-text');
            
            if (isRunning) {
                controlIndicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
                controlIndicator.style.backgroundColor = '#059669';
                controlStatusText.textContent = 'System Online';
                controlStatusText.style.color = '#059669';
            } else {
                controlIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
                controlIndicator.style.backgroundColor = '#DC2626';
                controlStatusText.textContent = 'System Offline';
                controlStatusText.style.color = '#DC2626';
            }
        }


    </script>
</x-layout>

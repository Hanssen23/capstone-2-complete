<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>RFID Monitor</x-topbar>

        <div class="bg-gray-100 p-6">
            <!-- Real-time Status -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">RFID System Status</h2>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-green-600 font-medium">System Online</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Current Active</p>
                                    <p class="text-2xl font-bold text-blue-900" id="current-active-count">-</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-800">Today's Check-ins</p>
                                    <p class="text-2xl font-bold text-green-900" id="today-checkins">-</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-red-800">Failed Attempts</p>
                                    <p class="text-2xl font-bold text-red-900" id="failed-attempts">-</p>
                                </div>
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-orange-800">Unknown Cards</p>
                                    <p class="text-2xl font-bold text-orange-900" id="unknown-cards">-</p>
                                </div>
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Active Members -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Currently Active Members</h2>
                        <button onclick="refreshActiveMembers()" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                    
                    <div id="active-members-list" class="space-y-3">
                        <div class="text-center py-8 text-gray-500">
                            <p>Loading active members...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent RFID Activity -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Recent RFID Activity</h2>
                        <div class="flex items-center gap-4">
                            <select id="log-filter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Actions</option>
                                <option value="check_in">Check-ins</option>
                                <option value="check_out">Check-outs</option>
                                <option value="unknown_card">Unknown Cards</option>
                                <option value="expired_membership">Expired Memberships</option>
                            </select>
                            <button onclick="refreshLogs()" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                    
                    <div id="rfid-logs-list" class="space-y-3">
                        <div class="text-center py-8 text-gray-500">
                            <p>Loading RFID logs...</p>
                        </div>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div id="rfid-logs-pagination" class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            <span id="logs-info">Showing 0 to 0 of 0 results</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button id="prev-page" onclick="changePage(-1)" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                Previous
                            </button>
                            <span id="page-info" class="px-3 py-2 text-sm text-gray-700">Page 1</span>
                            <button id="next-page" onclick="changePage(1)" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RFID Test Panel -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">RFID Test Panel</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Test Card Tap</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Card UID</label>
                                        <input type="text" id="test-card-uid" placeholder="Enter card UID for testing" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Device ID</label>
                                        <input type="text" id="test-device-id" placeholder="main_reader" value="main_reader" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <button onclick="testCardTap()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Test Card Tap
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Test Results</h3>
                                <div id="test-results" class="bg-white rounded-lg p-4 border border-gray-200 min-h-[120px]">
                                    <p class="text-gray-500 text-sm">Test results will appear here...</p>
                                </div>
                            </div>
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
            loadDashboardStats();
            loadActiveMembers();
            loadRfidLogs();
            
            // Auto-refresh every 3 seconds for real-time updates
            setInterval(function() {
                loadDashboardStats();
                loadActiveMembers();
                loadRfidLogs(currentPage); // Maintain current page during auto-refresh
            }, 3000);
        });

        // Load dashboard statistics
        function loadDashboardStats() {
            fetch('{{ route("dashboard.stats") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('current-active-count').textContent = data.current_active_members;
                    document.getElementById('today-checkins').textContent = data.today_attendance;
                    document.getElementById('failed-attempts').textContent = data.failed_rfid_today;
                })
                .catch(error => console.error('Error loading dashboard stats:', error));
        }

        // Load active members
        function loadActiveMembers() {
            fetch('{{ route("rfid.active-members") }}')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('active-members-list');
                    
                    if (data.active_members.length === 0) {
                        container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No active members currently</p></div>';
                        return;
                    }
                    
                    container.innerHTML = data.active_members.map(member => `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">${member.name}</p>
                                    <p class="text-sm text-gray-500">${member.membership_plan} • Checked in at ${member.check_in_time}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">${member.session_duration}</p>
                                <p class="text-xs text-gray-500">Session duration</p>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading active members:', error);
                    document.getElementById('active-members-list').innerHTML = '<div class="text-center py-8 text-red-500"><p>Error loading active members</p></div>';
                });
        }

        // Load RFID logs
        function loadRfidLogs(page = 1) {
            const filter = document.getElementById('log-filter').value;
            const url = new URL('{{ route("rfid.logs") }}', window.location.origin);
            
            if (filter) {
                url.searchParams.set('action', filter);
            }
            if (page > 1) {
                url.searchParams.set('page', page);
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('rfid-logs-list');
                    
                    if (data.logs.data.length === 0) {
                        container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No RFID logs found</p></div>';
                        updatePaginationInfo(data.logs);
                        return;
                    }
                    
                    container.innerHTML = data.logs.data.map(log => `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 ${log.status === 'success' ? 'bg-green-100' : 'bg-red-100'} rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 ${log.status === 'success' ? 'text-green-600' : 'text-red-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">${log.action.replace('_', ' ').toUpperCase()}</p>
                                    <p class="text-sm text-gray-500">${log.message}</p>
                                    <p class="text-xs text-gray-400">Card: ${log.card_uid}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">${new Date(log.timestamp).toLocaleTimeString()}</p>
                                <p class="text-xs text-gray-400">${new Date(log.timestamp).toLocaleDateString()}</p>
                            </div>
                        </div>
                    `).join('');
                    
                    updatePaginationInfo(data.logs);
                })
                .catch(error => {
                    console.error('Error loading RFID logs:', error);
                    document.getElementById('rfid-logs-list').innerHTML = '<div class="text-center py-8 text-red-500"><p>Error loading RFID logs</p></div>';
                });
        }

        // Test card tap
        function testCardTap() {
            const cardUid = document.getElementById('test-card-uid').value.trim();
            const deviceId = document.getElementById('test-device-id').value.trim();
            
            if (!cardUid) {
                alert('Please enter a card UID');
                return;
            }
            
            const resultsContainer = document.getElementById('test-results');
            resultsContainer.innerHTML = '<p class="text-blue-500">Testing card tap...</p>';
            
            fetch('{{ route("rfid.tap") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    card_uid: cardUid,
                    device_id: deviceId || 'main_reader'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultsContainer.innerHTML = `
                        <div class="text-green-600">
                            <p class="font-medium">✓ ${data.message}</p>
                            <p class="text-sm mt-1">Action: ${data.action}</p>
                            <p class="text-sm">Member: ${data.member.name}</p>
                        </div>
                    `;
                } else {
                    resultsContainer.innerHTML = `
                        <div class="text-red-600">
                            <p class="font-medium">✗ ${data.message}</p>
                            <p class="text-sm mt-1">Action: ${data.action}</p>
                        </div>
                    `;
                }
                
                // Refresh data after test
                setTimeout(() => {
                    loadDashboardStats();
                    loadActiveMembers();
                    loadRfidLogs(currentPage); // Maintain current page
                }, 1000);
            })
            .catch(error => {
                console.error('Error testing card tap:', error);
                resultsContainer.innerHTML = '<p class="text-red-600">Error testing card tap</p>';
            });
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
    </script>
</x-layout>

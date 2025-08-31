<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Dashboard</x-topbar>

        <div class="bg-gray-100 p-6">
            <!-- Clean, Minimal Overview Section -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Overview</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Current Active Members -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-700 mb-1">Currently Active</p>
                                    <p id="current-active-members-count" class="text-3xl font-bold text-blue-900">{{ $currentActiveMembersCount }}</p>
                                    <p class="text-xs text-blue-600 mt-1">Members logged in</p>
                                </div>
                                <div class="w-16 h-16 bg-blue-200 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Today's Attendance -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-700 mb-1">Today's Attendance</p>
                                    <p id="today-attendance-count" class="text-3xl font-bold text-green-900">{{ $todayAttendance }}</p>
                                    <p class="text-xs text-green-600 mt-1">Check-ins today</p>
                                </div>
                                <div class="w-16 h-16 bg-green-200 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Monthly Revenue -->
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-yellow-700 mb-1">Monthly Revenue</p>
                                    <p class="text-3xl font-bold text-yellow-900">₱{{ number_format($thisMonthRevenue, 0) }}</p>
                                    <p class="text-xs text-yellow-600 mt-1">This month</p>
                                </div>
                                <div class="w-16 h-16 bg-yellow-200 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-bold text-yellow-700">₱</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RFID System Status -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">RFID System Status</h2>
                        <div class="flex items-center gap-2">
                            <div id="rfid-status-indicator" class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span id="rfid-status" class="text-sm font-medium text-gray-700">Stopped</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <button onclick="startRfidReader()" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Start RFID Reader
                        </button>
                        <button onclick="stopRfidReader()" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10l2 2 4-4"></path>
                            </svg>
                            Stop RFID Reader
                        </button>
                    </div>
                </div>
            </div>

            <!-- Analytics & Reports Section -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Analytics & Reports</h2>
                    
                    <!-- Performance Metrics Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                    <p class="text-sm font-medium text-gray-600">Total Members</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $totalActiveMembersCount }}</p>
                                    </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">This Week</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $thisWeekAttendance }}</p>
                                    <p class="text-xs text-gray-500">Check-ins</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                        </div>
                    </div>
                </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Pending</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $pendingPaymentsCount }}</p>
                                    <p class="text-xs text-gray-500">Payments</p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                        <div>
                                    <p class="text-sm font-medium text-gray-600">Expiring</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $expiringMembershipsCount }}</p>
                                    <p class="text-xs text-gray-500">This week (Members: {{ $expiringMembershipsCount }}, Payments: {{ $expiringPaymentsCount }})</p>
                                    </div>
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Weekly Attendance Chart -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Weekly Attendance Trend</h3>
                            <div class="h-64">
                                <canvas id="weeklyAttendanceChart"></canvas>
                    </div>
                </div>

                        <!-- Monthly Revenue Chart -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue Trend</h3>
                            <div class="h-64">
                                <canvas id="monthlyRevenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Initialize charts when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            
            // Auto-refresh dashboard stats every 30 seconds
            setInterval(function() {
                fetch('{{ route("dashboard.stats") }}')
                    .then(response => response.json())
                    .then(data => {
                        // Update current active members count
                        const currentActiveElement = document.getElementById('current-active-members-count');
                        if (currentActiveElement) {
                            currentActiveElement.textContent = data.current_active_members;
                        }
                        
                        // Update today's attendance
                        const todayAttendanceElement = document.getElementById('today-attendance-count');
                        if (todayAttendanceElement) {
                            todayAttendanceElement.textContent = data.today_attendance;
                        }
                    })
                    .catch(error => console.error('Error updating stats:', error));
            }, 30000);
        });

        function initializeCharts() {
            // Weekly Attendance Chart
            const weeklyCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
            new Chart(weeklyCtx, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Attendance',
                        data: [12, 19, 15, 25, 22, 30, 28],
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Attendance: ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });

            // Monthly Revenue Chart
            const revenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: [15000, 18000, 22000, 19000, 25000, 28000],
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: ₱' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function startRfidReader() {
            // RFID start functionality
            console.log('Starting RFID reader...');
        }

        function stopRfidReader() {
            // RFID stop functionality
            console.log('Stopping RFID reader...');
        }
    </script>
</x-layout>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Silencio System</title>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        
        <!-- Dropdown Styles -->
        <link rel="stylesheet" href="{{ asset('css/dropdown.css') }}">
        <!-- Sidebar Styles -->
        <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    </head>
    <body class="m-0 p-0 h-screen w-screen">
        <main class="h-full w-full flex">
            {{ $slot }}
        </main>
        
        <!-- Dropdown JavaScript -->
        <script src="{{ asset('js/dropdown.js') }}"></script>
        <!-- Sidebar JavaScript -->
        <script src="{{ asset('js/sidebar.js') }}"></script>
        
        <!-- RFID Auto-Start Script -->
        <script>
            // RFID Auto-Start functionality
            let rfidProcess = null;
            let rfidStatus = localStorage.getItem('rfidStatus') || 'stopped';
            
            // Initialize status on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateRfidStatus(rfidStatus);
                if (rfidStatus === 'running') {
                    // Re-establish connection if it was running
                    startRfidReader();
                }
            });
            
            // Function to start RFID reader
            async function startRfidReader() {
                if (rfidStatus === 'running') {
                    console.log('RFID reader already running');
                    return;
                }
                
                try {
                    console.log('🚀 Starting RFID reader...');
                    rfidStatus = 'starting';
                    
                    // Make API call to start RFID reader
                    const response = await fetch('/rfid/start', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                    
                    if (response.ok) {
                        rfidStatus = 'running';
                        localStorage.setItem('rfidStatus', 'running');
                        console.log('✅ RFID reader started successfully');
                        updateRfidStatus('running');
                    } else {
                        throw new Error('Failed to start RFID reader');
                    }
                } catch (error) {
                    console.error('❌ Error starting RFID reader:', error);
                    rfidStatus = 'stopped';
                    updateRfidStatus('error');
                    
                    // Fallback: Show manual start instructions
                    showManualStartInstructions();
                }
            }
            
            // Function to stop RFID reader
            async function stopRfidReader() {
                if (rfidStatus === 'stopped') {
                    console.log('RFID reader already stopped');
                    return;
                }
                
                try {
                    console.log('🛑 Stopping RFID reader...');
                    
                    const response = await fetch('/rfid/stop', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                    
                    if (response.ok) {
                        rfidStatus = 'stopped';
                        localStorage.setItem('rfidStatus', 'stopped');
                        console.log('✅ RFID reader stopped successfully');
                        updateRfidStatus('stopped');
                    }
                } catch (error) {
                    console.error('❌ Error stopping RFID reader:', error);
                }
            }
            
            // Function to update RFID status display
            function updateRfidStatus(status) {
                const statusElement = document.getElementById('rfid-status');
                const indicatorElement = document.getElementById('rfid-status-indicator');
                
                if (statusElement && indicatorElement) {
                    // Update text
                    statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    
                    // Update indicator color
                    if (status === 'running') {
                        indicatorElement.className = 'w-3 h-3 rounded-full bg-green-500 animate-pulse';
                    } else if (status === 'error') {
                        indicatorElement.className = 'w-3 h-3 rounded-full bg-yellow-500';
                    } else {
                        indicatorElement.className = 'w-3 h-3 rounded-full bg-red-500';
                    }
                }
            }
            
            // Function to show manual start instructions
            function showManualStartInstructions() {
                const instructions = `
                    <div class="fixed top-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded z-50">
                        <strong>RFID Reader Not Started</strong><br>
                        Please run: <code>start_rfid_system.bat</code> in the project directory
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', instructions);
            }
            
            // Auto-start RFID reader when page loads (if user is authenticated)
            document.addEventListener('DOMContentLoaded', function() {
                // Check if user is authenticated (you can modify this condition)
                const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
                
                if (isAuthenticated) {
                    console.log('👤 User authenticated, starting RFID reader...');
                    // Delay start to ensure page is fully loaded
                    setTimeout(startRfidReader, 2000);
                }
            });
            
            // Stop RFID reader when page is unloaded
            window.addEventListener('beforeunload', function() {
                if (rfidStatus === 'running') {
                    stopRfidReader();
                }
            });
        </script>
    </body>
</html>

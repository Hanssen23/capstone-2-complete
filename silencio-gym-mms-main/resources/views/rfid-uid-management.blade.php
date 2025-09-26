<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>RFID UID Management</x-topbar>

        <div class="bg-gray-100 p-6">
            <!-- UID Mapping Instructions -->
            <div class="mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">RFID UID Mapping Instructions</h3>
                            <p class="text-sm text-gray-600">Map physical RFID card UIDs to member records</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 border border-blue-100">
                        <h4 class="font-medium text-gray-900 mb-3">How to Map Physical Cards:</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                            <li>Tap each physical RFID card on the reader</li>
                            <li>Note the UID that appears in the logs (e.g., E6415F5F)</li>
                            <li>Use the form below to assign that UID to the correct member</li>
                            <li>Test the card again to verify it works</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Current Members with UIDs -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Current Member UIDs</h2>
                        <button onclick="refreshMemberList()" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                    
                    <div id="members-list" class="space-y-3">
                        <div class="text-center py-8 text-gray-500">
                            <p>Loading members...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UID Update Form -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Update Member UID</h2>
                    
                    <form id="uid-update-form" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Member</label>
                                <select id="member-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Choose a member...</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New RFID Card UID</label>
                                <input type="text" id="new-uid" placeholder="e.g., E6415F5F" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase" required>
                                <p class="text-xs text-gray-500 mt-1">Enter the UID from your physical RFID card</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Update UID
                            </button>
                            <button type="button" onclick="clearForm()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent UID Changes -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent UID Changes</h2>
                    
                    <div id="uid-changes-list" class="space-y-3">
                        <div class="text-center py-8 text-gray-500">
                            <p>No recent changes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let members = [];
        let uidChanges = [];

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadMembers();
            loadUidChanges();
        });

        // Load members
        function loadMembers() {
            fetch('/members')
                .then(response => response.text())
                .then(html => {
                    // Parse the members from the HTML response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const memberRows = doc.querySelectorAll('tbody tr');
                    
                    members = Array.from(memberRows).map(row => {
                        const cells = row.querySelectorAll('td');
                        return {
                            id: row.dataset.memberId || '',
                            uid: cells[0]?.textContent?.trim() || '',
                            member_number: cells[1]?.textContent?.trim() || '',
                            name: cells[3]?.textContent?.trim() || '',
                            membership: cells[2]?.textContent?.trim() || ''
                        };
                    });
                    
                    updateMembersList();
                    updateMemberSelect();
                })
                .catch(error => {
                    console.error('Error loading members:', error);
                    document.getElementById('members-list').innerHTML = '<div class="text-center py-8 text-red-500"><p>Error loading members</p></div>';
                });
        }

        // Update members list display
        function updateMembersList() {
            const container = document.getElementById('members-list');
            
            if (members.length === 0) {
                container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No members found</p></div>';
                return;
            }
            
            container.innerHTML = members.map(member => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${member.name}</p>
                            <p class="text-sm text-gray-500">${member.member_number} • ${member.membership}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">${member.uid}</p>
                        <p class="text-xs text-gray-500">Current UID</p>
                    </div>
                </div>
            `).join('');
        }

        // Update member select dropdown
        function updateMemberSelect() {
            const select = document.getElementById('member-select');
            select.innerHTML = '<option value="">Choose a member...</option>' +
                members.map(member => `
                    <option value="${member.id}" data-current-uid="${member.uid}">
                        ${member.name} (${member.uid})
                    </option>
                `).join('');
        }

        // Handle form submission
        document.getElementById('uid-update-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const memberId = document.getElementById('member-select').value;
            const newUid = document.getElementById('new-uid').value.toUpperCase().trim();
            
            if (!memberId || !newUid) {
                alert('Please select a member and enter a new UID');
                return;
            }
            
            const member = members.find(m => m.id === memberId);
            if (!member) {
                alert('Member not found');
                return;
            }
            
            if (member.uid === newUid) {
                alert('The new UID is the same as the current UID');
                return;
            }
            
            // Check if UID already exists
            const existingMember = members.find(m => m.uid === newUid && m.id !== memberId);
            if (existingMember) {
                alert(`UID ${newUid} is already assigned to ${existingMember.name}`);
                return;
            }
            
            if (confirm(`Update ${member.name}'s UID from ${member.uid} to ${newUid}?`)) {
                updateMemberUid(memberId, newUid, member);
            }
        });

        // Update member UID
        function updateMemberUid(memberId, newUid, member) {
            fetch(`/members/${memberId}/uid`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    uid: newUid
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update local data
                    const memberIndex = members.findIndex(m => m.id === memberId);
                    if (memberIndex !== -1) {
                        members[memberIndex].uid = newUid;
                    }
                    
                    // Add to changes log
                    uidChanges.unshift({
                        member: member.name,
                        oldUid: member.uid,
                        newUid: newUid,
                        timestamp: new Date().toLocaleString()
                    });
                    
                    // Update displays
                    updateMembersList();
                    updateMemberSelect();
                    updateUidChangesList();
                    
                    // Clear form
                    clearForm();
                    
                    alert('UID updated successfully!');
                } else {
                    alert('Error updating UID: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error updating UID:', error);
                alert('Error updating UID');
            });
        }

        // Update UID changes list
        function updateUidChangesList() {
            const container = document.getElementById('uid-changes-list');
            
            if (uidChanges.length === 0) {
                container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No recent changes</p></div>';
                return;
            }
            
            container.innerHTML = uidChanges.map(change => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${change.member}</p>
                            <p class="text-sm text-gray-500">${change.oldUid} → ${change.newUid}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">${change.timestamp}</p>
                    </div>
                </div>
            `).join('');
        }

        // Clear form
        function clearForm() {
            document.getElementById('member-select').value = '';
            document.getElementById('new-uid').value = '';
        }

        // Refresh member list
        function refreshMemberList() {
            loadMembers();
        }

        // Auto-refresh every 30 seconds
        setInterval(() => {
            loadMembers();
        }, 30000);
    </script>
</x-layout>

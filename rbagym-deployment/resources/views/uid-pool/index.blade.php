@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">UID Pool Management</h1>
            <div class="flex gap-4">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    Available: {{ $availableCount }}
                </span>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    Assigned: {{ $assignedCount }}
                </span>
                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                    Total: {{ $totalCount }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Available UIDs -->
            <div class="bg-green-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-green-800 mb-3">Available UIDs</h3>
                @if($availableUids->count() > 0)
                    <div class="space-y-2">
                        @foreach($availableUids as $uid)
                            <div class="flex justify-between items-center bg-white p-2 rounded border">
                                <span class="font-mono text-sm">{{ $uid->uid }}</span>
                                <span class="text-xs text-gray-500">
                                    Added: {{ $uid->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-green-600 text-sm">No available UIDs</p>
                @endif
            </div>

            <!-- Assigned UIDs -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">Assigned UIDs</h3>
                @if($assignedUids->count() > 0)
                    <div class="space-y-2">
                        @foreach($assignedUids as $uid)
                            <div class="flex justify-between items-center bg-white p-2 rounded border">
                                <div>
                                    <span class="font-mono text-sm">{{ $uid->uid }}</span>
                                    <p class="text-xs text-gray-500">
                                        Assigned: {{ $uid->assigned_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-blue-600 text-sm">No assigned UIDs</p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-4">
            <a href="{{ route('uid-pool.refresh') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                Refresh Pool
            </a>
            <a href="{{ route('members.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                Back to Members
            </a>
        </div>
    </div>
</div>
@endsection

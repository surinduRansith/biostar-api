<div class="container mx-auto p-6 bg-gray-100 min-h-screen">
    @if (empty(($events['EventCollection']['rows'])))
        <div class="text-center text-xl font-semibold text-gray-700">
            <p>Data Not Available</p>
        </div>
    @else
        <div wire:poll.1s="eventData" class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Event Details</h2>

            <!-- Two-column layout: User Details Form + Image -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- User Details Form -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    @foreach($events['EventCollection']['rows'] as $event)
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold">Date & Time:</label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($event['datetime'])->setTimezone('Asia/Kolkata')->format('d-M-Y h:i A') }}" 
                                class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold">User ID:</label>
                            <input type="text" value="{{ $event['user_id']['user_id'] ?? 'N/A' }}" 
                                class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100 text-blue-600 font-bold" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold">User Name:</label>
                            <input type="text" value="{{ $event['user_id_name'] ?? 'N/A' }}" 
                                class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold">Device ID:</label>
                            <input type="text" value="{{ $event['device_id']['name'] ?? 'N/A' }}" 
                                class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold">Event Code:</label>
                            <input type="text" value="{{ collect($eventNames)->firstWhere('code', $event['event_type_id']['code'])['name'] ?? 'N/A' }}" 
                                class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold">Group:</label>
                            <input type="text" value="{{ $event['user_group_id']['name'] ?? 'N/A' }}" 
                                class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                    @endforeach
                </div>

                <!-- User Image Display -->
                <div class="flex justify-center items-center">
                    <img src="{{ $image ? asset('storage/' . $image) : asset('storage/21666259.jpg') }}"
                        alt="User Image"
                        class="rounded-lg shadow-md w-96 h-96 border-2 border-gray-300 object-cover">
                </div>
                

            </div>
        </div>
    @endif
</div>

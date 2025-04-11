<div >
    <!-- Success and Error Messages -->
    @if (session('message'))
        <div class="alert alert-success w-60 mb-3" wire:poll.1s>
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error w-60 mb-3" wire:poll.1s>
            {{ session('error') }}
        </div>
    @endif

    <!-- User Search Section -->
    <div class="relative mb-6">
        <div class="flex items-center bg-white p-3 rounded-lg shadow-md space-x-2">
            <input type="text"
                class="input input-bordered w-full input-sm focus:ring focus:ring-blue-300"
                placeholder="Search User..." 
                wire:model.live.debounce.300ms="userSearch"
                wire:keydown.escape="resetSearch"
            />
           
        </div>

        <!-- Dropdown Search Results -->
        @if (!empty($userSearch) && !empty($userlists))
            <div class="absolute left-0 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10">
                @foreach ($userlists as $i => $userlist)
                    <div class="px-4 py-2 cursor-pointer border-b hover:bg-blue-500 hover:text-white transition-all duration-200">
                        <button class="w-full text-left font-medium" wire:click="setUserid('{{ $userlist['user_id'] }}', '{{ $userlist['name'] }}')">
                            {{ $userlist['user_id'] . ' - ' . ($userlist['name'] ?? 'No Name') }}
                        </button>
                    </div>
                @endforeach
            </div>
        @elseif (!empty($userSearch))
            <div class="absolute left-0 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10">
                <div class="px-4 py-2 text-gray-500 text-center">No results found!</div>
            </div>
        @endif
    </div>

    <!-- Users in Shift Table -->
    <div>
        @if (!empty($setUserIDs))
         
        @foreach ($setUserIDs as $index => $setUserID)
        <div class="flex items-center bg-white rounded-lg shadow-md pt-2 space-x-2">
            <button class="btn btn-sm btn-accent flex items-center px-2 space-x-2" wire:click="removeUserid('{{ $index }}')">
                <input type="text"
                    class="input w-32 input-xs bg-transparent border-none focus:ring-0 text-white cursor-default "
                    value="{{ $setUserID['userid'] . ' - ' . $setUserID['name'] }}"
                    readonly
                />
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
            
        @endforeach
        <div class="flex items-center bg-white rounded-lg shadow-md pt-2 space-x-2">
            <button class="btn btn-sm btn-primary px-4" wire:click="AddusersShfit">
                Add users<span class="loading loading-dots loading-xs"wire:loading></span>
            </button>
        </div>
        @endif
        
       
    </div>
    <div class="overflow-x-auto bg-white rounded-lg shadow-lg pt-2">
        <table class="w-full border-collapse">
            <thead class="bg-blue-500 text-white text-left">
                <tr>
                    <th class="px-4 py-2">User ID</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Shift</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($addusersinshifts as $index => $addusersinshift)
                    <tr class="hover:bg-blue-100 transition-all duration-200">
                        <td class="px-4 py-2 text-gray-700">{{ $addusersinshift['user_id'] }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $addusersinshift['name'] }}</td>
                        <td class="px-4 py-2 font-semibold text-blue-600">{{ $addusersinshift['Shiftname'] }}</td>
                        <td>
                            <button class="btn btn-error btn-sm" 
                                wire:click="removeusershift('{{ $addusersinshift['user_id'] }}', {{ $shiftId }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span class="loading loading-dots loading-xs" wire:loading></span>
                            </button>
                        </td>
                        
                    </tr>
                   
                @endforeach
            </tbody>
        </table>
    </div>
</div>

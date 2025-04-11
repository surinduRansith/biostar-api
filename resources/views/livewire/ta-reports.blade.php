<div >
    <div >
        
        <!-- Success/Error Message -->
        @if (session('message'))
            <div class="alert bg-red-500 text-white text-sm p-3 rounded text-center mb-4" wire:poll.3s>
                <span>{{ session('message') }}</span>
            </div>
        @endif
  
        <!-- User Search Section -->
        <div class="relative mb-4">
            <div class="flex items-center bg-white p-3 rounded-lg shadow">
                <input type="text"
                    class="input input-bordered w-full input-sm"
                    placeholder="Search User..." 
                    wire:model.live.debounce.300ms="userSearch"
                    wire:keydown.escape="resetSearch"
                    />
                    
                <span class="ml-3 text-red-500 font-semibold">Selected ID: {{ $setUserID }}</span>
            </div>
  
            <!-- Dropdown Search Results -->
            @if (!empty($userSearch) && !empty($userlists))
            
                <div class="absolute left-0 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10">
                    @foreach ($userlists as $i => $userlist)
                  
                        <div class="px-4 py-2 cursor-pointer border-b hover:bg-blue-400">
                          <button class="w-full text-left" wire:click="setUserid('{{ $userlist['user_id'] }}')">
                            {{ $userlist['user_id'] . ' - ' . ($userlist['name'] ?? 'No Name') }}
                        </button>
                        
                        </div>
                    @endforeach
                </div>
            @elseif (!empty($userSearch))
                <div class="absolute left-0 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10">
                    <div class="px-4 py-2 text-gray-500">No results found!</div>
                </div>
            @endif
        </div>
  
        <!-- Date Filters -->
        <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow">
            <label class="text-gray-700 font-semibold flex items-center space-x-2">
                <span>Start Date:</span>
                <input type="datetime-local" class="w-full p-2 border border-black rounded-xl bg-red-200"
                    wire:model.live="StartDate">
            </label>
  
            <label class="text-gray-700 font-semibold flex items-center space-x-2">
                <span>End Date:</span>
                <input type="datetime-local" class="w-full p-2 border border-black rounded-xl bg-blue-200"
                    wire:model.live="EndDate">
            </label>
            
            <button wire:click="eventData" class="btn btn-primary btn-sm">Show Report<span class="loading loading-dots loading-xs"wire:loading></span></button>
        </div>
  
        <!-- Event Table -->
        <div class="mt-4">
            @if (count($firstinandlast) == 0)
                <div class="alert bg-yellow-500 text-white text-sm p-2 rounded w-full flex justify-center items-center">
                    <span class="item-center">No Data Found</span>
                </div>
            @else
                @php $count = 0; @endphp
                <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">User ID</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">First IN Time</th>
                            <th class="p-3">Last Out Time</th>
                            <th class="p-3">Result</th>
                            
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($firstinandlast as $index => $firstandlast)
                            @php $count++; @endphp
                            <tr class="{{ $count % 2 == 0 ? 'bg-gray-100' : 'bg-white' }}">
                                <td class="p-3">{{ $count }}</td>
                                <td class="p-3">{{  $firstandlast['userid'] }}</td>
                                <td class="p-3">{{ $firstandlast['date'] }}</td>
                                <td class="p-3">{{ $firstandlast['minTime'] }}</td>
                                <td class="p-3">{{ $firstandlast['maxTime'] }}</td>
                                <td class="p-3">{{ $firstandlast['eventresult'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
   
    </div>
  </div>
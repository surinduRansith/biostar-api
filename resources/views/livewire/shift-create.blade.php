<div>

        <div class="py-4 flex justify-center">
            <input type="text" wire:model="shiftName" placeholder="Enter Shift name"
                class="input input-bordered input-sm w-full max-w-xs rounded-md p-3 border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                
        </div>
        <div class="pt-2 flex justify-center" >
            @error('shiftName') <span class="text-red-500" >{{ $message }}</span> @enderror
        </div>
        
        <div class="py-4 flex justify-center space-x-4">
           <span>IN Time : </span> <input type="time" wire:model="inTime" placeholder="Enter IN Time"
                class="input w-30 p-2 border input-sm  border-black rounded-xl bg-red-200" />
                <span>OUT Time : </span>  <input type="time" wire:model="outTime" placeholder="Enter OUT Time"
                class="input input-sm w-30 p-2 border border-black rounded-xl bg-red-200" />
        </div>
        <div class="pt-2 flex justify-center gap-2" >
            @error('inTime') <span class="text-red-500">{{ $message }}</span> @enderror
            @error('outTime') <span class="text-red-500">{{ $message }}</span> @enderror

        </div>
      
        <div class="py-4 flex justify-center">
           
            <button class="btn btn-primary" wire:click="createShift">Create Shift</button>
            
        </div>

@if (!empty($shiftlists))

<table class="w-full   rounded-lg " >
    <thead class="bg-gray-200 text-gray-700">
        <tr>
            <th>Shift Name</th>
            <th>IN Time</th>
            <th>OUT Time</th>
            <th></th>
        </tr>
    </thead>
    <tbody class="text-center" >
        
        @foreach ($shiftlists as $shift)
        <tr>
            <td><a href="{{route('useraddshift',['shiftId' => $shift['id'] ])}}">{{ $shift['Shiftname'] }}</a></td>
            <td>{{ $shift['start_time'] }}</td>
            <td>{{ $shift['end_time']}}</td>
            <td>
                <a href="{{route('shiftedit', ['shiftId' => $shift['id'] ])}}" class="btn btn-primary btn-sm">Edit</a>
                <button class="btn btn-danger btn-sm" wire:click="deleteShift({{ $shift['id']}})">Delete</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
    
@endif
  
</div>

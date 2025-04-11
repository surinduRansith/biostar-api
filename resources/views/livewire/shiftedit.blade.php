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
        
        <button class="btn btn-warning" wire:click="updateShift">Update Shift</button>
        
    </div>
</div>

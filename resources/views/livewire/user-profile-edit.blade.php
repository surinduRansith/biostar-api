<div>
    <div>
        {{-- <div class="py-4">
        <form wire:submit.prevent="userGroupCreate">
            <input type="text" wire:model="userGroupName" placeholder="User Group Name" class="input input-bordered input-sm w-full max-w-xs rounded" />
            <button type="submit" class="btn btn-primary btn-sm ">Add</button>  
        </form>
    </div> --}}




        <div class="py-6 flex justify-center">
            
            <div class="w-full max-w-xl bg-white p-6 rounded-lg shadow-md border border-gray-200">

               @if (isset($profileImage))
               <div class=" flex justify-center">

                <img src="data:image/bmp;base64,{{ $profileImage }}" alt="Fingerprint Image"
                    class="max-w-full max-h-full object-contain">
            </div> 
               @endif
                @if (session('error'))
                    <div class="alert alert-error w-full text-center bg-red-500 text-white p-4 rounded-md" wire:poll>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="py-4 flex justify-center">
                    <input type="text" wire:model="user_id" placeholder="User ID"
                        class="input input-bordered input-sm w-full max-w-xs rounded-md p-3 border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    {{-- @error('userID')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror --}}
                </div>

                

                <div class="py-4 flex justify-center">
                    <input type="text" wire:model="userName" placeholder="User Name"
                        class="input input-bordered input-sm w-full max-w-xs rounded-md p-3 border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <div class="py-4 flex justify-center">

                    <label class="flex items-center gap-2 bg-white w-full max-w-xs text-sm">
                        <span class="text-gray-700 whitespace-nowrap">User Group:</span>
                        <select class="select select-sm select-bordered w-full bg-white" wire:model.live="userGroupId">
                            @if (!empty($userGroups))
                                @for ($i = 0; $i < $userGroups['total']; $i++)
                                    <option value="{{ $userGroups['rows'][$i]['id'] }}">
                                        {{ $userGroups['rows'][$i]['name'] }}
                                    </option>
                                @endfor
                            @endif
                        </select>
                    </label>






                </div>

                <div class="py-4 flex justify-center">
                    {{-- <input type="file" class="file-input w-full max-w-xs rounded-md p-3 border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" wire:model="image" accept="image/png, image/jpeg" id="image" /> --}}
                    <div>
                        <label class="flex items-center gap-2 bg-white w-full max-w-xs text-sm">
                            <span class="text-gray-700 whitespace-nowrap">Upload Image:</span>
                            <input type="file" wire:model="image" accept="image/png, image/jpeg, image/jpg"
                                class="file-input file-input-xs ">
                        </label>


                        @if ($message)
                            <div class="mt-4 p-2 rounded {{ $success ? 'bg-green-200' : 'bg-red-200' }}">
                                {{ $message }}
                            </div>
                        @endif

                        @if ($image)
                            <div class="mt-4 flex justify-center">
                                <img src="{{ $image->temporaryUrl() }}" class="w-32 h-32 object-cover rounded shadow">
                            </div>
                            <div class="mt-4 flex justify-center">
                                <button wire:click="scanVisualFace({{ $user_id }})"
                                    class="btn btn-accent btn-xs flex ">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                    </svg>

                                    Please Image Confirm
                                    <span class="loading loading-spinner text-primary" wire:loading></span>
                                </button>
                            </div>
                        @endif

                        @if ($visualfaceadd == true)
                            <div class="mt-4 flex justify-center">
                                <label class="flex items-center gap-2 bg-white w-full max-w-xs text-sm">
                                    <span class="text-gray-700 whitespace-nowrap">Add Visual Face:</span>

                                    <input type="checkbox" wire:model.live="visualfaceselected" value="1"
                                        class="checkbox" />
                                </label>
                                <label class="flex items-center gap-2 bg-white w-full max-w-xs text-sm">
                                    <span class="text-gray-700 whitespace-nowrap">Add Profile Picture:</span>
                                   
                                    <input type="checkbox" wire:model.live="profilePicselected" value="2" class="checkbox" />
                                </label>
                            </div>
                        @endif
                    </div>

                </div>
                @if (session('deviceselected'))
                    <div class="text-red-500 py-4 flex justify-center" wire:poll>
                        <span>{{ session('deviceselected') }}</span>
                    </div>
                @endif
                <div class="py-4 flex justify-center">
                    <label class="flex items-center gap-2 bg-white w-full max-w-xs text-sm">
                        <span class="text-gray-700 whitespace-nowrap">Select Device:</span>
                        <select wire:model.live="selectDevice" class="select select-sm select-bordered w-full bg-white">
                            <option value="" selected>None</option>
                            @foreach ($devicelist as $index => $device)
                                <optgroup label="[{{ $device['devicetype'] }}]">
                                    <option value="{{ $device['device_id'] }}">
                                        {{ $device['name'] }}
                                    </option>
                                </optgroup>
                            @endforeach
                        </select>
                    </label>

                </div>
                <div class="py-4 flex justify-center">
                    <label class="flex items-center gap-2 bg-white w-full max-w-xs text-sm">
                        <span class="text-gray-700 whitespace-nowrap">Finger Quality:</span>
                        <select wire:model.live="selectFingerQuality"
                            class="select select-sm select-bordered w-full bg-white">
                            @foreach ($fingerQualitys as $quality)
                                <option value="{{ $quality }}" @selected($quality == 80)>
                                    {{ $quality }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>



                <div class="py-4">
                    @if (!empty($scanfinger))


                        <div class="flex gap-4 justify-center">
                            @foreach ($scanfinger as $index => $fingerArray)
                                @if ($index == 0 || $index == 1)
                                    <div
                                        class="w-40 h-40 border-2 border-gray-300 flex justify-center items-center shadow-md bg-white">
                                        @if (empty($fingerArray['temp1image']))
                                            <span class="text-gray-500">No Image</span>
                                        @else
                                            <img src="data:image/bmp;base64,{{ $fingerArray['temp1image'] }}"
                                                alt="Fingerprint Image" class="max-w-full max-h-full object-contain">
                                        @endif
                                    </div>

                                    <button wire:click="deleteFinger({{ $index }})"><svg
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="red" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                @endif
                            @endforeach



                        </div>
                    @else
                        <div class="flex justify-center">
                            <div class="alert alert-info bg-blue-500 text-white p-2 rounded-md text-sm w-fit mx-auto">
                                <span>Please Scan Your Fingerprint</span>
                            </div>


                        </div>


                    @endif
                </div>

                <div class="flex justify-center">

                    @if ($cardnumber != 0)
                        <div class="alert alert-info bg-blue-500 text-white p-2 rounded-md text-sm w-fit mx-auto">
                            <span>Your Card Number: {{ $cardnumber }}</span>
                        </div>
                    @endif
                </div>

                <div class="py-4 flex justify-between gap-4">
                    <button wire:click="addCard({{ $user_id }})"
                        class="btn btn-accent flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 text-white rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 ease-in-out">
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19.7714 7.19922L19.2173 5.13111C18.8956 3.93081 17.6619 3.2185 16.4616 3.54012L3.90454 6.90476C2.70425 7.22638 1.99193 8.46014 2.31355 9.66044L3.74609 15.0068V10.4492C3.74609 8.65429 5.20117 7.19922 6.99609 7.19922H19.7714Z"
                                fill="#323544" />
                            <path
                                d="M22.0736 9.58365C22.1847 9.8501 22.2461 10.1425 22.2461 10.4492C22.2461 10.4489 22.2461 10.4495 22.2461 10.4492L22.2465 11.4434H4.74609V10.4492C4.74609 9.20658 5.75345 8.19922 6.99609 8.19922H19.9961C20.0106 8.19922 20.025 8.19936 20.0395 8.19963C20.9378 8.21656 21.7075 8.76076 22.0526 9.53495C22.0598 9.55108 22.0668 9.56732 22.0736 9.58365Z"
                                fill="#323544" />
                            <path
                                d="M22.2465 14.2407H4.74609V18.5159C4.74621 18.5162 4.74598 18.5157 4.74609 18.5159L4.74645 19.1257C4.74645 19.4364 4.80941 19.7323 4.92327 20.0015C4.92845 20.0138 4.93375 20.026 4.93914 20.0382C5.28934 20.8257 6.07859 21.3749 6.99609 21.3749C6.99621 21.3749 6.99598 21.3749 6.99609 21.3749L19.9965 21.3757C21.2391 21.3757 22.2465 20.3684 22.2465 19.1257V14.2407Z"
                                fill="#323544" />
                        </svg>
                        + Card
                        <span class="loading loading-spinner text-primary" wire:loading></span>
                    </button>



                    <button
                        class="btn btn-accent flex items-center justify-center gap-2 px-5 py-3 bg-green-600 text-white rounded-lg shadow-lg hover:bg-green-700 transition duration-300 ease-in-out"
                        wire:click="scan">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                        </svg>
                        Finger
                        <span class="loading loading-spinner text-primary" wire:loading></span>
                    </button>


                </div>

                <div class="py-6 flex justify-center gap-4">
                    <button wire:click="createUser"
                        class="btn btn-accent flex items-center justify-center gap-2 px-5 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition duration-300 ease-in-out">
                        Apply
                    </button>

                    <button wire:click="cancel"
                        class="btn btn-accent flex items-center justify-center gap-2 px-5 py-3 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600 transition duration-300 ease-in-out">
                        Cancel
                    </button>
                </div>

            </div>
        </div>


        <div>


        </div>




    </div>

</div>

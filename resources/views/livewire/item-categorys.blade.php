<div>
    @if (session('success'))
    <p class="text-green-600">{{ session('success') }}</p>
  
    @elseif (session('error'))
    <p class="text-red-600">{{ session('error') }}</p>
  @endif
  

<a href="{{ route('userregistration') }}" class="btn btn-primary btn-sm">Create New</a>





<div>
    
</div>
   <table class="table">
      <thead>
          <tr>
              <th>User ID</th>
              <th>Name</th>
              <th>Group</th>
              <th>Start Date</th>
              <th>Expiry Date</th>
          </tr>
      </thead>
      <tbody>
          @foreach ($apikey as $index => $user)
              @if(isset($user['rows'])) 
                  @foreach ($user['rows'] as $row)
                      <tr>
                        <td><a href="{{route('userprofileedit', ['user_id' => $row['user_id'] ])}}">  {{ $row['user_id'] }}</a></td>
                          <td><a href="{{route('userprofileedit', ['user_id' => $row['user_id'] ])}}">{{ $row['name']?? '-' }}</a></td>
                          <td>{{ $row['user_group_id']['name'] ?? 'N/A' }}</td>
                          <td>{{ $row['start_datetime'] }}</td>
                          <td>{{ $row['expiry_datetime'] }}</td>
                          <td><button wire:click="deleteUser({{ $row['user_id'] }})" class="btn btn-error">Delete</button></td>
                          
                      </tr>
                  @endforeach
              @endif
          @endforeach
      </tbody>
  </table>
  




   
</div>

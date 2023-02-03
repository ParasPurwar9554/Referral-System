<h1>Login</h1>

<form action="{{ route('login') }}" method="POST">
@csrf
 <input type="email" name="email" placeholder="Enter email">
   @error('email')
   <span style="color:red">{{ $message }}</span>
   @enderror
  <br/><br/>
  <input type="password" name="password" placeholder="Enter password">
   @error('password')
   <span style="color:red">{{ $message }}</span>
   @enderror
   <br/><br/>
    <input type="submit" value="Login">
</form>

@if(Session::has('error'))
 <p style="color:red">{{ Session::get('error')}}</p>
@endif
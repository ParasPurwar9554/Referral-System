<div style="text-align: center;width:100%">

<h1>Register</h1>

<form action="{{ route('registered') }}" method="POST">
    @csrf
    <input type="text" name="name" placeholder="Enter Name">
    @error('name')
      <span style="color:red">{{ $message }}</span>
    @enderror
    <br/><br/>
    <input type="email" name="email" placeholder="Enter Email">
    @error('email')
      <span style="color:red">{{ $message }}</span>
    @enderror
    <br/><br/>
    <input type="text" name="referral_code"  placeholder="Enter Referral Code (Optional)">
    <br/><br/>
    <input type="password" name="password" placeholder="Enter Password">
    @error('password')
      <span style="color:red">{{ $message }}</span>
    @enderror
    <br/><br/>
    <input type="password" name="confirm_password" placeholder="Enter Confirm Password">
    <br/><br/>
    <input type="submit" value="Register">
</form>
<a href="/login"> Login</a>&nbsp;
<a href="/register"> Register</a>
</div>

@if(Session::has('success'))
 <p style="color:green">{{ Session::get('success')}}</p>
@endif

@if(Session::has('error'))
 <p style="color:red">{{ Session::get('error')}}</p>
@endif
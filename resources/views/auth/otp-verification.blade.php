<!-- resources/views/auth/otp-verification.blade.php -->
@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('verify.otp.submit') }}">
    @csrf
    <label for="otp">Enter OTP:</label>
    <input type="text" name="otp" required>
    <button type="submit">Verify OTP</button>
</form>
@endsection
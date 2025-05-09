<!DOCTYPE html>
<html>
<head>
    <title>Test OTP Route</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Test OTP Route</h1>
    <form action="{{ route('password.sendOtp') }}" method="POST">
        @csrf
        <input type="email" name="email" value="{{ $email ?? 'test@example.com' }}">
        <button type="submit">Send OTP</button>
    </form>
</body>
</html> 
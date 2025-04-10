<!DOCTYPE html>
<html>
<head>
    <title>Dealer Register</title>
</head>
<body>
    <h2>Dealer Register</h2>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('dealer.register') }}">
        @csrf
        <label>Name:</label>
        <input type="text" name="name" required>
        <br>

        <label>Email:</label>
        <input type="email" name="email" required>
        <br>

            <label>Company Name</label>
            <input type="text" name="company_name" required>
        <br>
        

        <label>Password:</label>
        <input type="password" name="password" required>
        <br>

        <label>Confirm Password:</label>
        <input type="password" name="password_confirmation" required>
        <br>

        <button type="submit">Register</button>
    </form>

    <p>Already registered? <a href="{{ route('dealer.login.form') }}">Login here</a></p>
</body>
</html>
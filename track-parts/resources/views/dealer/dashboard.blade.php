<!DOCTYPE html>
<html>
<head>
    <title>Dealer Dashboard</title>
</head>
<body>
    <h2>Welcome, {{ auth()->user()->name }}</h2>

    <form method="POST" action="{{ route('dealer.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>

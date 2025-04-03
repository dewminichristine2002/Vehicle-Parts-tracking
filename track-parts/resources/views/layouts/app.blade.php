<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Parts Tracking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        input, select, button { margin: 5px 0; padding: 5px; }
    </style>
</head>
<body>

    <h1>@yield('title', 'Vehicle Parts Tracking')</h1>

    @yield('content')


<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if (session('success'))
            alert("✅ {{ session('success') }}");
        @elseif (session('error'))
            alert("❗ {{ session('error') }}");
        @endif
    });
</script>



</body>


</html>

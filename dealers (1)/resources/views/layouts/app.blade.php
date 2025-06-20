<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>


   
  
</head>
<body>

   

    @yield('content')


<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if (session('success'))
            alert(" {{ session('success') }}");
        @elseif (session('error'))
            alert(" {{ session('error') }}");
        @endif
    });
</script>



</body>


</html>

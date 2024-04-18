<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <x-front.links />
</head>

<body class="antialiased">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card shadow p-3">
                    <div class="card-body text-center">
                        <h3>@yield('code')</h3>
                        <h3>@yield('message')</h3>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Ricarica Pagina</a>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>JRCZ map filtering tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="/resources/js/app.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="{{ asset('map/interactive-map.js') }}"></script>
</head>
<nav class="top-bar">
    <img src="{{ asset('images/hz.png') }}" width="100" class="left-logo">
    <div class="footer-slot center">
        <h1 class="title info">Area Division Polygons NL</h1>
    </div>
    <img src="{{ asset('images/jrcz.png') }}" width="100" class="right-logo">
</nav>

<body>
    {{ $slot }}
    <div id="sidebar" class="sidebar">
        <div id="item-list">
            <!-- List items will be inserted here -->
        </div>
    </div>
</body>
<footer class="footer">
    <div class="footer-slot left">
        @if (Auth::check())
            <span>UserID: {{ Auth::user()->name }}</span>
            <button class="button is-danger is-small ml-2" id="logout-button">Log Out</button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @else
            UserID
        @endif
    </div>
    <div class="footer-slot center">
        <div class="error-message" id="error-message" style="display: none;">
            Error fetching GeoJSON data.
        </div>
    </div>
    <div class="footer-slot right">SpicySpinnach</div>
</footer>

<script>
    document.getElementById('logout-button').addEventListener('click', function() {
        document.getElementById('logout-form').submit();
    });
</script>

</html>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<nav class="top-bar">
    <img src="{{ asset('images/hz.png') }}" width="100" class="left-logo">
    <div class="footer-slot center">
        <h1 class="title">Area Division Polygons NL</h1>
    </div>
    <img src="{{ asset('images/jrcz.png') }}" width="100" class="right-logo">
</nav>
<body>
    <!-- Display Flash Messages -->
    <div id="system-message" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1000;">
        @if (session('status'))
            <div class="notification is-success" id="status-message">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="notification is-danger" id="error-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="content has-text-centered">
        <div class="buttons">
            <button class="button is-primary is-large" id="login-button">Log In</button>
            <button class="button is-link is-large" id="register-button">Register</button>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal" id="login-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Log In</p>
                <button class="delete" aria-label="close" id="close-login-modal"></button>
            </header>
            <section class="modal-card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input class="input" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <div class="control">
                            <input class="input" type="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="checkbox">
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                    </div>
                    <button type="submit" class="button is-primary">Log In</button>
                </form>
            </section>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal" id="register-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Register</p>
                <button class="delete" aria-label="close" id="close-register-modal"></button>
            </header>
            <section class="modal-card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="field">
                        <label class="label">Name</label>
                        <div class="control">
                            <input class="input" type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input class="input" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <div class="control">
                            <input class="input" type="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Confirm Password</label>
                        <div class="control">
                            <input class="input" type="password" name="password_confirmation" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    <button type="submit" class="button is-link">Register</button>
                </form>
            </section>
        </div>
    </div>

    <script>
        document.getElementById('login-button').addEventListener('click', function() {
            document.getElementById('login-modal').classList.add('is-active');
        });
        document.getElementById('register-button').addEventListener('click', function() {
            document.getElementById('register-modal').classList.add('is-active');
        });
        document.getElementById('close-login-modal').addEventListener('click', function() {
            document.getElementById('login-modal').classList.remove('is-active');
        });
        document.getElementById('close-register-modal').addEventListener('click', function() {
            document.getElementById('register-modal').classList.remove('is-active');
        });

        // Auto-hide notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const statusMessage = document.getElementById('status-message');
            const errorMessage = document.getElementById('error-message');
            
            if (statusMessage) {
                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 5000);
            }
            
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</body>
<footer class="footer">
    <div class="footer-slot left">UserID</div>
    <div class="footer-slot right">SpicySpinnach</div>
</footer>
</html>

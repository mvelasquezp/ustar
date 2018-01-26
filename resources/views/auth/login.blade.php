<!DOCTYPE html>
<html >
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8">
        <title>UnionStar</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css" />
        <link rel='stylesheet prefetch' href="{{ asset('css/lato.css') }}" />
        <link rel='stylesheet prefetch' href="{{ asset('css/font-awesome.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/login.css') }}" />

    </head>

    <body>

        <!-- Form Mixin-->
        <!-- Input Mixin-->
        <!-- Button Mixin-->
        <!-- Pen Title-->
        <div class="pen-title">
            <h1>Bienvenido a Union Star</h1>
            <span><!--Pen <i class='fa fa-paint-brush'></i> + <i class='fa fa-code'></i> by <a href='http://andytran.me'>Andy Tran</a>-->&nbsp;</span>
        </div>
        <!-- Form Module-->
        <div class="module form-module">
            <div>
                <!--i class="fa fa-times fa-pencil"></i>
                <div class="tooltip">Nuevo usuario</div-->
            </div>
            <div class="form">
                <img src="{{ asset('images/ustar_logo.png') }}" class="img-logo">
                <h2>Ingresa a tu cuenta</h2>
                <form id="form-login" action="{{ url('login/verificar') }}" method="post" >
                    <input type="text" id="user" name="user" placeholder="Usuario" />
                    <input type="password" id="pswd" name="pswd" placeholder="Contraseña" />
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    @if(isset($errmsg))
                    <p class="p-err">{{ $errmsg }}</p>
                    @endif
                    <button id="btn-login">Login</button>
                </form>
            </div>
            <div class="cta"><a href="http://andytran.me">¿Olvidaste tu contraseña?</a></div>
        </div>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/login.js') }}"></script>
        <script type="text/javascript">
            function submitForm(event) {
                var user = document.getElementById("user").value;
                var pswd = document.getElementById("pswd").value;
                if(user == "" || pswd == "") {
                    event.preventDefault();
                    alert("Los campos de usuario y contraseña no pueden dejarse en blanco.");
                }
            }
            function init() {
                $("#form-login").on("submit", submitForm);
                document.getElementById("user").value = "{{ isset($usr) ? $usr : '' }}";
                document.getElementById("pswd").value = "{{ isset($psw) ? $psw : '' }}";
            }
            $(init);
        </script>

    </body>
</html>

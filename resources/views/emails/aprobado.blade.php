<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>¡Cuenta aprobada!</title>
</head>
<body>
    <h2>¡Tu cuenta ha sido aprobada!</h2>
    <p>Hola {{ $name }},</p>
    <p>
        Tu cuenta en el sistema escolar ha sido aprobada.<br>
        Ya puedes iniciar sesión con tu correo: <strong>{{ $email }}</strong>
    </p>
    <br>
    <p>
        <a href="{{ url('/login') }}" style="color: #fff; background: #007bff; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
            Ir al sistema escolar
        </a>
    </p>
    <hr>
    <p style="color: #888; font-size: 0.9em;">
        Este es un mensaje automático, por favor no respondas a este correo.
    </p>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo registro en el sistema escolar</title>
</head>
<body>
    <h2>Nuevo registro en el sistema escolar</h2>
    <p><strong>Nombre:</strong> {{ $name }}</p>
    <p><strong>Correo:</strong> {{ $email }}</p>
    <br>
    <p>
        <a href="{{ url('/administrador') }}" style="color: #fff; background: #007bff; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
            Ir al panel de administrador
        </a>
    </p>
    <hr>
    
    <p style="color: #888; font-size: 0.9em;">
        Este es un mensaje automático, por favor no respondas a este correo.
    </p>
</body>
</html>
<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanitizar datos
    $nombre = strip_tags(trim($_POST["nombre"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $asunto_usuario = strip_tags(trim($_POST["asunto"]));
    $mensaje_usuario = strip_tags(trim($_POST["mensaje"]));

    // Validaciones básicas
    if (empty($nombre) || empty($mensaje_usuario) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos correctamente."]);
        exit;
    }

    // Configuración del correo
    $destinatario = "garciaseva.manuel@gmail.com";
    $asunto = "[Web Contacto] " . $asunto_usuario;
    
    $contenido = "Has recibido un nuevo mensaje desde tu portafolio web.\n\n";
    $contenido .= "--------------------------------------------------\n";
    $contenido .= "Nombre: $nombre\n";
    $contenido .= "Email del interesado: $email\n";
    $contenido .= "Asunto: $asunto_usuario\n";
    $contenido .= "--------------------------------------------------\n\n";
    $contenido .= "Mensaje:\n$mensaje_usuario\n";

    // Cabeceras para maximizar la entregabilidad
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/plain;charset=UTF-8" . "\r\n";
    $headers .= "From: Formulario Manuel <simbad@simbad.net>" . "\r\n"; // Remitente autorizado
    $headers .= "Reply-To: $email" . "\r\n"; // Al dar a responder, responderás al usuario
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Enviar correo
    if (mail($destinatario, $asunto, $contenido, $headers)) {
        echo json_encode(["status" => "success", "message" => "Mensaje enviado correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo enviar el mensaje. Inténtalo de nuevo más tarde."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Acceso no autorizado."]);
}
?>
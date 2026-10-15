<?php
header('Content-Type: application/json');

// Función para limpiar cabeceras de email y evitar inyecciones
function clean_header($data) {
    return str_replace(array("\r", "\n", "%0a", "%0d"), '', $data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Honeypot (Protección Anti-Spam)
    // Si el campo invisible 'website_url' tiene contenido, es un bot.
    if (!empty($_POST["website_url"])) {
        echo json_encode(["status" => "success", "message" => "Mensaje enviado correctamente (honeypot)."]);
        exit;
    }

    // 2. Recoger y sanitizar datos
    $nombre = clean_header(strip_tags(trim($_POST["nombre"])));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $asunto_usuario = clean_header(strip_tags(trim($_POST["asunto"])));
    $mensaje_usuario = strip_tags(trim($_POST["mensaje"]));

    // Validaciones básicas
    if (empty($nombre) || empty($mensaje_usuario) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos correctamente."]);
        exit;
    }

    // Configuración del correo
    $destinatario = "garciaseva.manuel@gmail.com";
    $asunto = "=?UTF-8?B?".base64_encode("[Contacto Web] " . $asunto_usuario)."?=";
    
    // Cuerpo del mensaje en HTML con estilo
    $mensaje_html = '
    <html>
    <head>
        <style>
            body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; color: #333; }
            .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e1e1e1; }
            .header { background: linear-gradient(135deg, #0d9488 0%, #6366f1 100%); color: white; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
            .content { padding: 30px; line-height: 1.6; }
            .field-box { background-color: #f8fafc; border-radius: 8px; padding: 15px; margin-bottom: 20px; border-left: 4px solid #14b8a6; }
            .label { font-weight: bold; color: #64748b; font-size: 12px; text-transform: uppercase; margin-bottom: 5px; display: block; }
            .value { color: #1e293b; font-size: 16px; font-weight: 500; }
            .message-text { white-space: pre-wrap; color: #334155; font-size: 15px; font-style: italic; background: #fff; padding: 15px; border-radius: 6px; border: 1px dashed #cbd5e1; }
            .footer { background: #f1f5f9; padding: 20px; text-align: center; color: #94a3b8; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Nuevo Mensaje de Contacto</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.8;">Portafolio Profesional Manuel García</p>
            </div>
            <div class="content">
                <div class="field-box">
                    <span class="label">De:</span>
                    <span class="value">' . htmlspecialchars($nombre) . '</span>
                </div>
                <div class="field-box">
                    <span class="label">Email:</span>
                    <span class="value">' . htmlspecialchars($email) . '</span>
                </div>
                <div class="field-box">
                    <span class="label">Asunto:</span>
                    <span class="value">' . htmlspecialchars($asunto_usuario) . '</span>
                </div>
                <div class="field-box">
                    <span class="label">Mensaje:</span>
                    <div class="message-text">' . nl2br(htmlspecialchars($mensaje_usuario)) . '</div>
                </div>
            </div>
            <div class="footer">
                Este mensaje fue enviado desde el formulario de contacto de tu sitio web.<br>
                Recibido el ' . date('d/m/Y H:i:s') . '
            </div>
        </div>
    </body>
    </html>';

    // Cabeceras para correo HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Formulario Web <simbad@simbad.net>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Enviar correo
    if (mail($destinatario, $asunto, $mensaje_html, $headers)) {
        echo json_encode(["status" => "success", "message" => "Mensaje enviado correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo enviar el mensaje. Inténtalo de nuevo más tarde."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Acceso no autorizado."]);
}
?>
<?php
include '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibe los datos del cliente desde la solicitud POST
    $data = json_decode(file_get_contents('php://input'), true);

    // Valida que los datos se hayan recibido correctamente
    if (!isset($data['name']) || !isset($data['company']) || !isset($data['email']) || !isset($data['phone']) || !isset($data['address'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos del cliente.']);
        exit;
    }

    // Limpia y valida los datos recibidos
    $name = htmlspecialchars(trim($data['name']));
    $company = htmlspecialchars(trim($data['company']));
    $email = htmlspecialchars(trim($data['email']));
    $phone = htmlspecialchars(trim($data['phone']));
    $address = htmlspecialchars(trim($data['address']));

    // Valida el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Formato de correo electrónico inválido.']);
        exit;
    }

    // Prepara la consulta SQL para insertar el nuevo cliente
    $sql = "INSERT INTO clientes (nombre, empresa, email, telefono, direccion) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Enlaza los parámetros a la consulta preparada
    $stmt->bind_param("sssss", $name, $company, $email, $phone, $address);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        // Si la consulta se ejecuta correctamente, devuelve una respuesta de éxito
        echo json_encode(['success' => true, 'message' => 'Cliente agregado exitosamente.']);
    } else {
        // Si hay un error al ejecutar la consulta, devuelve una respuesta de error
        echo json_encode(['success' => false, 'message' => 'Error al agregar el cliente: ' . $stmt->error]);
    }

    // Cierra la declaración y la conexión a la base de datos
    $stmt->close();
    $conn->close();
} else {
    // Si la solicitud no es de tipo POST, devuelve un error
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
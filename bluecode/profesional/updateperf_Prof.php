<?php
$celular = $_POST['celular'];
$domicilio = $_POST['domicilio'];
$rol = $_POST['rol'];
$email = $_POST['email'];
$contraseña = $_POST['contraseña']; 
$foto = $_FILES['foto'];

require_once '../cn.php';

session_start();
$idProfesional = $_SESSION['id'];

$nombreArchivo = $foto['name'];
$tipoArchivo = $foto['type'];
$datosArchivo = file_get_contents($foto['tmp_name']);
$rutaCarpeta = '../img_perf/';
$rutaArchivo = $rutaCarpeta . $nombreArchivo;

// Verificar la conexión
if ($con->connect_error) {
  die('Error en la conexión: ' . $con->connect_error);
}

$stmt = $con->prepare("SELECT contraseña FROM 7a_profesional WHERE idprofesional = ?");
$stmt->bind_param("i", $idProfesional);
$stmt->execute();
$result = $stmt->get_result();
$profesional = $result->fetch_assoc();
$contraseña_actual = $profesional['contraseña'];

// Verificar si se proporciona una nueva contraseña y si es diferente de la actual
if (!empty($contraseña) && $contraseña !== $contraseña_actual) {
    // Se proporciona una nueva contraseña y es diferente de la actual
    $contraseña_hash = password_hash($contraseña_nueva, PASSWORD_DEFAULT);
    // Luego, actualiza la contraseña en la base de datos
    $stmt = $con->prepare("UPDATE 7a_profesional SET contraseña = ? WHERE idpaciente = ?");
    $stmt->bind_param("si", $contraseña_hash, $idPaciente);
    if ($stmt->execute()) {
        echo "La contraseña se ha actualizado exitosamente";
        header('location:perfProf.php ');
    } else {
        echo "Error al actualizar la contraseña: " . $stmt->error;
    }
} 

$stmt = $con->prepare("UPDATE 7a_profesional SET celular = ?, domicilio = ?, rol = ?, email = ?, foto = ? WHERE idprofesional = ?");
$stmt->bind_param("sssssi", $celular, $domicilio, $rol, $email, $rutaArchivo, $idProfesional);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Los cambios se han guardado exitosamente";
    header('Location: perfilAdm.php');
}else {
    echo "Error al guardar los cambios: " . $stmt->error;
}

// Cerrar la conexión
$stmt->close();
$con->close();
 // Mover la imagen a la ubicación deseada 
 if (move_uploaded_file($foto['tmp_name'], $rutaArchivo)) {
  header('Location: perfProf.php');
  echo $rutaArchivo;
 } else {
    echo "Error al guardar la imagen en la carpeta.";
  }
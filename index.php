<?php
include 'bd/BD.php';

header('Access-Control-Allow-Origin: *');

// --- Peticiones GET ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $query = "SELECT * FROM `frameworks` WHERE id =" . $_GET['id'];
        $resultado = metodoGET($query); // metodoGET ya devuelve un array (fetchAll)
        echo json_encode($resultado); // Solo codifica el array a JSON
    } else {
        $query = "SELECT * FROM `frameworks`";
        $resultado = metodoGET($query); // metodoGET ya devuelve un array (fetchAll)
        echo json_encode($resultado); // Solo codifica el array a JSON
    };
    header("HTTP/1.1 200 OK");
    exit();
}

// --- Peticiones POST ---
// Nota: La forma en que manejas POST/PUT/DELETE usando $_POST['METHOD'] es común para APIs REST
// que no pueden usar los métodos HTTP nativos (ej. formularios HTML sin JavaScript para PUT/DELETE).
// Si tu cliente (ReactJS) puede enviar métodos PUT/DELETE reales, es mejor usar $_SERVER['REQUEST_METHOD']
// para POST, PUT, DELETE directamente, sin el campo 'METHOD' en el cuerpo.
if(isset($_POST['METHOD']) && $_POST['METHOD'] == 'POST'){
    unset($_POST['METHOD']);
    $nombre=$_POST['nombre'];
    $lanzamiento=$_POST['lanzamiento'];
    $desarrollador=$_POST['desarrollador'];
    $query="insert into frameworks(nombre, lanzamiento, desarrollador) values ('$nombre', '$lanzamiento', '$desarrollador')";
    $queryAutoincrement="select MAX(id) as id from frameworks";
    $resultado=metodoPOST($query, $queryAutoincrement);
    echo json_encode($resultado);
    header("HTTP/1.1 201 Created"); // 'Created' es más apropiado aquí
    exit();
}

// --- Peticiones PUT ---
if(isset($_POST['METHOD']) && $_POST['METHOD'] == 'PUT'){
    unset($_POST['METHOD']);
    $id=$_GET['id']; // Asegúrate de que el ID venga por GET
    $nombre=$_POST['nombre'];
    $lanzamiento=$_POST['lanzamiento'];
    $desarrollador=$_POST['desarrollador'];
    $query="UPDATE frameworks SET nombre='$nombre', lanzamiento='$lanzamiento', desarrollador='$desarrollador' WHERE id='$id'";
    $resultado=metodoPUT($query);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

// --- Peticiones DELETE ---
if(isset($_POST['METHOD']) && $_POST['METHOD'] == 'DELETE'){
    unset($_POST['METHOD']);
    $id=$_GET['id']; // Asegúrate de que el ID venga por GET
    $query="DELETE from frameworks WHERE id='$id'";
    $resultado=metodoDELETE($query);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

// --- Si ningún método coincide ---
header("HTTP/1.1 405 Method Not Allowed");
?>
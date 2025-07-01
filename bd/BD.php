<?php
$pdo = null;
$host = "localhost";
$user = "root";
$password = "root"; // ¡Verifica si tu contraseña de root es realmente 'root' o vacía para XAMPP!
$database = "crud_php_reactjs";


function conectar() {
    try {
        $GLOBALS['pdo'] = new PDO("mysql:host=" . $GLOBALS['host'] . ";dbname=" . $GLOBALS['database'] . ";charset=utf8mb4", $GLOBALS['user'], $GLOBALS['password']);
        $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ¡Crucial para depurar!
        
    } catch (PDOException $th) { // Captura PDOException específicamente para errores de conexión
        print_r("Error de conexión a la base de datos: " . $th->getMessage());
        die();
    }
}

function desconectar() {
    $GLOBALS['pdo'] = null;
}


function metodoGET($query) {
    try {
        conectar(); // <-- ¡ESTO ES LO CRÍTICO: CONECTAR ANTES DE USAR $pdo!
        $sentencia = $GLOBALS['pdo']->prepare($query);
        $sentencia->setFetchMode(PDO::FETCH_ASSOC); // <-- CORRECCIÓN: Llamar el método en $sentencia
        $sentencia->execute();
        
        // No te desconectes aquí si planeas obtener resultados fuera de la función.
        // Es mejor obtener los resultados aquí y luego desconectar.
        $resultados = $sentencia->fetchAll(); 
        $sentencia->closeCursor(); // Buena práctica para cerrar el cursor
        desconectar();
        return $resultados; // Retorna los resultados, no la sentencia
        
    } catch (PDOException $th) { // Captura PDOException para errores de consulta
        die("Error en la consulta GET: " . $th->getMessage());
    }
}

function metodoPOST($query, $queryAutoIncrement) {
    try {
        conectar();
        $sentencia = $GLOBALS['pdo']->prepare($query);
        $sentencia->execute();
        
        // Asumiendo que queryAutoIncrement es una consulta para obtener el último ID insertado.
        // Es importante que este metodoGET ya esté corregido y funcione.
        $id = metodoGET($queryAutoIncrement); // metodoGET ahora retorna resultados, no la sentencia
        // Si $id es un array de arrays (porque fetchAll), necesitarás acceder al primer elemento
        $lastId = $id[0] ?? null; // Obtiene la primera fila, o null si está vacía
        
        $resultado = array_merge($lastId, $_POST); // $lastId debe ser un array asociativo
        
        $sentencia->closeCursor(); // Nombre del método corregido
        desconectar();
        return $resultado;

    } catch (PDOException $th) { // Captura PDOException para errores de consulta
        die("Error en la consulta POST: " . $th->getMessage());
    }
}

function metodoPUT($query) {
    try {
        conectar();
        $sentencia = $GLOBALS['pdo']->prepare($query);
        $sentencia->execute();
        
        // $GET está indefinido. Probablemente te refieres a $_GET.
        // Además, considera qué *realmente* quieres devolver aquí.
        // Para un UPDATE, a menudo es el número de filas afectadas, o simplemente true/false.
        $affectedRows = $sentencia->rowCount();
        
        // Si necesitas los datos fusionados, asegúrate de que $_GET esté poblado como se espera
        // $resultado = array_merge($_GET, $_POST); 
        
        $sentencia->closeCursor(); 
        desconectar();
        // return $resultado; // Si aún quieres devolver datos fusionados, asegúrate de que $_GET sea válido
        return $affectedRows; // Devolver filas afectadas es común para PUT
        

    } catch (PDOException $th) {
        die("Error en la consulta PUT: " . $th->getMessage());
    }
}


function metodoDELETE($query) {
    try {
        conectar();
        $sentencia = $GLOBALS['pdo']->prepare($query);
        $sentencia->execute();
        
        $affectedRows = $sentencia->rowCount(); // Obtener filas afectadas para DELETE
        $sentencia->closeCursor(); 
        desconectar();
        // $resultado está indefinido aquí. Devuelve filas afectadas o true/false.
        return $affectedRows; 

    } catch (PDOException $th) {
        die("Error en la consulta DELETE: " . $th->getMessage());
    }
}

?>
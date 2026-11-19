
<?php
function OpenConnection()
{
    $conn = new mysqli("localhost", "wwwdata", "abc", "pibd");

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    return $conn;
}

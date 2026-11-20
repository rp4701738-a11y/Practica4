<?php
function obtenerPaises($conn)
{
    $paises = [];
    $resultado = $conn->query("SELECT IdPais, NomPais FROM Paises ORDER BY NomPais ASC");
    while ($fila = $resultado->fetch_assoc()) {
        $paises[] = $fila;
    }
    $resultado->close();
    return $paises;
}

function renderSelectPaises($paises, $seleccionado = '', $textoVacio = '-- Selecciona un país --', $id = 'pais', $name = 'pais')
{
    $idEsc = htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8');
    $nameEsc = htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8');
    $vacioEsc = htmlspecialchars((string)$textoVacio, ENT_QUOTES, 'UTF-8');
    echo '<select id="' . $idEsc . '" name="' . $nameEsc . '">';
    echo '<option value="">' . $vacioEsc . '</option>';
    foreach ($paises as $pais) {
        $idPais = (string)$pais['IdPais'];
        $nomPais = (string)$pais['NomPais'];
        $esSeleccionado = ((string)$seleccionado !== '' && ((string)$seleccionado === $idPais || strcasecmp((string)$seleccionado, $nomPais) === 0));
        $attr = $esSeleccionado ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($idPais, ENT_QUOTES, 'UTF-8') . '"' . $attr . '>' . htmlspecialchars($nomPais, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>';
}
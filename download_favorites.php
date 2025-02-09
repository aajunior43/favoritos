<?php
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="favorites.txt"');
header('Content-Transfer-Encoding: binary');

// Lê e envia o arquivo
readfile('favorites.txt');
?>

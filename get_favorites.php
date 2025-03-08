<?php
header('Content-Type: application/json');

// Verificar se o arquivo de favoritos existe
if (!file_exists('favorites.txt')) {
    echo json_encode([
        'success' => true,
        'favorites' => []
    ]);
    exit;
}

// Ler o arquivo de favoritos
$content = file_get_contents('favorites.txt');
$lines = explode("\n", $content);
$favorites = [];

// Processar cada linha
foreach ($lines as $line) {
    if (trim($line) !== "") {
        $parts = explode(",", $line, 2);
        if (count($parts) === 2) {
            $favorites[] = [
                'name' => $parts[0],
                'url' => $parts[1]
            ];
        }
    }
}

// Retornar os favoritos como JSON
echo json_encode([
    'success' => true,
    'favorites' => $favorites
]);
?>

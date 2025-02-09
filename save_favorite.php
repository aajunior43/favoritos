<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['name']) && isset($data['url'])) {
        // Validar URL
        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'error' => 'URL inválida']);
            exit;
        }

        // Sanitizar dados
        $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
        $url = filter_var($data['url'], FILTER_SANITIZE_URL);
        
        // Verificar/criar arquivo
        if (!file_exists('favorites.txt')) {
            file_put_contents('favorites.txt', '');
        }

        $newLine = "\n" . $name . "," . $url;
        
        if (file_put_contents('favorites.txt', $newLine, FILE_APPEND) !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao salvar no arquivo']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    }
}
?>

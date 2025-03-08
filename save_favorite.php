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

        // Ler o conteúdo atual para determinar se precisamos adicionar uma quebra de linha
        $content = file_get_contents('favorites.txt');
        
        // Se o arquivo não estiver vazio e não terminar com uma quebra de linha, adicionamos uma
        if (!empty($content) && substr($content, -1) !== "\n") {
            $newLine = "\n" . $name . "," . $url;
        } else if (empty($content)) {
            // Se o arquivo estiver vazio, não precisamos de quebra de linha no início
            $newLine = $name . "," . $url;
        } else {
            // O arquivo tem conteúdo e termina com quebra de linha
            $newLine = $name . "," . $url;
        }
        
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

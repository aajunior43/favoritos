<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $password = isset($data['password']) ? $data['password'] : '';
    
    $correct_password = "123456";
    
    if ($password !== $correct_password) {
        echo json_encode(['success' => false, 'error' => 'Senha incorreta']);
        exit;
    }

    if (isset($data['oldName']) && isset($data['oldUrl']) && isset($data['name']) && isset($data['url'])) {
        // Validar URL
        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'error' => 'URL inválida']);
            exit;
        }
        
        $content = file_get_contents('favorites.txt');
        $lines = explode("\n", $content);
        $new_content = [];
        $updated = false;
        
        foreach ($lines as $line) {
            if (trim($line) === "") {
                $new_content[] = $line;
                continue;
            }
            
            if (trim($line) === $data['oldName'] . "," . $data['oldUrl']) {
                $new_content[] = $data['name'] . "," . $data['url'];
                $updated = true;
            } else {
                $new_content[] = $line;
            }
        }
        
        if ($updated && file_put_contents('favorites.txt', implode("\n", $new_content)) !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar arquivo ou item não encontrado']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    }
}
?>

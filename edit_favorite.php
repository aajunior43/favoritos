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
        $content = file_get_contents('favorites.txt');
        $lines = explode("\n", $content);
        $updated = false;
        
        foreach ($lines as &$line) {
            if (trim($line) === $data['oldName'] . "," . $data['oldUrl']) {
                $line = $data['name'] . "," . $data['url'];
                $updated = true;
                break;
            }
        }
        
        if ($updated && file_put_contents('favorites.txt', implode("\n", $lines)) !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar arquivo']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    }
}
?>

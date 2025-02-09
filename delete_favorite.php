<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $password = isset($data['password']) ? $data['password'] : '';
    
    // Senha definida (você deve alterar isto)
    $correct_password = "123456";
    
    if ($password !== $correct_password) {
        echo json_encode(['success' => false, 'error' => 'Senha incorreta']);
        exit;
    }

    if (isset($data['name']) && isset($data['url'])) {
        $content = file_get_contents('favorites.txt');
        $lines = explode("\n", $content);
        $new_content = [];
        
        foreach ($lines as $line) {
            if (trim($line) !== "" && $line !== ($data['name'] . "," . $data['url'])) {
                $new_content[] = $line;
            }
        }
        
        if (file_put_contents('favorites.txt', implode("\n", $new_content)) !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao atualizar arquivo']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    }
}
?>

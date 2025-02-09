<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['oldName']) && isset($data['oldUrl']) && 
        isset($data['newName']) && isset($data['newUrl'])) {
        
        if (!filter_var($data['newUrl'], FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'error' => 'URL inválida']);
            exit;
        }

        $content = file_get_contents('favorites.txt');
        $lines = explode("\n", $content);
        $new_content = [];
        
        foreach ($lines as $line) {
            if (trim($line) === "") continue;
            
            if ($line === ($data['oldName'] . "," . $data['oldUrl'])) {
                $new_content[] = $data['newName'] . "," . $data['newUrl'];
            } else {
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

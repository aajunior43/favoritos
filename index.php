<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Favoritos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="favorites-container">
        <h1>Meus Favoritos</h1>
        
        <div class="links-grid">
            <?php
            $content = file_get_contents('favorites.txt');
            $lines = explode("\n", $content);
            
            foreach ($lines as $line) {
                if (trim($line) !== "") {
                    list($name, $url) = explode(",", $line);
                    echo '<div class="link-card">
                            <div class="link-title">' . htmlspecialchars($name) . '</div>
                            <div class="link-url">' . htmlspecialchars($url) . '</div>
                            <div class="button-group">
                                <a href="' . htmlspecialchars($url) . '" target="_blank" class="btn btn-visit">Visitar</a>
                                <button onclick="editLink(\'' . htmlspecialchars($name) . '\', \'' . htmlspecialchars($url) . '\')" class="btn btn-edit">Editar</button>
                                <button onclick="deleteLink(\'' . htmlspecialchars($name) . '\', \'' . htmlspecialchars($url) . '\')" class="btn btn-delete">Excluir</button>
                            </div>
                          </div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Editar Link</h2>
            <form id="editForm">
                <div class="form-group">
                    <label for="editName">Nome:</label>
                    <input type="text" id="editName" required>
                </div>
                <div class="form-group">
                    <label for="editUrl">URL:</label>
                    <input type="url" id="editUrl" required>
                </div>
                <div class="form-group">
                    <label for="editPassword">Senha:</label>
                    <input type="password" id="editPassword" required>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-edit">Salvar</button>
                    <button type="button" onclick="closeModal()" class="btn">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function editLink(name, url) {
        document.getElementById('editModal').style.display = 'flex';
        document.getElementById('editName').value = name;
        document.getElementById('editUrl').value = url;
        document.getElementById('editPassword').value = '';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function deleteLink(name, url) {
        if (confirm('Tem certeza que deseja excluir este link?')) {
            const password = prompt('Digite a senha para confirmar a exclusão:');
            if (password) {
                fetch('delete_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name, url, password })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Erro ao excluir o link');
                    }
                });
            }
        }
    }

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = {
            oldName: document.getElementById('editName').getAttribute('data-original'),
            oldUrl: document.getElementById('editUrl').getAttribute('data-original'),
            name: document.getElementById('editName').value,
            url: document.getElementById('editUrl').value,
            password: document.getElementById('editPassword').value
        };

        fetch('edit_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Erro ao editar o link');
            }
        });
    });
    </script>
</body>
</html>

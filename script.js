window.addEventListener('DOMContentLoaded', (event) => {
    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    let currentDeleteItem = null;
    let buttons = [];

    function displayFavorites() {
        const buttonGrid = document.querySelector('.button-grid');
        buttonGrid.innerHTML = '';
        buttons = [];

        favorites.forEach(({name, url}) => {
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'site-button';

            const button = document.createElement('button');
            button.textContent = name;
            button.dataset.name = name.toLowerCase();
            button.onclick = () => window.open(url, '_blank');

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-button';
            deleteBtn.innerHTML = '&times;';
            deleteBtn.onclick = (e) => {
                e.stopPropagation();
                showPasswordModal(name, url);
            };

            buttonContainer.appendChild(button);
            buttonContainer.appendChild(deleteBtn);
            buttonGrid.appendChild(buttonContainer);
            buttons.push(buttonContainer);
        });

        return buttons;
    }

    // Funções de Modal
    window.showPasswordModal = (name, url) => {
        currentDeleteItem = { name, url };
        document.querySelector('.password-modal').style.display = 'block';
        document.getElementById('deletePassword').value = '';
    };

    window.closePasswordModal = () => {
        document.querySelector('.password-modal').style.display = 'none';
        currentDeleteItem = null;
    };

    window.closeForm = () => {
        document.querySelector('.add-link-form').style.display = 'none';
        document.getElementById('linkName').value = '';
        document.getElementById('linkUrl').value = '';
    };

    // Funções de Manipulação de Favoritos
    window.saveNewLink = () => {
        const name = document.getElementById('linkName').value.trim();
        const url = document.getElementById('linkUrl').value.trim();
        
        // Validar URL
        try {
            new URL(url);
        } catch (e) {
            alert('Por favor, insira uma URL válida');
            return;
        }
        
        if (name && url) {
            // Verificar se já existe
            if (favorites.some(f => f.url === url)) {
                alert('Esta URL já existe nos favoritos!');
                return;
            }

            favorites.push({ name, url });
            localStorage.setItem('favorites', JSON.stringify(favorites));
            
            fetch('save_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, url })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Erro ao salvar:', data.error);
                }
            })
            .catch(error => console.error('Erro:', error));

            buttons = displayFavorites();
            closeForm();
        } else {
            alert('Por favor, preencha todos os campos!');
        }
    };

    window.confirmDelete = () => {
        const password = document.getElementById('deletePassword').value;
        if (!currentDeleteItem) return;

        fetch('delete_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name: currentDeleteItem.name,
                url: currentDeleteItem.url,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                favorites = favorites.filter(f => 
                    f.name !== currentDeleteItem.name || f.url !== currentDeleteItem.url
                );
                localStorage.setItem('favorites', JSON.stringify(favorites));
                buttons = displayFavorites();
                closePasswordModal();
            } else {
                alert(data.error || 'Erro ao excluir');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir o site');
        });
    };

    // Adicionar feedback visual
    function showMessage(message, isError = false) {
        const msg = document.createElement('div');
        msg.className = `message ${isError ? 'error' : 'success'}`;
        msg.textContent = message;
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 3000);
    }

    // Event Listeners
    document.querySelector('.search-input').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        buttons.forEach(button => {
            const isVisible = button.querySelector('button').dataset.name.includes(searchTerm);
            button.style.display = isVisible ? 'block' : 'none';
        });
    });

    document.querySelector('.add-new-button').addEventListener('click', () => {
        document.querySelector('.add-link-form').style.display = 'block';
    });

    const editButton = document.querySelector('.edit-mode-button');
    editButton.addEventListener('click', () => {
        const isEditMode = document.body.classList.toggle('edit-mode');
        editButton.textContent = isEditMode ? '✓' : '✎';
        editButton.style.backgroundColor = isEditMode ? 'rgba(255, 68, 68, 0.8)' : 'rgba(77, 255, 184, 0.2)';
    });

    // Download de Favoritos
    window.downloadFavorites = () => {
        window.location.href = 'download_favorites.php';
    };

    // Inicialização
    if (favorites.length === 0) {
        fetch('favorites.txt')
            .then(response => response.text())
            .then(data => {
                if (data.trim()) {
                    const lines = data.trim().split('\n');
                    favorites = lines.map(line => {
                        const [name, url] = line.split(',');
                        return { name, url };
                    });
                    localStorage.setItem('favorites', JSON.stringify(favorites));
                    buttons = displayFavorites();
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        buttons = displayFavorites();
    }
});

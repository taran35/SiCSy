
function getFiles(parent) {
console.log(parent); //DEBUG
fetch('./php/getfiles.php?parent=' + encodeURIComponent(parent))
  .then(response => response.json())
  .then(data => {
    const container = document.getElementById('file-container');

    if (data.error) {
      container.innerHTML = `<div class="error">${data.error}</div>`;
      return;
    }

    
    data.content.sort((a, b) => {
    if (a.type === 'folder' && b.type !== 'folder') return -1;
    if (a.type !== 'folder' && b.type === 'folder') return 1;
    return a.name.localeCompare(b.name);
    });

    data.content.forEach(item => {
    const fileItem = document.createElement('div');
    fileItem.className = item.type === 'folder' ? 'folder' : 'file';
    fileItem.textContent = htmlspecialchars(item.name);

    if (item.type === 'folder') {
        fileItem.addEventListener('click', function () {
        getFiles(parent + "/" + item.name);
        });

       
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('delete-folder-button');
        deleteButton.innerHTML = `🗑`;
        deleteButton.addEventListener('click', function (e) {
        e.stopPropagation();
        deleteFolder(item.path, item.name);
        });

        fileItem.appendChild(deleteButton);

    } else {

        fileItem.addEventListener('click', function () {
        openFilePopup(item.path, item.name);
        });
    }

    container.appendChild(fileItem);
    });


  })
  .catch(error => {
    document.getElementById('file-container').innerHTML = `<div class="error">Erreur : ${error.message}</div>`;
  });

}


document.addEventListener("DOMContentLoaded", function () {
    let globalParentPath = null;
    function unescapeHtml(html) {
        const textarea = document.createElement("textarea");
        textarea.innerHTML = html;
        return textarea.value;
    }
    async function sessionGet() {
        try {
            const response = await fetch('./main/php/parent.php?action=get', {
                headers: {
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                }
            });
            const data = await response.text();
            globalParentPath = data;
        } catch {
            alert('Erreur lors de la récupération du chemin des fichiers')
        }
    }

    async function sessionUp(folder) {
        try {
            await fetch('./main/php/parent.php?action=up&folder=' + encodeURIComponent(folder), {
                headers: {
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                }
            });
        } catch {
            alert('Erreur lors du changement du chemin des fichiers')
        }
    }
    async function sessionBack() {
        try {
            await fetch('./main/php/parent.php?action=back', {
                headers: {
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                }
            });
        } catch {
            alert('Erreur lors du changement du chemin des fichiers')
        }
    }

    function back() {
        (async () => {
            await sessionBack();
        })();
        (async () => {
            await sessionGet();
            getFiles(globalParentPath);
        })();
    }


    //GET FILES
    function getFiles(parent) {
        removeItems('ITEM');
        fetch('./main/php/getfiles.php?parent=' + encodeURIComponent(parent), {
            headers: {
                'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
            }
        })
            .then(response => response.json())
            .then(data => {

                if (data.error) {
                    alert("erreur lors de la récuperation des fichiers");
                    return;
                }

                const container = document.getElementById('file-container');

                if (data.content == 'empty') {
                    const backButon = document.createElement('back-button');
                    backButon.textContent = 'Retour en arrière';
                    backButon.classList.add('back-button');
                    backButon.classList.add("ITEM");
                    backButon.addEventListener('click', function () {
                        back();
                    });
                    container.appendChild(backButon);
                } else {


                    if (parent != "/") {
                        const backButon = document.createElement('back-button');
                        backButon.textContent = 'Retour en arrière';
                        backButon.classList.add('back-button');
                        backButon.classList.add("ITEM");
                        backButon.addEventListener('click', function () {
                            back();
                        });
                        container.appendChild(backButon);
                    }



                    data.content.sort((a, b) => {
                        if (a.type === 'folder' && b.type !== 'folder') return -1;
                        if (a.type !== 'folder' && b.type === 'folder') return 1;
                        return a.name.localeCompare(b.name);
                    });

                    data.content.forEach(item => {
                        const fileItem = document.createElement('div');
                        fileItem.className = item.type === 'folder' ? 'folder' : 'file';
                        const cleanName = unescapeHtml(item.name);
                        fileItem.textContent = cleanName;
                        fileItem.classList.add("ITEM");

                        if (item.type === 'folder') {
                            fileItem.addEventListener('click', function () {
                                if (parent == '/') {
                                    getFiles('/' + item.name);
                                } else {
                                    getFiles(parent + "/" + item.name);
                                }
                                (async () => {
                                    await sessionUp(item.name);
                                })();
                            });


                            const deleteButton = document.createElement('button');
                            deleteButton.classList.add('delete-folder-button');
                            deleteButton.innerHTML = `🗑`;
                            deleteButton.addEventListener('click', function (e) {
                                e.stopPropagation();
                                showDeleteConfirmationFolder(parent, item.name);
                            });

                            fileItem.appendChild(deleteButton);

                        } else {

                            fileItem.addEventListener('click', function () {
                                openFilePopup(item.name, parent);
                            });
                        }

                        container.appendChild(fileItem);
                    });

                }
            })
            .catch(error => {
                alert('Erreur lors de la récupération des fichiers')
            });
        //launch update buttons
        removeButtons('button');
        addUtilityButtons(parent);
        //end
        (async () => {
            await sessionGet();
        })();
    }





    //BOUTONS div buttons
    function addUtilityButtons(directory) {
        try {
            const fileList = document.getElementById('buttons');

            const createFileButton = document.createElement('button');
            createFileButton.textContent = '📄 Créer un fichier';
            createFileButton.classList.add('create-file-button', 'button');
            createFileButton.addEventListener('click', function () {
                createFile(directory);
            });
            fileList.appendChild(createFileButton);

            const uploadFileButton = document.createElement('button');
            uploadFileButton.textContent = '⬆️ Téléverser un fichier';
            uploadFileButton.classList.add('upload-file-button', 'button');
            uploadFileButton.addEventListener('click', function () {
                uploadFile(directory);
            });
            fileList.appendChild(uploadFileButton);

            const createFolderButton = document.createElement('button');
            createFolderButton.textContent = '📁 Créer un dossier';
            createFolderButton.classList.add('create-folder-button', 'button');
            createFolderButton.addEventListener('click', function () {
                createFolder(directory);
            });
            fileList.appendChild(createFolderButton);
        } catch {
            alert('Erreur lors de l\'implémentation des boutons de gestion')
        }
    }

    //UPDATE div buttons
    function removeButtons(id) {
        try {
            var boutons = document.getElementsByClassName(id);
            while (boutons.length > 0) {
                boutons[0].remove();
            }
        } catch {
            alert('Erreur lors de la mise à jour des boutons')
        }
    }
    //UPDATE div files
    function removeItems(id) {
        try {
            var files = document.getElementsByClassName(id);
            while (files.length > 0) {
                files[0].remove();
            }
        } catch {
            alert('Erreur lors de la mise à jour des fichiers')
        }
    }


    //FILE POPUP
    function openFilePopup(name, parent) {
        try {
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');

            const popup = document.createElement('div');
            popup.classList.add('popup');

            const title = document.createElement('h2');
            title.textContent = 'Options pour ' + name;
            popup.appendChild(title);

            const editButton = document.createElement('button');
            editButton.textContent = 'Éditer en ligne';
            editButton.addEventListener('click', function () {
                closePopup(overlay);
                openTextEditor(name, parent);
            });
            popup.appendChild(editButton);

            const downloadButton = document.createElement('button');
            downloadButton.textContent = 'Télécharger';
            downloadButton.addEventListener('click', function () {
                downloadFile(name, parent);
                closePopup(overlay);
            });
            popup.appendChild(downloadButton);

            const moveButton = document.createElement('button');
            moveButton.textContent = 'Déplacer';
            moveButton.addEventListener('click', function () {
                closePopup(overlay);
                moveFile(name, parent);
            });
            popup.appendChild(moveButton);

            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Supprimer';
            deleteButton.classList.add('delete-button');
            deleteButton.addEventListener('click', function () {
                showDeleteConfirmation(name, parent);
                closePopup(overlay);
            });
            popup.appendChild(deleteButton);

            const closeButton = document.createElement('button');
            closeButton.textContent = 'Fermer';
            closeButton.addEventListener('click', function () {
                closePopup(overlay);
            });
            popup.appendChild(closeButton);

            const renameButton = document.createElement('button');
            renameButton.textContent = 'Renommer';
            renameButton.addEventListener('click', function () {
                renameFile(name, parent);
                closePopup(overlay);
            });
            popup.appendChild(renameButton);

            overlay.appendChild(popup);
            document.body.appendChild(overlay);
        } catch {
            alert('Erreur lors de l\'ouverture du menu de gestion du fichier')
        }
    }

    //VERIF DELETE FILE POPUP
    function showDeleteConfirmation(name, parent) {
        try {
            const confirmationOverlay = document.createElement('div');
            confirmationOverlay.classList.add('overlay');

            const confirmationPopup = document.createElement('div');
            confirmationPopup.classList.add('popup');

            const message = document.createElement('p');
            message.textContent = 'Êtes-vous sûr de vouloir supprimer "' + name + '" ?';
            confirmationPopup.appendChild(message);

            const confirmButton = document.createElement('button');
            confirmButton.textContent = 'Oui, supprimer';
            confirmButton.classList.add('confirm-button');
            confirmButton.addEventListener('click', function () {
                deleteFile(name, parent);
                closePopup(confirmationOverlay);
            });
            confirmationPopup.appendChild(confirmButton);

            const cancelButton = document.createElement('button');
            cancelButton.textContent = 'Annuler';
            cancelButton.classList.add('cancel-button');
            cancelButton.addEventListener('click', function () {
                closePopup(confirmationOverlay);
            });
            confirmationPopup.appendChild(cancelButton);

            confirmationOverlay.appendChild(confirmationPopup);
            document.body.appendChild(confirmationOverlay);
        } catch {
            alert('Erreur lors de l\'ouverture de la fenêtre de suppression du fichier')
        }
    }

    //CLOSE POPUP
    function closePopup(overlay) {
        try {
            document.body.removeChild(overlay);
        } catch {
            alert('Erreur lors de la fermeture de la popup')
        }
    }

    //TEXT EDITOR
    function openTextEditor(name, parent) {
        try {
            const Sparent = String(parent);
            const Sname = String(name);
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');

            const editorContainer = document.createElement('div');
            editorContainer.classList.add('popup');

            const title = document.createElement('h2');
            title.textContent = 'Édition de ' + Sname;
            editorContainer.appendChild(title);

            const textArea = document.createElement('textarea');
            textArea.style.display = 'none';
            editorContainer.appendChild(textArea);

            fetch('./main/php/getfilecontent.php?parent=' + encodeURIComponent(Sparent) + '&name=' + encodeURIComponent(Sname), {
                headers: {
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                }
            })// -test -> debug sans mysqli
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement du fichier.');
                    }
                    return response.text();
                })
                .then(content => {
                    const Fcontent = content.replace(/\\n/g, '\n');
                    textArea.value = Fcontent;



                    const codeMirrorEditor = CodeMirror.fromTextArea(textArea, {
                        lineNumbers: true,
                        mode: 'text/plain',
                        theme: 'monokai',
                        tabSize: 2,
                        indentWithTabs: true,
                    });

                    codeMirrorEditor.setSize('100%', '500px');

                    const saveButton = document.createElement('button');
                    saveButton.textContent = 'Enregistrer';
                    saveButton.addEventListener('click', function () {
                        const updatedContent = codeMirrorEditor.getValue();
                        saveFile(Sname, Sparent, updatedContent);
                        closePopup(overlay);
                    });
                    editorContainer.appendChild(saveButton);
                })
                .catch(error => {
                    alert('Erreur lors de la récupération du contenu du fichier');
                    closePopup(overlay);
                });

            const closeButton = document.createElement('button');
            closeButton.textContent = 'Fermer';
            closeButton.addEventListener('click', function () {
                closePopup(overlay);
            });
            editorContainer.appendChild(closeButton);

            overlay.appendChild(editorContainer);
            document.body.appendChild(overlay);
        } catch {
            alert('Erreur lors de l\'ouverture de la popup d\'édition du fichier')
        }
    }

    //UPDATE FILE
    function saveFile(name, parent, content) {
        const Pcontent = content.replace(/\r?\n/g, '\\n');
        if (Pcontent.length < 10000) {
            fetch('./main/php/editFileContent.php?parent=' + encodeURIComponent(parent) + '&name=' + encodeURIComponent(name) + '&content=' + encodeURIComponent(Pcontent), {
                headers: {
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                }
            })
                .then(response => {
                    if (response == "error") {
                        alert('Erreur lors de la modification du fichier.');
                    } else {
                        alert('contenu modifié');
                        logs('updateFile', parent, name, Pcontent);
                    }
                })
        } else {
            alert('contenu trop long (+10000 caractères)');
        }
    }

    //RENAMEFILE
    function renameFile(name, parent) {
        try {
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');

            const popup = document.createElement('div');
            popup.classList.add('popup');

            const title = document.createElement('h2');
            title.textContent = 'Renommer le fichier';
            popup.appendChild(title);

            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Nom du fichier';
            popup.appendChild(input);

            const renameButton = document.createElement('button');
            renameButton.textContent = 'Renommer';
            renameButton.addEventListener('click', function () {
                const fileName = input.value.trim();
                closePopup(overlay);

                fetch('./main/php/renameFile.php?parent=' + encodeURIComponent(parent) + '&name=' + encodeURIComponent(name) + '&fname=' + encodeURIComponent(fileName), {
                    headers: {
                        'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                    }
                })
                    .then(response => response.text())
                    .then(response => {
                        if (response == 'success') {
                            logs('renameFile', parent, fileName, name);
                            getFiles(parent);

                        } else {
                            alert('Erreur lors de la modification du fichier')
                        }
                    })

            });
            popup.appendChild(renameButton);

            const closeButton = document.createElement('button');
            closeButton.textContent = 'Annuler';
            closeButton.addEventListener('click', function () {
                closePopup(overlay);
            });
            popup.appendChild(closeButton);

            overlay.appendChild(popup);
            document.body.appendChild(overlay);
        } catch {
            alert('Erreur lors de la modification du fichier')
        }
    }

    function uploadFile(directory) {
        const overlay = document.createElement('div');
        overlay.classList.add('overlay');

        const popup = document.createElement('div');
        popup.classList.add('popup');

        const title = document.createElement('h2');
        title.textContent = 'Téléverser un fichier';
        popup.appendChild(title);

        const input = document.createElement('input');
        input.type = 'file';
        popup.appendChild(input);

        const uploadButton = document.createElement('button');
        uploadButton.textContent = 'Téléverser';
        uploadButton.addEventListener('click', function () {
            const file = input.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('directory', directory);

                fetch('main/php/uploadFile.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                    },
                }).then(response => response.text())
                    .then(response => {
                        if (response == 'success') {
                            logs('uploadFile', directory, file.name, 'null');
                            getFiles(directory);
                            closePopup(overlay);

                        } else {
                            alert('Erreur lors de l\'upload du fichier :' + response);
                        }
                    }).catch(error => {
                        alert('Erreur lors de l\'upload du fichier ');
                    });
            } else {
                alert('Veuillez sélectionner un fichier.');
            }
        });
        popup.appendChild(uploadButton);

        const closeButton = document.createElement('button');
        closeButton.textContent = 'Annuler';
        closeButton.addEventListener('click', function () {
            closePopup(overlay);
        });
        popup.appendChild(closeButton);

        overlay.appendChild(popup);
        document.body.appendChild(overlay);
    }



    //CREATE FILE
    function createFile(parent) {
        try {
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');

            const popup = document.createElement('div');
            popup.classList.add('popup');

            const title = document.createElement('h2');
            title.textContent = 'Créer un nouveau fichier';
            popup.appendChild(title);

            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Nom du fichier';
            popup.appendChild(input);

            const createButton = document.createElement('button');
            createButton.textContent = 'Créer';
            createButton.addEventListener('click', function () {
                const fileName = input.value.trim();
                closePopup(overlay);

                fetch('./main/php/createFile.php?parent=' + encodeURIComponent(parent) + '&name=' + encodeURIComponent(fileName), {
                    headers: {
                        'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                    }
                })
                    .then(response => response.text())
                    .then(response => {
                        if (response == 'success') {
                            logs('createFile', parent, fileName, 'null');
                            openTextEditor(fileName, parent);
                            getFiles(parent);

                        } else {
                            alert('Erreur lors de la création du fichier')
                        }
                    })

            });
            popup.appendChild(createButton);

            const closeButton = document.createElement('button');
            closeButton.textContent = 'Annuler';
            closeButton.addEventListener('click', function () {
                closePopup(overlay);
            });
            popup.appendChild(closeButton);

            overlay.appendChild(popup);
            document.body.appendChild(overlay);
        } catch {
            alert('Erreur lors de la création du fichier')
        }
    }
    //DOWNLOAD FILE
    function downloadFile(name, parent) {
        try {
            const Sparent = String(parent);
            const Sname = String(name);
            fetch('./main/php/getfilecontent.php?parent=' + encodeURIComponent(Sparent) + '&name=' + encodeURIComponent(Sname), {
                headers: {
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        alert('Erreur lors de la récupération du contenu du fichier')
                    }
                    return response.text();
                })
                .then(content => {
                    const Fcontent = content.replace(/\\n/g, '\n');
                    const blob = new Blob([Fcontent], { type: 'text/plain' });
                    const url = URL.createObjectURL(blob);

                    const a = document.createElement('a');
                    a.href = url;
                    a.download = name;
                    a.click();

                    URL.revokeObjectURL(url);
                    logs('downloadFile', parent, name, 'null');
                });
        } catch {
            alert('Erreur lors de la préparation du téléchargement du fichier')
        }
    }



    //DELETE FILE 
    function deleteFile(name, parent) {
        try {
            fetch('./main/php/deletefile.php?parent=' + encodeURIComponent(parent) + '&name=' + encodeURIComponent(name), {
                headers: {
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                }
            })
                .then(response => response.text())
                .then(response => {
                    if (response == 'success') {
                        logs('deleteFile', parent, name, 'null');
                        getFiles(parent);
                    } else {
                        alert('Erreur lors de la suppression du fichier')
                    }
                })
        } catch {
            alert('Erreur lors de la suppression du fichier')
        }
    }


    //CREATE FOLDER
    function createFolder(parent) {
        try {
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');

            const popup = document.createElement('div');
            popup.classList.add('popup');

            const title = document.createElement('h2');
            title.textContent = 'Créer un nouveau dossier';
            popup.appendChild(title);

            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Nom du dossier';
            popup.appendChild(input);

            const createButton = document.createElement('button');
            createButton.textContent = 'Créer';
            createButton.addEventListener('click', function () {
                const folderName = input.value.trim();
                closePopup(overlay);

                fetch('./main/php/createFolder.php?parent=' + encodeURIComponent(parent) + '&name=' + encodeURIComponent(folderName), {
                    headers: {
                        'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                    }
                })
                    .then(response => response.text())
                    .then(response => {
                        if (response == 'success') {
                            logs('createFolder', parent, folderName, 'null');
                            getFiles(parent);
                        } else if (response == 'folder_already_exist') {
                            alert('Un dossier avec ce nom existe déjà à cet endroit')
                        } else {
                            alert('Erreur lors de la création du dossier')
                        }
                    })
            });
            popup.appendChild(createButton);

            const closeButton = document.createElement('button');
            closeButton.textContent = 'Annuler';
            closeButton.addEventListener('click', function () {
                closePopup(overlay);
            });
            popup.appendChild(closeButton);

            overlay.appendChild(popup);
            document.body.appendChild(overlay);
        } catch {
            alert('Erreur lors de la création du dossier')
        }
    }


    //MOVE FILE
    function moveFile(name, parent) {
        try {
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');

            const popup = document.createElement('div');
            popup.classList.add('popup');

            const title = document.createElement('h2');
            title.textContent = 'Déplacer dans un dossier';
            popup.appendChild(title);

            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Nom du dossier';
            popup.appendChild(input);

            const createButton = document.createElement('button');
            createButton.textContent = 'Déplacer';
            createButton.addEventListener('click', function () {
                const folderName = input.value.trim();
                closePopup(overlay);
                fetch('./main/php/moveFile.php?parent=' + encodeURIComponent(parent) + '&name=' + encodeURIComponent(name) + '&path=' + encodeURIComponent(folderName), {
                    headers: {
                        'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                    }
                })
                    .then(response => response.text())
                    .then(response => {
                        if (response == 'success') {
                            logs('moveFile', parent, name, folderName);
                            getFiles(parent);
                        } else if (response == 'no_change') {
                            alert("Le fichier est déjà à cet endroit")
                        } else if (response == 'name_indisp') {
                            alert("Un fichier avec ce nom existe déjà dans le dossier de destination")
                        } else {
                            alert("Erreur lors du déplacement du fichier")
                        }
                    })
            });
            popup.appendChild(createButton);

            const closeButton = document.createElement('button');
            closeButton.textContent = 'Annuler';
            closeButton.addEventListener('click', function () {
                closePopup(overlay);
            });
            popup.appendChild(closeButton);

            overlay.appendChild(popup);
            document.body.appendChild(overlay);
        } catch {
            alert('Erreur lors du déplacement du fichier')
        }
    }

    //DELETE FOLDER
    function deleteFolder(parent, name) {
        fetch('./main/php/deleteFolder.php?parent=' + encodeURIComponent(parent) + '&name=' + encodeURIComponent(name), {
            headers: {
                'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
            }
        })
            .then(response => response.text())
            .then(response => {
                if (response == 'success') {
                    logs('deleteFolder', parent, name, 'null');
                    getFiles(parent);
                } else {
                    alert("Erreur lors de la suppression du dossier")
                }
            })
    }


    //SHOW DELETE CONFIRMATION FOLDER
    function showDeleteConfirmationFolder(parent, name) {
        try {
            const confirmationOverlay = document.createElement('div');
            confirmationOverlay.classList.add('overlay');

            const confirmationPopup = document.createElement('div');
            confirmationPopup.classList.add('popup');

            const message = document.createElement('p');
            message.textContent = 'Êtes-vous sûr de vouloir supprimer ce dossier ?';
            confirmationPopup.appendChild(message);

            const confirmButton = document.createElement('button');
            confirmButton.textContent = 'Oui, supprimer';
            confirmButton.classList.add('confirm-button');
            confirmButton.addEventListener('click', function () {
                deleteFolder(parent, name);
                closePopup(confirmationOverlay);
            });
            confirmationPopup.appendChild(confirmButton);

            const cancelButton = document.createElement('button');
            cancelButton.textContent = 'Annuler';
            cancelButton.classList.add('cancel-button');
            cancelButton.addEventListener('click', function () {
                closePopup(confirmationOverlay);
            });
            confirmationPopup.appendChild(cancelButton);

            confirmationOverlay.appendChild(confirmationPopup);
            document.body.appendChild(confirmationOverlay);
        } catch {
            alert("Erreur lors de l\'ouverture de la popup de suppression")
        }
    }


    function logs(type, parent, name, content) {
        const Pcontent = content.replace(/\r?\n/g, '\\n');
        if (parent == '/') {
            var path = '/' + name;
        } else {
            var path = parent + '/' + name;
        }
        fetch('./main/php/logs.php?type=' + encodeURIComponent(type) + '&path=' + encodeURIComponent(path) + '&content=' + encodeURIComponent(Pcontent), {
            headers: {
                'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
            }
        })
            .then(response => response.text())
            .then(response => {
                if (response == 'success') {
                } else {
                    alert('Erreur lors de la mise à jour des logs')
                }
            })
    }



    //INITIALISATION
    getFiles(Sparent)

});



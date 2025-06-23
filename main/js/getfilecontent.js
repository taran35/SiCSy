function getfileContent(parent, name) {
    const Sparent = String(parent);
    const Sname = String(name);

    fetch('./php/getfilecontent.php?parent=' + encodeURIComponent(Sparent) + '&name=' + encodeURIComponent(Sname))
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur lors du chargement du fichier.');
        }
        return response.json();
    })
    .then(data => {
        console.log(data)
    })
    .catch(error => {
        console.error('Erreur fetch:', error);
    });
}



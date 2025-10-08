<?php
if (isset($_COOKIE['setup'])) {
    header("Location: index.php");
    exit;
} else {
    $configPath = "./bdd/config.json";
    $json = file_get_contents($configPath);
    $data = json_decode($json, true);
    $etat = $data['etat'];
    if ($etat == "true") {
        setcookie("setup", "true", time() + 604800, "/");
        header("Location: login.php");
        exit;
    }
}

?>




<body>

    <h2>Configuration</h2>
    <div id="switch_container">
        <label class="switch">
            <input type="checkbox" id="switchBtn">
            <span class="slider"></span>
        </label>
        <div id="label_switch">
            <p>Vous avez deux types de Configuration: <br></p>
            <ul>
                <li>
                    <p>Simple (non coché), SiCSy utilise une seule base de donnée</p>
                </li>
                <li>
                    <p>Multiple (coché), SiCSy utilise deux bases de données (fichiers et utilisateurs séparés)</p>
                </li>
            </ul>
        </div>
    </div>
    <div id="simple_bdd">
        <h2>Configuration de la base de données simple: </h2>
        <form action="#" id="form_simple">

            <label for="host">Hôte :</label>
            <input type="text" id="host" name="host" required><br><br>

            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="db_password">Mot de passe :</label>
            <input type="password" id="db_password" name="db_password" required><br><br>

            <label for="dbname">Nom de la base de données :</label>
            <input type="text" id="dbname" name="dbname" required><br><br>

            <input type="submit" value="Configurer BDD Simple">
        </form>
    </div>
    <div id="multiple_bdd">
        <h2>Configuration de la base de données multiple: </h2>
        <form action="#" id="form_multiple">
            <h3> Configuration de la base de données utilisateurs: </h3>
            <label for="u_host">Hôte :</label>
            <input type="text" id="u_host" name="u_host" required><br><br>

            <label for="u_username">Nom d'utilisateur :</label>
            <input type="text" id="u_username" name="u_username" required><br><br>

            <label for="u_password">Mot de passe :</label>
            <input type="password" id="u_password" name="u_password" required><br><br>

            <label for="u_dbname">Nom de la base de données :</label>
            <input type="text" id="u_dbname" name="u_dbname" required><br><br>

            <h3> Configuration de la base de données fichiers: </h3>
            <label for="f_host">Hôte :</label>
            <input type="text" id="f_host" name="f_host" required><br><br>

            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="f_username" name="f_username" required><br><br>

            <label for="password">Mot de passe :</label>
            <input type="password" id="f_password" name="f_password" required><br><br>

            <label for="dbname">Nom de la base de données :</label>
            <input type="text" id="f_dbname" name="f_dbname" required><br><br>

            <input type="submit" value="Configurer BDD multiple">
        </form>
    </div>


    <div id="user">
        <form action="#" id="userform">
            <h2>Création du compte administrateur:</h2>
                <label for="pseudo">Pseudo :</label>
                <input type="text" id="pseudo" placeholder="Pseudo" required />

                <label for="email">Adresse email :</label>
                <input type="email" id="email" placeholder="Adresse email" required />

                <label for="password">Mot de passe (8 caractères minimum) :</label>
                <input type="password" id="password" placeholder="Mot de passe" required minlength="8" autocomplete="new-password"/>

                <label for="password2">Vérifier le mot de passe :</label>
                <input type="password" id="password2" placeholder="Vérifier le mot de passe" required minlength="8" autocomplete="new-password"/>


            <input type="submit" value="Créer le compte admin" />
        </form>
    </div>


    <script>
    fetch('./bdd/config.json')
        .then(response => response.json())
        .then(data => {
            if (data.etape === "0") {
                
            } else if (data.etape === "1") {
                generate_admin();
            }
        })
        .catch(error => console.error('Erreur lors du chargement du fichier de configuration :', error));
</script>



    <script>
        const switchBtn = document.getElementById("switchBtn");
        const multiple_bdd = document.getElementById("multiple_bdd");
        const simple_bdd = document.getElementById("simple_bdd");

        switchBtn.addEventListener("change", () => {
            if (switchBtn.checked) {
                multiple_bdd.style.display = "block";
                simple_bdd.style.display = "none";
            } else {
                multiple_bdd.style.display = "none";
                simple_bdd.style.display = "block";
            }
        });

document.getElementById("userform").addEventListener("submit", async function (e) {
    e.preventDefault();
    const pseudo = document.getElementById("pseudo").value.trim();
    const email = document.getElementById("email").value.trim();    
    const password = document.getElementById("password").value.trim();
    const password2 = document.getElementById("password2").value.trim();
    if (password !== password2) {
        alert("Les mots de passe ne correspondent pas.");
        return;
    }
    try {
        const res = await fetch('init_back.php', {
            method: 'POST',
            headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                 },
            body: JSON.stringify({
                action: 'register_admin',
                pseudo, email, password
            })
        });
        const response = await res.text();
        if (response == "success") {
            alert("Administrateur créé avec succès. Vous pouvez maintenant vous connecter.");
            valid_setup();
            window.location.href = "./admin/login.php";
        } else {
            alert("Erreur lors de la création de l'administrateur: " + response);
        }
    } catch (e) {
        alert("Erreur serveur : " + e);
    }
});

        document.getElementById("form_simple").addEventListener("submit", async function (e) {
            e.preventDefault();

            const host = document.getElementById("host").value.trim();
            const username = document.getElementById("username").value.trim();
            const dbname = document.getElementById("dbname").value.trim();
            const password = document.getElementById("db_password").value.trim();

            if (await test_bdd(host, username, password, dbname)) {
                if (await write_bdd(host, username, password, dbname, 'file_bdd.php')) {
                    if (await write_bdd(host, username, password, dbname, 'account_bdd.php')) {
                        setup_db();
                    } else {
                        alert("Erreur lors de l'écriture dans le fichier account_bdd.php.");
                    }
                } else {
                    alert("Erreur lors de l'écriture dans le fichier file_bdd.php.");
                }
            } else {
                alert("Erreur de connexion à la base de données. Veuillez vérifier vos informations.");
            }
        });




        document.getElementById("form_multiple").addEventListener("submit", async function (e) {
            e.preventDefault();

            const u_host = document.getElementById("u_host").value.trim();
            const u_username = document.getElementById("u_username").value.trim();
            const u_dbname = document.getElementById("u_dbname").value.trim();
            const u_password = document.getElementById("u_password").value.trim();

            const f_host = document.getElementById("f_host").value.trim();
            const f_username = document.getElementById("f_username").value.trim();
            const f_dbname = document.getElementById("f_dbname").value.trim();
            const f_password = document.getElementById("f_password").value.trim();

            if (await test_bdd(u_host, u_username, u_password, u_dbname)) {
                if (await test_bdd(f_host, f_username, f_password, f_dbname)) {
                    if (await write_bdd(u_host, u_username, u_password, u_dbname, 'account_bdd.php')) {
                        if (await write_bdd(f_host, f_username, f_password, f_dbname, 'file_bdd.php')) {
                            setup_db();
                        } else {
                            alert("Erreur lors de l'écriture dans le fichier file_bdd.php.");
                        }
                    } else {
                        alert("Erreur lors de l'écriture dans le fichier account_bdd.php.");
                    }

                } else {
                    alert("Erreur de connexion à la base de données fichiers. Veuillez vérifier vos informations.");
                }
            } else {
                alert("Erreur de connexion à la base de données utilisateurs. Veuillez vérifier vos informations.");
            }
        });

        async function test_bdd(host, user, pass, db) {
            try {
            const res = await fetch('init_back.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                 },
                body: JSON.stringify({
                    action: 'test_bdd',
                    host, user, pass, db
                })
            });
            const data = await res.json();
            return data.success;
             } catch (e) {
        alert("Erreur serveur : " + e);
        return false;
    }
        }

        async function write_bdd(host, user, pass, db, file) {
            try {
            const res = await fetch('init_back.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                 },
                body: JSON.stringify({
                    action: 'write_bdd',
                    host, user, pass, db, file
                })
            });
            const data = await res.json();
            return data.success;
             } catch (e) {
        alert("Erreur serveur : " + e);
        return false;
    }
        }

        async function setup_db() {
            try {
            const res = await fetch('init_back.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                 },
                body: JSON.stringify({
                    action: 'setup_bdd'
                })
            });
            const data = await res.json();
            if (data.success == true) {
                etape_up(); 
                generate_admin();
            } else {
                alert("Erreur lors de la configuration de la base de données: " + data.error);
            }
            return data.success;
             } catch (e) {
        alert("Erreur serveur : " + e);
        return false;
    }
        }

        function generate_admin() {
            const switch_container = document.getElementById("switch_container");
            const multiple_bdd = document.getElementById("multiple_bdd");
            const simple_bdd = document.getElementById("simple_bdd");
            const user = document.getElementById("user");

            multiple_bdd.style.display = "none";
            simple_bdd.style.display = "none";
            switch_container.style.display = "none";
            user.style.display = "block";
        }

        async function etape_up() {
                        try {
            const res = await fetch('init_back.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                 },
                body: JSON.stringify({
                    action: 'etape_up'
                })
            });
             } catch (e) {
        alert("Erreur serveur : " + e);
        return false;
    }
        }

        async function valid_setup() {
                        try {
            const res = await fetch('init_back.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                 },
                body: JSON.stringify({
                    action: 'valid_setup'
                })
            });
             } catch (e) {
        alert("Erreur serveur : " + e);
        return false;
    }
        }
    </script>

</body>



<style>
    #simple_bdd {
        margin-top: 20px;
        padding: 10px;
        background: #e9fbe9;
        border: 1px solid #4CAF50;
        display: block;
    }

    #multiple_bdd {
        margin-top: 20px;
        padding: 10px;
        background: #e9fbe9;
        border: 1px solid #4CAF50;
        display: none;
    }

    #label_switch {
        margin-top: 10px;
        padding-left: 5px;
        background: #e9fbe9;
        border: 1px solid #4CAF50;
    }

    #user {
        margin-top: 10px;
        padding-left: 5px;
        background: #e9fbe9;
        border: 1px solid #4CAF50;
        display: none;
    }

    /* --- Style du switch --- */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #4CAF50;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }
</style>
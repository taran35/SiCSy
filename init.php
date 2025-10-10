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

    <h1> ⚙️ Configuration ⚙️ </h1>

	        <div id="label_switch">
		<div class="switch_container">
			<label class="switch">
				<input type="checkbox" id="switchBtn">
				<span class="slider"></span>
			</label>
		</div>
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
    <div class="bdd" id="bdd">
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
				multiple_bdd.style.opacity = "1";
				multiple_bdd.style.visibility = "visible";

                simple_bdd.style.opacity = "0";
				setTimeout(() => {
				  simple_bdd.style.visibility = "hidden";
				}, 500);
            } else {
                multiple_bdd.style.opacity = "0";
				setTimeout(() => {
				  multiple_bdd.style.visibility = "hidden";
				}, 500);
				
				simple_bdd.style.opacity = "1";
				simple_bdd.style.visibility = "visible";
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
                    'X-CSRF-TOKEN': csrfToken
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
            window.location.href = "./admin/interface/login.php";
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
                    'X-CSRF-TOKEN': csrfToken
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
                    'X-CSRF-TOKEN': csrfToken
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
                    'X-CSRF-TOKEN': csrfToken
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
           const switch_container = document.getElementById("label_switch");
            const multiple_bdd = document.getElementById("multiple_bdd");
            const simple_bdd = document.getElementById("simple_bdd");
            const user = document.getElementById("user");
			const bdd = document.getElementById("bdd");
			

            multiple_bdd.style.display = "none";
            simple_bdd.style.display = "none";
            switch_container.style.display = "none";
			bdd.style.display = "none";
            user.style.display = "block";
        }

        async function etape_up() {
                        try {
            const res = await fetch('init_back.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
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
                    'X-CSRF-TOKEN': csrfToken
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

body {
  background-color: #2E2E2E;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #EEE;
  margin: 0;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 100vh;
}

h1 {
  font-size: 2.5rem;
  margin-bottom: 30px;
  user-select: none;
  text-shadow: 0 0 8px #4CAF50;
}


#label_switch {
  background: #388E3C;
  padding: 20px 25px;
  border-radius: 25px;
  box-shadow: 0 4px 15px rgba(56, 142, 60, 0.7);
  width: 80%;
  margin-bottom: 40px;
  user-select: none;
}

#label_switch p,
#label_switch ul {
  font-size: 1rem;
  line-height: 1.4;
  color: #DFF0D8;
  margin: 10px 0;
}

#label_switch ul {
  padding-left: 20px;
}

#label_switch li {
  margin-bottom: 10px;
}


.switch_container {
  margin-bottom: 15px;
  display: flex;
  justify-content: center;
}

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
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #9E9E9E;
  transition: 0.4s;
  border-radius: 34px;
  box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
  box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}

input:checked + .slider {
  background-color: #4CAF50;
}

input:checked + .slider:before {
  transform: translateX(26px);
}


.bdd {
  position: relative;
  width: 85%;
  height: auto;
  min-height: 400px;
  margin-bottom: 40px;
}


#simple_bdd, #multiple_bdd {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  padding: 25px 30px;
  border-radius: 20px;
  box-shadow: 0 6px 12px rgba(0,0,0,0.4);
  background-color: #424242;
  color: #F0F0F0;
  transition: opacity 0.5s ease;
  overflow-y: auto;
  max-height: 500px;
  box-sizing: border-box;
}


#simple_bdd {
  opacity: 1;
  visibility: visible;
  z-index: 2;
}

#multiple_bdd {
  opacity: 0;
  visibility: hidden;
  z-index: 1;
}


h2, h3 {
  margin-top: 0;
  margin-bottom: 15px;
  font-weight: 600;
  color: #A5D6A7;
}


label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  font-size: 0.95rem;
  color: #C8E6C9;
}

input[type="text"],
input[type="password"],
input[type="email"] {
  width: 100%;
  padding: 8px 12px;
  margin-bottom: 18px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  box-sizing: border-box;
  background-color: #2A2A2A;
  color: #E0E0E0;
  box-shadow: inset 1px 1px 4px rgba(0,0,0,0.7);
  transition: background-color 0.3s ease;
}

input[type="text"]:focus,
input[type="password"]:focus,
input[type="email"]:focus {
  outline: none;
  background-color: #3A3A3A;
  box-shadow: 0 0 8px #4CAF50;
  color: #FFF;
}


input[type="submit"] {
  width: 100%;
  background-color: #4CAF50;
  color: white;
  font-size: 1.1rem;
  font-weight: 600;
  padding: 12px 0;
  border: none;
  border-radius: 15px;
  cursor: pointer;
  box-shadow: 0 5px 15px rgba(76, 175, 80, 0.6);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  margin-top: 10px;
}

input[type="submit"]:hover {
  background-color: #45a049;
  box-shadow: 0 7px 20px rgba(69, 160, 73, 0.8);
}


#user {
  width: 85%;
  background-color: #263238;
  padding: 25px 30px;
  border-radius: 20px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.6);
  color: #B0BEC5;
  display: none;
  user-select: none;
}

#user h2 {
  color: #81C784;
  margin-top: 0;
  margin-bottom: 25px;
}

#userform label {
  color: #90A4AE;
}

#userform input[type="text"],
#userform input[type="email"],
#userform input[type="password"] {
  background-color: #37474F;
  color: #ECEFF1;
  box-shadow: inset 1px 1px 5px rgba(0,0,0,0.7);
}

#userform input[type="text"]:focus,
#userform input[type="email"]:focus,
#userform input[type="password"]:focus {
  background-color: #455A64;
  box-shadow: 0 0 10px #81C784;
  color: #FFF;
}

/* Responsive mobile */
@media (max-width: 400px) {
  body {
    padding: 15px;
  }
  #label_switch, .bdd, #user {
    width: 100%;
  }
}

</style>
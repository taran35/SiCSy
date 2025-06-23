<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<body>
    <header>
        <title>Création de compte</title>
    </header>
    <main>
        <div class="reg">
            <h1> Informations:</h1>
            <hr>
            <br><br>
            <div class="form">
                <form action="#" method="POST" id="registerform">
                    <input type="text" id="pseudo" size="30" placeholder="Pseudo" required />
                    <input type="email" id="email" size="30" placeholder="Adresse email" required />
                    <input type="password" id="password" name="password" minlength="8" placeholder="Mot de passe"
                        required />
                    <input type="password" id="password2" name="password2" minlength="8"
                        placeholder="Vérifier le mot de passe" required />
                    <input type="submit" value="Créer un compte" />
                </form>
            </div>

            <p class="login">Cliquez <a href="./login.php">ici</a> pour vous connecter a votre compte.</p>

        </div>
    </main>
</body>

</html>

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        background: rgba(219, 224, 224, 0.877);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .reg {
        background-color: rgb(134, 136, 151);
        display: flex;
        flex-direction: column;
        align-items: center;
        border: 1px black solid;
        border-radius: 25px;
        box-shadow: 12px 12px 2px 1px rgba(0, 0, 255, 0.2);
        margin: 5%;
        padding: 10%;
        width: 100%;
        text-align: center;
    }

    input[type=email],
    input[type=password],
    input[type=text] {
        width: 80%;
        padding: 10px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        border-radius: 5px;
        align-self: center;
        background-color: rgba(219, 224, 224, 0.877);
    }

    input[type=email] {
        width: 100%;
    }

    input[type=submit] {
        width: 70%;
        border: 1px solid black;
        padding: 7px;
        border-radius: 5px;

    }

    input[type=submit]:hover {
        border: 2px solid black;
        font-weight: bold;
    }

    hr {
        width: 90%;
        border: 1.5px rgb(78, 77, 77) solid;
        border-radius: 1px;
        margin: 15px;
    }

    .login {
        margin-top: 10px;
    }
</style>



<script>

    const form = document.getElementById("registerform").addEventListener("submit", function (e) {
        e.preventDefault();
        const pseudo = document.getElementById("pseudo").value.trim();
        const mail = document.getElementById("email").value.trim();
        const pass = document.getElementById("password").value.trim();
        const pass2 = document.getElementById("password2").value.trim();
        if (pass === pass2) {
            //accountCreate(mail, pass, pseudo);
        } else {
            alert("Les mots de passe ne correspondent pas !");
        }
    });


    function accountCreate(mail, password, pseudo) {
        fetch('account-create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
            },
            body: new URLSearchParams({
                'email': mail,
                'pass': password,
                'pseudo': pseudo
            })
        })
            .then(response => response.text())
            .then(response => {
                if (response == 'success') {
                    console.log('compte créé');
                } else {
                    console.warn('Échec :', response);
                }
            })
            .catch(error => console.error(error));
    }

</script>
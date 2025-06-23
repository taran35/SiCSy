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
            <title>Page de connexion</title>
        </header>
        <main>
            <div class="log">
                <h1> Connection</h1>
                <hr>
                <br><br>
                <div class="form">
                    <form action="#" method="POST" id="loginform">
                    <input type="email" id="email"  size="30" placeholder="Adresse email" required />
                    <br>
                    <input type="password" id="password" name="password" minlength="8" placeholder="Mot de passe" required />
                    <input type="submit" value="Se connecter" />
                    </form>
                </div>

                <p class="lost">Cliquez <a href="./password-change.html">ici</a> pour modifier votre mot de passe.</p>

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
    .log {
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
    input[type=email], input[type=password]{
        width: 80%;
        padding: 10px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        border-radius: 5px;
        align-self: center;
        background-color:  rgba(219, 224, 224, 0.877);
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
    .lost {
        margin-top: 10px;
    }


</style>



<script>

    const form = document.getElementById("loginform").addEventListener("submit", function(e) {
        e.preventDefault();
        const mail = document.getElementById("email").value.trim();
        const pass = document.getElementById("password").value.trim();
        console.log(mail)
        console.log(pass)
        <?php
        $_SESSION['username'] = 'user';
        ?>
        window.location.replace("../../index.php");

        //loginVerif(mail, pass)
    })

function loginVerif(mail, pass) {
    fetch('log.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ 
            'email': mail, 
            'pass': pass,
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('connection effectuée');
            window.location.replace("../../index.php");
            $_SESSION['username'] = $username
        } else {
            console.warn('Échec :', data.message);
        }
    })
    .catch(error => console.error(error));
}        

</script>
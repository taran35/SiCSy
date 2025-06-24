<?php
session_start();
require_once 'adm.php';
?>

<!DOCTYPE html>
<html>

<body>

    <head>
        <meta charset="UTF-8">
        <title>Ajouter un administrateur</title>
    </head>
    <main>
        <div class="log">
            <h1> ajouter un administrateur</h1>
            <hr>
            <br><br>
            <form action="#" method="POST" id="form">
                <div class="form-group">
                    <input type="email" id="email" size="30" placeholder="Adresse email" required />
                    <div id="emailError" class="error-message"></div>
                </div>
                <input type="submit" value="Ajouter" />
                <div id="globalError" class="error-message" style="text-align: center;"></div>
            </form>
        </div>
    </main>
</body>

</html>

<style>
    :root {
        --login-bg: #f0f2f5;
        --card-bg: #ffffff;
        --input-bg: #f9f9f9;
        --input-border: #ccc;
        --input-focus: #007bff;
        --btn-bg: #007bff;
        --btn-bg-hover: #0056b3;
        --btn-text: #ffffff;
        --text-color: #333;
        --link-color: #007bff;
        --link-hover: #0056b3;
    }

    .error-message {
        font-size: 13px;
        color: red;
        margin-top: 5px;
        margin-bottom: 10px;
        text-align: left;
    }

    input.error {
        border-color: red;
    }


    body {
        background-color: var(--login-bg);
        font-family: "Segoe UI", sans-serif;
        color: var(--text-color);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    .log {
        background-color: var(--card-bg);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        text-align: center;
        box-sizing: border-box;
    }


    .log h1 {
        font-size: 28px;
        margin-bottom: 20px;
        color: darkred;
    }

    hr {
        width: 100%;
        border: 0;
        height: 1px;
        background-color: #ddd;
        margin: 20px 0;
    }

    .form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        margin-bottom: 18px;
    }


    input[type="email"] {
        width: 100%;
        padding: 12px 16px;
        margin-bottom: 12px;
        border-radius: 8px;
        border: 1px solid var(--input-border);
        background-color: var(--input-bg);
        font-size: 15px;
        box-sizing: border-box;
    }

    input[type="email"]:focus {
        outline: none;
        border-color: var(--input-focus);
    }

    input[type="submit"] {
        margin-top: 10px;
        padding: 12px;
        font-size: 16px;
        background-color: var(--btn-bg);
        color: var(--btn-text);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
    }

    input[type="submit"]:hover {
        background-color: var(--btn-bg-hover);
    }


    @media (max-width: 480px) {
        .log {
            padding: 25px;
            margin: 20px;
        }
    }
</style>



<script>
    document.getElementById("form").addEventListener("submit", function (e) {
        e.preventDefault();
        clearErrors();

        const mail = document.getElementById("email").value.trim();
        let hasError = false;

        if (mail === "") {
            showError("emailError", "L'adresse email est requise");
            document.getElementById("email").classList.add("error");
            hasError = true;
        }
        if (!validateEmail(email.value)) {
            showError("emailError", "L'adresse email est incorrecte");
            document.getElementById("email").classList.add("error");
            hasError = true;
        }

        if (!hasError) {
            addAdmin(mail);
        }
    });
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    function addAdmin(mail) {
        fetch('admin-add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
            },
            body: new URLSearchParams({
                'mail': mail
            })
        })
            .then(response => response.text())
            .then(response => {
                if (response == "success") {
                    window.location.replace("gestion-admin.php");
                } else {
                    showError("globalError", "Email incorrect.");
                    console.log(response);
                }
            })
            .catch(error => {
                showError("globalError", "Erreur de connexion au serveur.");
                console.error(error);
            });
    }

    function showError(id, message) {
        document.getElementById(id).textContent = message;
    }

    function clearErrors() {
        document.querySelectorAll(".error-message").forEach(el => el.textContent = "");
        document.querySelectorAll("input").forEach(el => el.classList.remove("error"));
    }
</script>
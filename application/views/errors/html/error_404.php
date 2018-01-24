<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <title>Erreur 404</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <style>
        h3 > span {
            vertical-align: middle;
        }

        p {
            margin: 0 0 1rem;
            font-size: 1.6rem;
        }
    </style>
</head>
<body>
    <header>
        <nav><a href="/" class="brand-logo small-caps">Teckmeb</a></nav>
    </header>
    <main class="container section">
        <h3>
            <i class="material-icons medium">error</i>
            <span>Erreur 404</span>
        </h3>
        <p>La page demandée n'existe pas</p>
        <a href="/" class="btn waves-effect waves-light">Revenir à l'acceuil</a>
    </main>
</body>
</html>
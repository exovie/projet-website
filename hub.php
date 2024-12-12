<?php

session_start();

$redirect = $_SESSION['origin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['postdata'] = $_POST;
    // Redirige vers la seconde page ou une page de traitement direct
    header("Location: $redirect"); // Ou simplement utilisez une autre logique ici
    exit();
}
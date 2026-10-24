<?php
require __DIR__ . '/../app/config.php';

if (is_logged_in()) {
    redirect('menu.php');
}

if (is_post()) {
    $email = trim($_POST['email'] ?? '');

    try {
        $user = user_service()->login($email, $_POST['password'] ?? '');
        log_in($user);
        redirect('menu.php');
    } catch (CasalenaException $e) {
        flash('error', $e->getMessage());
        keep_input(['email' => $email]);
        redirect('login.php');
    }
}

render('login', ['title' => 'Iniciar sesión']);

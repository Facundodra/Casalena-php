<?php
require __DIR__ . '/../app/config.php';

if (is_logged_in()) {
    redirect('menu.php');
}

if (is_post()) {
    $input = [
        'type'      => ($_POST['type'] ?? '') === Admin::TYPE ? Admin::TYPE : Customer::TYPE,
        'name'      => trim($_POST['name'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'address'   => trim($_POST['address'] ?? ''),
        'job_title' => trim($_POST['job_title'] ?? ''),
    ];
    // El dato propio del subtipo: cargo si es admin, dirección si es cliente.
    $extra = $input['type'] === Admin::TYPE ? $input['job_title'] : $input['address'];

    try {
        user_service()->register($input['type'], $input['email'], $input['name'], $_POST['password'] ?? '', $extra);
        flash('success', 'Cuenta creada. Ya podés iniciar sesión.');
        keep_input(['email' => $input['email']]);
        redirect('login.php');
    } catch (CasalenaException $e) {
        flash('error', $e->getMessage());
        keep_input($input);
        redirect('register.php');
    }
}

render('register', ['title' => 'Crear cuenta']);

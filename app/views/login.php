<?php /** @var array $old */ ?>

<section class="auth">
    <h1>Iniciar sesión</h1>

    <form class="card form" method="post" action="login.php">
        <label>Email
            <input type="email" name="email" required autocomplete="username"
                   value="<?= e($old['email'] ?? '') ?>">
        </label>

        <label>Contraseña
            <input type="password" name="password" required autocomplete="current-password">
        </label>

        <button class="button block" type="submit"><?= icon('login') ?>Entrar</button>
        <p class="form-footer">¿No tenés cuenta? <a href="register.php">Creá una</a></p>
    </form>

    <aside class="card hint">
        <h2>Usuarios de prueba</h2>
        <p>Administradora: <code>admin@casalena.uy</code> / <code>admin123</code></p>
        <p>Cliente: <code>ana@mail.com</code> / <code>ana123</code></p>
    </aside>
</section>

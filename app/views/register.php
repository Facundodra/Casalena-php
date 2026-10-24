<?php /** @var array $old */ ?>
<?php $type = $old['type'] ?? Customer::TYPE; ?>

<section class="auth">
    <h1>Crear cuenta</h1>

    <form class="card form register-form" method="post" action="register.php">
        <fieldset class="segmented">
            <legend class="visually-hidden">Tipo de cuenta</legend>
            <label><input type="radio" name="type" value="<?= Customer::TYPE ?>"
                <?= $type === Customer::TYPE ? 'checked' : '' ?>> Cliente</label>
            <label><input type="radio" name="type" value="<?= Admin::TYPE ?>"
                <?= $type === Admin::TYPE ? 'checked' : '' ?>> Administrador/a</label>
        </fieldset>

        <label>Nombre
            <input type="text" name="name" required maxlength="100" autocomplete="name"
                   value="<?= e($old['name'] ?? '') ?>">
        </label>

        <label>Email
            <input type="email" name="email" required maxlength="120" autocomplete="email"
                   value="<?= e($old['email'] ?? '') ?>">
        </label>

        <label>Contraseña
            <input type="password" name="password" required minlength="6" autocomplete="new-password">
        </label>

        <!-- Solo se ve el campo del tipo elegido (CSS con :has, sin JavaScript).
             Sin "required": un campo oculto requerido bloquearía el envío.
             Que no esté vacío lo valida el servicio. -->
        <label class="customer-fields">Dirección
            <input type="text" name="address" maxlength="200" autocomplete="street-address"
                   value="<?= e($old['address'] ?? '') ?>">
        </label>

        <label class="admin-fields">Cargo
            <input type="text" name="job_title" maxlength="100"
                   value="<?= e($old['job_title'] ?? '') ?>">
        </label>

        <button class="button block" type="submit"><?= icon('check') ?>Crear cuenta</button>
        <p class="form-footer">¿Ya tenés cuenta? <a href="login.php">Iniciá sesión</a></p>
    </form>
</section>

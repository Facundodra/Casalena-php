<?php
/** @var array<int, array{item: MenuItem, quantity: int}> $lines */
/** @var string $address */
$total = 0.0;
foreach ($lines as $line) {
    $total += $line['item']->price * $line['quantity'];
}
?>

<div class="page-narrow wide">
    <?= back_link() ?>
    <h1>Mi carrito</h1>

    <?php if ($lines === []): ?>
        <div class="card empty-state">
            <p>Tu carrito está vacío.</p>
            <a class="button" href="menu.php">Ver la carta</a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th class="number">Precio</th>
                    <th class="center">Cantidad</th>
                    <th class="number">Subtotal</th>
                    <th class="actions"><span class="visually-hidden">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lines as ['item' => $item, 'quantity' => $quantity]): ?>
                    <tr>
                        <td data-label="Nombre">
                            <a class="item-name" href="item.php?id=<?= $item->id ?>"><?= e($item->name) ?></a>
                        </td>
                        <td data-label="Precio" class="number"><?= money($item->price) ?></td>
                        <td data-label="Cantidad" class="center">
                            <!-- Dos botones, un formulario: cada uno manda la cantidad nueva -->
                            <form class="stepper" method="post" action="cart_update.php">
                                <input type="hidden" name="menu_item_id" value="<?= $item->id ?>">
                                <button type="submit" name="quantity" value="<?= $quantity - 1 ?>" aria-label="Uno menos">
                                    <?= icon('minus') ?>
                                </button>
                                <span><?= $quantity ?></span>
                                <button type="submit" name="quantity" value="<?= $quantity + 1 ?>" aria-label="Uno más"
                                    <?= $quantity >= CART_MAX_QUANTITY ? 'disabled' : '' ?>>
                                    <?= icon('plus') ?>
                                </button>
                            </form>
                        </td>
                        <td data-label="Subtotal" class="number price"><?= money($item->price * $quantity) ?></td>
                        <td class="actions">
                            <form method="post" action="cart_update.php">
                                <input type="hidden" name="menu_item_id" value="<?= $item->id ?>">
                                <input type="hidden" name="quantity" value="0">
                                <button class="button danger small" type="submit"><?= icon('trash') ?>Quitar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- El formulario solo manda cómo se entrega: los ítems están en la sesión
             y los precios se releen de la base al confirmar. -->
        <form class="card checkout" method="post" action="checkout.php">
            <div class="checkout-total">
                <span>Total</span>
                <strong><?= money($total) ?></strong>
            </div>

            <fieldset class="delivery-options">
                <legend>¿Cómo lo recibís?</legend>
                <label class="option-card">
                    <input type="radio" name="delivery_mode" value="<?= DeliveryMode::DELIVERY->value ?>" checked>
                    <span>
                        <strong><?= e(DeliveryMode::DELIVERY->label()) ?></strong>
                        <small><?= e($address) ?></small>
                    </span>
                </label>
                <label class="option-card">
                    <input type="radio" name="delivery_mode" value="<?= DeliveryMode::PICKUP->value ?>">
                    <span>
                        <strong><?= e(DeliveryMode::PICKUP->label()) ?></strong>
                        <small>Te avisamos cuando esté listo</small>
                    </span>
                </label>
            </fieldset>

            <p class="muted payment-note">Pagás al recibir: efectivo o tarjeta.</p>

            <button class="button block" type="submit"><?= icon('check') ?>Confirmar pedido</button>
        </form>
    <?php endif; ?>
</div>

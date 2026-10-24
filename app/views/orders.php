<?php /** @var Order[] $orders */ ?>

<div class="page-narrow wide">
    <?= back_link() ?>
    <h1>Mis pedidos</h1>

    <?php if ($orders === []): ?>
        <div class="card empty-state">
            <p>Todavía no hiciste ningún pedido.</p>
            <a class="button" href="menu.php">Ver la carta</a>
        </div>
    <?php endif; ?>

    <?php foreach ($orders as $order): ?>
        <article class="card order">
            <header class="order-header">
                <div>
                    <h2>Pedido #<?= $order->id ?></h2>
                    <p class="muted"><?= e(format_date($order->createdAt, true)) ?></p>
                </div>
                <p class="tag"><?= e($order->deliveryMode->label()) ?></p>
            </header>

            <!-- Nombre y precio son los del momento de la compra: no cambian después -->
            <table class="data-table compact">
                <thead>
                    <tr>
                        <th>Ítem</th>
                        <th class="number">Precio</th>
                        <th class="center">Cantidad</th>
                        <th class="number">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order->lines as $line): ?>
                        <tr>
                            <td data-label="Ítem"><?= e($line->itemName) ?></td>
                            <td data-label="Precio" class="number"><?= money($line->unitPrice) ?></td>
                            <td data-label="Cantidad" class="center"><?= $line->quantity ?></td>
                            <td data-label="Subtotal" class="number price"><?= money($line->subtotal()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p class="order-total">Total <strong><?= money($order->total()) ?></strong></p>
        </article>
    <?php endforeach; ?>
</div>

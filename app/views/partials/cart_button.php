<?php
/**
 * Agregar al carrito (una unidad). Solo viaja el id: nunca el precio.
 * @var MenuItem $item
 * @var string $buttonSize  '' | 'small'
 */
?>
<form method="post" action="cart_add.php">
    <input type="hidden" name="menu_item_id" value="<?= $item->id ?>">
    <input type="hidden" name="return" value="<?= e($_SERVER['REQUEST_URI']) ?>">
    <button class="button <?= e($buttonSize) ?>" type="submit"><?= icon('cart') ?>Agregar</button>
</form>

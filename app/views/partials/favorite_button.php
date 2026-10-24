<?php
/**
 * Botón de favorito: agrega o quita según el estado actual.
 * @var MenuItem $item
 * @var bool $isFavorite
 * @var string $buttonSize  '' | 'small'
 */
?>
<form method="post" action="<?= $isFavorite ? 'favorite_remove.php' : 'favorite_add.php' ?>">
    <input type="hidden" name="menu_item_id" value="<?= $item->id ?>">
    <input type="hidden" name="return" value="<?= e($_SERVER['REQUEST_URI']) ?>">
    <button class="button secondary <?= e($buttonSize) ?> favorite-button<?= $isFavorite ? ' is-favorite' : '' ?>"
            type="submit" aria-pressed="<?= $isFavorite ? 'true' : 'false' ?>"
            title="<?= $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' ?>">
        <?= icon('heart') ?><?= $isFavorite ? 'Favorito' : 'Guardar' ?>
    </button>
</form>

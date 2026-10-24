<?php /** @var Favorite[] $favorites */ ?>

<div class="page-narrow wide">
    <?= back_link() ?>
    <h1>Mis favoritos</h1>

    <?php if ($favorites === []): ?>
        <div class="card empty-state">
            <p>Todavía no guardaste ningún favorito.</p>
            <a class="button" href="menu.php">Ver la carta</a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Sección</th>
                    <th class="number">Precio</th>
                    <th>Guardado el</th>
                    <th class="actions"><span class="visually-hidden">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($favorites as $favorite): ?>
                    <?php $item = $favorite->menuItem; ?>
                    <tr>
                        <td data-label="Nombre">
                            <a class="item-name" href="item.php?id=<?= $item->id ?>"><?= e($item->name) ?></a>
                        </td>
                        <td data-label="Sección"><?= e($item->section()->title()) ?></td>
                        <td data-label="Precio" class="number price"><?= money($item->price) ?></td>
                        <td data-label="Guardado el"><?= e(format_date($favorite->markedOn)) ?></td>
                        <td class="actions">
                            <form method="post" action="favorite_remove.php">
                                <input type="hidden" name="menu_item_id" value="<?= $item->id ?>">
                                <button class="button danger small" type="submit"><?= icon('close') ?>Quitar</button>
                            </form>
                            <?php $buttonSize = 'small'; require __DIR__ . '/partials/cart_button.php'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

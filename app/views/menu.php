<?php
/** @var array<int, array{section: MenuSection, items: MenuItem[]}> $sections */
/** @var string $sort */
/** @var string $search */
/** @var Availability|null $mealTime */
/** @var int[] $favoriteIds */

// Los filtros activos, para mostrarlos debajo del formulario.
$activeFilters = [];
if ($search !== '') {
    $activeFilters[] = '«' . $search . '»';
}
if ($mealTime !== null) {
    $activeFilters[] = $mealTime->label();
}
?>

<section class="menu-hero">
    <h1>La carta</h1>
    <p class="muted">Cocina casera a la leña, de mediodía a medianoche.</p>
</section>

<!-- Formulario GET: los filtros quedan en la URL, se pueden refrescar y compartir -->
<form class="filters" method="get" action="menu.php" role="search">
    <input type="search" name="q" placeholder="Buscar en la carta…" aria-label="Buscar en la carta"
           value="<?= e($search) ?>">

    <select name="meal" aria-label="Horario">
        <option value="">Todo</option>
        <?php foreach ([Availability::LUNCH, Availability::DINNER] as $option): ?>
            <option value="<?= $option->value ?>" <?= $mealTime === $option ? 'selected' : '' ?>>
                <?= e($option->label()) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="sort" aria-label="Ordenar">
        <option value="name"  <?= $sort === 'name'  ? 'selected' : '' ?>>Por nombre</option>
        <option value="price" <?= $sort === 'price' ? 'selected' : '' ?>>Por precio</option>
    </select>

    <button class="button" type="submit"><?= icon('filter') ?>Aplicar</button>
</form>

<?php if ($activeFilters !== []): ?>
    <p class="search-status">
        Filtrando por <?= e(implode(' · ', $activeFilters)) ?> ·
        <a href="<?= e('menu.php?sort=' . $sort) ?>">Limpiar</a>
    </p>
<?php endif; ?>

<?php if ($sections === []): ?>
    <p class="empty">No encontramos nada con esos filtros.</p>
<?php endif; ?>

<?php foreach ($sections as $group): ?>
    <section class="menu-section">
        <h2><?= e($group['section']->title()) ?></h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Disponible</th>
                    <th>Detalle</th>
                    <th class="number">Precio</th>
                    <th class="actions"><span class="visually-hidden">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($group['items'] as $item): ?>
                    <tr>
                        <td data-label="Nombre">
                            <a class="item-name" href="item.php?id=<?= $item->id ?>"><?= e($item->name) ?></a>
                        </td>
                        <td data-label="Disponible"><?= e($item->availability->label()) ?></td>
                        <td data-label="Detalle"><?= e(item_details($item)) ?></td>
                        <td data-label="Precio" class="number price"><?= money($item->price) ?></td>
                        <td class="actions">
                            <?php if (is_admin()): ?>
                                <a class="button secondary small" href="item_edit.php?id=<?= $item->id ?>"><?= icon('edit') ?>Editar</a>
                                <form method="post" action="item_delete.php">
                                    <input type="hidden" name="id" value="<?= $item->id ?>">
                                    <button class="button danger small" type="submit"><?= icon('trash') ?>Eliminar</button>
                                </form>
                            <?php elseif (is_customer()): ?>
                                <?php
                                $isFavorite = in_array($item->id, $favoriteIds, true);
                                $buttonSize = 'small';
                                require __DIR__ . '/partials/favorite_button.php';
                                require __DIR__ . '/partials/cart_button.php';
                                ?>
                            <?php else: ?>
                                <a class="button secondary small" href="item.php?id=<?= $item->id ?>">Ver<?= icon('next') ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php endforeach; ?>

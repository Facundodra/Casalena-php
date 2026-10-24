<?php /** @var MenuItem $item */ /** @var bool $isFavorite */ ?>

<div class="page-narrow">
    <?= back_link() ?>

    <article class="card item-detail">
        <header class="item-detail-header">
            <div>
                <p class="tag"><?= e($item->section()->title()) ?></p>
                <h1><?= e($item->name) ?></h1>
            </div>
            <p class="big-price"><?= money($item->price) ?></p>
        </header>

        <dl class="facts">
            <div>
                <dt>Tipo</dt>
                <dd><?= e($item->typeLabel()) ?></dd>
            </div>
            <div>
                <dt>Disponible</dt>
                <dd><?= e($item->availability->label()) ?></dd>
            </div>

            <?php if ($item instanceof Dish): ?>
                <div>
                    <dt>Categoría</dt>
                    <dd><?= e($item->category->label()) ?></dd>
                </div>
                <div>
                    <dt>Calorías</dt>
                    <dd><?= $item->calories ?> kcal</dd>
                </div>
            <?php endif; ?>

            <?php if ($item instanceof Drink): ?>
                <div>
                    <dt>Con alcohol</dt>
                    <dd><?= $item->alcoholic ? 'Sí' : 'No' ?></dd>
                </div>
                <div>
                    <dt>Contenido</dt>
                    <dd><?= $item->milliliters ?> ml</dd>
                </div>
            <?php endif; ?>
        </dl>

        <?php if (is_customer()): ?>
            <footer class="card-actions">
                <?php $buttonSize = ''; require __DIR__ . '/partials/favorite_button.php'; ?>

                <form class="quantity-form" method="post" action="cart_add.php">
                    <input type="hidden" name="menu_item_id" value="<?= $item->id ?>">
                    <input type="hidden" name="return" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                    <label>
                        <span class="visually-hidden">Cantidad</span>
                        <input type="number" name="quantity" value="1" min="1" max="<?= CART_MAX_QUANTITY ?>">
                    </label>
                    <button class="button" type="submit"><?= icon('cart') ?>Agregar al carrito</button>
                </form>
            </footer>
        <?php endif; ?>

        <?php if (is_admin()): ?>
            <footer class="card-actions">
                <form method="post" action="item_delete.php">
                    <input type="hidden" name="id" value="<?= $item->id ?>">
                    <button class="button danger" type="submit"><?= icon('trash') ?>Eliminar</button>
                </form>
                <a class="button" href="item_edit.php?id=<?= $item->id ?>"><?= icon('edit') ?>Editar</a>
            </footer>
        <?php endif; ?>
    </article>
</div>

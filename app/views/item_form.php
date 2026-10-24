<?php
/**
 * Un formulario para el alta y la edición.
 * @var string $title
 * @var string $action   item_new.php | item_edit.php
 * @var bool $editing
 * @var int|null $id
 * @var array $values    valores iniciales (vacío en el alta)
 * @var array $old       lo tipeado antes de un error: tiene prioridad
 */
$value = fn(string $field): string => $old[$field] ?? $values[$field] ?? '';
$type  = $value('type') !== '' ? $value('type') : Dish::TYPE;
?>

<div class="page-narrow">
    <?= back_link() ?>

    <form class="card form item-form" method="post" action="<?= e($action) ?>">
        <header class="card-header">
            <h1><?= e($title) ?></h1>
            <?php if ($editing): ?>
                <p class="tag"><?= $type === Dish::TYPE ? 'Plato' : 'Bebida' ?></p>
            <?php endif; ?>
        </header>

        <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= (int) $id ?>">
            <!-- El tipo no se edita; si alguien lo cambia, el servicio lo rechaza -->
            <input type="hidden" name="type" value="<?= e($type) ?>">
        <?php else: ?>
            <fieldset class="segmented">
                <legend class="visually-hidden">Tipo</legend>
                <label><input type="radio" name="type" value="<?= Dish::TYPE ?>"
                    <?= $type === Dish::TYPE ? 'checked' : '' ?>> Plato</label>
                <label><input type="radio" name="type" value="<?= Drink::TYPE ?>"
                    <?= $type === Drink::TYPE ? 'checked' : '' ?>> Bebida</label>
            </fieldset>
        <?php endif; ?>

        <div class="form-grid">
            <label class="span-2">Nombre
                <input type="text" name="name" required maxlength="100" value="<?= e($value('name')) ?>">
            </label>

            <label>Precio
                <input type="number" name="price" required min="1" step="0.01" value="<?= e($value('price')) ?>">
            </label>

            <label>Disponibilidad
                <select name="availability">
                    <?php foreach (Availability::cases() as $availability): ?>
                        <option value="<?= $availability->value ?>"
                            <?= $value('availability') === $availability->value ? 'selected' : '' ?>>
                            <?= e($availability->label()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <?php if (!$editing || $type === Dish::TYPE): ?>
            <section class="subtype-panel dish-fields">
                <h2>Datos del plato</h2>
                <div class="form-grid">
                    <label>Categoría
                        <select name="category">
                            <?php foreach (Category::cases() as $category): ?>
                                <option value="<?= $category->value ?>"
                                    <?= $value('category') === $category->value ? 'selected' : '' ?>>
                                    <?= e($category->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Calorías
                        <input type="number" name="calories" min="1" value="<?= e($value('calories')) ?>">
                    </label>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!$editing || $type === Drink::TYPE): ?>
            <section class="subtype-panel drink-fields">
                <h2>Datos de la bebida</h2>
                <div class="form-grid">
                    <label>Mililitros
                        <input type="number" name="milliliters" min="1" value="<?= e($value('milliliters')) ?>">
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="alcoholic" value="1" <?= $value('alcoholic') === '1' ? 'checked' : '' ?>>
                        Tiene alcohol
                    </label>
                </div>
            </section>
        <?php endif; ?>

        <footer class="card-actions">
            <a class="button secondary" href="menu.php"><?= icon('close') ?>Cancelar</a>
            <button class="button" type="submit">
                <?= icon($editing ? 'check' : 'plus') ?><?= $editing ? 'Guardar cambios' : 'Agregar a la carta' ?>
            </button>
        </footer>
    </form>
</div>

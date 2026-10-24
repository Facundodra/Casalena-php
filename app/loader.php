<?php
// Todas las clases del proyecto, en orden: los padres antes que los hijos.

declare(strict_types=1);

// Excepciones: la madre primero
require_once __DIR__ . '/exceptions/CasalenaException.php';
require_once __DIR__ . '/exceptions/DuplicateEmailException.php';
require_once __DIR__ . '/exceptions/InvalidCredentialsException.php';
require_once __DIR__ . '/exceptions/DuplicateMenuItemException.php';
require_once __DIR__ . '/exceptions/MenuItemNotFoundException.php';
require_once __DIR__ . '/exceptions/MenuItemInOrderException.php';
require_once __DIR__ . '/exceptions/DuplicateFavoriteException.php';
require_once __DIR__ . '/exceptions/EmptyCartException.php';
require_once __DIR__ . '/exceptions/MenuItemTypeChangeException.php';
require_once __DIR__ . '/exceptions/UserNotFoundException.php';
require_once __DIR__ . '/exceptions/InvalidDataException.php';

// Dominio: enumerados
require_once __DIR__ . '/domain/MenuSection.php';
require_once __DIR__ . '/domain/Availability.php';
require_once __DIR__ . '/domain/Category.php';
require_once __DIR__ . '/domain/DeliveryMode.php';

// Dominio: usuarios
require_once __DIR__ . '/domain/User.php';
require_once __DIR__ . '/domain/Admin.php';
require_once __DIR__ . '/domain/Customer.php';

// Dominio: carta
require_once __DIR__ . '/domain/MenuItem.php';
require_once __DIR__ . '/domain/Dish.php';
require_once __DIR__ . '/domain/Drink.php';

// Dominio: favoritos y pedidos
require_once __DIR__ . '/domain/Favorite.php';
require_once __DIR__ . '/domain/OrderLine.php';
require_once __DIR__ . '/domain/Order.php';

// Repositorios: todo el SQL vive acá
require_once __DIR__ . '/repositories/MenuItemMapper.php';
require_once __DIR__ . '/repositories/UserRepository.php';
require_once __DIR__ . '/repositories/MenuItemRepository.php';
require_once __DIR__ . '/repositories/FavoriteRepository.php';
require_once __DIR__ . '/repositories/OrderRepository.php';

// Servicios: las reglas de negocio
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/MenuService.php';
require_once __DIR__ . '/services/FavoriteService.php';
require_once __DIR__ . '/services/OrderService.php';

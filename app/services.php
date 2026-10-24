<?php
// Fábricas de servicios: cada página pide el servicio que necesita en una línea,
// sin repetir cómo se arma con sus repositorios.

declare(strict_types=1);

function user_service(): UserService
{
    return new UserService(new UserRepository(connection()));
}

function menu_service(): MenuService
{
    return new MenuService(new MenuItemRepository(connection()));
}

function favorite_service(): FavoriteService
{
    return new FavoriteService(new FavoriteRepository(connection()), new MenuItemRepository(connection()));
}

function order_service(): OrderService
{
    return new OrderService(new OrderRepository(connection()), new MenuItemRepository(connection()));
}

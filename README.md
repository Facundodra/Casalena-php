# Casaleña — aplicación web de un restaurante

Laboratorio del Taller de Desarrollo de Sitios Web con PHP: la carta de un restaurante,
que los administradores gestionan y los clientes consultan, marcan como favorita y
usan para hacer pedidos.

**Stack:** PHP 8.3 · Apache · MySQL 8 · PDO con *prepared statements* · sesiones ·
Docker Compose · CSS propio. Sin frameworks, sin Composer, sin JavaScript.

## Levantar todo

```bash
# 1. Levantar los contenedores (la primera vez construye la imagen y crea el esquema)
docker compose up -d --build

# 2. Cargar los datos de ejemplo (idempotente: si ya hay datos, no toca nada)
docker compose exec web php /var/www/app/data/seed.php
```

| Qué | Dónde |
|---|---|
| La aplicación | http://localhost:8090 |
| phpMyAdmin (mirar la base) | http://localhost:8091 — usuario `casalena`, contraseña `casalena` |

Para apagar: `docker compose down`. Para borrar todo y arrancar de cero:
`docker compose down -v` (y repetir los pasos 1 y 2).

## Usuarios de prueba

| Usuario | Contraseña | Qué es |
|---|---|---|
| `admin@casalena.uy` | `admin123` | Administradora: gestiona la carta |
| `ana@mail.com` | `ana123` | Cliente, con 3 favoritos y 2 pedidos |
| `luis@mail.com` | `luis123` | Cliente, con 1 favorito y 1 pedido |

## Datos precargados: cada uno prueba algo

16 ítems en 5 secciones, 4 favoritos y 3 pedidos.

| Para probar | Con qué |
|---|---|
| Las secciones de la carta | Entradas, Principales, Postres, Bebidas sin y con alcohol |
| Ordenar por precio y por nombre | en Principales, por nombre va primero *Asado de tira* y por precio *Ñoquis caseros* |
| Buscar sin distinguir mayúsculas | "DULCE" encuentra los 2 postres con dulce de leche |
| Filtrar por horario | "Almuerzo" muestra *Ñoquis caseros* (almuerzo) y *Milanesa* (todo el día), no *Asado de tira* (cena) |
| La herencia en pantalla | platos (categoría, calorías) y bebidas (alcohólica, mililitros) |
| "Más reciente primero" | los pedidos de Ana son de hace 10 días y hace 2 |
| Que lo pedido no se puede eliminar | *Milanesa a la napolitana* está en un pedido |
| La cascada de favoritos al eliminar | *Chajá* es favorito de Ana y no está en ningún pedido |
| Eliminar sin consecuencias | *Tortilla de papas* no tiene favoritos ni pedidos |
| El precio histórico | la milanesa se pidió a **$540** y hoy vale **$590**: el historial de Ana no cambió |
| El control de acceso | con la sesión de Ana, escribir a mano `localhost:8090/item_new.php` → vuelve a la carta con un aviso |

## Casos de uso

| # | Caso de uso | Quién | Dónde |
|---|---|---|---|
| 1 | Registrar un usuario (administrador o cliente) | cualquiera | `register.php` |
| 2 | Iniciar sesión (contraseñas con `password_hash`) | cualquiera | `login.php`, `logout.php` |
| 3 | Agregar un ítem a la carta | administrador | `item_new.php` |
| 4 | Modificar un ítem | administrador | `item_edit.php` |
| 5 | Eliminar un ítem | administrador | `item_delete.php` |
| 6 | Ver la carta | cualquiera | `menu.php` |
| Opcional 1 | Favoritos, con la fecha en que se marcó | cliente | `favorites.php`, `favorite_add.php`, `favorite_remove.php` |
| Opcional 2 | Ordenar la carta por precio y por nombre | cualquiera | `menu.php?sort=` |
| Opcional 3 | Buscar por parte del nombre, sin distinguir mayúsculas | cualquiera | `menu.php?q=` |
| Opcional 4 | Detalle con los datos propios de su tipo | cualquiera | `item.php` |
| Opcional 5 | Carrito, confirmar el pedido e historial | cliente | `cart.php`, `cart_add.php`, `cart_update.php`, `checkout.php`, `orders.php` |
| Opcional 6 | Uso cómodo en el teléfono | cualquiera | bloque `@media` de `styles.css` |

Agregados propios: filtro por horario en la carta (`menu.php?meal=`), modalidad de
entrega (envío a domicilio o retiro en el local) y tema claro/oscuro (`theme.php`).

## Estructura

```
docker-compose.yml    web (PHP + Apache) + db (MySQL) + phpMyAdmin
Dockerfile            php:8.3-apache + pdo_mysql
sql/01-schema.sql     el esquema: corre solo la primera vez que se crea el volumen
public/               lo ÚNICO accesible por URL: una página por pantalla + styles.css
app/                  fuera del alcance de las URLs (solo por require)
  config.php            punto de entrada de toda página: sesión + requires
  settings.php          datos de conexión a la base
  loader.php            la lista de clases, en orden
  connection.php        connection(): PDO
  session.php           usuario logueado, control de acceso (require_*), flash, carrito
  view.php              render(), e() (escape de HTML), formatos, íconos
  services.php          fábricas de servicios
  forms.php             formulario de ítem → objeto del dominio
  domain/               clases y enumerados
  exceptions/           una excepción por regla de negocio
  repositories/         TODO el SQL, con prepared statements
  services/             las reglas de negocio
  views/                el HTML (partials/ + una vista por pantalla)
  data/seed.php         datos de ejemplo
  tests/                verificaciones por consola (no forman parte de la aplicación)
```

La regla de dependencia: **página → servicio → repositorio → base**. El SQL vive solo en
`repositories/`; las reglas, solo en `services/`; el control de acceso, solo en `session.php`.

## Verificar sin navegador

```bash
# Las reglas de negocio sobre los datos de ejemplo (deja la base como estaba)
docker compose exec web php /var/www/app/tests/smoke_test.php

# Los repositorios y las restricciones de la base, con datos propios que borra al terminar
docker compose exec web php /var/www/app/tests/repositories_check.php
```

## Nombres: la letra y el código

El código, las tablas y las columnas están en inglés; la interfaz, en español.

| En la letra | En el código | En la base |
|---|---|---|
| carpeta `publico/` | carpeta `public/` | — |
| columna discriminadora `tipo` | `type()` | columna `type` |
| `Usuario` → `Administrador` / `Cliente` | `User` → `Admin` / `Customer` | `users` |
| `cargo` / `direccion` | `jobTitle` / `address` | `job_title` / `address` |
| `Menu` → `Plato` / `Bebida` | `MenuItem` → `Dish` / `Drink` | `menu_items` |
| `calorias` / `alcoholica`, `mililitros` | `calories` / `alcoholic`, `milliliters` | ídem |
| `Disponibilidad` {ALMUERZO, CENA, TODO_EL_DIA} | `Availability` {LUNCH, DINNER, ALL_DAY} | `availability` |
| `Favorito` (cliente, menú, fecha) | `Favorite` | `favorites` |
| `Pedido` + `LineaPedido` | `Order` + `OrderLine` | `orders` + `order_lines` |

Enumerados agregados: `Category` {STARTER, MAIN, DESSERT} para los platos y
`DeliveryMode` {DELIVERY, PICKUP} para los pedidos.

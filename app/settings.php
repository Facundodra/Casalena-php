<?php
// Datos de conexión. Los usan las páginas (vía config.php) y los scripts de consola.
// Dentro de Docker el host es "db": el nombre del servicio en docker-compose.yml.

declare(strict_types=1);

const DB_HOST     = 'db';
const DB_NAME     = 'casalena';
const DB_USER     = 'casalena';
const DB_PASSWORD = 'casalena';

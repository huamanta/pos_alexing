# POS Alexing

Sistema Punto de Venta desarrollado en PHP con MVC.

## Requisitos

- PHP 7.x / 8.x
- MySQL
- XAMPP o entorno similar

## Copiar .env.example a .env
```
copy .env.example .env
```

## Composer
```
> composer install
```

## Después de eso configura tus datos de conexión en .env
```
DB_HOST=127.0.0.1
DB_DATABASE=db_name
DB_USERNAME=root
DB_PASSWORD=
```

## Extensiones a habilitar

### Para generar PDFs
Habilita la extensión `intl` en tu archivo `php.ini`. Quita el punto y coma (`;`) del principio de la línea correcta según tu versión de PHP:

- `extension=intl` (PHP 7 y superiores)
- `extension=php_intl.dll` (versiones antiguas de Windows/PHP)


## Instrucciones de uso

1. Copia el proyecto a tu servidor local (por ejemplo, `c:\xampp\htdocs\pos_alexing`).
2. Configura `configuraciones/local.php` con tus credenciales de base de datos.
3. Abre el navegador en `http://localhost/pos_alexing`.
4. Ve a los módulos en el menú y usa los formularios para crear, editar, activar/desactivar registros.

## Notas

- Los módulos usan AJAX y DataTables para listar y administrar registros.
- El campo de estado puede variar entre `condicion` y `estado` según la tabla.
- Si necesitas integrar el módulo al menú principal, agrega el enlace en `vistas/modulos/menu.php`.


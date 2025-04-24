# Darp5756/PyDolar

## Instalación

1. Instala el paquete mediante Composer:
```bash
composer require darp5756/pydolar
```

## Configuración

Este paquete incluye dos archivos de configuración, uno normal y otro específico para pruebas.

**Publicar la configuración normal:**

Para publicar el archivo de configuración principal (`config/pydolar.php`), ejecuta el siguiente comando:
```bash
php artisan vendor:publish --tag="pydolar-config"
```

**Publicar la configuración de pruebas:**

Para publicar el archivo de configuración de pruebas (`config/pydolar-test.php`), ejecuta el siguiente comando:
```bash
php artisan vendor:publish --tag="pydolar-test-config"
```

## Créditos

Este paquete utiliza la API proporcionada por [fcoagz](https://github.com/fcoagz) en el proyecto [api-pydolarvenezuela](https://github.com/fcoagz/api-pydolarvenezuela).

- API: [pydolarve.org](https://pydolarve.org/)
- Documentación: [docs.pydolarve.org](https://docs.pydolarve.org/)
- Swagger API: [pydolarve.org/apidocs](https://pydolarve.org/apidocs)

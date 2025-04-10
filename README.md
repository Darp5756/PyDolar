# Darp5756/PyDolar

## Instalación

1. Instala el paquete mediante Composer:
```bash
composer require darp5756/pydolar
```

## Configuración

Para utilizar algunas funciones de la api, es necesario contar con el token de acceso.
Agrega la siguiente variable de entorno en el archivo `.env`:

```
PYDOLAR_TOKEN=token
```

Las consultas no tienen timeout definido. Puedes pasarlo como parámetro a las funciones
o establecer uno global como variable de entorno en el archivo `.env`:

```
PYDOLAR_TIMEOUT=segundos
```

## Créditos

Este paquete utiliza la API proporcionada por [fcoagz](https://github.com/fcoagz) en el proyecto [api-pydolarvenezuela](https://github.com/fcoagz/api-pydolarvenezuela).

- API: [pydolarve.org](https://pydolarve.org/)
- Documentación: [docs.pydolarve.org](https://docs.pydolarve.org/)
- Swagger API: [pydolarve.org/apidocs](https://pydolarve.org/apidocs)

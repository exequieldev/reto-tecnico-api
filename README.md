# Reto Técnico  -  Backbone Systems

Para abordar el reto técnico se han tenido en cuenta las siguientes materiales.

Materiales:

- Api:  <https://jobs.backbonesystems.io/api/zip-codes/01210> 

- Fuente: <https://www.correosdemexico.gob.mx/SSLServicios/ConsultaCP/CodigoPostal_Exportar.aspx> 

- Sintaxis: [GET] [https://jobs.backbonesystems.io/api/zip-codes/{zip_code}](https://jobs.backbonesystems.io/api/zip-codes/%7bzip_code%7d)
 

# **Proceso de Desarrollo**

Para al proceso de desarrollo se creó un repositorio en donde será almacenado el código fuente del reto.

1. **Descargar fuente**

Se realizado la descarga de todo los datos necesarios para simular el entorno, donde se pudieron obtener los tipos de datos y campos, para recrear la base de datos.

2. **Creación de base de dato local**

Se llevó a  cabo la creación de una base de datos en local para realizar pruebas con el motor MYSQL.

3. **Migración**

Se crea un archivo de migración para crear la tabla zip\_codes, con sus respectivos campos en la base de datos.
```php
CreateZipCodesTable.php
```

4. **Modelos**

Se crea un modelo llamado ZipCode para la creación de los registros en la base de datos.
```php
ZipCode.php
```

5. **Seeder**

Para realizar la carga de la base de datos, se creó un archivo seeder, que contiene  todo los datos descargados de la fuente proporcionada.
```php
ZipCodeSeeder.php
```

6. **Controlador** 

Se creó un controlador y se almaceno en un fichero separada, en este archivo se realiza  la funcionalidad para  la búsqueda de los datos, y su representación.
```php
ZipCodeController.php
```
7. **Ruta Api**

Para finalizar se creó una ruta api, con la sintaxis requerida y su respectivo controlador.

# **Proceso de publicación de la Api**



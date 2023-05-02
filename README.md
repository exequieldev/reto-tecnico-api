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

2. **Creación de base de dato**

Se llevó a cabo la creación de una base de datos para realizar pruebas con el motor MYSQL.

3. **Migración**: [CreateZipCodesTable.php](https://github.com/exequieldev/reto-tecnico-api/blob/main/database/migrations/2023_04_28_213820_create_zip_codes_table.php)

Se crea un archivo de migración para crear la tabla zip\_codes, con sus respectivos campos en la base de datos.
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZipCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zip_codes', function (Blueprint $table) {
            $table->string("d_codigo",10);
            $table->string("d_asenta",120);
            $table->string("d_tipo_asenta",120);
            $table->string("D_mnpio",120);
            $table->string("d_estado",120);
            $table->string("d_ciudad",120)->nullable();
            $table->string("d_CP",120)->nullable();
            $table->string("c_estado",120)->nullable();
            $table->string("c_oficina",120);
            $table->string("c_CP",10)->nullable();
            $table->string("c_tipo_asenta",10)->nullable();
            $table->string("c_mnpio",10)->nullable();
            $table->string("id_asenta_cpcons",10)->nullable();
            $table->string("d_zona",120);
            $table->string("c_cve_ciudad",120)->nullable();
            $table->index('d_codigo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zip_codes');
    }
}
```

4. **Modelos**: [ZipCode.php](https://github.com/exequieldev/reto-tecnico-api/blob/main/app/Models/ZipCode.php)

Se crea un modelo llamado ZipCode para la creación de los registros en la base de datos.
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZipCode extends Model
{
    use HasFactory;

    public $tables = "zip_codes";
    public $timestamps = false;
}

```

5. **Seeder**: [ZipCodeSeeder.php](https://github.com/exequieldev/reto-tecnico-api/blob/main/database/seeders/ZipCodeSeeder.php)

Para realizar la carga de la base de datos, se creó un archivo seeder, que contiene  todo los datos descargados de la fuente proporcionada.


6. **Controlador**  [ApiZipCode/ZipCodeController.php](https://github.com/exequieldev/reto-tecnico-api/blob/main/app/Http/Controllers/ApiZipCode/ZipCodeController.php)

Se creó un controlador y se almaceno en un fichero separada, en este archivo se realiza  la funcionalidad para  la búsqueda de los datos, y su representación.
```php
<?php

namespace App\Http\Controllers\ApiZipCode;

use Exception;
use App\Models\ZipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ZipCodeController extends Controller
{
    public function getDataZipCode(Request $request){

        $zipCode = $this->validateCharacter($request->zip_code);

        $dataZipCode = $this->fetchZipCode($zipCode);

        return response()->json($dataZipCode);

    }

    public function fetchZipCode($code){
        try {
            $zipCodeList = ZipCode::where('d_codigo',$code)->get();
            
            $zipCode = $zipCodeList->first();

            if(empty($zipCode)){
                return response()->json([
                        'message'=>'The zip code '.$code.' not exist'
                    ],422);
            }

            $dCiudad = $this->removeAccents($zipCode->d_ciudad);
            $dEstado = $this->removeAccents($zipCode->d_estado);
            $dMnpio  = $this->removeAccents($zipCode->D_mnpio);
            
            $locality = $this->strUpperCase($dCiudad);

            $federalEntity = [
                    "key"  => (int)$zipCode->c_estado,
                    "name" => $this->strUpperCase($dEstado),
                    "code" => $zipCode->c_cp ?: null
            ];

            $settlements = $zipCodeList->map(function ($zipCode){
                $dAsenta = $this->removeAccents($zipCode->d_asenta);
                $dZona   = $this->removeAccents($zipCode->d_zona);
            
                return [
                    'key'             => (int)$zipCode->id_asenta_cpcons,
                    'name'            => $this->strUpperCase($dAsenta),
                    'zone_type'       => $this->strUpperCase($dZona),
                    'settlement_type' => [
                        'name'        => $zipCode->d_tipo_asenta,
                    ],
                ];
            });

            $municipality = [
                'key'  => (int)$zipCode->c_mnpio,
                'name' => $this->strUpperCase($dMnpio),
            ];


            
            return [
                "zip_code"          => $code,
                "locality"          => $locality,
                "federal_entity"    => $federalEntity,
                "settlements"       => $settlements,
                "municipality"      => $municipality
            ];

        } catch (Exception $e) {
            Log::info([
                    'error'    =>'Error in ZipCodeController@getDataZipCode',
                    'messagge' => $e->getMessage(),
                    'line'     => $e->getLine(),
                ]
            );
        }
    }

    private function validateCharacter($zipCode){
        if(strlen($zipCode) == 4 ){
            $zipCode = '0'.$zipCode;
        }
        return $zipCode;
    }

    private function strUpperCase($string)
    {
        $textUpperCase = ucwords(mb_strtoupper($string));

        return $textUpperCase;
    }

    public function removeAccents($string){
        $search  = array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú','ñ');
        $replace = array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U','n');

        $textWithAccents = str_replace($search, $replace, $string);

        return $textWithAccents;
    }

}
```
7. **Ruta Api**: [zip-codes/{zip_code}](https://github.com/exequieldev/reto-tecnico-api/blob/main/routes/api.php)

Para finalizar se creó una ruta api, con la sintaxis requerida y su respectivo controlador.

# **Proceso de publicación de la Api**

Para publicar la Api se utiliza la plataforma Railwaiy, la url resultante es la siguiente:

[https://reto-tecnico-api-production.up.railway.app/api/zip-codes/01210]

**Instalación**

1.  Se creo una nueva base de datos con mysql, en la plataforma, y se crearion las credenciales para poder accederla.

2.  Se creo un nuevo proyecto, y se eligio el repositorio del reto para clonarlo, una ves creado se genero una url para poder utilizarlo con el proyecto.

3.  Se agregaron variables de entorno para conectar la base de datos con el proyecto, y se migraton los datos y las tablas a la base de datos.

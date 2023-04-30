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

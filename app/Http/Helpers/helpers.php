<?php


use Illuminate\Support\Str;
use App\Http\Lib\ClientInfo;
use Illuminate\Support\Facades\File;


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}

function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}

function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}

function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function getPaginate($paginate = 20)
{
    return $paginate;
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


/**
 * Elimina un archivo si existe.
 *
 * @param string $filePath
 * @return void
 */
function deleteFile($filePath)
{
    if ($filePath && file_exists(public_path($filePath))) {
        File::delete(public_path($filePath));
    }
}


/**
 * Sube un archivo a un directorio específico y devuelve su ruta.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @param string $directory
 * @return string
 */
function uploadFile($file, $directory = 'uploads')
{
    // Comprobamos que la ruta del directorio sea correcta y que sea un directorio absoluto
    $directoryPath = public_path($directory);

    // Si el directorio no existe, lo creamos
    if (!File::exists($directoryPath)) {
        File::makeDirectory($directoryPath, 0777, true); // 0777: permisos completos
    }

    // Generamos el nombre del archivo con timestamp
    $filename = time() . '.' . $file->getClientOriginalExtension();

    // Movemos el archivo al directorio correspondiente
    $file->move($directoryPath, $filename);

    // Devolvemos la ruta relativa al archivo 
    return $directory . '/' . $filename;
}

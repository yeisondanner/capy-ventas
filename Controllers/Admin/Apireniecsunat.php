<?php

class Apireniecsunat extends Controllers
{
    public function __construct()
    {
        isSession();
        parent::__construct();
    }

    public function getpeople()
    {
        if (!$_GET) {
            registerLog("Ocurrió un error inesperado", "Método GET no encontrado, al momento de consultar información en la API", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método GET no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que exista el campo data
        if (!isset($_GET["data"])) {
            registerLog("Ocurrió un error inesperado", "El campo data no existe en la consulta a la API", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Campo data no existe",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $numberDocument = strClean($_GET["data"]);
        //validamos que los campos no esten vacios
        if ($numberDocument == "") {
            registerLog("Ocurrió un error inesperado", "El campo DNI/RUC no debe estar vacio para poder consultar en la API", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Campo DNI/RUC no debe estar vacio",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el DNI sea de 8 digitos o de 11 digitos
        if (strlen($numberDocument) != 8 && strlen($numberDocument) != 11) {
            registerLog("Ocurrió un error inesperado", "El DNI/RUC debe ser de 8 dígitos o de 11 digitos para poder consultar en la API", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El DNI/RUC debe ser de 8 dígitos o de 11 digitos",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que sea numerico
        if (!is_numeric($numberDocument)) {
            registerLog("Ocurrió un error inesperado", "El DNI/RUC debe ser numérico", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El DNI/RUC debe ser numérico",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que la consulta no sea repetida muchas veces
        if (!isset($_SESSION['lastConsult'])) {
            $_SESSION['lastConsult'] = array(
                "ndocument" => $numberDocument,
                "datetime" => date("Y-m-d H:i:s")
            );
        } else {
            //creamos la variable que obitne la fecha y la hora
            $datetime = $_SESSION['lastConsult']["datetime"];
            //creamos una variable para almacenar el tiempo de la ultima consulta
            $ndocument = $_SESSION['lastConsult']["ndocument"];
            //obtenemos la cantidad de segundos que han pasado desde la ultima consulta
            $timeSeconds = dateDifference($datetime, date("Y-m-d H:i:s"))["total_segundos"];
            $timeRestante = 10 - $timeSeconds;
            //validamos que la cantidad de segundos sea mayor a 10 segundos
            if ($timeSeconds <= 10) {
                registerLog("Ocurrió un error inesperado", "La consulta no puede ser repetida en menos de 10 segundos", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "La consulta no puede ser repetida en menos de {$timeRestante} segundos",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //ahora validamos que los documentos no sean los mismo que se consulto
            if ($ndocument == $numberDocument && $timeSeconds < 60) {
                registerLog("Ocurrió un error inesperado", "La consulta no puede ser repet ida con el mismo documento", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "La consulta no puede ser repetida con el mismo documento",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //Token de la api
        $token = decryption(getApiKeyReniec());
        //validamos que el DNI sea de 8 digitos
        if (strlen($numberDocument) == 8) {
            //Metodo de consulta por DNI
            // Datos
            $dni = $numberDocument;
            // Iniciar llamada a API
            $curl = curl_init();
            // Buscar dni
            curl_setopt_array($curl, array(
                CURLOPT_URL => getApiUrlReniec() . $dni,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 2,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Referer: https://apis.net.pe/consulta-dni-api',
                    'Authorization: Bearer ' . $token
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            registerLog("Consulta correcta por DNI", "Se logró consultar de manera correcta los datos de la persona " . $response, 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "info" => json_decode($response),
                "status" => true
            );
            $_SESSION['lastConsult'] = array(
                "ndocument" => $numberDocument,
                "datetime" => date("Y-m-d H:i:s")
            );
            toJson($data);
        } else if (strlen($numberDocument) == 11) {
            //Metodo de consulta por RUC
            // Datos
            $ruc = $numberDocument;
            // Iniciar llamada a API
            $curl = curl_init();
            // Buscar ruc sunat
            curl_setopt_array($curl, array(
                CURLOPT_URL => getApiUrlSunat() . $ruc,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Referer: http://apis.net.pe/api-ruc',
                    'Authorization: Bearer ' . $token
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            // Datos de empresas según padron reducido
            registerLog("Consulta correcta por RUC", "Se logró consultar de manera correcta los datos de la empresa " . $response, 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "info" => json_decode($response),
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "Consulta no realizada ya que el DNI o RUC debe ser de 8 o 11 dígitos", 1, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El DNI o RUC debe ser de 8 o 11 dígitos",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
}
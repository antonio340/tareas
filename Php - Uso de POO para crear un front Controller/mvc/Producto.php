<?php

require_once './datos/ConexionBD.php';

class Producto {

    public static function get($parametros) {
        switch (count($parametros)) {
            case 0:
                return self::getAll();
            case 1:
                return self::getId($parametros[0]);
            case 2:
                return self::getMany($parametros[0], $parametros[1]);
            default:
                throw new ExcepcionApi(400, "Parámetros incorrectos para GET");
        }
    }

    private static function getId($id) {
        // Simulación de respuesta, reemplazar con consulta a base de datos si se desea
        return [
            "estado" => 1,
            "producto" => [
                "id" => $id,
                "nombre" => "Producto $id",
                "precio" => 1000 * $id
            ]
        ];
    }

    private static function getMany($idIni, $idFin) {
        $productos = [];
        for ($i = $idIni; $i <= $idFin; $i++) {
            $productos[] = [
                "id" => $i,
                "nombre" => "Producto $i",
                "precio" => 1000 * $i
            ];
        }
        return [
            "estado" => 1,
            "productos" => $productos
        ];
    }

    private static function getAll() {
        $productos = [];
        for ($i = 1; $i <= 5; $i++) {
            $productos[] = [
                "id" => $i,
                "nombre" => "Producto $i",
                "precio" => 1000 * $i
            ];
        }
        return [
            "estado" => 1,
            "productos" => $productos
        ];
    }

    public static function post($parametros) {
        $cuerpo = file_get_contents("php://input");
        $datos = json_decode($cuerpo, true);

        if (!$datos || !isset($datos["id"]) || !isset($datos["nombre"]) || !isset($datos["precio"])) {
            throw new ExcepcionApi(400, "Datos incompletos para POST");
        }

        // Aquí se simularía la inserción en BD
        return [
            "estado" => 1,
            "mensaje" => "Producto creado/modificado correctamente",
            "producto" => $datos
        ];
    }

    public static function put($parametros) {
        return self::post($parametros);
    }

    public static function delete($parametros) {
        if (count($parametros) != 1) {
            throw new ExcepcionApi(400, "Se requiere un ID para DELETE");
        }

        $id = $parametros[0];

        // Aquí se simularía la eliminación en BD
        return [
            "estado" => 1,
            "mensaje" => "Producto con ID $id eliminado correctamente"
        ];
    }
}

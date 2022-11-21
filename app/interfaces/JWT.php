<?php

use Firebase\JWT\JWT;

class JWTAuth{
    private static $clave = 'T3sT$JWT';
    private static $tipo_encriptacion = ['HS256'];

    public static function crearToken($data) {
        $time_now = time();
        $payload = array(
            'iat' => $time_now,
            'exp' => $time_now + (60000)*24*365,
            'aud' => self::Aud(),
            'data' => $data,
            'app' => "Test JWT"
        );
        return JWT::encode($payload, self::$clave);
    }

    private static function Aud() {
        $aud = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    public static function verificarToken($token) {
        if (empty($token)) {
            throw new Exception("Token inexistente o vacio.");
        }
        try {
            $decoded = JWT::decode(
                $token,
                self::$clave,
                self::$tipo_encriptacion
            );

        } catch (Exception $e) {
            throw $e;
        }
        if ($decoded->aud !== self::Aud()) {
            throw new Exception("Usuario incorrecto.");
        }
    }

    public static function getPayload($token) {
        if (empty($token)) {
            throw new Exception("Token inexistente o vacio.");
        }
        return JWT::decode(
            $token,
                self::$clave,
                self::$tipo_encriptacion
        );
    }

    public static function getInfoToken($token) {
        $array = JWT::decode(
            $token,
            self::$clave,
            self::$tipo_encriptacion
        )->data;
        return $array;
    }
}
?>
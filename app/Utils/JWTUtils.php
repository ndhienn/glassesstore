<?php
namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTUtils {
    private static $secretKey = "MY_SECRET_KEY";

    public static function generateToken($email) {
        $payload = [
            "iss" => "myapp",
            "iat" => time(),
            "exp" => time() + 3600, // Token hết hạn sau 1 giờ
            "email" => $email
        ];
        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    public static function verifyToken($token) {
        try {
            return JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (\Exception $e) { // Lưu ý: cần có dấu `\` trước Exception
            return null;
        }
    }
}
?>

<?php
// Tidak ada secret key
$secret = null;

/**
 * Encode data to Base64 URL format.
 */
function base64UrlEncode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Decode from Base64 URL format.
 */
function base64UrlDecode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

/**
 * Create JWT (JSON Web Token)
 */
function create_jwt($payload)
{
    // Algorithm diatur menjadi None (tidak ada signature)
    $header = json_encode(['typ' => 'JWT', 'alg' => 'none']);
    $payload = json_encode($payload);

    $encodedHeader = base64UrlEncode($header);
    $encodedPayload = base64UrlEncode($payload);

    // Mengembalikan token tanpa signature
    return "$encodedHeader.$encodedPayload.";
}

/**
 * Verify JWT signature
 */
function verify_jwt($token)
{
    $parts = explode('.', $token);

    // Membagi token menjadi header dan payload saja (tidak ada signature)
    if (count($parts) < 2) {
        return false;
    }

    list($encodedHeader, $encodedPayload) = array_pad($parts, 2, null);

    // Validasi None Algorithm
    $header = json_decode(base64UrlDecode($encodedHeader), true);
    if (isset($header['alg']) && $header['alg'] === 'none') {
        return true;
    }

    // Tidak memvalidasi signature karena mmg tidak ada signature
    return false;
}

/**
 * Decode JWT payload
 */
function decode_payload($token)
{
    $parts = explode('.', $token);

    // Membagi token menjadi 2 bagian, yaitu header dan payload saja
    if (count($parts) < 2) {
        return null;
    }
    return json_decode(base64UrlDecode($parts[1]), true);
}

// Simpan JWT ke dalam cookie tanpa HttpOnly atau Secure
function set_jwt_cookie($jwt)
{
    // Tidak menggunakan Secure/HttpOnly dalam cookie
    setcookie("session", $jwt, time() + 3600, "/");
}

// Payload
$payload = [
    "name" => "user",
    "role" => "admin",
];

$jwt = create_jwt($payload);
set_jwt_cookie($jwt);

echo "JWT token created and stored in cookie.";
?>

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
 * Create JWT tanpa signature 
 */
function create_jwt($payload)
{
    // Header dengan algoritma none
    $header = json_encode(['typ' => 'JWT', 'alg' => 'none']);
    $payload = json_encode($payload);

    // Encode header dan payload saja
    $encodedHeader = base64UrlEncode($header);
    $encodedPayload = base64UrlEncode($payload);

    // Tidak ada signature
    return "$encodedHeader.$encodedPayload.";
}

/**
 * Verify JWT tanpa validasi signature
 */
function verify_jwt($token)
{
    $parts = explode('.', $token);

    // Token hanya divalidasi untuk 2 bagian (header dan payload)
    if (count($parts) != 2) {
        return false; // Tidak valid jika formatnya salah
    }

    // Tidak ada validasi lebih lanjut
    return true;
}

/**
 * Decode JWT payload
 */
function decode_payload($token)
{
    $parts = explode('.', $token);
    // memastikan bahwa setidaknya token memiliki 2 bagian, yaitu header dan payload.
    // Token tanpa signature akan dianggap valid
    if (count($parts) < 2) return null;

    // mendecode payload ke dalam format Base64 URL
    $payload = json_decode(base64UrlDecode($parts[1]), true);

    // Tidak ada validasi expired time 
    return $payload;
}

/**
 * Simpan JWT ke dalam cookie tanpa Secure atau HttpOnly attributes (kerentanan keenam)
 */
function set_jwt_cookie($jwt)
{
    setcookie("session", $jwt, time() + 3600, "/"); // Tidak ada Secure atau HttpOnly
}

// Ambil username dari input POST (i.e. dari form login)
$username = $_POST['username'] ?? null;

// Validasi username dan tentukan role
if ($username) {
    $role = "user"; //  role default adalah user
} else {
    $username = "guest";
    $role = "guest"; // Default untuk tamu
}

// Payload JWT tanpa expired time
$payload = [
    "name" => $username,
    "role" => $role
];

// Buat token JWT
$jwt = create_jwt($payload);

// Simpan token JWT ke dalam cookie
set_jwt_cookie($jwt);

echo "JWT token created and stored in cookie.";
?>

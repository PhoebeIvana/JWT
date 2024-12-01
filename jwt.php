<?php
$secret = 'insecure_secret_key';

/**
 * Encode data to Base64 URL format.
 * Perintah strtr(base64_encode($data), '+/', '-_') akan mengubah + dengan -, / dengan _. Hal tsb. dikarenakan + dan / tidak aman untuk digunakan pada URL.
 * Kemudian, karakter = di akhir string akan dihapuskan.
 */
function base64UrlEncode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Decode from Base64 URL format.
 * Perintah strtr($data, '-_', '+/') akan mengubah - menjadi +, dan _ menjadi / (sesaui standar sblm decode).
 * Kemudian, akan ditambahkan padding = jika length dari data bukan merupakan kelipatan 4. Sebab, Format Base64 membutuhkan length kelipatan 4 untuk melakukan decoding.
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
    global $secret;
    // Membentuk header token berupa JSON yang mendefinisikan tipenya sbg. JWT dan menggunakan algorithm hashing HS256. 
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    // Mengubah payload yang berada dalam bentuk array menjadi string JSON.
    $payload = json_encode($payload);

    // Encode header and payload to Base64 URL format, dengan tujuan agar dapat ditransmisikan dengan aman via URL.
    $encodedHeader = base64UrlEncode($header);
    $encodedPayload = base64UrlEncode($payload);

    /*Create signature token dengan cara:
    * 1. Menggabungkan encorderHeader & encoderPayload melalui tanda .
      2. Menggunakan algorithm hashing HMAC-SHA256 + secret key agar dapat membentuk hash signature. 
      3. Hasil hash diconvert ke dalam format Base64 URL-Safe.
    */
    $signature = base64UrlEncode(hash_hmac('sha256', "$encodedHeader.$encodedPayload", $secret, true));

    // Return the JWT in the format: header.payload.signature
    return "$encodedHeader.$encodedPayload.$signature";
}

/**
 * Verify JWT signature
 */
function verify_jwt($token)
{
    global $secret;
    // Membagi token menjadi 3 bagian (header, payload, signature) based on sign .
    $parts = explode('.', $token);
    // Jikalau gagal, maka token dianggap invalid.
    if (count($parts) != 3)
        return false;
    //Jikalau berhasil, maka masing-masing bagian token akan disimpan ke dalam variabel terkait.
    list($encodedHeader, $encodedPayload, $signature) = $parts;

    // Validate signature (sama seperti sebelumnya, berupa proses pembentukkan signature)
    $validSignature = base64UrlEncode(hash_hmac('sha256', "$encodedHeader.$encodedPayload", $secret, true));
    // Bila signature yang asli sama dengan signature verification, maka token dianggap valid.
    return ($signature === $validSignature);
}

/**
 * Decode JWT payload
 */
function decode_payload($token)
{
    // Membagi token menjadi 3 bagian (header, payload, signature) based on sign .
    $parts = explode('.', $token);
    // Jikalau gagal, maka token dianggap invalid.
    if (count($parts) != 3)
        return null;
    /* Jikalau berhasil, maka nilai payload akan didecode. 
    * Kemudian, keseluruhan string JSON payload akan diconvert menjadi array. 
    */
    return json_decode(base64UrlDecode($parts[1]), true);
}
?> 


 
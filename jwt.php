<?php
// CLEAN CODE

// Menggunakan secret key (unique) untuk membentuk dan memverifikasi signature
$secret = "my_strong_secret_key"; 

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
 * Create JWT dengan signature
 */
function create_jwt($payload)
{
    global $secret;
    // Header dengan algoritma HS256
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode($payload);

    // Encode header dan payload
    $encodedHeader = base64UrlEncode($header);
    $encodedPayload = base64UrlEncode($payload);

    // Buat signature menggunakan HMAC SHA256
    // Key: secret key untuk membuat HMAC, memastikan bahwa signature hanya dapat dibentuk oleh server dengan key tsb.
    // binary: efficiency karena tahap berikutnya akan melakukan encoding Base64 URL.
    $signature = hash_hmac('sha256', "$encodedHeader.$encodedPayload", $secret, true);
    $encodedSignature = base64UrlEncode($signature);

    // Kembalikan token dengan signature
    return "$encodedHeader.$encodedPayload.$encodedSignature";
}


/**
 * Verify JWT dengan validasi signature
 */
function verify_jwt($token)
{
    global $secret;
    $parts = explode('.', $token);

    // Token harus memiliki 3 bagian (header, payload, signature)
    if (count($parts) !== 3) {
        return false; // Format salah
    }

    list($encodedHeader, $encodedPayload, $encodedSignature) = $parts;

    // Verifikasi signature
    $signature = hash_hmac('sha256', "$encodedHeader.$encodedPayload", $secret, true);
    $expectedSignature = base64UrlEncode($signature);

    return hash_equals($expectedSignature, $encodedSignature); // Prevent timing attack
}

/**
 * Decode JWT payload
 */
function decode_payload($token)
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    // Decode payload dan kembalikan sebagai array
    return json_decode(base64UrlDecode($parts[1]), true);
}

/**
 * Simpan JWT ke dalam cookie dengan Secure dan HttpOnly attributes
 */
function set_jwt_cookie($jwt)
{
    setcookie("personal-session", $jwt, [
        'expires' => time() + 3600, // Expired in 1h
        'path' => '/',
        'secure' => true, // Hanya dpt diakses melalui HTTPS
        'httponly' => true, // Tidak dpt diakses oleh JavaScript
        'samesite' => 'Strict' // Protection dari CSRF
    ]);
}

// Fungsi untuk membuat token guest
function create_guest_token()
{
    $payload = [
        "username" => "guest",
        "role" => "guest",
        "exp" => time() + 3600 
    ];
    return create_jwt($payload);
}

// Proses login atau mode guest
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa username dan password
    $stmt = $conn->prepare("SELECT username, role, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password 
        if (password_verify($password, $user['password'])) {
            // Buat payload berdasarkan data pengguna
            $payload = [
                "username" => $user['username'],  // Simpan username yang login
                "role" => $user['role'],          // Simpan role yang login
                "exp" => time() + 3600          
            ];
            // Buat token JWT dan simpan dalam cookie
            $jwt = create_jwt($payload);
            set_jwt_cookie($jwt);
            echo "Login successful. JWT token created for user: {$user['username']}.";
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }
}
?>
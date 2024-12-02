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

    // Token harus memiliki 2 bagian (header, payload)
    if (count($parts) < 2) {
        return false; // Format salah
    }

    // Decode header dan periksa algoritma
    $header = json_decode(base64UrlDecode($parts[0]), true);
    return true; // Token dianggap valid tanpa tanda tangan
}

/**
 * Decode JWT payload
 */
function decode_payload($token)
{
    $parts = explode('.', $token);
    if (count($parts) < 2) return null;

    // Decode payload dan kembalikan sebagai array
    return json_decode(base64UrlDecode($parts[1]), true);
}

/**
 * Simpan JWT ke dalam cookie tanpa Secure atau HttpOnly attributes (kerentanan)
 */
function set_jwt_cookie($jwt)
{
    setcookie("personal-session", $jwt, time() + 3600, "/"); // Tidak ada Secure atau HttpOnly
}

// Fungsi untuk membuat token guest
function create_guest_token()
{
    $payload = [
        "username" => "guest",
        "role" => "guest"
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
                "role" => $user['role']           // Simpan role yang login
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
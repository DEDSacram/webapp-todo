<?php
include 'db.php';
include 'endpoint.php';
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


function createuser($data) {
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $sql = "SELECT COUNT(*) FROM Users WHERE Email = :email";
    $params = array(':email' => $email);

    $db = new Database();
    $stmt = $db->query($sql, $params);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        send_response([
            'status' => 0,
            'message' => 'Uživatel již existuje',
        ]);
        $db->close();
        return;
    }

    $sql = "INSERT INTO Users (Email, Password) VALUES (:email, :password)";
    $params = array(':email' => $email, ':password' => $password);
    $stmt = $db->query($sql, $params);
    $db->close();
    send_response([
        'status' => 1,
        'message' => 'Uživatel úspěšně vytvořen',
    ]);
}

function loginuser($data) {
    $email = $data['email'];
    $password = $data['password'];

    $sql = "SELECT * FROM Users WHERE Email = :email";
    $params = array(':email' => $email);

    $db = new Database();
    $stmt = $db->query($sql, $params);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['Password'])) {
        send_response([
            'status' => 0,
            'message' => 'Neplatné přihlašovací údaje',
        ]);
        $db->close();
        return;
    }


    if ($data['action-remember'] == 'true') { // If the user checked "Remember Me"
        $token = bin2hex(random_bytes(16)); // Generate a random token
        $hashedToken = hash('sha256', $token); // Hash the token

        // Store the hashed token and user ID in your database
        // Replace this with your actual database code
        $timestamp = time() + 60 * 60 * 24 * 30;
        $datetime = date('Y-m-d H:i:s', $timestamp);
        $stmt = $db->query('UPDATE Users SET Cookie = ?, ExpiryTime = ? WHERE UserID = ?', [$hashedToken, $datetime, $user['UserID']]);
        // Set a cookie in the user's browser containing the original token and user ID
        // The cookie expires in 30 days
        setcookie('rememberme', $user['UserID'] . ':' . $token,  $timestamp);
    }

    session_set_cookie_params([
        'samesite' => 'Lax', // or 'Strict' or 'None'
    ]);
    session_start();
    $_SESSION['guard'] = true;
    $_SESSION['user'] = $user['UserID'];
    send_response([
        'status' => 1,
        'message' => 'Přihlášení úspěšné',
    ]);
    $db->close();
}

function checkcookie($data) {
    session_set_cookie_params([
        'samesite' => 'Lax', // or 'Strict' or 'None'
    ]);
    session_start();
    // SESSION ONLY, NO REMEMBER ME COOKIE
    if(isset($_SESSION['user'])){
        send_response([
            'status' => 1,
            'message' => 'Session only',
        ]);
        return;
    }
     // SET GUARD ABOVE TO ALREADY DEFAULT TO FALSE TO AVOID UNECESSARY CODE ASSIGNMENTS
    $_SESSION['guard'] = false;
  
    if (!isset($_COOKIE['rememberme'])) {
        send_response([
            'status' => 0,
            'message' => 'Neplatný token',
        ]);
        return;
    }
    $cookie = $_COOKIE['rememberme'];
    $parts = explode(':', $cookie);
    $userID = $parts[0];
    $token = $parts[1];

    

    $sql = "SELECT * FROM Users WHERE UserID = :userID";
    $params = array(':userID' => $userID);

    $db = new Database();
    $stmt = $db->query($sql, $params);
    $user = $stmt->fetch();

    if (!$user) {
        send_response([
            'status' => 0,
            'message' => 'Neplatný token',
        ]);
        $db->close();
        return;
    }

    $hashedToken = hash('sha256', $token);
    if ($user['Cookie'] !== $hashedToken) {
        send_response([
            'status' => 0,
            'message' => 'Neplatný token',
        ]);
        $db->close();
        return;
    }

    if (strtotime($user['ExpiryTime']) < time()) {
        send_response([
            'status' => 0,
            'message' => 'Platnost tokenu vypršela',
        ]);
        $db->close();
        return;
    }

    $_SESSION['user'] = $user['UserID'];
    //guarding of site
    $_SESSION['guard'] = true;
    send_response([
        'status' => 1,
        'message' => 'Přihlášení úspěšné',
    ]);
    $db->close();
}




$data = get_request_data();
switch ($data['action']) {
    case 'destroysession':
        session_start();
        session_destroy();
        send_response([
            'status' => 1,
            'message' => 'Odhlášení úspěšné',
        ]);
        break;
    case 'checkcookie':
        checkcookie($data);
        break;
    case 'login':
        loginuser($data);
        break;
    case 'register':
        createuser($data);
        break;
    default:
        send_response([
            'status' => 0,
            'message' => 'Neplatná akce',
        ]);
}
createuser($data);



# Chat System

## 🛠️ Installation
```bash
# installing docker
sudo apt update -y && sudo apt upgrade -y
sudo apt install git -y
sudo apt install docker.io -y
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -aG docker $USER
sudo apt install docker-compose -y

# installing project
cd /opt && sudo git clone https://github.com/2120865/chat_system_fix.git
cd chat_system_fix
docker-compose up -d
```

## 🏃 Getting started
- Web App
    - http://127.0.0.1:81
- PhpMyAdmin
    - http://127.0.0.1:82 
- Database
    - mysql -h 127.0.0.1 -P 3307 -u root -p

## Fix 1 - SQLi Login Bypass

login.php

```php
// vulnerable
if(preg_match('/-+/', $_POST["username"]) || preg_match('/admin.+/i', $_POST["username"])) {
  $_SESSION['msg'] = "Login Failed, Hacking Attempt detected!";
  header('location: index.php');
  exit;
}
$query = "SELECT * FROM user WHERE username='".$_POST["username"]."' AND password='".md5($_POST["password"])."'";
$result = mysqli_query($conn, $query);

// fix
$stmt = $conn->prepare("SELECT * FROM `user` WHERE username=? AND password=?");
$stmt->bind_param("ss", $_POST["username"], md5($_POST["password"]));
$stmt->execute();
$result = $stmt->get_result();
```

## Fix 2 - SQLi Password Protected Room Bypass

confirm_password.php

```php
// vulnerable
$result=mysqli_query($conn,"select * from chatroom where chatroomid='$cid' and chat_password='$pass'");

// fix
$stmt = $conn->prepare("SELECT * FROM `chatroom` WHERE chatroomid=? AND chat_password=?");
$stmt->bind_param("is", $cid, $pass);
$stmt->execute();
$result = $stmt->get_result();
```

## Fix 3 - Stored XSS

fetch_chat.php

```php
// vulnerable
<span style="font-size:11px; position:relative; top:-2px; left:50px;"><strong><?php echo $row['uname']; ?></strong>: <?php echo $row['message']; ?></span><br>

// fix
<span style="font-size:11px; position:relative; top:-2px; left:50px;"><strong><?php echo htmlspecialchars($row['uname']); ?></strong>: <?php echo htmlspecialchars($row['message']); ?></span><br>
```

chatlist.php

```php
// vulnerable
<span class="badge"><?php echo mysqli_num_rows($num); ?></span> <?php echo $row['chat_name']; ?>

// fix
<span class="badge"><?php echo htmlspecialchars(mysqli_num_rows($num)); ?></span> <?php echo htmlspecialchars($row['chat_name']); ?>
```

mychat.php
```php
// vulnerable
<td><span class="glyphicon glyphicon-user"></span><span class="badge"><?php echo mysqli_num_rows($nq); ?></span> <a href="chatroom.php?id=<?php echo $myrow['chatroomid']; ?>"><?php echo $myrow['chat_name']; ?></a></td>

// fix
<td><span class="glyphicon glyphicon-user"></span><span class="badge"><?php echo  htmlspecialchars(mysqli_num_rows($nq)); ?></span> <a href="chatroom.php?id=<?php echo  htmlspecialchars($myrow['chatroomid']); ?>"><?php echo htmlspecialchars($myrow['chat_name']); ?></a></td>
```

userlist.php
```php
// vulnerable
<span class="badge"><?php echo mysqli_num_rows($num); ?></span> <?php echo $row['chat_name']; ?>

// fix
<span class="badge"><?php echo htmlspecialchars(mysqli_num_rows($num)); ?></span> <?php echo htmlspecialchars($row['chat_name']); ?>
```

room.php
```php
// vulnerable
<span style="font-size:18px; margin-left:10px; position:relative; top:13px;"><strong><span  id="user_details"><span class="glyphicon glyphicon-user"></span><span class="badge"><?php echo mysqli_num_rows($cmem); ?></span></span> <?php echo $chatrow['chat_name']; ?></strong></span>

// fix
<span style="font-size:18px; margin-left:10px; position:relative; top:13px;"><strong><span  id="user_details"><span class="glyphicon glyphicon-user"></span><span class="badge"><?php echo htmlspecialchars(mysqli_num_rows($cmem)); ?></span></span> <?php echo htmlspecialchars($chatrow['chat_name']); ?></strong></span>
```

navbar.php
```php
// vulnerable
<li><a href="#account" data-toggle="modal"><span class="glyphicon glyphicon-lock"></span> <?php echo $user; ?></a></li>

// fix
<li><a href="#account" data-toggle="modal"><span class="glyphicon glyphicon-lock"></span> <?php echo htmlspecialchars($user); ?></a></li>
```

modal.php
```php
// vulnerable
<center><strong><span style="font-size: 15px;">Username: <?php echo $user; ?></span></strong></center>
<input type="text" style="width:350px;" class="form-control" name="mname" value="<?php echo $srow['uname']; ?>">
<input type="text" style="width:350px;" class="form-control" name="musername" value="<?php echo $srow['username']; ?>">

//fix
<center><strong><span style="font-size: 15px;">Username: <?php echo htmlspecialchars($user); ?></span></strong></center>
<input type="text" style="width:350px;" class="form-control" name="mname" value="<?php echo htmlspecialchars($srow['uname']); ?>">
<input type="text" style="width:350px;" class="form-control" name="musername" value="<?php echo htmlspecialchars($srow['username']); ?>">

```

## Fix 4 - Insecure File Upload

update_photo.php

```php
// vulnerable
// The MIME type provided by the client can be manipulated or incorrect. An attacker might send a file with a misleading MIME type to bypass certain checks.
$uploaded_name = $_FILES[ 'image' ][ 'name' ];
$uploaded_type = $_FILES[ 'image' ][ 'type' ];
$uploaded_size = $_FILES[ 'image' ][ 'size' ]; 

if( ( $uploaded_type == "image/jpeg" || $uploaded_type == "image/png") && $uploaded_size < 150000 ) {
  move_uploaded_file($_FILES["image"]["tmp_name"], "../upload/" . $uploaded_name);
  ...
}

// fix
// MIME type detection on the server side (using libraries like fileinfo or getimagesize) is generally more reliable than trusting the client-supplied MIME type.
$uploaded_name = sanitize_file_name(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
$uploaded_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
// File name is generated using md5(uniqid() . $uploaded_name) to create a unique and unpredictable name. This reduces the risk of attackers exploiting predictable file names.
$target_file = md5(uniqid() . $uploaded_name) . '.' . $uploaded_ext;
[..]
if (
    in_array($uploaded_ext, ['jpg', 'jpeg', 'png']) &&
    in_array($image_info[2], $valid_image_types) &&
    $uploaded_size < 150000
)
[..]
// Strip any malicious metadata from the image by re-encoding it using imagecreatefrompng.
$img = imagecreatefrompng($uploaded_tmp);
[..]
```

## Fix 5 - No Password Policy

register.php

```
password length 
e.g. min 16 characters, max 64 characters

password complexity 
e.g. using upper/lower-case letters, numbers, special characters

account lockout 
temporarily disable an account after a certain number of unsuccessful login attempts

password history 
are user able to set the new password to the old one or to one of their last passwords?

password expiration
required password change e.g. every 3, 6, 12 months
```

```php
// example password policy check
function is_valid_password($password) {
    // Check minimum length
    if (strlen($password) < 12) {
        return false;
    }

    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }

    // Check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }

    // Check for at least one digit
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }

    // Check for at least one special character (you can customize this list)
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        return false;
    }

    // Password meets all criteria
    return true;
}
```

## Fix 6 - Username Uniqueness

register.php

```php
// vulnerable
no username lookup implemented

// fix
function usernameExists($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM user WHERE LOWER(username) = LOWER(?)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
```

## Fix 7 - Sensitive Data Exposure

- http://127.0.0.1:81/phpinfo.php
- http://127.0.0.1:81/robots.txt
```
It's not recommended to make phpinfo.php publicly accessible on a production server due to security concerns.
The phpinfo() function in PHP displays a lot of information about your PHP environment,
including configuration settings, server information, PHP extensions, and more.
```

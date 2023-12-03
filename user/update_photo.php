<?php
include('session.php');

function sanitize_file_name($filename) {
    return preg_replace("/[^a-zA-Z0-9-_]/", "", $filename);
}

$uploaded_name = sanitize_file_name(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
$uploaded_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
$uploaded_size = $_FILES['image']['size'];
$uploaded_type = $_FILES['image']['type'];
$uploaded_tmp = $_FILES['image']['tmp_name'];

$target_directory = __DIR__ . '/../upload/';
$target_file = md5(uniqid() . $uploaded_name) . '.' . $uploaded_ext;
$temp_file = (ini_get('upload_tmp_dir') == '' ? sys_get_temp_dir() : ini_get('upload_tmp_dir')) . DIRECTORY_SEPARATOR . md5(uniqid() . $uploaded_name) . '.' . $uploaded_ext;

$image_info = getimagesize($uploaded_tmp);
$valid_image_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG];

if (
    in_array($uploaded_ext, ['jpg', 'jpeg', 'png']) &&
    in_array($image_info[2], $valid_image_types) &&
    $uploaded_size < 150000
) {
    if (!file_exists($target_directory)) {
        mkdir($target_directory, 0777, true);
    }

    // Strip any metadata, by re-encoding image (Note, using php-Imagick is recommended over php-GD)
    if ($uploaded_type == 'image/jpeg') {
        $img = imagecreatefromjpeg($uploaded_tmp);
        imagejpeg($img, $temp_file, 100);
    } else {
        $img = imagecreatefrompng($uploaded_tmp);
        imagepng($img, $temp_file, 9);
    }
    imagedestroy($img);

    if (rename($temp_file, $target_directory . $target_file)) {
        $stmt = $conn->prepare("UPDATE `user` SET photo = ? WHERE userid = ?");
        $relative_path = 'upload/' . $target_file;
        $stmt->bind_param("ss", $relative_path, $_SESSION['id']);
        $stmt->execute();
        $stmt->close();

        ?>
        <script>
            window.alert('Photo updated successfully!');
            window.history.back();
        </script>
        <?php
    } else {
        ?>
        <script>
            window.alert('Error uploading the file. Please try again.');
            window.history.back();
        </script>
        <?php
    }

    if (file_exists($temp_file)) {
        unlink($temp_file);
    }
} else {
    ?>
    <script>
        window.alert('Photo not updated. Please upload JPG or PNG files only!');
        window.history.back();
    </script>
    <?php
}
?>

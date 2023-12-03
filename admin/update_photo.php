<?php
include('session.php');

function sanitize_file_name($filename) {
    return preg_replace("/[^a-zA-Z0-9-_]/", "", $filename);
}

$uploaded_name = sanitize_file_name(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
$uploaded_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$uploaded_name = $uploaded_name . '_' . time() . '.' . $uploaded_ext;

$image_info = getimagesize($_FILES['image']['tmp_name']);
$valid_mime_types = ['image/jpeg', 'image/png'];

if ($image_info && in_array($image_info['mime'], $valid_mime_types) && $_FILES['image']['size'] < 150000) {
    $upload_directory = __DIR__ . '/../upload/';

    if (!file_exists($upload_directory)) {
        mkdir($upload_directory, 0777, true);
    }

    $upload_path = $upload_directory . $uploaded_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        $stmt = $conn->prepare("UPDATE `user` SET photo = ? WHERE userid = ?");
        $relative_path = 'upload/' . $uploaded_name;
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
} else {
    ?>
    <script>
        window.alert('Photo not updated. Please upload JPG or PNG files only!');
        window.history.back();
    </script>
    <?php
}
?>

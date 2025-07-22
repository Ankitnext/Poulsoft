<?php
$conn = new mysqli("localhost", "username", "password", "your_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
if (isset($_POST["submit"])) {
    $purchaseId = (int) $_POST["purchase_id"];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $uploadOk = 1;

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $message = "<div class='alert alert-danger'>File is not a valid image.</div>";
        $uploadOk = 0;
    }

    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedTypes)) {
        $message = "<div class='alert alert-warning'>Only JPG, JPEG, PNG & GIF allowed.</div>";
        $uploadOk = 0;
    }

    if ($uploadOk && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $filename = $conn->real_escape_string(basename($_FILES["image"]["name"]));
        $sql = "UPDATE pur_purchase SET purchase_image = '$filename' WHERE id = $purchaseId";
        if ($conn->query($sql)) {
            $message = "<div class='alert alert-success'>Image uploaded and linked to purchase ID $purchaseId.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Database error: " . $conn->error . "</div>";
        }
    } else if ($uploadOk) {
        $message = "<div class='alert alert-danger'>Image upload failed.</div>";
    }
}
$conn->close();
?>

<!-- HTML Layout -->
<!DOCTYPE html>
<html>
<head>
    <title>Upload Purchase Image</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="m-0 hold-transition">
    <div class="m-0 p-0 wrapper">
        <section class="m-0 p-0 content">
            <div class="container-fluid">
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Upload Image for Purchase</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form action="" method="post" enctype="multipart/form-data">
                            <table class="table table-bordered">
                                <tr>
                                    <td><label for="purchase_id">Purchase ID:</label></td>
                                    <td><input type="number" name="purchase_id" id="purchase_id" class="form-control" required></td>
                                </tr>
                                <tr>
                                    <td><label for="image">Select Image:</label></td>
                                    <td><input type="file" name="image" id="image" class="form-control-file" required></td>
                                    <td><button type="submit" name="submit" class="btn btn-success btn-sm">Upload</button></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>

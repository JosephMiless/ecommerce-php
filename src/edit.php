<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
$productID = $_SESSION['productID'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['update'])){
   $productName = $_POST['product_name'];
   $productPrice = $_POST['product_price'];
   $fileName = $_FILES['file']['name'];
   $fileType = $_FILES['file']['type'];
   $fileSize = $_FILES['file']['size'];
   $fileTmp = $_FILES['file']['tmp_name'];

    
   $fileContent = file_get_contents($fileTmp);

    $query = $conn->prepare("UPDATE `products` SET name = ?, price = ?, image = ?, image_type = ?,
     image_size = ?, image_name = ? WHERE id = ? ")  or die('query failed');
      $query->bind_param("sississ", $productName, $productPrice, $fileContent, $fileType, $fileSize, $fileTmp, $productID) or die('query failed');
      if ($query->execute()) {
         $message[] = 'product updated successfully!';
     } else {
      $message[] = 'product update failed!';
     }
 
     $query->close();
      
   header("location: admin.index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<div class="container">

<div class="form-container">

<form action="" method="post" enctype="multipart/form-data">
   <h3>Update product</h3>
   <input type="text" name="product_name" required placeholder="enter new product name" class="box">
   <input type="text" name="product_price" required placeholder="enter new price" class="box">
   <input type="file" name="file" class="box">
   <input type="submit" name="update" class="btn" value="Update">
</form>

</div>

</body>
</html>
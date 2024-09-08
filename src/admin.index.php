<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_GET['logout'])){
   unset($user_id);
   session_destroy();
   header('location:login.php');
};

if(isset($_POST['register_product'])){

    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $fileName = $_FILES['file']['name'];
    $fileType = $_FILES['file']['type'];
    $fileSize = $_FILES['file']['size'];
    $fileTmp = $_FILES['file']['tmp_name'];

    // Read the file content
    $fileContent = file_get_contents($fileTmp);

    $select_product = mysqli_query($conn, "SELECT * FROM `products` WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_product) > 0){
      $message[] = 'product already registered!';
   }else{
      $query = $conn->prepare("INSERT INTO `products` (name, price, image, image_type, image_size, image_name) VALUES (?,?,?,?,?,?)")  or die('query failed');
      $query->bind_param("sissis", $name, $price, $fileContent, $fileType, $fileSize, $fileTmp) or die('query failed');
      if ($query->execute()) {
         $message[] = 'product registered successfully!';
     } else {
      $message[] = 'product upload failed!';
     }
 
     $query->close();
      
   }

};

if(isset($_POST['edit'])){
   $name = $_POST['product_name'];

   $select_product = mysqli_query($conn, "SELECT * FROM `products` WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_product) > 0){
      $product = $select_product->fetch_assoc();
      $_SESSION['productID'] = $product['id'];
      header("location: edit.php");
   }
}

if(isset($_POST['delete'])){
   $id = $_POST['product_id'];
   mysqli_query($conn, "DELETE FROM `products` WHERE id = '$id'") or die('query failed');
   header('location:admin.index.php');
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
   
<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?>

<div class="container">

<div class="user-profile">

   <?php
      $select_user = mysqli_query($conn, "SELECT * FROM `user_info` WHERE id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_user) > 0){
         $fetch_user = mysqli_fetch_assoc($select_user);
      };
   ?>

   <p> username : <span><?php echo $fetch_user['name']; ?></span> </p>
   <p> email : <span><?php echo $fetch_user['email']; ?></span> </p>
   <div class="flex">
      <a href="login.php" class="btn">login</a>
      <a href="register.php" class="option-btn">register</a>
      <a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('are your sure you want to logout?');" class="delete-btn">logout</a>
   </div>

</div>

<div class="form-container">

<form action="" method="post" enctype="multipart/form-data">
   <h3>Register product</h3>
   <input type="text" name="product_name" required placeholder="enter product name" class="box">
   <input type="text" name="product_price" required placeholder="enter price" class="box">
   <input type="file" name="file" class="box">
   <input type="submit" name="register_product" class="btn" value="Register">
</form>

</div>

<div class="products">

   <h1 class="heading">latest products</h1>

   <div class="box-container">

   <?php
      $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
      if(mysqli_num_rows($select_product) > 0){
         while($fetch_product = mysqli_fetch_assoc($select_product)){
   ?>
      <form method="post" class="box" action="">
         <img src="images/<?php $fileName?>" alt="">
         <div class="name"><?php echo $fetch_product['name']; ?></div>
         <div class="price">$<?php echo $fetch_product['price']; ?>/-</div>
         <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
         <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
         <input type="hidden" name="product_id" value="<?php echo $fetch_product['id']; ?>">
         <input type="submit" value="Edit" name="edit" class="btn">
         <input type="submit" value="delete" name="delete" class="delete-btn" onclick="return confirm('are your sure you want to logout?')">
      </form>
   <?php
      };
   };
   ?>

   </div>


</div>

</body>
</html>
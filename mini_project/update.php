<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = ($name);

   $email = $_POST['email'];
   $email = ($email);
   
   if(!empty($name)){
      $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE user_id = ?");
      $update_name->execute([$name, $user_id]);
   }

   if(!empty($email)){
      $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
      $select_email->execute([$email]);
      if($select_email->rowCount() > 0){
         $message[] = '<p style="color:var(--red);">อีเมลถูกนำไปใช้แล้ว!</p>';
      }else{
         $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE user_id = ?");
         $update_email->execute([$email, $user_id]);
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE user_id = ?");
   $select_prev_pass->execute([$user_id]);
   $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
   $prev_pass = $fetch_prev_pass['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = ($old_pass);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = ($new_pass);
   $confirm_pass = sha1($_POST['confirm_pass']);
   $confirm_pass = ($confirm_pass);

   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){
         $message[] = '<p style="color:var(--red);">รหัสผ่านเก่าไม่ตรงกัน!</p>';
      }elseif($new_pass != $confirm_pass){
         $message[] = '<p style="color:var(--red);">ยืนยันรหัสผ่านไม่ตรงกัน!</p>';
      }else{
         if($new_pass != $empty_pass){
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE user_id = ?");
            $update_pass->execute([$confirm_pass, $user_id]);
            $message[] = '<p style="color:var(--red);">อัปเดตรหัสผ่านเรียบร้อยแล้ว!</p>';
         }else{
            $message[] = '<p style="color:var(--red);">กรุณากรอกรหัสผ่านใหม่!</p>';
         }
      }
   }  

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>อัพเดตโปรไฟล์</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

   <form action="" method="post">
      <h3>อัพเดตโปรไฟล์</h3>
      <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box" maxlength="50">
      <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="enter your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="บันทึก" name="submit" class="btn">
   </form>

</section>










<?php include 'components/footer.php'; ?>






<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
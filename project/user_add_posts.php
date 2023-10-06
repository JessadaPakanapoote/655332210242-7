<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['publish'])){

   $name = $_POST['name'];
   $name = ($name);
   $title = $_POST['title'];
   $title = ($title);
   $content = $_POST['content'];
   $content = ($content);
   $status = 'active';
   $image = $_FILES['image']['name'];
   $image = ($image);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND user_id = ?");
   $select_image->execute([$image, $user_id]);

   if(isset($image)){
      if($select_image->rowCount() > 0 AND $image != ''){
         $message[] = '<p style="color:var(--red);">ชื่อภาพซ้ำ!</p>';
      }elseif($image_size > 2000000){
         $message[] = '<p style="color:var(--red);">ขนาดภาพใหญ่เกินไป!</p>';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);
      }
   }else{
      $image = '';
   }

   if($select_image->rowCount() > 0 AND $image != ''){
      $message[] = '<p style="color:var(--red);">กรุณาเปลี่ยนชื่อภาพของคุณ!</p>';
   }else{
      $insert_post = $conn->prepare("INSERT INTO `posts`(user_id, name, title, content, image, status) VALUES(?,?,?,?,?,?)");
      $insert_post->execute([$user_id, $name, $title, $content, $image, $status]);
      $message[] = '<p style="color:#00c234;">กระทู้เผยแพร่แล้ว!</p>';
   }
   
}

if(isset($_POST['draft'])){

   $name = $_POST['name'];
   $name = ($name);
   $title = $_POST['title'];
   $title = ($title);
   $content = $_POST['content'];
   $content = ($content);
   $status = 'deactive';
   
   $image = $_FILES['image']['name'];
   $image = ($image);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND user_id = ?");
   $select_image->execute([$image, $user_id]); 

   if(isset($image)){
      if($select_image->rowCount() > 0 AND $image != ''){
         $message[] = '<p style="color:var(--red);">ชื่อภาพซ้ำ!</p>';
      }elseif($image_size > 2000000){
         $message[] = '<p style="color:var(--red);">ขนาดภาพใหญ่เกินไป!</p>';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);
      }
   }else{
      $image = '';
   }

   if($select_image->rowCount() > 0 AND $image != ''){
      $message[] = '<p style="color:var(--red);">กรุณาเปลี่ยนชื่อภาพของคุณ!</p>';
   }else{
      $insert_post = $conn->prepare("INSERT INTO `posts`(user_id, name, title, content, image, status) VALUES(?,?,?,?,?,?,?)");
      $insert_post->execute([$user_id, $name, $title, $content, $image, $status]);
      $message[] = '<p style="color:#00c234;">บันทึกแล้ว!</p>';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>posts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<style>
   .box1{
   width: 100%;
   margin:1rem 0;
   border-radius: .5rem;
   border: var(--border);
   padding:1.4rem;
   font-size: 1.8rem;
   color:var(--black);
   background-color: var(--light-bg);
   }
</style>

<body style="font-size: 40px;">
   
<?php include 'components/user_header.php'; ?>


<section class="posts-container" style="padding-bottom: 0;">

   <?php
      if($user_id != ''){  
   ?>
   <h1 class="heading">ตั้งกระทู้ใหม่</h1>
<div class="box-container" style="border: solid #34495E; text-align-last: auto; font-size: 
                                  1.8rem; border-radius: 30px; padding: 20px;">
<form action="" method="post" enctype="multipart/form-data">
   <input type="hidden" name="name" value="<?= $fetch_profile['name']; ?>">
   <p>ชื่อกระทู้ <span>*</span></p>
   <input type="text" name="title" maxlength="100" required placeholder="เพิ่มชื่อกระทู้" class="box1" >
   <p style="color:var(--light-color);">*พิมพ์ข้อความได้ไม่เกิร 100 ตัวอักษร.</p><br>
   <p>รายละเอียดของกระทู้ <span>*</span></p>
   <textarea name="content" class="box1" required maxlength="10000" placeholder="เขียนเนื้อหาของคุณ..." cols="30" rows="10"></textarea>
   <p style="color:var(--light-color);">*พิมพ์ข้อความได้ไม่เกิร 10000 ตัวอักษร.</p><br>
   <p>เพิ่มรูป</p>
   <input type="file" name="image" class="box1" accept="image/jpg, image/jpeg, image/png, image/webp">
   <p style="color:var(--light-color);">*ขนาดรูปต้องไม่เกิน 2 MB และรองรับแค่ jpg. jpeg. png. webp.</p><br>
   <div class="flex-btn">
      <input type="submit" value="ส่งกระทู้" name="publish" class="btn">
      <a href="view_list_posts.php" class="option-btn">ยกเลิก</a>
   </div>
</form>      
</div>
   <?php
   }else{
   ?>
   <div class="box" style="border: solid #34495E; text-align-last: auto; font-size: 
                                  1.8rem; border-radius: 30px; padding: 20px;">
      <p style="text-align: center; font-size: 1.8rem;">กรุณาเข้าสู่ระบบเพื่อตั้งกระทู้ใหม่ของคุณ</p>
      <a  href="login.php" class="btn">ล็อกอิน</a>
   </div>
   <?php
      }
   ?>


</section>
<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
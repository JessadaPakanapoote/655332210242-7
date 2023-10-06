<?php

include 'connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['save'])){

   $post_id = $_GET['id'];
   $title = $_POST['title'];
   $title = ($title);
   $content = $_POST['content'];
   $content = ($content);
   $status = 'active';

   $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, status = ? WHERE post_id = ?");
   $update_post->execute([$title, $content, $status, $post_id]);

   $message[] = '<p style="color:#00c234;">อัปเดตกระทู้แล้ว!</p>';
   
   $old_image = $_POST['old_image'];
   $image = $_FILES['image']['name'];
   $image = ($image);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND user_id = ?");
   $select_image->execute([$image, $user_id]);

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = '<p style="color:var(--red);">ขนาดภาพใหญ่เกินไป!</p>';
      }elseif($select_image->rowCount() > 0 AND $image != ''){
         $message[] = '<p style="color:var(--red);">กรุณาเปลี่ยนชื่อภาพของคุณ!</p>';
      }else{
         $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE post_id = ?");
         move_uploaded_file($image_tmp_name, $image_folder);
         $update_image->execute([$image, $post_id]);
         if($old_image != $image AND $old_image != ''){
            unlink('uploaded_img/'.$old_image);
         } 
         $message[] = '<p style="color:#00c234;">อัปเดตรูปภาพแล้ว!</p>';
      }
   }

   
}

if(isset($_POST['delete_post'])){

   $post_id = $_POST['post_id'];
   $post_id = ($post_id);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE post_id = ?");
   $delete_image->execute([$post_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('uploaded_img/'.$fetch_delete_image['image']);
   }
   $delete_post = $conn->prepare("DELETE FROM `posts` WHERE post_id = ?");
   $delete_post->execute([$post_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
   $delete_comments->execute([$post_id]);
   $message[] = '<p style="color:#00c234;">ลบกระทู้เรียบร้อยแล้ว!</p>';

}

if(isset($_POST['delete_image'])){

   $empty_image = '';
   $post_id = $_POST['post_id'];
   $post_id = ($post_id);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE post_id = ?");
   $delete_image->execute([$post_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('uploaded_img/'.$fetch_delete_image['image']);
   }
   $unset_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE post_id = ?");
   $unset_image->execute([$empty_image, $post_id]);
   $message[] = '<p style="color:#00c234;">ลบภาพสำเร็จ!</p>';

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
<body>

<?php include 'components/user_header.php'; ?>

<section class="post-editor">
<h1 class="heading">แก้ไขกระทู้</h1>
<div class="box-container" style="border: solid #34495E; text-align-last: auto; font-size: 
                                  1.8rem; border-radius: 30px; padding: 20px;">
   

   <?php
      $post_id = $_GET['id'];
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE post_id = ?");
      $select_posts->execute([$post_id]);
      if($select_posts->rowCount() > 0){
         while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_posts['image']; ?>">
      <input type="hidden" name="post_id" value="<?= $fetch_posts['post_id']; ?>">
      <p>ชื่อกระทู้ <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="เพิ่มชื่อกระทู้" class="box1" value="<?= $fetch_posts['title']; ?>">
      <p style="color:var(--light-color);">*พิมพ์ข้อความได้ไม่เกิร 100 ตัวอักษร.</p><br>
      <p>รายละเอียดของกระทู้ <span>*</span></p>
      <textarea name="content" class="box1" required maxlength="10000" placeholder="เขียนเนื้อหาของคุณ..." cols="30" rows="10"><?= $fetch_posts['content']; ?></textarea>
      </select>
      <p>เพิ่มรูป</p>
      <input type="file" name="image" class="box1" accept="image/jpg, image/jpeg, image/png, image/webp">
      <p style="color:var(--light-color);">*ขนาดรูปต้องไม่เกิน 2 MB และรองรับแค่ jpg. jpeg. png. webp.</p><br>
      <?php if($fetch_posts['image'] != ''){ ?>
         <img style="max-height: 500px;" src="uploaded_img/<?= $fetch_posts['image']; ?>" class="image" alt="">
         <input type="submit" value="delete image" class="inline-delete-btn" name="delete_image">
      <?php } ?>
      <div class="flex-btn">
         <input type="submit" value="save post" name="save" class="btn">
         <a href="view_list_posts.php" class="option-btn">go back</a>
      </div>
   </form>

   <?php
         }
      }else{
         header('location:view_list_posts.php');
   ?>
   <?php
      }
   ?>
<div>
</section>










<!-- custom js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
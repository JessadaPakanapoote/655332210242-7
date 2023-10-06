<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/like_post.php';

$get_id = $_GET['post_id'];


if(isset($_POST['add_comment'])){
   $name_id = $_POST['name_id'];
   $name_id = ($name_id);
   $user_name = $_POST['user_name'];
   $user_name = ($user_name);
   $comment = $_POST['comment'];
   $comment = ($comment);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ? AND name_id = ? AND user_id = ? AND user_name = ? AND comment = ?");
   $verify_comment->execute([$get_id, $name_id, $user_id, $user_name, $comment]);


   if($verify_comment->rowCount() > 0){
      $message[] = '<p style="color:#00c234;">เพิ่มความคิดเห็นแล้ว!</p>';
   }else{
      $insert_comment = $conn->prepare("INSERT INTO `comments`(post_id, name_id, user_id, user_name, comment) VALUES(?,?,?,?,?)");
      $insert_comment->execute([$get_id, $name_id, $user_id, $user_name, $comment]);
      $message[] = '<p style="color:#00c234;">เพิ่มความคิดเห็นใหม่แล้ว!</p>';
   }

}

if(isset($_POST['edit_comment'])){
   $edit_comment_id = $_POST['edit_comment_id'];
   $edit_comment_id = ($edit_comment_id);
   $comment_edit_box = $_POST['comment_edit_box'];
   $comment_edit_box = ($comment_edit_box);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE comment = ? AND id = ?");
   $verify_comment->execute([$comment_edit_box, $edit_comment_id]);

   if($verify_comment->rowCount() > 0){
      $message[] = '<p style="color:#00c234;">เพิ่มความคิดเห็นแล้ว!</p>';
   }else{
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$comment_edit_box, $edit_comment_id]);
      $message[] = '<p style="color:#00c234;">ความคิดเห็นของคุณแก้ไขเรียบร้อยแล้ว!</p>';
   }
}

if(isset($_POST['delete_comment'])){
   $delete_comment_id = $_POST['comment_id'];
   $delete_comment_id = ($delete_comment_id);
   $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
   $delete_comment->execute([$delete_comment_id]);
   $message[] = '<p style="color:#00c234;">ลบความคิดเห็นเรียบร้อยแล้ว!</p>';
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
   <title>view post</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<?php
   if(isset($_POST['open_edit_box'])){
   $comment_id = $_POST['comment_id'];
   $comment_id = ($comment_id);
?>
   <section class="comment-edit-form">
   <p>แก้ไขความคิดเห็นของคุณ</p>
   <?php
      $select_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
      $select_edit_comment->execute([$comment_id]);
      $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="POST">
      <input type="hidden" name="edit_comment_id" value="<?= $comment_id; ?>">
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="กรุณาใส่ความคิดเห็นของคุณ"><?= $fetch_edit_comment['comment']; ?></textarea>
      <button type="submit" class="inline-btn" name="edit_comment">แก้ไขความคิดเห็น</button>
      <div class="inline-option-btn" onclick="window.location.href = 'view_post.php?post_id=<?= $get_id; ?>';">ยกเลิกการแก้ไข</div>
   </form>
   </section>
<?php
   }
?>


<section class="posts-container" style="padding-bottom: 0;">
   
   <div class="box-container">

      <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? AND post_id = ?");
         $select_posts->execute(['active', $get_id]);
         if($select_posts->rowCount() > 0){
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
               
            $post_id = $fetch_posts['post_id'];

            $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            $total_post_comments = $count_post_comments->rowCount(); 

            $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            $total_post_likes = $count_post_likes->rowCount();

            $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
            $confirm_likes->execute([$user_id, $post_id]);
      ?>
      <form class="box" method="post">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <input type="hidden" name="name_id" value="<?= $fetch_posts['user_id']; ?>">
         <div class="post-admin">
            <i class="fas fa-user"></i>
            <div>
               <a href="author_posts.php?author=<?= $fetch_posts['name']; ?>"><?= $fetch_posts['name']; ?></a>
               <div><?= $fetch_posts['date']; ?></div>
            </div>
         </div>
         
         <?php
            if($fetch_posts['image'] != ''){  
         ?>
         <img src="uploaded_img/<?= $fetch_posts['image']; ?>" class="post-image" alt="">
         <?php
         }
         ?>
<!------------------------------------------------------------------------------>
         <div class="post-title" ><?= $fetch_posts['title']; ?></div>
         <div class="post-content" ><?= $fetch_posts['content']; ?></div>
         <div class="icons">
            <div><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></div>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if($confirm_likes->rowCount() > 0){ echo 'color:var(--red);'; } ?>  "></i><span>(<?= $total_post_likes; ?>)</span></button>
         </div>
         <div>
         <?php
               if($fetch_posts['user_id'] == $user_id){  
               ?>
            <!---------------------------------------------------------------------------------->
         <div class="flex-btn">
            <a href="components/edit_post.php?id=<?= $post_id; ?>" class="option-btn">แก้ไข</a>
               <button type="submit" name="delete_post" class="delete-btn" onclick="return confirm('delete this post?');">ลบ</button>
                  </div>
                     <?php
               
                     ?>
                  </div>
               <?php
                  }else{
                  }
                  ?>
         </div> <br>


      
      </form>
      <?php
         }
      }else{
         header('location:view_list_posts.php');
      }
      ?>

   </div>

</section>

<section class="comments-container">

   <p class="comment-title">เพิ่มความเห็น</p>
   <?php
      if($user_id != ''){  
         $select_name_id = $conn->prepare("SELECT * FROM `posts` WHERE post_id = ?");
         $select_name_id->execute([$get_id]);
         $fetch_name_id = $select_name_id->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="post" class="add-comment">
      <input type="hidden" name="name_id" value="<?= $fetch_name_id['user_id']; ?>">
      <input type="hidden" name="user_name" value="<?= $fetch_profile['name']; ?>">
      <p class="user"><i class="fas fa-user"></i><a href="update.php"><?= $fetch_profile['name']; ?></a></p>
      <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="เขียนความคิดเห็นของคุณ" required></textarea>
      <input type="submit" value="เพิ่มความเห็น" class="inline-btn" name="add_comment">
   </form>
   <?php
   }else{
   ?>
   <div class="add-comment">
      <p>กรุณาเข้าสู่ระบบเพื่อเพิ่มหรือแก้ไขความคิดเห็นของคุณ</p>
      <a href="login.php" class="inline-btn">ล็อกอิน</a>
   </div>
   <?php
      }
   ?>
   
   <p class="comment-title">ความคิดเห็น</p>
   <div class="user-comments-container">
      <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="show-comments" style="<?php if($fetch_comments['user_id'] == $user_id){echo 'order:-1;'; } ?>">
         <div class="comment-user">
            <i class="fas fa-user"></i>
            <div>
               <span><?= $fetch_comments['user_name']; ?></span>
               <div><?= $fetch_comments['date']; ?></div>
            </div>
         </div>
         <!---------------------------------------------------------------------------------->
         <div class="comment-box" style="<?php if($fetch_comments['user_id'] == $user_id)
         {echo 'color:var(--white); background:var(--black);'; } ?>"><?= $fetch_comments['comment']; ?>

         </div>
         <?php
            if($fetch_comments['user_id'] == $user_id){  
         ?>
          <!---------------------------------------------------------------------------------->
         <form action="" method="POST">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">แก้ไขความคิดเห็น</button>
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">ลบความคิดเห็น</button>
         </form>
         <?php
         }
         ?>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">ยังไม่มีความคิดเห็นเพิ่ม!</p>';
         }
      ?>
   </div>

</section>


<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>


</body>
</html>
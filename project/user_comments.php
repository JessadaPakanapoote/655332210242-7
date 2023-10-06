<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update profile</title>

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
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="please enter your comment"><?= $fetch_edit_comment['comment']; ?></textarea>
      <button type="submit" class="inline-btn" name="edit_comment">บันทึกการแก้ไข</button>
      <div class="inline-option-btn" onclick="window.location.href = 'user_comments.php';">ยกเลิกการแก้ไข</div>
   </form>
   </section>
<?php
   }
?>

<section class="comments-container">

   <h1 class="heading">ความคิดเห็นของคุณ</h1>

   <p class="comment-title">ความคิดเห็นของคุณในกระทู้</p>
   <div class="user-comments-container">
      <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
         $select_comments->execute([$user_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="show-comments">
         <?php
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ?");
            $select_posts->execute([$fetch_comments['post_id']]);
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
         ?>
         <div class="post-title"> from : <span><?= $fetch_posts['title']; ?></span> <a href="view_post.php?post_id=<?= $fetch_posts['id']; ?>" >view post</a></div>
         <?php
            }
         ?>
         <div class="comment-box"><?= $fetch_comments['comment']; ?></div>
         <form action="" method="POST">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">แก้ไขความคิดเห็น</button>
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">ลบความคิดเห็นดอ</button>
         </form>
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
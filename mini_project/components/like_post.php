<?php

if(isset($_POST['like_post'])){

   if($user_id != ''){
      
      $post_id = $_POST['post_id'];
      $post_id = ($post_id);

      
      $select_post_like = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ? AND user_id = ?");
      $select_post_like->execute([$post_id, $user_id]);

      if($select_post_like->rowCount() > 0){
         $remove_like = $conn->prepare("DELETE FROM `likes` WHERE post_id = ?");
         $remove_like->execute([$post_id]);
         $message[] = '<p style="color:var(--red);">ลบออกจากสิ่งที่คุณกดถูกใจ</p>';
      }else{
         $add_like = $conn->prepare("INSERT INTO `likes`(user_id, post_id) VALUES(?,?)");
         $add_like->execute([$user_id, $post_id]);
         $message[] = '<p style="color:#00c234;">เพิ่มในสิ่งที่คุณกดถูกใจ</p>';
      }
      
   }else{
         $message[] = '<p style="color:var(--red);">กรุณาเข้าสู่ระบบก่อน!</p>';
   }

}

?>
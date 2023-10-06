<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">ThaiRocketFestival.</a>

      <form action="search.php" method="POST" class="search-form">
         <input type="text" name="search_box" class="box" maxlength="100" placeholder="search for blogs" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <nav class="navbar">
         <a href="home.php"> <i class="fas fa-angle-right"></i> หน้าหลัก</a>
         <a href="user_add_posts.php"> <i class="fas fa-angle-right"></i> ตั้งกระทู้</a>
         <a href="view_list_posts.php"> <i class="fas fa-angle-right"></i> ดูกระทู้ทั้งหมด</a>
      </nav>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE user_id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p class="name"><?= $fetch_profile['name']; ?></p>
         <a href="update.php" class="btn">อัพเดตโปรไฟล์</a> 
         <a href="components/user_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">ออกจากระบบ</a>
         <?php
            }else{
         ?>
            <p class="name">กรุณาเข้าสู่ระบบก่อน!</p>
            <a href="login.php" class="option-btn">ล็อกอิน</a>
         <?php
            }
         ?>
      </div>

   </section>

</header>
<?php
$host = "localhost";
$port = "5433";
$dbname = "benhvien";
$user = "postgres";
$password = "12345"; 
$connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
$dbconn = pg_connect($connection_string);
if(isset($_POST['submit'])&&!empty($_POST['submit'])){
    
      $sql = "insert into public.user(name,email,password,mobno)values('".$_POST['name']."','".$_POST['email']."','".md5($_POST['pwd'])."','".$_POST['mobno']."')";
    $ret = pg_query($dbconn, $sql);
    if($ret){
        
            echo "Data saved Successfully";
            header("location:login.php");
    }else{
        
            echo "Soething Went Wrong";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Đăng ký </title>
  <meta name="keywords" content="PHP,PostgreSQL,Insert,Login">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <style>
    body {
        background:#daf5ee;
    }
    .form__data{
        padding: 3rem;
        width: 100%;
        height: 80vh;
        margin-left: 100%;
    }
    
  </style>
</head>
<body>
<div class="container">
    <div class="col-lg-4 col-md-8 col-sm-10 col-12 mx-lg-auto mx-md-auto mx-5m-auto mx-auto">
  
  <form method="post" class="form__data">
  <h2>Đăng Ký </h2>
    <div class="form-group">
      <label for="name">Tên:</label>
      <input type="text" class="form-control" id="name" placeholder="Nhập tên" name="name" requuired>
    </div>
    
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" placeholder="Nhập email" name="email">
    </div>
    
    <div class="form-group">
      <label for="pwd">Số điện thoại:</label>
      <input type="number" class="form-control" maxlength="10" id="mobileno" placeholder="Nhập số điện thoại" name="mobno">
    </div>
    
    <div class="form-group">
      <label for="pwd">Mật khẩu:</label>
      <input type="password" class="form-control" id="pwd" placeholder="Nhập mật khẩu" name="pwd">
    </div>
     
    <input type="submit" name="submit" class="btn btn-primary" value="Đăng Ký">
    <a href="login.php"  style="margin-left: 130px"> Đăng nhập</a>
  </form>
  </div>
</div>

</body>
</html>
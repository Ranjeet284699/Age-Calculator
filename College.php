<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        <h1>Details</h1>
        Student
        <input type="password" name="id"><br>

        name
        <input type="text" name="name"><br>

        Roll
        <input type="text" name="roll"><br>

        marks
        <input type="text" name="marks">

      <input type="submit" name="sb">
    </form>

    <?php
       $con = mysqli_connect('localhost','root','','college');
       if(isset($_post['sb']))
        {
            $Student=$_post['Id'];
            $name=$_post['name'];
            $Roll=$_post['roll'];
            $marks=$_post['marks'];

            $query = "INSERT INTO student (id, name, roll, marks)values ('$id', '$name', '$Roll', '$marks',)";
            $execute=mysqli_query($con,$query);
            
        }
    ?>
</body>
</html>
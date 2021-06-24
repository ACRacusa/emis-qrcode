<?php error_reporting (E_ALL ^ E_NOTICE); ?>
<?php
if ( isset($_POST["submit"]) ) {
    if (isset($_FILES["file"])) {
         if ($_FILES["file"]["error"] > 0) {
         } else {
            if (file_exists("upload/" . $_FILES["file"]["name"])) {
                unlink("upload/" . $_FILES["file"]["name"]);
                $storagename = "uploaded_file.csv";
                move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $storagename);
            } else {
                $storagename = "uploaded_file.csv";
                move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $storagename);
            }
         }
      }
    header( "refresh:2; url=parse.php" );
 }
?>

<table width="600">
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
        <tr>
            <td width="20%">Select file</td>
            <td width="80%"><input type="file" name="file" id="file" /></td>
        </tr>

        <tr>
            <td>Submit</td>
            <td><input type="submit" name="submit" /></td>
        </tr>

    </form>
</table>

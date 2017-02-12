<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<?php
$action=$_REQUEST['action'];
if ($action=="")    /* display the contact form */
    {
    ?>
    <form  action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="submit">
    Your name:<br>
    <input name="name" type="text" value="" size="30"/><br>
    Type of feedback:<br>
	 <input name="type" type="radio" value="Feedback">Feedback</input>
 	 <input name="type" type="radio" value="Update">Update</input>
    Your message:<br>
    <textarea name="message" rows="7" cols="30"></textarea><br>
    <input type="submit" value="Send email"/>
    </form>
    <?php
    } 
else                /* send the submitted data */
    {
    $name=$_REQUEST['name'];
    $type=$_REQUEST['type'];
    $message=$_REQUEST['message'];
    if (($name=="")||($type=="")||($message==""))
        {
		echo "All fields are required, please fill <a href=\"\">the form</a> again.";
	    }
    else{		
	    $from="From: $name";
        $subject="Kitpage: $type";
		  mail("dkearns@khviii.net", $subject, $message, $from);
		echo "Email sent!";
	    }
    }  
?>

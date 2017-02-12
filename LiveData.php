<?php
error_reporting(E_ALL); ini_set('display_errors','1');
require_once('inc/config.php');
$school_name = $db->query("SELECT * FROM `Config` WHERE `Option` LIKE 'school_name'")->fetch_object()->Value;
$schoolid = $db->query("SELECT * FROM `Config` WHERE `Option` LIKE 'schoolid'")->fetch_object()->Value;

print('<div class="header">');
print($school_name.' Todays Entries');
print('</div>');

$date = "2016-06-23";
$date = date('Y-m-d');
$results = $db->query("SELECT * FROM `Results` WHERE `date` like '".$date."'");//->fetch_object();
$num = $results->num_rows;


?>
<html>
<head>
<meta http-equiv="refresh" content="5" > 
   <link rel="stylesheet" type="text/css" href="style.css">
   <title>Enquiry Data</title>

	<script type="text/javascript" language="javascript">// <![CDATA[
		function checkAll(formname, checktoggle)
		{
			var checkboxes = new Array(); 
			checkboxes = document[formname].getElementsByTagName('input');
			for (var i=0; i<checkboxes.length; i++)  {
				if (checkboxes[i].type == 'checkbox')   {
					checkboxes[i].checked = checktoggle;
			}
			}
		}
		// ]]>
		function myFunction()
		{
			window.alert("sometext");
		}
	</script>

</head>
<body>
<div class="list">
<form name="export" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
<div>
<table>
	<tr>
	<th>ID</th>
	<th>Contact</th>
	<th>Pupil</th>
	<th>Marketing</th>
	<th>Phone</th>
	<th>EMail</th>
	<th>Current School</th>
	<th>First Visit</th>
<?php
print("<br>Searching for:".$date." ");
print("[".$results->num_rows." found]<br>");

$rownum=1;
$file = fopen("Enquiry.xml","w") or die("Unable to open file!");
fwrite($file,'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL);
fwrite($file,'<EnquiryImport>'.PHP_EOL);
fwrite($file,'<Header>'.PHP_EOL);
fwrite($file,'	<DocumentName>Enquiry Import File</DocumentName>'.PHP_EOL);
fwrite($file,'		<Version>1.0</Version>'.PHP_EOL);
fwrite($file,'		<DateTime>2013-8-19 14:43:3</DateTime>'.PHP_EOL);
fwrite($file,'		<DestSchool>'.PHP_EOL);
fwrite($file,'		<SchoolID>'.$schoolid.'</SchoolID>'.PHP_EOL);
fwrite($file,'		</DestSchool>'.PHP_EOL);
fwrite($file,'</Header>'.PHP_EOL);
fwrite($file,'<EnquiryCollection>'.PHP_EOL);

while($row = $results->fetch_assoc())
  {
	echo "<tr>";
//	echo '<td><input type="checkbox" name="select[]" value="'.$row['id'].'">';
	echo "<td>".$row['id']."</td>";
	echo "<td>".$row['title'] . " "
		. $row['surname']." ("
		. $row['relationship'].")</td>"
		. "<td>".$row['pupilfirstname']." ".$row['pupilsurname']."</td>"
		. "<td>".$row['marketing']."</td>"
		. "<td>".$row['phone']."</td>"
		. "<td>".$row['email']."</td>"
		. "<td>".$row['otherschool']."</td>";
	if($row["newvisit"])
	{ echo "<td>Yes</td>";} else { echo "<td>No</td>";}
	echo "</tr>";

	fwrite($file,'<Enquiry>'.PHP_EOL);
	fwrite($file,'<EnquiryID>'.$row['id'].'</EnquiryID>'.PHP_EOL);
	fwrite($file,'<MarketingSource>'.$row['marketing'].'</MarketingSource>'.PHP_EOL);
	fwrite($file,'<Enquirer>'.PHP_EOL);
	fwrite($file,'	<Title>'.$row['title'].'</Title>'.PHP_EOL);
	fwrite($file,'	<Surname>'.$row['surname'].'</Surname>'.PHP_EOL);
	fwrite($file,'	<Forename>'.$row['firstname'].'</Forename>'.PHP_EOL);
	fwrite($file,'	<Address>'.PHP_EOL);
	fwrite($file,'		<HouseNumber>'.$row['house'].'</HouseNumber>'.PHP_EOL);
	fwrite($file,'		<StreetDescription>'.$row['address'].'</StreetDescription>'.PHP_EOL);
	fwrite($file,'		<Town>'.$row['city'].'</Town>'.PHP_EOL);
	fwrite($file,'		<Postcode>'.$row['postcode'].'</Postcode>'.PHP_EOL);
	fwrite($file,'		<Country>'.$row['country'].'</Country>'.PHP_EOL);
	fwrite($file,'	</Address>'.PHP_EOL);
	fwrite($file,'	<Relationship>'.$row['relationship'].'</Relationship>'.PHP_EOL);
	fwrite($file,'	<Contact>'.PHP_EOL);
	fwrite($file,'		<HomeEmailAddress>'.$row['email'].'</HomeEmailAddress>'.PHP_EOL);
	fwrite($file,'		<HomeTelephoneNumber>'.$row['phone'].'</HomeTelephoneNumber>'.PHP_EOL);
	fwrite($file,'		<WorkTelephoneNumber></WorkTelephoneNumber>'.PHP_EOL);
	fwrite($file,'		<MobileTelephoneNumber></MobileTelephoneNumber>'.PHP_EOL);
	fwrite($file,'	</Contact>'.PHP_EOL);
	fwrite($file,'</Enquirer>'.PHP_EOL);
	fwrite($file,'<ProspectivePupilCollection>'.PHP_EOL);
	fwrite($file,'<ProspectivePupil>'.PHP_EOL);
	fwrite($file,'	<Surname>'.$row['pupilsurname'].'</Surname>'.PHP_EOL);
	fwrite($file,'	<Forename>'.$row['pupilfirstname'].'</Forename>'.PHP_EOL);
	fwrite($file,'	<Gender>'.$row['gender'].'</Gender>'.PHP_EOL);
	fwrite($file,'	<DOB>'.$row['pupildob'].'</DOB>'.PHP_EOL);
	fwrite($file,'	<CurrentSchool>'.$row['otherschool'].'</CurrentSchool>'.PHP_EOL);
	fwrite($file,'</ProspectivePupil>'.PHP_EOL);
	fwrite($file,'</ProspectivePupilCollection>'.PHP_EOL);
	fwrite($file,'</Enquiry>'.PHP_EOL);
	fwrite($file,''.PHP_EOL);

	$rownum++;
  }
	fwrite($file,'</EnquiryCollection>'.PHP_EOL);
	fwrite($file,'</EnquiryImport>'.PHP_EOL);
	fclose($file);
	$_POST['date'] = $date;
?>
</table>
</div>
<br/>
</form>
<a href="index.html">Menu</a>

</body>
</html>


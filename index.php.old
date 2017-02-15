<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<?php
error_reporting(E_ALL); ini_set('display_errors','1');
require_once('config.php');
$armMin=0;
$armMax=150;
$class="Amulet";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$armMin=$_POST["armouryMin"];
	$armMax=$_POST["armouryMax"];
}
?>

<html>
<head>
<title>Elephant Kit List</title>
<link rel="stylesheet" type="text/css" href="style.css">
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-91781728-1', 'auto');
  ga('send', 'pageview');

</script>
<script src="sorttable.js"></script>
<script>
function checkType() {
var weapons = ["Blade", "Blunt", "Dagger", "Polearm", "Projectile", "Two-Handed", "Whip"];
if (weapons.indexOf(document.getElementById("type").value) === -1) {
	document.getElementById("slot").disabled = false;
} else {
	document.getElementById("slot").disabled = true;
}
}
</script>
</head>
<body>
<form method="post" name="armour" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<select name="class" id="type" onchange="checkType()">
<optgroup>
	<option value='Blade'>Blade</option>
	<option value='Blunt'>Blunt</option>
	<option value='Dagger'>Dagger</option>
	<option value='Polearm'>Polearm</option>
	<option value='Projectile'>Projectile</option>
	<option value='Staff'>Staff</option>
	<option value='Two-Handed'>Two-Handed</option>
	<option value='Whip'>Whip</option>
</optgroup>
<optgroup label="----------">
	<option value='Cloth'>Cloth</option>
	<option value='Mail'>Mail</option>
	<option value='Leather'>Leather</option>
	<option value='Plate'>Plate</option>
</optgroup>
</select>
<select id="slot" name="slot" disabled="true">
	<option value="%">All</option>
	<option value="Head">Head</option>
	<option value="Body">Body</option>
	<option value="Hands">Hands</option>
	<option value="Legs">Legs</option>
	<option value="Feet">Feet</option>
</select>
Min:<input type="number" name="armouryMin" min="0" max="150" value="<?php print($armMin);?>">
Max:<input type="number" name="armouryMax" min="1" max="150" value="<?php print($armMax);?>">
<input type="submit" value="Search">
</form>
<!--
<form method="post" name="weapons" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<select name="weapontype">
<?php
foreach (array("Blade","Blunt","Dagger","Polearm","Projectile","Staff","Two-Handed","Whip") as $idx => $value)
	print("<option name='".$value."'>".$value."</option>");
?>
</select>
</form>
-->
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	print("<br>");
	$alt = true;
	if (in_array($_POST["class"],array("Blade","Blunt","Dagger","Polearm","Projectile","Staff","Two-Handed","Whip","Amulet","Ring","Shield")))
	{
	$results = $db->query("SELECT * FROM `equipment` WHERE `type` like '".$_POST["class"]."' AND `Skill` BETWEEN ".$_POST["armouryMin"]." AND ".$_POST["armouryMax"]." ORDER BY `Skill` DESC");
	} else {
	$results = $db->query("SELECT * FROM `equipment` WHERE `type` like '".$_POST["class"]."' AND `Skill` BETWEEN ".$_POST["armouryMin"]." AND ".$_POST["armouryMax"]." AND `Slot` LIKE '".$_POST["slot"]."' ORDER BY `Skill` DESC");
	}
	print("<div class='datagrid'><table class='sortable'><thead><tr><th>Name</th><th>Weight</th><th>Skill</th><th>Location</th><th>Notes</th></tr></thead><tr>");
	while($row = $results->fetch_assoc())
	{
		if ($alt) {
		print("<tr>");
		$alt = !$alt;
		} else {
		print("<tr class='alt'>");
		$alt = !$alt;
		}
		print("<td>".$row['Name']."</td><td>".$row['Weight']."</td><td>".$row['Skill']."</td><td>".$row['Location']."</td><td>".$row['Notes']."</td>\n");
		print("</tr>");
	}
	print("</table></div>");
}
include("counter.php");
?>

<style>
body {
	background-color: black;
	color: white;
}
</style>
<?php
$start_time = microtime(TRUE);
if(isset($_GET["vnum"])) $start_vnum = $_GET["vnum"]; else $start_vnum = 1;

define(MAP_EXIT_N,1);
define(MAP_EXIT_E,2);
define(MAP_EXIT_S,4);
define(MAP_EXIT_W,8);
define(MAP_EXIT_U,16);
define(MAP_EXIT_D,32);
define(MAP_EXIT_NE,64);
define(MAP_EXIT_NW,128);
define(MAP_EXIT_SE,256);
define(MAP_EXIT_SW,512);
define(MAP_DIR_N,(1 << MAP_EXIT_N));
define(MAP_DIR_E,(1 << MAP_EXIT_E));
define(MAP_DIR_S,(1 << MAP_EXIT_S));
define(MAP_DIR_W,(1 << MAP_EXIT_W));
define(MAP_DIR_U,(1 << MAP_EXIT_U));
define(MAP_DIR_D,(1 << MAP_EXIT_D));
define(MAP_DIR_NE,(1 << (MAP_EXIT_N|MAP_EXIT_E)));
define(MAP_DIR_NW,(1 << (MAP_EXIT_N|MAP_EXIT_W)));
define(MAP_DIR_SE,(1 << (MAP_EXIT_S|MAP_EXIT_E)));
define(MAP_DIR_SW,(1 << (MAP_EXIT_S|MAP_EXIT_W)));
define(EXIT_FLAG_HIDE,(1 << 0));

$colour = array("Black", "Red", "Green", "Yellow", "Blue", "Magenta", "Cyan", "White");

function get_arg_in_braces($str){
	$line = substr(str_replace("{","",$str),1);
	$ret = array_map('trim',explode("}",$line));
	return $ret;
}

function draw_room($room,$line){
	global $map;
	global $start_vnum;

	$display = "";
	if ($room == NULL)
		$display .= "      ";
	else {
	switch ($line) {
		case "1":
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_NW) $display .= "\\ "; else $display .= "  ";
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_N) $display .= "|"; else $display .= " ";
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_U) $display .=  "+"; else $display .= " ";
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_NE) $display .= "/ "; else $display .= "  ";
		break;
		case "2":
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_W) $display .= "-"; else $display .= " ";
			if ($map->room_list[$room]->vnum == $start_vnum) {
				$display .= "<a href='map.php?vnum=".$map->room_list[$room]->vnum."' title='[".$map->room_list[$room]->vnum."] ".$map->room_list[$room]->desc."'>[#]</a>";
			} else {
			$display .= "<a href='map.php?vnum=".$map->room_list[$room]->vnum."' title='[".$map->room_list[$room]->vnum."] ".$map->room_list[$room]->desc."'>[ ]</a>";
			}
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_E) $display .= "-"; else $display .= " ";
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_E) $display .= "-"; else $display .= " ";
		break;
		case "3":
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_SW) $display .= "/"; else $display .= " ";
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_D) $display .= "-"; else $display .= " ";
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_S) $display .= "| "; else $display .= "  ";
			if ($map->room_list[$room]->exit_dirs & MAP_DIR_SE) $display .= "\\ "; else $display .= "  ";
		break;
		}
	}
	return $display;
}

function create_exit($room,$data){
	global $map;
	$newexit->vnum = intval(trim($data[0]));
	$newexit->name = trim($data[1]);
	$newexit->cmd = trim($data[2]);
	$newexit->dir = intval(trim($data[3]));
	$newexit->flags = intval(trim($data[4]));
	$newexit->data = $data[5];
	$map->room_list[$room->vnum]->f_exit[] = $newexit;
	$map->room_list[$room->vnum]->l_exit[] = $newexit;
	$map->room_list[$room->vnum]->exit_size++;
	$map->room_list[$room->vnum]->exit_dirs |= (1 << $newexit->dir);
}

function create_room($data){
	global $map;
	$newroom = new stdClass();
	$newroom->vnum = intval(trim($data[0]));
	$newroom->flags = $data[1];
	$newroom->colour = $data[2];
	$newroom->name = $data[3];
	$newroom->symbol = $data[4];
	$newroom->desc = preg_replace('/\x1b(\[|\(|\))[;?0-9]*[0-9A-Za-z]/', "",$data[5]);
	$newroom->area = $data[6];
	$newroom->note = $data[7];
	$newroom->terrain = $data[8];
	$newroom->data = $data[9];
	$newroom->weight = $data[10];
	$newroom->exit_dirs = 0;
	$newroom->exit_size = 0;
	$map->room_list[$newroom->vnum] = $newroom;
	return $newroom;
}

function HAS_BIT($data,$bit){
	return ($data & $bit);
}


function mygrid_build($vnum, $x, $y, $z)
{
	global $map;
	$map_grid_x = $x;
	$map_grid_y = $y;

	$room = $map->room_list[$vnum];

	$node = new stdClass();
	$node->x = $x / 2;
	$node->y = $y / 2;
	$node->z = $z / 2;

	for ($vnum = 0 ; $vnum < $x * $y ; $vnum++)
	{
		$map->grid[vnum] = NULL;
	}


	$map->grid[$node->x + $map_grid_x * $node->y] = $room;

//	for ($index = 0;$index<=sizeof($map->room_list);$index++)
	for ($index = 0;$index <= 50000;$index++)
	{
		if ($map->room_list[$temp->vnum]->display_stamp != 1)
		{
		foreach ($room->f_exit as $exit)
		{
//			if ($exit->vnum == $last)
//				continue;
			$temp->vnum = $exit->vnum;
			$temp->x = $node->x;
			$temp->y = $node->y;
			if (HAS_BIT($exit->dir,MAP_EXIT_E)) $temp->x = $node->x + 1;
			if (HAS_BIT($exit->dir,MAP_EXIT_W)) $temp->x = $node->x - 1;
			if (HAS_BIT($exit->dir,MAP_EXIT_N)) $temp->y = $node->y - 1;
			if (HAS_BIT($exit->dir,MAP_EXIT_S)) $temp->y = $node->y + 1;

			if (HAS_BIT($exit->dir,MAP_EXIT_N) & HAS_BIT($exit->dir,MAP_EXIT_W)) {$temp->x = $node->x - 1 ; $temp->y = $node->y - 1;}
			if (HAS_BIT($exit->dir,MAP_EXIT_N) & HAS_BIT($exit->dir,MAP_EXIT_E)) {$temp->x = $node->x + 1 ; $temp->y = $node->y - 1;}
			if (HAS_BIT($exit->dir,MAP_EXIT_S) & HAS_BIT($exit->dir,MAP_EXIT_W)) {$temp->x = $node->x - 1 ; $temp->y = $node->y + 1;}
			if (HAS_BIT($exit->dir,MAP_EXIT_S) & HAS_BIT($exit->dir,MAP_EXIT_E)) {$temp->x = $node->x + 1 ; $temp->y = $node->y + 1;}

			$temp->length = $node->length + 1;
			$map->grid[$temp->x + $map_grid_x * $temp->y] = $map->room_list[$temp->vnum];
			$map->room_list[$room->vnum]->display_stamp = 1;
			$last = $room->vnum;
			$next = $exit->vnum;
		}
		$room = $map->room_list[$next];
		$node->x = $temp->x;
		$node->y = $temp->y;
		$node->z = $temp->z;
		}
	}
	showgrid($x,$y);
}

function showgrid($x,$y)
{
	global $map;
	for ($rows = 1; $rows <= $y; $rows++)
	{
	for ($line = 1 ; $line <= 3; $line++)
	{
		$buf = "";
		for ($col = 0 ; $col <= $x; $col++)
		{
			$buf .= draw_room($map->grid[$col + $x * $rows]->vnum,$line);
		}
		echo $buf."<br>";
	}
	}
}

function map_read($file)
{
$mapfile = fopen($file,"r") or die("Error opening file.");

if ($mapfile) {
	while (($line = fgets($mapfile)) !== false) {
	switch (substr($line,0,1)) {
		case "R":
		$room = create_room(get_arg_in_braces($line));
		break;
		case "E":
		create_exit($room,get_arg_in_braces($line));
		break;
		}
	}
}
fclose($mapfile);
}

map_read("ele.map");

echo "Rooms:".sizeof($map->room_list)."<br><hr><pre><br>";

mygrid_build($start_vnum,20,10,0);


$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken,5);
echo '<center><small>Page generated in '.$time_taken.' seconds.</small></center>';
//var_dump($map->room_list[59]);
?>

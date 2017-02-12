<style>
body {
	background-color: black;
	color: white;
}
</style>
<?php
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

function get_arg_in_braces($str){
	$line = str_replace("{","",substr($str,2,strlen($str)));
	return explode("}",$line);
}

function draw_room($room,$line){
	global $room_list;
	$display = "";
	switch ($line) {
		case "1":
			if ($room_list[$room]->exit_dirs & MAP_DIR_NW) $display .= "\\ "; else $display .= "  ";
			if ($room_list[$room]->exit_dirs & MAP_DIR_N) $display .= "|"; else $display .= " ";
			if ($room_list[$room]->exit_dirs & MAP_DIR_U) $display .=  "+"; else $display .= " ";
			if ($room_list[$room]->exit_dirs & MAP_DIR_NE) $display .= "/ "; else $display .= "  ";
		break;
		case "2":
			if ($room_list[$room]->exit_dirs & MAP_DIR_W) $display .= "-"; else $display .= " ";
			$display .= "[ ]";
			if ($room_list[$room]->exit_dirs & MAP_DIR_E) $display .= "-"; else $display .= " ";
		break;
		case "3":
			if ($room_list[$room]->exit_dirs & MAP_DIR_SW) $display .= "/"; else $display .= " ";
			if ($room_list[$room]->exit_dirs & MAP_DIR_D) $display .= "-"; else $display .= " ";
			if ($room_list[$room]->exit_dirs & MAP_DIR_S) $display .= "| "; else $display .= "  ";
			if ($room_list[$room]->exit_dirs & MAP_DIR_SE) $display .= "\\ "; else $display .= "  ";
		break;
		}
	return $display;
}

function create_exit($data){
	global $room_list;
	global $room;
	$newexit = new stdClass();
	$newexit->vnum = intval(trim($data[0]));
	$newexit->name = trim($data[1]);
	$newexit->cmd = trim($data[2]);
	$newexit->dir = intval(trim($data[3]));
	$newexit->flags = intval(trim($data[4]));
	$newexit->data = $data[5];
	$room_list[$room->vnum]->f_exit = $newexit;
	$room_list[$room->vnum]->l_exit = $newexit;
	$room_list[$room->vnum]->exit_size++;
	$room_list[$room->vnum]->exit_dirs |= (1 << $newexit->dir);
}

function create_room($data){
	global $room_list;
	$newroom = new stdClass();
	$newroom->vnum = intval(str_replace(" ","",$data[0]));
	$newroom->flags = $data[1];
	$newroom->colour = $data[2];
	$newroom->name = $data[3];
	$newroom->symbol = $data[4];
	$newroom->desc = $data[5];
	$newroom->area = $data[6];
	$newroom->note = $data[7];
	$newroom->terrain = $data[8];
	$newroom->data = $data[9];
	$newroom->weight = $data[10];
	$newroom->exit_dirs = 0;
	$newroom->exit_size = 0;
	$room_list[$newroom->vnum] = $newroom;
	return $newroom;
}

function HAS_BIT($data,$bit){
	return ($data & $bit);
}

function displaygrid_build($vnum, $x, $y, $z)
{
	global $room_list;
	$head = 0;
	$tail = 1;
	$map_grid_x = $x;
	$map_grid_y = $y;

	$grid_node = new stdClass();
	$node = new stdClass();
	$temp = new stdClass();
//	$list = new stdClass();

define(MAP_BF_SIZE,50000);//sizeof($room_list));
//	node = &list[head];

	$node->vnum   = $vnum;
	$node->x      = $x / 2;
	$node->y      = $y / 2;
	$node->z      = $z / 2;
	$node->length = 0;

	$map->display_stamp++;

	for ($vnum = 0 ; $vnum < $x * $y ; $vnum++)
	{
		$map->grid[vnum] = NULL;
	}

	while ($head != $tail)
	{
		$node = $list[$head];
		$head = ($head + 1) % MAP_BF_SIZE;

		$room = $map->room_list[$node->vnum];

		if ($map->display_stamp != $room->display_stamp)
		{
			$room->display_stamp = $map->display_stamp;
		}
		else if ($room->length <= $node->length)
		{
			continue;
		}

		$room->length = $node->length;

		if ($node->x >= 0 && $node->x < $map_grid_x && $node->y >= 0 && $node->y < $map_grid_y && $node->z == 0)
		{
			if ($map->grid[$node->x + $map_grid_x * $node->y] == NULL)
			{
				$map->grid[$node->x + $map_grid_x * $node->y] = $room;
			}
			else
			{
				continue;
			}
		}
		for ($exit = $room->f_exit ; $exit ; $exit = $exit->next)
		{
			if ($map->display_stamp == $map->room_list[$exit->vnum]->$display_stamp)
			{
				if ($room->length >= $map->room_list[$exit->vnum]->length)
				{
					continue;
				}
			}

			if ($exit->dir == 0)
			{
				continue;
			}

			if (HAS_BIT($exit->flags, EXIT_FLAG_HIDE) || HAS_BIT($map->room_list[$exit->vnum]->flags, ROOM_FLAG_HIDE))
			{
				continue;
			}
			if ($head == ($tail + 1) % MAP_BF_SIZE)
			{
				break;
			}

			$temp = $list[$tail];

			$temp->vnum   = $exit->vnum;
			$temp->x      = $node->x + (HAS_BIT($exit->dir, MAP_EXIT_E) ?  1 : HAS_BIT($exit->dir, MAP_EXIT_W) ? -1 : 0);
			$temp->y      = $node->y + (HAS_BIT($exit->dir, MAP_EXIT_N) ?  1 : HAS_BIT($exit->dir, MAP_EXIT_S) ? -1 : 0);
			$temp->z      = $node->z + (HAS_BIT($exit->dir, MAP_EXIT_U) ?  1 : HAS_BIT($exit->dir, MAP_EXIT_D) ? -1 : 0);
			$temp->length = $node->length + 1;

			$tail = ($tail + 1) % MAP_BF_SIZE;
		}
	}
var_dump($map);
}


function displaygrid_build_own($vnum, $x, $y, $z){
	global $room_list;
	global $map;
	define(MAP_BF_SIZE,sizeof($room_list));
	$map_grid_x = $x;
	$map_grid_y = $y;
	$head = 0;
	$tail = 1;

	$node = new stdClass();
	$node->vnum = $vnum;
	$node->x = $x;
	$node->y = $y;
	$node->z = $z;
	$node->length = 0;

	$map = new stdClass();

	for ($vnum = 0 ; $vnum < $x * $y ; $vnum++)
	{
		$map->grid[vnum] = null;
	}

	while ($head != $tail)
	{
		$node = $list[$head];
		$head = ($head + 1) % MAP_BF_SIZE;
		$room = $room_list[$node->vnum];
		if ($node->x >= 0 && $node->x < $map_grid_x && $node->y >= 0 && $node->y < $map_grid_y && $node->z == 0)
		{
			if ($map->grid[$node->x + $map_grid_x * $node->y] == NULL)
			{
				$map->grid[$node->x + $map_grid_x * $node->y] = $room;
			}
			else
			{
				continue;
			}
		}
		for ($exit = $room->f_exit ; $exit ; $exit = $exit->next)
		{
		echo "1";
		}
	}
var_dump($map);
	for ($y = $map_grid_y -1 ; $y >= 0; $y--){
		for ($line = 1 ; $line <= 3 ; $line++)
		{
			$buf = "";
			for ($x = 0 ; $x < $map_grid_x ; $x++)
			{
//				$buf =. draw_room(ses->map->grid[x + map_grid_x * y], line));
			}
			print($buf."<br>");
		}
	}
}

$mapfile = fopen("ele.map","r") or die("Error opening file.");

if ($mapfile) {
	while (($line = fgets($mapfile)) !== false) {
	switch (substr($line,0,1)) {
		case "R":
		$room = create_room(get_arg_in_braces($line));
		break;
		case "E":
		create_exit(get_arg_in_braces($line));
		break;
		}
	}
}
fclose($mapfile);

$grid = "20x10";
echo "Rooms:".sizeof($room_list)."<br><hr><pre><br>";

$map_grid_x = intval(explode("x",$grid)[0]);
$map_grid_y = intval(explode("x",$grid)[1]);

print(draw_room(59,1)."<br>");
print(draw_room(59,2)."<br>");
print(draw_room(59,3)."<br>");

displaygrid_build(59,20,10,0);
?>

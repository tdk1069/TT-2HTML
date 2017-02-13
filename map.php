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
	$line = substr(str_replace("{","",$str),1);
	$ret = array_map('trim',explode("}",$line));
	return $ret;
}

function draw_room($room,$line){
	global $map;

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
			$display .= "[ ]";
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
	$newexit = new stdClass();
	$newexit->vnum = intval(trim($data[0]));
	$newexit->name = trim($data[1]);
	$newexit->cmd = trim($data[2]);
	$newexit->dir = intval(trim($data[3]));
	$newexit->flags = intval(trim($data[4]));
	$newexit->data = $data[5];
	$map->room_list[$room->vnum]->f_exit = $newexit;
	$map->room_list[$room->vnum]->l_exit = $newexit;
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
	$newroom->desc = $data[5];
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
	//echo $node->x + $map_grid_x * $node->y;

	$map->grid[$node->x + $map_grid_x * $node->y] = $room;

	showgrid($x,$y);
}

function displaygrid_build($vnum, $x, $y, $z)
{
	global $map;
	define(MAP_BF_SIZE,sizeof($map->room_list));
//	define(MAP_BF_SIZE,100000);
	$head = 0;
	$tail = MAP_BF_SIZE;
	$map_grid_x = $x;
	$map_grid_y = $y;

	$grid_node = new stdClass();
	$node = new stdClass();
	$temp = new stdClass();

	$node = $list[$head];

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
		$head = ($head + 1);// % MAP_BF_SIZE;
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
			$buf .= draw_room($map->grid[$col + $x *$rows]->vnum,$line);
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

mygrid_build(59,10,10,0);
?>

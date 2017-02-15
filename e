/ACTION {%* collapses before the ferocious attack.}
{
	release soul;
	get coins
}
{5}

/ACTION {%* collapses to the ground in a heap.}
{
	release soul;
	get coins
}
{5}

/ACTION {%* staggers and falls to the ground... dead!}
{
	release soul;
	get coins
}
{5}

/ACTION {%?: %+]}
{
	/variable {exits} {%2};
	/replace {exits} {, } {;}
}
{5}

/ACTION {You cannot go that way!}
{
	/map undo
}
{5}

/ACTION {You run away east!}
{
	/map move e
}
{5}

/ACTION {You run away north!}
{
	/map move n
}
{5}

/ACTION {You run away northeast!}
{
	/map move ne
}
{5}

/ACTION {You run away northwest!}
{
	/map move nw
}
{5}

/ACTION {You run away south!}
{
	/map move s
}
{5}

/ACTION {You run away southeast!}
{
	/map move se
}
{5}

/ACTION {You run away southwest!}
{
	/map move sw
}
{5}

/ACTION {You run away west!}
{
	/map move w
}
{5}

/ACTION {Your shield dissipates....}
{
	shield
}
{5}

/ACTION {[%?:%*]}
{
	/buffer get rdesc 1;
	/map set roomdesc $rdesc
}
{5}

/ACTION {] add %*}
{
	party add %1
}
{5}

/ACTION {has invited you to join party '%*'.}
{
	party join %1
}
{5}

/ACTION {keels over and dies.}
{
	release soul;
	get coins
}
{5}

/ALIAS {dig/foreach}
{
	$exits
}
{say}

/ALIAS {dig}
{
	/foreach {$exits} {say}
	{
		/map dig $say
	}
}
{5}

/ALIAS {m}
{
	/map map
}
{5}

/ALIAS {mm}
{
	/map map
}
{5}

/ALIAS {run}
{
	/map run {%1} 0.4
}
{5}

/CONFIG           {256 COLORS}  {ON}
/CONFIG           {AUTO TAB}  {5000}
/CONFIG           {BUFFER SIZE}  {20000}
/CONFIG           {CHARSET}  {ASCII}
/CONFIG           {COLOR PATCH}  {ON}
/CONFIG           {COMMAND COLOR}  {<078>}
/CONFIG           {COMMAND ECHO}  {ON}
/CONFIG           {CONNECT RETRY}  {0}
/CONFIG           {HISTORY SIZE}  {1000}
/CONFIG           {LOG}  {RAW}
/CONFIG           {PACKET PATCH}  {0.00}
/CONFIG           {REPEAT CHAR}  {!}
/CONFIG           {REPEAT ENTER}  {OFF}
/CONFIG           {SCROLL LOCK}  {ON}
/CONFIG           {SPEEDWALK}  {OFF}
/CONFIG           {TINTIN CHAR}  {/}
/CONFIG           {VERBATIM}  {OFF}
/CONFIG           {VERBATIM CHAR}  {\}
/CONFIG           {VERBOSE}  {OFF}
/CONFIG           {WORDWRAP}  {ON}
/MACRO {\e[11~}
{
	dig
}

/PATHDIR          {d}  {u}  {32}
/PATHDIR          {e}  {w}  {2}
/PATHDIR          {n}  {s}  {1}
/PATHDIR          {ne}  {sw}  {3}
/PATHDIR          {nw}  {se}  {9}
/PATHDIR          {s}  {n}  {4}
/PATHDIR          {se}  {nw}  {6}
/PATHDIR          {sw}  {ne}  {12}
/PATHDIR          {u}  {d}  {16}
/PATHDIR          {w}  {e}  {8}
/VARIABLE         {exits}  {n;ne;e;sw;w;nw}
/VARIABLE         {rdesc}  {[0;37;40mPath on the western side of the Withered Peaks[0;37;40m[0m}
/VARIABLE         {say}  {nw}

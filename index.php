<?php
include 'inc/dbinc.php';
include 'inc/entry.php';

$example_string="---Create
dank=true
memes=false
memes=true // just kidding Kappa 123
direction = irandom_range(0,7)*45
speed = 3
image_speed = 1/15
a_string=\"this is a string\"
/* first line
multiline comment
last line*/
/*** single line multiline comment */

---Step
image_blend = make_color_hsv(random(360),255,255)
image_alpha = random_range(0.25,0.75)
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code
//many lines of code

---Draw
draw_self()
if random() < 0.1 {
    draw_set_color(c_black)
    draw_set_alpha(1)
    draw_set_font(fontmeme)
    draw_set_halign(fa_center)
    draw_set_valign(fa_center)
    draw_text(view_xview+random(800),view_yview+random(608),'neat')
}
//comment at end";

$input="";
if (isset($_POST['input-code'])) $input=$_POST['input-code'];

$method="";
if (isset($_POST['preview'])) $method="preview";
else if (isset($_POST['example'])) {
	$method="preview";
	$input=$example_string;
} else if (isset($_POST['create'])) $method="create";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	//split request URI, remove empty elements, and rebase array to 0
	$arr = array_values(array_filter(split("/",$_SERVER['REQUEST_URI'])));
	$arr_size=count($arr);
	if ($arr_size>=2 && $arr[$arr_size-2]==='gm') { //gm & hash
		$hash = $arr[$arr_size-1];
		$entry = getEntry($hash);
		$input = $entry->getTextContent();
		$method="display";
	} else if ($arr_size>=2 && $arr[$arr_size-3]==='gm') { //gm & hash & editor hash
		$hash = $arr[$arr_size-1];
		$entry = getEntry($hash);
		$input = $entry->getTextContent();
		$method="edit";
	}
}

if ($method=="preview" || $method=="display" || $method=="edit") {
	$lines = explode("\n", $input);
	$output=array();
	$o_i=-1;
	for ($i=0;$i<count($lines);$i+=1) {
		if (substr($lines[$i],0,3)==='---') {
			$o_i++;
			$output[$o_i][0]=substr($lines[$i],3);
			$output[$o_i][1]="";
		} else {
			if ($o_i>=0) {
				$output[$o_i][1].=$lines[$i]."\n";
			}
		}
	}
} else if ($method=="create") {
	$entry = addEntry($input,'test title','test description');
	if (!is_null($entry)) {
		header('Location: /gm/'.$entry->hash.'/'.$entry->editor_hash);
		die();
	}
}

function syntax_highlight($input) {
	$KEYWORDS = array(
		"argument",
		"argument0",
		"argument1",
		"argument10",
		"argument11",
		"argument12",
		"argument13",
		"argument14",
		"argument15",
		"argument2",
		"argument3",
		"argument4",
		"argument5",
		"argument6",
		"argument7",
		"argument8",
		"argument9",
		"argument_count",
		"argument_relative",
		"background_alpha",
		"background_blend",
		"background_color",
		"background_foreground",
		"background_height",
		"background_hspeed",
		"background_htiled",
		"background_index",
		"background_showcolor",
		"background_visible",
		"background_vspeed",
		"background_vtiled",
		"background_width",
		"background_x",
		"background_xscale",
		"background_y",
		"background_yscale",
		"caption_health",
		"caption_lives",
		"caption_score",
		"current_day",
		"current_hour",
		"current_minute",
		"current_month",
		"current_second",
		"current_time",
		"current_weekday",
		"current_year",
		"cursor_sprite",
		"debug_mode",
		"display_aa",
		"error_last",
		"error_occurred",
		"event_action",
		"event_number",
		"event_object",
		"event_type",
		"fps",
		"game_id",
		"gamemaker_pro",
		"gamemaker_registered",
		"gamemaker_version",
		"health",
		"instance_count",
		"instance_id",
		"keyboard_key",
		"keyboard_lastchar",
		"keyboard_lastkey",
		"keyboard_string",
		"lives",
		"mouse_button",
		"mouse_lastbutton",
		"mouse_x",
		"mouse_y",
		"os_device",
		"os_type",
		"program_directory",
		"room",
		"room_caption",
		"room_first",
		"room_height",
		"room_last",
		"room_persistent",
		"room_speed",
		"room_width",
		"score",
		"secure_mode",
		"show_health",
		"show_lives",
		"show_score",
		"temp_directory",
		"transition_color",
		"transition_kind",
		"transition_steps",
		"view_angle",
		"view_current",
		"view_enabled",
		"view_hborder",
		"view_hport",
		"view_hspeed",
		"view_hview",
		"view_object",
		"view_vborder",
		"view_visible",
		"view_vspeed",
		"view_wport",
		"view_wview",
		"view_xport",
		"view_xview",
		"view_yport",
		"view_yview",
		"working_directory",
		"alarm",
		"bbox_bottom",
		"bbox_left",
		"bbox_right",
		"bbox_top",
		"depth",
		"direction",
		"friction",
		"gravity",
		"gravity_direction",
		"hspeed",
		"id",
		"image_alpha",
		"image_angle",
		"image_blend",
		"image_index",
		"image_number",
		"image_single",
		"image_speed",
		"image_xscale",
		"image_yscale",
		"mask_index",
		"object_index",
		"path_endaction",
		"path_index",
		"path_orientation",
		"path_position",
		"path_positionprevious",
		"path_scale",
		"path_speed",
		"persistent",
		"solid",
		"speed",
		"sprite_height",
		"sprite_index",
		"sprite_width",
		"sprite_xoffset",
		"sprite_yoffset",
		"timeline_index",
		"timeline_loop",
		"timeline_position",
		"timeline_running",
		"timeline_speed",
		"visible",
		"vspeed",
		"x",
		"xprevious",
		"xstart",
		"y",
		"yprevious",
		"ystart",
	);

	$input = str_replace(' ', '&nbsp;', $input); //space preservation
	$input = preg_replace('/("[^\"]*\")/m','<span class="constant">$1</span>',$input); //double quote strings
	$input = preg_replace("/('[^']*')/m",'<span class="constant">$1</span>',$input); //single quote strings
	$input = preg_replace('~(//.*?)(?:$|\n)~s','<span class="comment">$1</span>',$input); //single line comments
	$input = preg_replace('~(/\*.*?\*/)~s','<span class="comment">$1</span>',$input); //double line comments
	$input = preg_replace('~(\d+)~m','<span class="constant">$1</span>',$input); //numbers
	$input = preg_replace('~\b(true|false|else|if|for|while)\b~m','<span class="constant">$1</span>',$input); //keywords
	$input = preg_replace('~\b('.implode('|',$KEYWORDS).')\b~m','<span class="function">$1</span>',$input); //built-in variables
	$input = preg_replace('/(\b[a-zA-Z_]+)(?=\()/m','<span class="function">$1</span>',$input); //functions
	return nl2br($input);
}

?>

<html>
	<head>
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
		<link rel="stylesheet" href="style/style.css">
	</head>
	<body>
		<div class="main-content">
			<?php if ($method != "display") { ?>
			<form class="pure-form" method="post">
				<fieldset class="pure-group">
					<textarea class="pure-input-1" name="input-code"><?=$input?></textarea>
				</fieldset>
				<input class="pure-button pure-input-1 pure-button-primary" type="submit" name="example" value="Show Me An Example!" />
				<input class="pure-button pure-input-1 pure-button-primary" type="submit" name="preview" value="Preview" />
				<input class="pure-button pure-input-1 pure-button-primary" type="submit" name="create" value="Create" />
			</form>
			<?php } ?>
			<?php
				for ($i=0;$i<count($output);$i+=1) {
					echo '<div class="event-div"><p class="event-name">'.$output[$i][0].'</p><p class="event-code">'.syntax_highlight($output[$i][1]).'</p></div>';
				}
			?>
		</div>
	</body>
</html>
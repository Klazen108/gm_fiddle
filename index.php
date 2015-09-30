<?php
include 'inc/config.php';
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
show_message(\"hello!\")

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
$title="";
$desc="";
if (isset($_POST['input-code'])) $input=$_POST['input-code'];
if (isset($_POST['input-title'])) $title=$_POST['input-title'];
if (isset($_POST['input-desc'])) $desc=$_POST['input-desc'];

$method="";
if (isset($_POST['preview'])) $method="preview";
else if (isset($_POST['example'])) {
	$method="preview";
	$input=$example_string;
	$title="Example Snippet";
	$desc="Just a bunch of nonsense to show you the format!";
} else if (isset($_POST['create'])) $method="create";
else if (isset($_POST['update'])) $method="update";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	//split request URI, remove empty elements, and rebase array to 0
	$arr = array_values(array_filter(split("/",$_SERVER['REQUEST_URI'])));
	$arr_size=count($arr);
	if ($arr_size>=2 && $arr[$arr_size-2]==='gm') { //gm & hash
		$hash = $arr[$arr_size-1];
		$entry = getEntry($hash);
		$input = $entry->getTextContent();
		$title = $entry->getTitle();
		$desc = $entry->getDescription();
		$method="display";
	} else if ($arr_size>=2 && $arr[$arr_size-3]==='gm') { //gm & hash & editor hash
		$hash = $arr[$arr_size-2];
		$editor_hash = $arr[$arr_size-1];
		$entry = getEntryForEdit($hash,$editor_hash);
		$input = $entry->getTextContent();
		$title = $entry->getTitle();
		$desc = $entry->getDescription();
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
	if ($o_i==-1) {
		$output[0][0]="Script";
		$output[0][1]=$input;
	}
} else if ($method=="create") {
	if (empty($input)) {
		$method=""; //blank out the method if the user hit create without entering any text - just pretend nothing ever happened
	} else {
		$entry = addEntry($input,$title,$desc);
		if (!is_null($entry)) {
			header('Location: '.GMF_PATH.$entry->hash.'/'.$entry->editor_hash);
			die();
		}
	}
} else if ($method=="update") {
	if (!empty($_POST['hash']) && !empty($_POST['ehash'])) {
		$hash = $_POST['hash'];
		$editor_hash = $_POST['ehash'];
		$entry = updateEntry($hash,$editor_hash,$input,$title,$desc);
		header('Location: '.GMF_PATH.$entry->hash.'/'.$entry->editor_hash);
		die();
	}
	
}

?>

<html>
	<head>
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
		<script src="<?=GMF_PATH?>style/jquery-1.11.3.min.js"></script>
		<link rel="stylesheet" href="<?=GMF_PATH?>style/style.css">
		<script type="text/javascript">
			$(document).delegate('textarea', 'keydown', function(e) {
			  var keyCode = e.keyCode || e.which;
			
			  if (keyCode == 9) {
			    e.preventDefault();
			    var start = $(this).get(0).selectionStart;
			    var end = $(this).get(0).selectionEnd;
			
			    // set textarea value to: text before caret + tab + text after caret
			    $(this).val($(this).val().substring(0, start)
			                + "\t"
			                + $(this).val().substring(end));
			
			    // put caret at right position again
			    $(this).get(0).selectionStart =
			    $(this).get(0).selectionEnd = start + 1;
			  }
			});
		</script>
	</head>
	<body>
	    <div id="header">
			<?php switch ($method) {
				case "":
					echo "GM Snippet Thingy IDK - enter some game maker code below to share a snippet. Try an example to see how!";
					break;
				case "display": ?>
					GM Snippet Thingy IDK - <a href='<?=GMF_PATH?>'>Make your own!</a><br>
					Share This Snippet: <a target="_blank" href="<?=GMF_PATH.$hash?>"><?=GMF_HOST_NAME.GMF_PATH.$hash?></a><br>
					<?php break;
				case "edit": ?>
					GM Snippet Thingy IDK - Editor Panel - <b>Bookmark this link - you can edit your snippet with it</b><br>
					Share This Snippet: <a target="_blank" href="<?=GMF_PATH.$hash?>"><?=GMF_HOST_NAME.GMF_PATH.$hash?></a><br>
					Edit: <a target="_blank" href="<?=GMF_PATH.$hash.'/'.$editor_hash?>"><?=GMF_HOST_NAME.GMF_PATH.$hash.'/'.$editor_hash?></a><br>
					<?php break;
				case "preview":
					echo "GM Snippet Thingy IDK - Previewing your code";
					break;
				case "update":
					echo "how did you get here?";
					break;
				default: echo $method; break;
			} ?>
		</div>
	    <div id="content">
	    	<?php if ($method != "display") { ?>
				<form class="pure-form" method="post">
					<fieldset class="pure-group">
						<input class="pure-input-1" name="input-title" maxlength="64" placeholder="Title" value="<?=$title?>"/>
						<input class="pure-input-1" name="input-desc" maxlength="256" placeholder="Description" value="<?=$desc?>"/>
						<textarea class="pure-input-1" name="input-code" placeholder="code"><?=$input?></textarea>
					</fieldset>
					<?php if ($method != "edit") { ?>
						<input class="pure-button pure-input-1 pure-button-primary" type="submit" name="example" value="Show Me An Example!" />
					<?php } ?>
					<?php if ($method != "edit") { ?>
						<input class="pure-button pure-input-1 pure-button-primary" type="submit" name="preview" value="Preview" />
						<input class="pure-button pure-input-1 pure-button-primary" type="submit" name="create" value="Create" />
					<?php } else { ?>
						<input type="hidden" name="hash" value="<?=$hash?>"/>
						<input type="hidden" name="ehash" value="<?=$editor_hash?>"/>
						<input class="pure-button pure-input-1 pure-button-primary" type="submit" name="update" value="Update" />
					<?php } ?>
				</form>
			<?php } else { ?>
				<h1><?=$title?></h1>
				<h2><?=$desc?></h2>
			<?php } ?>
			<?php
				for ($i=0;$i<count($output);$i+=1) {
					echo '<div class="event-div"><p class="event-name">'.format_card_title($output[$i][0]).'</p><p class="event-code">'.syntax_highlight($output[$i][1]).'</p></div>';
				}
			?>
	    </div>
	    <div id="footer"><p>Site Design (c) 2015 Klazen108 &amp; Patrickgh3</p></div>
	</body>
</html>
<?php

function format_card_title($title) {
	if (preg_match('/alarm/i', $title)) {
		return '<img src="'.GMF_PATH.'style/icons/alarm.png" />&nbsp;'.$title;
	} else if (preg_match('/colli(?:de|sion)/i', $title)) {
		return '<img src="'.GMF_PATH.'style/icons/collision.png" />&nbsp;'.$title;
	} else if (preg_match('/create/i', $title)) {
		return '<img src="'.GMF_PATH.'style/icons/create.png" />&nbsp;'.$title;
	} else if (preg_match('/destroy/i', $title)) {
		return '<img src="'.GMF_PATH.'style/icons/destroy.png" />&nbsp;'.$title;
	} else if (preg_match('/draw/i', $title)) {
		return '<img src="'.GMF_PATH.'style/icons/draw.png" />&nbsp;'.$title;
	} else if (preg_match('/(?:key|press)/i', $title)) {
		return '<img src="'.GMF_PATH.'style/icons/keyboard.png" />&nbsp;'.$title;
	} else if (preg_match('/script/i', $title)) {
		return '<img src="'.GMF_PATH.'style/icons/script.png" />&nbsp;'.$title;
	} else if (preg_match('/step/i', $title)) {
		return '<img src="'.GMF_PATH.'style/icons/step.png" />&nbsp;'.$title;
	} else {
		return '<img src="'.GMF_PATH.'style/icons/other.png" />&nbsp;'.$title;
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
	$input = preg_replace('~\b(\d+)\b~m','<span class="constant">$1</span>',$input); //numbers
	$input = preg_replace('~\b(true|false|else|if|for|while)\b~m','<span class="constant">$1</span>',$input); //keywords
	$input = preg_replace('~\b('.implode('|',$KEYWORDS).')\b~m','<span class="function">$1</span>',$input); //built-in variables
	$input = preg_replace('/(\b[a-zA-Z_]+)(?=\()/m','<span class="function">$1</span>',$input); //functions
	return nl2br($input);
}
?>
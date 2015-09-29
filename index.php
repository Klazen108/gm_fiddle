<?php
$input="";
$method="";
$match_count=0;
if (isset($_POST['input-code'])) {
	$input=$_POST['input-code'];
	$method="preview";
	
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
	
	//$match_count = preg_match_all('/^---(.+)$([\s\n.]+)/m',$input,$section_array);
}

function syntax_highlight($input) {
	$input = preg_replace('~(//.*)$~m','<span class="comment">$1</span>',$input);
	$input = preg_replace('~(\d+)~m','<span class="number">$1</span>',$input);
	$input = preg_replace('~\b(true|false|else|if|for|while)\b~m','<span class="number">$1</span>',$input);
	$input = str_replace(' ', '&nbsp;', $input);
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
			<form class="pure-form" method="post">
				<fieldset class="pure-group">
					<textarea class="pure-input-1" name="input-code"><?=$input?></textarea>
				</fieldset>
				<input class="pure-button pure-input-1 pure-button-primary" type="submit" value="Preview" />
			</form>
			<?php
				for ($i=0;$i<count($output);$i+=1) {
					echo '<div class="event-div"><p class="event-name">'.$output[$i][0].'</p><p class="event-code">'.syntax_highlight($output[$i][1]).'</p></div>';
				}
			?>
		</div>
	</body>
</html>
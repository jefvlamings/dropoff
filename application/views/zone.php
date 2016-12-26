<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

	<div id="zoneid"><?php echo $zoneid ?></div>

	<div id="top">
		<div id="home">
			<p><a href="<?php echo site_url(); ?>">&larr; home</a></p>
		</div>
	</div>

	<div id="container">
		
		<div id="zonename">
			<input id="title" maxlength="22" value="<?php echo $zonename ?>" placeholder="New title"></input>
		</div>
		
		<div id="one" class="column"></div>
		
		<div id="two" class="column">
			<div id="post" class="snippet">
				<div id="preview"></div>
				<input type="text" id="message" placeholder="Add a snippet"></input></br>
			</div>
		</div>
		
		<div id="three" class="column"></div>

	</div>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>


<div id="start">
	<h1>Start here!</h1>
	<a href="<?php echo site_url('controller/create_new_zone') ?>">New dropoff zone</a>
</div>

<div id="latest">
	<h1>Latest zones</h1>
	<ol>
	<?php foreach($allid as $data){?>
		<li><a href="<?php echo site_url('controller/zone/') . $data["id"] ?>"><?php echo $data["name"] ?> (#<?php echo $data["id"] ?>)</a></li>
	<?php }; ?>
	</ol>
</div>





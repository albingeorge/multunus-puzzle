<html>
	<head>
		<title>Twitter Wheel</title>
		<style type="text/css">
			.focus {
				position: relative;
				height: 24em;
				width: 24em;
				left: 24em;
				background-size: cover;
				list-style-type: none;
				margin: 100px;
				padding: 0;
			}

			.focus li {
				position: absolute;
				top: 50%;
				margin-top: -25px;
				height: 50px;
			}

			.focus li img {
				height: 100px;
				width: 100px;
				border-radius: 100px;
				border: solid thin black;
				overflow: hidden;
			}
		</style>
	</head>
	<body>
		<ul class="focus">
			<li>
				<img src="<?php echo $userImage; ?>"  />
			</li>
			<?php
			$deg = -90;
			foreach ($users as $user) { ?>
				<li style="-webkit-transform: rotate(<?php echo $deg ?>deg) translate(12em) rotate(<?php echo -$deg ?>deg)">
					<img src="<?php echo $user['profile_image_url']  ?>" title="<?php echo $user['followers_count']  ?>" />
				</li>
			<?php
			$deg = $deg + 36;
			} ?>
		</ul>
	
</body></html>
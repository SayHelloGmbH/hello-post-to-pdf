<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<?php

if (have_posts()) {
	while (have_posts()) {
		the_post();

		printf(
			'<h1>[Post ID %1$s] %2$s</h1>',
			get_the_ID(),
			get_the_title()
		);

		if (has_post_thumbnail()) {
			the_post_thumbnail('large');
		}

		the_content();
	}
}
?>

</body>
</html>
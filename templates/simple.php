<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<?php
	$css_file = trailingslashit(dirname(dirname(__FILE__))).'assets/dist/styles/plugin.css';
	if (file_exists($css_file)) {
		printf(
			'<style>%s</style>',
			file_get_contents($css_file)
		);
	}
	?>
	</head>
	<body>

	<div class="c-posttopdf">
	<?php

	if (have_posts()) {
		while (have_posts()) {
			the_post();

			printf(
				'<h1 class="c-posttopdf__title">%s</h1>',
				get_the_title()
			);

			if (has_post_thumbnail()) {
				printf(
					'<figure class="c-posttopdf__thumbnail">%s</figure>',
					get_the_post_thumbnail(get_the_ID(), 'large')
				);
			}

			the_content();

			printf(
				'<hr><p class="c-posttopdf__footer">%s</p>',
				sprintf(
					_x('PDF generated on %1$s at %2$s', 'PDF generation date and time', 'hello-post-to-pdf'),
					date(get_option('date_format')),
					date(get_option('time_format'))
				)
			);

			printf(
				'<p class="c-posttopdf__footer">%1$s</p><hr>',
				sprintf(
					_x('Originally published at %1$s on %2$s<br>%3$s', 'Text in PDF template', 'hello-post-to-pdf'),
					get_bloginfo('name'),
					get_the_date(),
					get_permalink()
				)
			);
		}
	}
	?>
</div>

</body>
</html>

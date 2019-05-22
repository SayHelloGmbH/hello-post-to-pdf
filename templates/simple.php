<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<?php
	if (file_exists(plugin_dir_path(trailingslashit(dirname(__FILE__))).'../assets/dist/styles/hello-post-to-pdf.css')) {
		printf('<style>%s</style>', file_get_contents(plugin_dir_path(trailingslashit(dirname(__FILE__))).'../assets/dist/styles/hello-post-to-pdf.css'));
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
				'<h1 class="c-posttopdf__title">%1$s</h1><p class="c-posttopdf__subtitle">%2$s %3$s</p>',
				get_the_title(),
				_x('Published on', 'Post ID prefix text', 'hello-post-to-pdf'),
				get_the_date()
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
				'<p class="c-posttopdf__footer">%s</p><hr>',
				sprintf(
					_x('PDF generation plugin by %s', 'Credit footer in generated PDF', 'hello-post-to-pdf'),
					'<a href="https://sayhello.ch/">Say Hello GmbH</a>'
				)
			);
		}
	}
	?>
</div>

</body>
</html>

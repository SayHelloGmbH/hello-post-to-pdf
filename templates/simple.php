<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="https://sayhellogmbh.github.io/css-reset/css-reset.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,700,700i" rel="stylesheet">
	<style>
		html, body {
			margin: 0;
			padding: 0;
			color: #111;
			font-family: 'Source Sans Pro', sans-serif;
		}
		a {
			color: inherit;
		}
		.c-posttopdf {
			margin: 0 auto;
			max-width: 40rem;
		}
		.c-posttopdf__title {
			font-size: 2rem;
			line-height: 1.2;
			margin: 1rem 0;
		}
		.c-posttopdf__subtitle {
			font-size: .85rem;
			line-height: 1.2;
			margin: 1rem 0 2rem;
			color: gray;
		}
		.c-posttopdf__footer {
			margin: 0;
			color: gray;
		}
		.c-posttopdf__thumbnail {
			display: block;
			width: 100%;
			margin: 1rem 0;
		}
		img {
			page-break-before: auto;
			page-break-after: auto;
			page-break-inside: avoid;
		}
	</style>
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
				_x('Post ID', 'Post ID prefix text', 'hello-post-to-pdf'),
				get_the_ID()
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
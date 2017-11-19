<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<title>MVC - Home</title>
</head>
<body>
	<div class="container">
		<header>
			<h1>Architecture MVC</h1>
		</header>
		<div class="col-md-12">
			<?php
			foreach ($articles as $article){ ?>
			<article>
				<h2><?php echo $article['title'] ?></h2>
				<p><?php echo $article['content'] ?></p>
			</article>
			<?php } ?>
		</div>
		<footer class="footer">
		</footer>
	</div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Folklori yne</title>

	<!-- Bootstrap -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Font Awesome-->
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body>

	<div class="modal fade heart" role="dialog" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Modal title</h4>
				</div>

				<div class="modal-body">
					<p>One fine body&hellip;</p>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<nav class="navbar navbar-default">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<a class="navbar-brand" href="#">
					<img alt="Folklori yne" src="">
				</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

			</div><!-- /.navbar-collapse -->
		</div><!-- /.container -->
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<form action="/post" class="" method="post" name="form" role="form">
					<div class="input-group input-group-lg">
						<input class="form-control" id="input" name="input" type="text" />
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
						</span>
					</div>
				</form>
			</div>
		</div>
	</div><!-- /.container -->

	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-9">
				<p style="color: #aaa; margin-top: 28px;">
					{{ $performance or '' }}
				</p>

				<h3>{{ $search_results or '' }}</h3>

				<?php
					if (isset($output))
					{
						print $output;
					}
				?>
			</div>

			<div class="col-xs-12 col-md-3" style="margin-top: 48px;">
				<h3>
					{{ $filters or '' }}
				</h3>

				<?php
					if (isset($categories_hits)) {
						print $categories_count;
					}

					if (isset($tellers_hits)) {
						print $tellers_count;
					}

					if (isset($places_hits)) {
						print $places_count;
					}

					if (isset($volumes_hits)) {
						print $volumes_count;
					}
				?>

			</div>
		</div>
	</div><!-- /.container -->

	<!-- div class="container">
		<div class="row">
			<div class="col-xs-12">

				<?php
/*			        if (isset($aggregations)) {
						echo '<h3>Search</h3>';
			            echo '<pre>';
			            print_r($places_hits);
			            echo '<br><br><br>';
			            print_r($places);
			            echo '<br><br><br>';
			            print_r($aggregations);
			            echo '</pre>';
			        }
				?>
			</div>
		</div>
	</div><!-- /.container -->


	<!-- div class="container">
		<div class="row">
			<div class="col-xs-12">

				<?php
/*			        if (isset($inner_hits)) {
						echo '<h3>Inner Hits</h3>';
			            echo '<pre>';
			            print_r($inner_hits);
			            echo '</pre>';
			        }
*/				?>
			</div>
		</div>
	</div><!-- /.container -->

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<script src="resources/js/script.js"></script>

</body>
</html>

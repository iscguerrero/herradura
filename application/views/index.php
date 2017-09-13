<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>LA HERRADURA </title>
		<link href="<?php echo base_url('resources/bootstrap/dist/css/bootstrap.min.css')?>" rel="stylesheet">
		<link href="<?php echo base_url('resources/font-awesome/css/font-awesome.min.css')?>" rel="stylesheet">
		<link href="<?php echo base_url('resources/nprogress/nprogress.css')?>" rel="stylesheet">
		<link href="<?php echo base_url('resources/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')?>" rel="stylesheet">
		<link href="<?php echo base_url('resources/font-awesome/css/font-awesome.min.css')?>" rel="stylesheet">
		<link href="<?php echo base_url('build/css/custom.min.css')?>" rel="stylesheet">
		<link href="<?php echo base_url('css/comun.css')?>" rel="stylesheet">
	</head>
	<body class="nav-md">
		<div id="modalAlert" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title">MENSAJE DEL SISTEMA</h4>
					</div>
					<div class="modal-body">
						<strong id="msjAlert"></strong>
					</div>
				</div>
			</div>
		</div>
		<div class="container body">
			<div class="main_container">
				<div class="col-md-3 left_col">
					<div class="left_col scroll-view">
						<div class="navbar nav_title" style="border: 0;">
							<a href="index.html" class="site_title"><i class="fa fa-pie-chart"></i> <span>LA HERRADURA</span></a>
						</div>
						<div class="clearfix"></div>
						<!-- menu profile quick info -->
						<div class="profile clearfix">
							<div class="profile_pic">
								<img src="<?php echo base_url('resources/images/user.png')?>" alt="..." class="img-circle profile_img">
							</div>
							<div class="profile_info">
								<span>Bienvenido,</span>
								<h2>Usuario</h2>
							</div>
							<div class="clearfix"></div>
						</div>
						<!-- /menu profile quick info -->
						<br />
						<!-- sidebar menu -->
						<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
							<div class="menu_section">
								<h3>Menu principal</h3>
								<ul class="nav side-menu">
									<li><a href="Reporte"><i class="fa fa-sitemap"></i> Rangos</a></li>
									<li><a href="Escalas"><i class="fa fa-list-alt"></i> Escalas</a></li>
								</ul>
							</div>
						</div>
						<!-- /sidebar menu -->
					</div>
				</div>
				<!-- top navigation -->
				<div class="top_nav">
					<div class="nav_menu">
						<nav>
							<div class="nav toggle">
								<a id="menu_toggle"><i class="fa fa-bars"></i></a>
							</div>
							<ul class="nav navbar-nav navbar-right">
							</ul>
						</nav>
					</div>
				</div>
				<!-- /top navigation -->

				<!-- page content -->
				<div class="right_col" role="main">
					<div class="">
						<div class="page-title">
							<div class="title_left">
								<h4>REPORTE DE VENTAS POR TIPO DE VENTA</h4>
							</div>
							<div class="title_right">
								<form method="POST" action="" id="formReport">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<div class="form-group">
												<div class='input-group date simple-dp'>
														<input type='text' class="form-control text-center" name="fi" id="fi" readonly="readonly" placeholder="Fecha Inicial" />
														<span class="input-group-addon">
															<span class="glyphicon glyphicon-calendar"></span>
														</span>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<div class="form-group">
												<div class='input-group date simple-dp'>
														<input type='text' class="form-control text-center" name="ff" id="ff" readonly="readonly" placeholder="Fecha Final" />
														<span class="input-group-addon">
															<span class="glyphicon glyphicon-calendar"></span>
														</span>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12">
											<div class="checkbox">
												<label>
													<input type="checkbox" id="checkOpen"> Mostrar árbol expandido
												</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-offset-8 col-xs-4">
											<button type="submit" class="btn btn-primary btn-block" ><i class="fa fa-thumbs-up"></i> Generar</button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-lg-offset-1 col-lg-10 col-md-12 col-sm-12 col-xs-12">
								<div class="x_panel">
									<div class="x_content">
											<table class="table table-hover table-condensed table-bordered" id="tablaUno">
												<thead>
													<tr>
														<th class='text-center'><i class="fa fa-sitemap fa-border"></i></th>
														<th>CATEGORIA</th>
														<th class="text-right">CANTIDAD</th>
														<th class="text-right">IMPORTE</th>
													</tr>
												</thead>
												<tbody id="bodyTablaUno"></tbody>
											</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /page content -->

				<!-- footer content -->
				<footer>
					<div class="pull-right">
						La Herradura Copyright © 2017. All Right Reserved.
					</div>
					<div class="clearfix"></div>
				</footer>
				<!-- /footer content -->
			</div>
		</div>

		<script src="<?php echo base_url('resources/jquery/dist/jquery.min.js')?>"></script>
		<script src="<?php echo base_url('resources/bootstrap/dist/js/bootstrap.min.js')?>"></script>
		<script src="<?php echo base_url('resources/fastclick/lib/fastclick.js')?>"></script>
		<script src="<?php echo base_url('resources/nprogress/nprogress.js')?>"></script>
		<script src="<?php echo base_url('resources/moment/min/moment.min.js')?>"></script>
		<script src="<?php echo base_url('resources/moment/locale/es.js')?>"></script>
		<script src="<?php echo base_url('resources/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		<script src="<?php echo base_url('/build/js/custom.min.js')?>"></script>
		<script src="<?php echo base_url('js/comun.js')?>"></script>
		<script src="<?php echo base_url('js/index.js')?>"></script>
	</body>
</html>
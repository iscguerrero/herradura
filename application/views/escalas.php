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
		<link href="<?php echo base_url('resources/handsontable/handsontable.full.min.css')?>" rel="stylesheet">
		<link href="<?php echo base_url('resources/toastr/toastr.min.css')?>" rel="stylesheet">
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
								<!-- <li class="">
									<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<img src="<?php echo base_url('resources/images/user.png')?>" alt="">Usuario
										<span class=" fa fa-angle-down"></span>
									</a>
									<ul class="dropdown-menu dropdown-usermenu pull-right">
										<li><a href="javascript:;"> Perfil</a></li>
										<li><a href="javascript:;">Ayuda</a></li>
										<li><a href="login.html"><i class="fa fa-sign-out pull-right"></i> Salir</a></li>
									</ul>
								</li> -->
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
								<h4>ESCALAS DE TIPO DE VENTA</h4>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-xs-12">
								<div class="x_panel">
									<div class="x_content">
										<div class="col-xs-4">
											<div class=x_panel>
												<div class="x_title">
													<h5>VERSIONES <small>parámetros de busqueda</small></h5>
												</div>
												<div class="x_content">
													<form method="POST" action="">
														<div class="form-group">
															Fecha de Inicio
															<div class='input-group date simple-dp'>
																	<input type='text' class="form-control text-center" name="fi" id="fi" readonly="readonly" />
																	<span class="input-group-addon">
																		<span class="glyphicon glyphicon-calendar"></span>
																	</span>
															</div>
														</div>
														<div class="form-group">
															Fecha de Fin
															<div class='input-group date simple-dp'>
																	<input type='text' class="form-control text-center" name="ff" id="ff" readonly="readonly" />
																	<span class="input-group-addon">
																		<span class="glyphicon glyphicon-calendar"></span>
																	</span>
															</div>
														</div>
														<div class="form-group">
															Versión
															<select class="form-control" name="version" id="version">
																<option value="">Selecciona una versión</option>
															</select>
														</div>
														<div class="ln_solid"></div>
														<button type="button" class="btn btn-primary btn-block" id="btnGenerarReporte"><i class="fa fa-send"></i> Generar Reporte</button>
														<button type="button" class="btn btn-info btn-block" id="btnExcel"><i class="fa fa-file-excel-o"></i> Todos los Items</button>
														<button type="button" class="btn btn-warning btn-block" id="btnBorrarVersion"><i class="fa fa-times-circle-o"></i> Borrar Versión Seleccionada</button>
														<div class="ln_solid"></div>
														<font color="#8f0202">Los cambios realizados serán guardados como una nueva versión</font>
														<div class="ln_solid"></div>
															<button type="button" class="btn btn-success btn-block" id="btnGuardarVersion"><i class="fa fa-floppy-o"></i> Guardar Nueva Versión</button>
													</form>
												</div>
											</div>
										</div>
										<div class="col-xs-8">
											<div id="tablaEscalas"></div>
										</div>
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
		<script src="<?php echo base_url('resources/handsontable/handsontable.full.min.js')?>"></script>
		<script src="<?php echo base_url('resources/handsontable/ZeroClipboard.js')?>"></script>
		<script src="<?php echo base_url('resources/toastr/toastr.min.js')?>"></script>
		<script src="<?php echo base_url('/build/js/custom.min.js')?>"></script>
		<script src="<?php echo base_url('js/comun.js')?>"></script>
		<script src="<?php echo base_url('js/scale-list.js')?>"></script>
	</body>
</html>

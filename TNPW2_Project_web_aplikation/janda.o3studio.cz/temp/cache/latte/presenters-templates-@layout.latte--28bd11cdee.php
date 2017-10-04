<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/@layout.latte

use Latte\Runtime as LR;

class Template28bd11cdee extends Latte\Runtime\Template
{
	public $blocks = [
		'cssExtra' => 'blockCssExtra',
		'content' => 'blockContent',
		'scriptsExtra' => 'blockScriptsExtra',
	];

	public $blockTypes = [
		'cssExtra' => 'html',
		'content' => 'html',
		'scriptsExtra' => 'html',
	];


	function main()
	{
		extract($this->params);
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Start Store Room</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 63 */ ?>/assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 65 */ ?>/assets/bower_components/font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 67 */ ?>/assets/bower_components/Ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 69 */ ?>/assets/dist/css/AdminLTE.min.css">
        <!-- my style -->
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 71 */ ?>/assets/css/custom.css">
        <?php
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('cssExtra', get_defined_vars());
?>
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 75 */ ?>/assets/dist/css/skins/_all-skins.min.css">
        <meta name="description" content="Pokladna">
        <meta name="author" content="Josef Janda">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">

            <header class="main-header">
                <!-- Logo -->
                <a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 94 */ ?>/pokladna" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>S</b>ST</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>Start Store Room</span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>

                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">                         
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 115 */ ?>/assets/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                                    <span class="hidden-xs"><?php echo LR\Filters::escapeHtmlText($user->getIdentity()->data['username']) /* line 116 */ ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-right">
                                            <a href="/sign/out" class="btn btn-default btn-flat">Odhlásit se</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->
                        </ul>
                    </div>
                </nav>
            </header>

            <!-- =============================================== -->

            <!-- Left side column. contains the sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu" data-widget="tree">
                        <li class="header">POKLADNA</li>
                        <li <?php
		if ($this->getParameter('presenter')->name == "Pokladna:Prodej") {
			?>class='active'<?php
		}
?>>
                            <a href="/pokladna/prodej">
                                <i class="fa fa-tablet"></i> <span>Prodej</span>
                            </a>
                        </li>
                        <li <?php
		if ($this->getParameter('presenter')->name == "Pokladna:Sklad") {
			?>class='active'<?php
		}
?>>
                            <a href="/pokladna/sklad">
                                <i class="fa fa-gift"></i> <span>Zboží</span>
                            </a>
                        </li>
                        <li class="header">PŘEHLED</li>
                        <li <?php
		if ($this->getParameter('presenter')->name == "Pokladna:Uctenky") {
			?>class='active'<?php
		}
?>>
                            <a href="/pokladna/uctenky">
                                <i class="fa fa-list"></i> <span>Účtenky</span>
                            </a>
                        </li>
                        <li class="header">DALŠÍ</li>
                        <li <?php
		if ($this->getParameter('presenter')->name == "Pokladna:Nastaveni") {
			?>class='active'<?php
		}
?>>
                            <a href="/pokladna/nastaveni">
                                <i class="fa fa-gears"></i> <span>Nastavení</span>
                            </a>
                        </li>
                        <li <?php
		if ($this->getParameter('presenter')->name == "Pokladna:Owebu") {
			?>class='active'<?php
		}
?>>
                            <a href="/pokladna/owebu">
                                <i class="fa fa-th"></i> <span>O webu</span>
                            </a>
                        </li>
<?php
		if ($user->isInRole('admin')) {
?>
                            <li class="header">ADMINISTRACE</li>
                            <li <?php
			if ($this->getParameter('presenter')->name == "Pokladna:Uzivatele") {
				?>class='active'<?php
			}
?>>
                                <a href="/pokladna/uzivatele">
                                    <i class="fa fa-users"></i> <span>Uživatelé</span>
                                </a>
                            </li>
<?php
		}
?>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- =============================================== -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <?php
		$this->renderBlock('content', get_defined_vars());
?>
            </div>
            <!-- /.content-wrapper -->

            <footer class="main-footer">
                <div class="pull-right hidden-xs">

                </div>
                <strong>Copyright &copy; 2017 </strong>
            </footer>
        </div>
        <!-- ./wrapper -->

        <!-- jQuery 3 -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 206 */ ?>/assets/bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 208 */ ?>/assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- SlimScroll -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 210 */ ?>/assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 212 */ ?>/assets/bower_components/fastclick/lib/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 214 */ ?>/assets/dist/js/adminlte.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 216 */ ?>/assets/dist/js/demo.js"></script>
        <script>
            $(document).ready(function () {
                $('.sidebar-menu').tree()
            })
        </script>
        <?php
		$this->renderBlock('scriptsExtra', get_defined_vars());
?>
    </body>
</html>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockCssExtra($_args)
	{
		
	}


	function blockContent($_args)
	{
		
	}


	function blockScriptsExtra($_args)
	{
		
	}

}

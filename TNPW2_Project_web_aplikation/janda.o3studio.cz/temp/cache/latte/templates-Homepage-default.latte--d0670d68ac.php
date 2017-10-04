<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PublicModule/presenters/templates/Homepage/default.latte

use Latte\Runtime as LR;

class Templated0670d68ac extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<!DOCTYPE html>
<html lang="cs">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Pokladna">
        <meta name="author" content="Josef Janda">
        <title>Start Store Room - Store Room for your business!</title>
        <!-- Bootstrap Core CSS -->
        <link href="/www/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Theme CSS -->
        <link href="/www/css/freelancer.min.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="/www/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body id="page-top" class="index">
        <div id="skipnav"><a href="#maincontent">Skip to main content</a></div>

        <!-- Navigation -->
        <nav id="mainNav" class="navbar navbar-default navbar-fixed-top navbar-custom">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header page-scroll">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                    </button>

                    <a class="navbar-brand" href="#page-top">Store Room</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="hidden">
                            <a href="#page-top"></a>
                        </li>
                        <li class="page-scroll">
                            <a href="#portfolio">Portfolio</a>
                        </li>
                        <li class="page-scroll">
                            <a href="#about">O aplikaci</a>
                        </li>
                        <li class="page-scroll">
                            <a href="#contact">Napište nám</a>
                        </li>
                        <li class="page-scroll">
                            <a href="/sign/up">Registrace</a>

                        </li>
                        <li class="page-scroll">

                            <a href="/sign/in">Přihlásit se</a>
                        </li>

                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
        </nav>

        <!-- Header -->
        <header>
            <div class="container" id="maincontent" tabindex="-1">
                <div class="row">
                    <div class="col-lg-12">
                        <img class="img-responsive" src="img/profile.png" alt="">
                        <div class="intro-text">
                            <h1 class="name">Start Store Room</h1>
                            <hr class="star-light">
                            <span class="skills">Store Room for your business</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Portfolio Grid Section -->
        <section id="portfolio">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Kde všude se Vám můžeme hodit</h2>
                        <hr class="star-primary">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 portfolio-item">
                        <a href="#portfolioModalX" class="portfolio-link" data-toggle="modal">
                            <div class="caption">
                                <div class="caption-content">
                                    <i class="fa fa-search-plus fa-3x"></i>
                                </div>
                            </div>
                            <img src="img/portfolio/cake.png" class="img-responsive" alt="Cabin">
                        </a>
                    </div>
                    <div class="col-sm-4 portfolio-item">
                        <a href="#portfolioModalX" class="portfolio-link" data-toggle="modal">
                            <div class="caption">
                                <div class="caption-content">
                                    <i class="fa fa-search-plus fa-3x"></i>
                                </div>
                            </div>
                            <img src="img/portfolio/clothesshop.png" class="img-responsive" alt="Slice of cake">
                        </a>
                    </div>
             
                    <div class="col-sm-4 portfolio-item">
                        <a href="#portfolioModalX" class="portfolio-link" data-toggle="modal">
                            <div class="caption">
                                <div class="caption-content">
                                    <i class="fa fa-search-plus fa-3x"></i>
                                </div>
                            </div>
                            <img src="img/portfolio/submarine.png" class="img-responsive" alt="Submarine">
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="success" id="about">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h2>O projektu</h2>
                        <hr class="star-light">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 col-lg-offset-0">
                        <p>Store Room je webová aplikace, která slouží pro správu prodeje Vaší firmy, zejména jste-li začínajícím podnikatelem a snažíte se minimalizovat své výdaje za provoz, pak je apllikace od nás skvělou volbou. Tato webová aplikace byla napsána za účelem nejen školního projektu, ale hlavně pro mého kamaráda Vláďu, který je začínajícím podnikatel na poli obuvního průmyslu a který mě o podobnou aplikaci požádal.


                    </div>
                    <div class="col-lg-5 col-lg-offset-2">
                        <p>
                            Pro tuto aplikaci bylo použito materiálů volně dostupných na internetu, avšak ne za účelem obohacení. Pokud tedy budete kopírovat kód z těchto stránek zavazujete se též dodržet podmínky stanovené autorem, jemuž tímto děkuji za zpracování. Pro tento školní projekt se jimi já zabývat nehodlám. </p>

                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Napište nám</h2>
                        <hr class="star-primary">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <!-- To configure the contact form email address, go to mail/contact_me.php and update the email address in the PHP file on line 19. -->
                        <!-- The form should work on most web servers, but if the form is not working you may need to configure your web server differently. -->
                        <form name="sentMessage" id="contactForm" novalidate>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label for="name">Jméno</label>
                                    <input type="text" class="form-control" placeholder="Jméno" id="name" required data-validation-required-message="Please enter your name.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label for="email">Emailová Adresa</label>
                                    <input type="email" class="form-control" placeholder="Emailová Adresa" id="email" required data-validation-required-message="Please enter your email address.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label for="phone">Telefon</label>
                                    <input type="tel" class="form-control" placeholder="Telefon" id="phone" required data-validation-required-message="Please enter your phone number.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label for="message">Text</label>
                                    <textarea rows="5" class="form-control" placeholder="Text" id="message" required data-validation-required-message="Please enter a message."></textarea>
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <br>
                            <div id="success"></div>
                            <div class="row">
                                <div class="form-group col-xs-12">
                                    <button type="submit" class="btn btn-success btn-lg">Odeslat zprávu</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="text-center">
            <div class="footer-above">
                <div class="container">
                    <div class="row">
                        <div class="footer-col col-md-4">
                            <h3>Sídlo firmy</h3>
                            <p>204 00 Brno
                                <br>Černovická 256</p>
                        </div>
                        <div class="footer-col col-md-4">
                            <h3>Odkazy "aneb co o nás napsali".</h3>
                            <ul class="list-inline">
                                <li>
                                    <a href="https://facebook.com/" class="btn-social btn-outline"><span class="sr-only">Facebook</span><i class="fa fa-fw fa-facebook"></i></a>
                                </li>
                            </ul>
                        </div>
                        <div class="footer-col col-md-4">
                            <h3>O aplikaci</h3>
                            <p>Store Room for your business.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-below">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            Copyright &copy Josef Janda 2017
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
        <div class="scroll-top page-scroll hidden-sm hidden-xs hidden-lg hidden-md">
            <a class="btn btn-primary" href="#page-top">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>

        <!-- Portfolio Modals -->
         <div class="portfolio-modal modal fade" id="portfolioModalX" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-content">
                <div class="close-modal" data-dismiss="modal">
                    <div class="lr">
                        <div class="rl">
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2">
                            <div class="modal-body">
                                <h2>Start Store Room</h2>
                                <hr class="star-primary">
                                <p>
                                    Rádi Vám pomůžeme v oblastech, jako je:<br>
                                    - drobný prodej občerstvení (např. zmrzliny)<br>
                                    - prodej oblečení (např. second hand)
                                </p>
                                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Zavřít</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- jQuery -->
        <script src="/www/js/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="/www/vendor/bootstrap/js/bootstrap.min.js"></script>

        <!-- Plugin JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

        <!-- Contact Form JavaScript -->
        <script src="js/jqBootstrapValidation.js"></script>
        <script src="js/contact_me.js"></script>

        <!-- Theme JavaScript -->
        <script src="js/freelancer.min.js"></script>

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

}

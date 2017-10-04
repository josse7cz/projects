<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Owebu/default.latte

use Latte\Runtime as LR;

class Template68f4dc9463 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
	];

	public $blockTypes = [
		'content' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
?><!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        TNPW2 projekt
    </h1>
    <ol class="breadcrumb">
        <li><a href="/pokladna"><i class="fa fa-tablet"></i> Pokladna</a></li>
        <li class="active">O webu</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">


    <!-- Default box -->
    <div class="box">
        <div class="box-body">
            Store Room je webová aplikace, která slouží pro správu prodeje Vaší firmy, zejména jste-li začínajícím podnikatelem a snažíte se minimalizovat
            své výdaje za provoz, pak je apllikace od nás skvělou volbou. Tato webová aplikace byla napsána za účelem nejen školního projektu,
            ale hlavně pro mého kamaráda Vláďu, který je začínajícím podnikatel na poli obuvního průmyslu a který mě o podobnou aplikaci požádal.
            Pro tuto aplikaci bylo použito materiálů volně dostupných na internetu, avšak ne za účelem obohacení. Pokud tedy budete kopírovat
            kód z těchto stránek zavazujete se též dodržet podmínky stanovené autorem, jemuž tímto děkuji za zpracování. Pro tento školní projekt se jimi já zabývat nehodlám.
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

</section>
<!-- /.content --><?php
	}

}

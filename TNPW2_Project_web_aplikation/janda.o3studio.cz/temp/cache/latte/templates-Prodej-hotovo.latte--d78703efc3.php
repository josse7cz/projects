<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Prodej/hotovo.latte

use Latte\Runtime as LR;

class Templated78703efc3 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'cssExtra' => 'blockCssExtra',
	];

	public $blockTypes = [
		'content' => 'html',
		'cssExtra' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		$this->renderBlock('cssExtra', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['produkt'])) trigger_error('Variable $produkt overwritten in foreach on line 34');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Účtenka
    </h1>
    <ol class="breadcrumb">
        <li><a href="/pokladna"><i class="fa fa-tablet"></i> Pokladna</a></li>
        <li class="active"><a href="/pokladna/prodej">Prodej</a></li>
        <li class="active">Účtenka</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="row noprint">

        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <a href='javascript:void(0);' onclick="window.print();" class="btn btn-app">
                <i class="fa fa-print"></i> Tisk
            </a>


        </div>
    </div>

    <div class="row">
        <!-- Default box -->
        <div class="box">
            <div class="box-body">
                <div style='border-bottom: 1px solid black;' id="uctenka">
<?php
		$iterations = 0;
		foreach ($zbozi as $produkt) {
?>
                        <div style='border-bottom: 1px solid black;'>
                            <?php echo LR\Filters::escapeHtmlText($produkt->name) /* line 36 */ ?>

                            <?php echo LR\Filters::escapeHtmlText($produkt->amount) /* line 37 */ ?> *
                            <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $produkt->price, 2, ',', ' ')) /* line 38 */ ?> =
                            Kč <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $produkt->amount * $produkt->price, 2, ',', ' ')) /* line 39 */ ?>

                        </div>
<?php
			$iterations++;
		}
?>

                    <div>Celkem: Kč <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $celkem, 2, ',', ' ')) /* line 43 */ ?></div>
                    <div>Placeno: Kč <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receipt->placeno, 2, ',', ' ')) /* line 44 */ ?></div>
                    <div style='border-bottom: 1px solid black;' >Vráceno: Kč <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $receipt->placeno - $celkem, 2, ',', ' ')) /* line 45 */ ?></div>
                    <div style='border-bottom: 1px solid black;'><?php echo LR\Filters::escapeHtmlText($receipt->datetime->format("d.m.Y H:i")) /* line 46 */ ?></div>
                    <div>FIK: <?php echo LR\Filters::escapeHtmlText($receipt->fik) /* line 47 */ ?></div>
                    <div>BKP: <?php echo LR\Filters::escapeHtmlText($receipt->bkp) /* line 48 */ ?></div>
                    <?php
		if ($receipt->fik == "") {
			?><div>PKP:<?php echo LR\Filters::escapeHtmlText($receipt->pkp) /* line 49 */ ?></div><?php
		}
?>

                </div>

            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</section>
<!-- /.content -->
<?php
	}


	function blockCssExtra($_args)
	{
		extract($_args);
		?><link media="print" rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 61 */ ?>/assets/css/print.css">
<?php
	}

}

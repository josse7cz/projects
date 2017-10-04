<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Prodej/pridatzbozi.latte

use Latte\Runtime as LR;

class Template3430cd8ffc extends Latte\Runtime\Template
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
		if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 17');
		if (isset($this->params['produkt'])) trigger_error('Variable $produkt overwritten in foreach on line 38');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Prodej - Přidat
    </h1>
    <ol class="breadcrumb">
        <li><a href="/pokladna"><i class="fa fa-tablet"></i> Pokladna</a></li>
        <li class="active"><a href="/pokladna/prodej/">Prodej</a></li>
        <li class="active">Přidat</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?php
		$iterations = 0;
		foreach ($flashes as $flash) {
			?>    <div<?php if ($_tmp = array_filter(['alert', $flash->type, 'alert-dismissible'])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> OK!</h4>
        <?php echo LR\Filters::escapeHtmlText($flash->message) /* line 20 */ ?>

    </div>
<?php
			$iterations++;
		}
?>
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Seznam zboží</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Název</th>
                            <th>Cena</th>
                            <th>Na skladě</th>
                            <th style="width: 40px">Akce</th>
                        </tr>
<?php
		$iterations = 0;
		foreach ($iterator = $this->global->its[] = new LR\CachingIterator($zbozi) as $produkt) {
?>
                            <tr>
                                <td><?php echo LR\Filters::escapeHtmlText($iterator->counter) /* line 40 */ ?>.</td>
                                <td> <?php echo LR\Filters::escapeHtmlText($produkt->name) /* line 41 */ ?></td>
                                <td>
                                     <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $produkt->price, '2', ',', ' ')) /* line 43 */ ?>

                                </td>
                                <td>
                                     <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $produkt->amount, '3', ',', ' ')) /* line 46 */ ?>

                                </td>
                                <td><a class="btn btn-reddit" href='/pokladna/prodej/vybrat/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($produkt->id)) /* line 48 */ ?>'>Přidat</a></td>
                            </tr>
<?php
			$iterations++;
		}
		array_pop($this->global->its);
		$iterator = end($this->global->its);
?>
                    </table>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.box -->
    </div>
</section>
<!-- /.content -->

<?php
	}

}

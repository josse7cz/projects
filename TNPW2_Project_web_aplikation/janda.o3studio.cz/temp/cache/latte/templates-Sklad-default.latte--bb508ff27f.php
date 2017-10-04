<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Sklad/default.latte

use Latte\Runtime as LR;

class Templatebb508ff27f extends Latte\Runtime\Template
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
		if (isset($this->params['produkt'])) trigger_error('Variable $produkt overwritten in foreach on line 53');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sklad
    </h1>
    <ol class="breadcrumb">
        <li><a href="/pokladna"><i class="fa fa-tablet"></i> Sklad</a></li>
        <li class="active">Sklad</li>
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
    <div class="box">

        <div class="box-body">
            <a href='/pokladna/sklad/pridatzbozi' class="btn btn-app">
                <i class="fa fa-star"></i> Přidat nové
            </a>
            <a href='/pokladna/sklad/import' class="btn btn-app">
                <i class="fa fa-upload"></i> Import
            </a>
            <a href='/pokladna/sklad/export' class="btn btn-app">
                <i class="fa fa-download"></i> Export
            </a>
        </div>
        <!-- /.box-body -->
    </div>
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
                            <th>#</th>
                            <th>Název</th>
                            <th>Cena</th>
                            <th>Množství</th>
                            <th>Akce</th>
                        </tr>
<?php
		$iterations = 0;
		foreach ($iterator = $this->global->its[] = new LR\CachingIterator($zbozi) as $produkt) {
?>
                            <tr>
                                <td><?php echo LR\Filters::escapeHtmlText($iterator->counter) /* line 55 */ ?>.</td>
                                <td> <?php echo LR\Filters::escapeHtmlText($produkt->name) /* line 56 */ ?></td>
                                <td>
                                    <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $produkt->price, '2', ',', ' ')) /* line 58 */ ?>

                                </td>
                                <td>
                                    <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $produkt->amount, '3', ',', ' ')) /* line 61 */ ?>

                                </td>
                                <td>
                                    <a class="btn btn-reddit" href='upravit/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($produkt->id)) /* line 64 */ ?>'><i class="fa fa-pencil"></i> upravit</a>
                                    <a onclick="return confirm('Opravdu smazat?');"; class="btn btn-reddit" href='smazat/<?php
			echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($produkt->id)) /* line 65 */ ?>'><i class="fa fa-bomb"></i> smazat</a>
                                </td>
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

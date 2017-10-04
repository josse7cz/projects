<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Prodej/default.latte

use Latte\Runtime as LR;

class Templatef4ad8816b9 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'scriptsExtra' => 'blockScriptsExtra',
	];

	public $blockTypes = [
		'content' => 'html',
		'scriptsExtra' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		$this->renderBlock('scriptsExtra', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 17');
		if (isset($this->params['produkt'])) trigger_error('Variable $produkt overwritten in foreach on line 79');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pokladna
    </h1>
    <ol class="breadcrumb">
        <li><a href="/pokladna"><i class="fa fa-tablet"></i> Pokladna</a></li>
        <li class="active">Pokladna</li>
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

        <!-- /.box-body -->
    </div>
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $celkem, 2, ',', ' ')) /* line 42 */ ?></h3>

                    <p>Celkem</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <a href='/pokladna/prodej/pridatzbozi' class="btn btn-app">
                <i class="fa fa-star"></i> Přidat
            </a>

            <a href='/pokladna/prodej/platba' class="btn btn-app">
                <i class="fa fa-money"></i> Přejít k platbě
            </a>
        </div>
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
                            <th style="width: 10px">#</th>
                            <th>Název</th>
                            <th>Cena / j.</th>
                            <th>Množství</th>
                            <th>Cena celkem</th>
                            <th style="width: 40px">Akce</th>
                        </tr>
<?php
		$iterations = 0;
		foreach ($iterator = $this->global->its[] = new LR\CachingIterator($zbozi) as $produkt) {
?>
                            <tr>
                                <td><?php echo LR\Filters::escapeHtmlText($iterator->counter) /* line 81 */ ?>.</td>
                                <td> <?php echo LR\Filters::escapeHtmlText($produkt->name) /* line 82 */ ?></td>
                                <td>
                                    <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $produkt->price, '2', ',', ' ')) /* line 84 */ ?>

                                </td>
                                <td>
                                    <input class="autochange" data-bid="<?php echo LR\Filters::escapeHtmlAttr($produkt->bid) /* line 87 */ ?>" type="text" value="<?php
			echo LR\Filters::escapeHtmlAttr(call_user_func($this->filters->number, $produkt->amount, '3', ',', ' ')) /* line 87 */ ?>" id="amount<?php
			echo LR\Filters::escapeHtmlAttr($produkt->bid) /* line 87 */ ?>"><button onclick="zmenMnozstvi(<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::escapeJs($produkt->bid)) /* line 87 */ ?>)">Změnit</button>
                                </td>
                                <td>
                                    <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $produkt->amount * $produkt->price, '2', ',', ' ')) /* line 90 */ ?>

                                </td>
                                <td><a class="btn btn-reddit" href='/pokladna/prodej/odebrat/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($produkt->bid)) /* line 92 */ ?>'>Odebrat</a></td>
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


	function blockScriptsExtra($_args)
	{
		extract($_args);
?>
<script>
    $(document).ready(function () {

        $('.autochange').keypress(function (e) {
            if (e.which == 13) {
                zmenMnozstvi($(this).attr("data-bid"));
                return false;
            }
        });
    });
    function zmenMnozstvi(id)
    {
        location.href = "?bid=" + id + "&newamount=" + $("#amount" + id).val();
    }
</script>
<?php
	}

}

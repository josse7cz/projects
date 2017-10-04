<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Prodej/platba.latte

use Latte\Runtime as LR;

class Template047b80fdfb extends Latte\Runtime\Template
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
        <li class="active"><a href="/pokladna/prodej">Prodej</a></li>
        <li class="active">Platba</li>
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
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="hidden" id="celkem"><?php echo LR\Filters::escapeHtmlText($celkem) /* line 25 */ ?></div>
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->number, $celkem, 2, ',', ' ')) /* line 28 */ ?></h3>

                    <p>Celkem</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- form start -->
    <div class="row">
        <div class="col-lg3 col-xs-6">
<?php
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["dokoncitForm"];
		?>            <form role="form"<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'role' => NULL,
		), FALSE) ?>>
                <div class="box-body ">
                    <div class="form-group">
                        <label for="castka">Obdrženo</label>
                        <input style="font-size: 4em; height: 200%;" type="text" class="form-control " id="castka" placeholder=""<?php
		$_input = end($this->global->formsStack)["placeno"];
		echo $_input->getControlPart()->addAttributes(array (
		'style' => NULL,
		'type' => NULL,
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-lg"<?php
		$_input = end($this->global->formsStack)["submit"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		))->attributes() ?>>Dokončit</button>
                </div>
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), FALSE);
?>            </form>
        </div>
    </div>
    <div class="row" style="padding-top: 30px;">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div id="akced" class="small-box bg-aqua">
                <div class="inner">
                    <h3 id="vratitCastka">0,00</h3>

                    <p id="akce">Vrátit</p>
                </div>
                <div class="icon">
                    <i class="ion ion-cash"></i>
                </div>
            </div>
        </div>
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
        zmenit();
        $("#castka").change(
                zmenit
                );
        $("#castka").keyup(
                zmenit
                );
    });
    function zmenit() {
        var castka = parseFloat($("#castka").val());
        if ($("#castka").val() === "")
        {
            castka = 0;
        }

        var celkem = parseFloat($("#celkem").html());

        $("#vratitCastka").html(castka - celkem);
        if (castka - celkem > 0)
        {
            $("#akce").html("Vrátit");
            $("#akced").addClass("bg-aqua");
            $("#akced").removeClass("bg-red");
            $("#akced").removeClass("bg-green");
        }
        if (castka - celkem == 0)
        {
            $("#akce").html("Přesně!");
            $("#akced").removeClass("bg-aqua");
            $("#akced").removeClass("bg-red");
            $("#akced").addClass("bg-green");
        }
        if (castka - celkem < 0)
        {
            $("#akce").html("Chybí");
            $("#akced").removeClass("bg-aqua");
            $("#akced").addClass("bg-red");
            $("#akced").removeClass("bg-green");
        }
    }
</script>
<?php
	}

}

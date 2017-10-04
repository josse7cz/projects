<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Sklad/upravit.latte

use Latte\Runtime as LR;

class Template42efa1e6a5 extends Latte\Runtime\Template
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
        <li><a href="/pokladna/sklad">Sklad</a></li>
        <li class="active">Upravit</li>
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
    <!-- form start -->
<?php
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["upravitZboziForm"];
		?>    <form role="form"<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'role' => NULL,
		), FALSE) ?>>
        <input value="<?php echo LR\Filters::escapeHtmlAttr($produkt->id) /* line 24 */ ?>"<?php
		$_input = end($this->global->formsStack)["id"];
		echo $_input->getControlPart()->addAttributes(array (
		'value' => NULL,
		))->attributes() ?>>
        <div class="box-body">
            <div class="form-group">
                <label for="exampleInputEmail1">Název</label>
                <input  type="text" class="form-control" id="exampleInputEmail1" placeholder="název" value="<?php
		echo LR\Filters::escapeHtmlAttr($produkt->name) /* line 28 */ ?>"<?php
		$_input = end($this->global->formsStack)["name"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
		'value' => NULL,
		))->attributes() ?>>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Cena</label>
                <input class="form-control" id="exampleInputEmail1" placeholder="cena" value="<?php echo LR\Filters::escapeHtmlAttr($produkt->price) /* line 32 */ ?>"<?php
		$_input = end($this->global->formsStack)["price"];
		echo $_input->getControlPart()->addAttributes(array (
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
		'value' => NULL,
		))->attributes() ?>>
            </div>
            <div class="form-group">
                <label for="exampleInputAmount">Množství</label>
                <input class="form-control" id="exampleInputEmail1" placeholder="množství" value="<?php
		echo LR\Filters::escapeHtmlAttr($produkt->amount) /* line 36 */ ?>"<?php
		$_input = end($this->global->formsStack)["amount"];
		echo $_input->getControlPart()->addAttributes(array (
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
		'value' => NULL,
		))->attributes() ?>>
            </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary"<?php
		$_input = end($this->global->formsStack)["submit"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		))->attributes() ?>>Upravit zboží</button>
        </div>
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), FALSE);
?>    </form>
</div>
<!-- /.box -->

</section>
<!-- /.content --><?php
	}

}

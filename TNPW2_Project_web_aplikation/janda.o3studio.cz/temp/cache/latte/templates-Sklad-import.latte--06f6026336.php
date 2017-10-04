<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Sklad/import.latte

use Latte\Runtime as LR;

class Template06f6026336 extends Latte\Runtime\Template
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
		if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 34');
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
        <li class="active">Import</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <h2>Import</h2>
    <p>Formát XML:</p>
    <code>
        &lt;sklad&gt;
        &lt;produkt&gt;
        &lt;nazev&gt;Jablko
        &lt;/nazev&gt;
        &lt;cena&gt;30.90 &lt;/cena&gt;
        &lt;mnozstvi&gt;21&lt;/mnozstvi&gt;
        &lt;/produkt&gt;
        &lt;produkt&gt;
        &lt;nazev&gt;Hruška
        &lt;/nazev&gt;
        &lt;cena&gt;20.50&lt;/cena&gt;
        &lt;mnozstvi&gt;20&lt;/mnozstvi&gt;
        &lt;/produkt&gt;
        &lt;/sklad&gt;
    </code>
<?php
		$iterations = 0;
		foreach ($flashes as $flash) {
			?>    <div<?php if ($_tmp = array_filter(['alert', $flash->type, 'alert-dismissible'])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> OK!</h4>
        <?php echo LR\Filters::escapeHtmlText($flash->message) /* line 37 */ ?>

    </div>
<?php
			$iterations++;
		}
?>
    <!-- form start -->
<?php
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["importZboziForm"];
		?>    <form role="form"<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'role' => NULL,
		), FALSE) ?>>
        <div class="box-body">
            <div class="form-group">
                <label for="exampleInputEmail1">XML soubor importu</label>
                <input type="file" class="form-control" id="exampleInputEmail1" placeholder="název"<?php
		$_input = end($this->global->formsStack)["importfile"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
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
		))->attributes() ?>>Importovat</button>
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

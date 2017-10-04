<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Prodej/changeamount.latte

use Latte\Runtime as LR;

class Template4dcdf5a7f8 extends Latte\Runtime\Template
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
		extract($_args);
?>
<h1>Prodej - změna množství</h1>
<?php
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["zmenitMnozstviForm"];
		?><form<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		), FALSE) ?>>
    <input value="<?php echo LR\Filters::escapeHtmlAttr($produkt->id) /* line 4 */ ?>"<?php
		$_input = end($this->global->formsStack)["id"];
		echo $_input->getControlPart()->addAttributes(array (
		'value' => NULL,
		))->attributes() ?>>
    Nové množství: <input<?php
		$_input = end($this->global->formsStack)["amount"];
		echo $_input->getControlPart()->attributes() ?>><br>
    <input value='Změnit množství'<?php
		$_input = end($this->global->formsStack)["submit"];
		echo $_input->getControlPart()->addAttributes(array (
		'value' => NULL,
		))->attributes() ?>>

<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), FALSE);
?></form>
<?php
	}

}

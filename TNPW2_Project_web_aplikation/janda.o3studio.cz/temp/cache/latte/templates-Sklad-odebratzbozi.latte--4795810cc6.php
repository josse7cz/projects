<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Sklad/odebratzbozi.latte

use Latte\Runtime as LR;

class Template4795810cc6 extends Latte\Runtime\Template
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
<h1>Sklad - detail</h1>
<?php
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["odebratZboziForm"];
		?><form<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		), FALSE) ?>>
    Název: <input<?php
		$_input = end($this->global->formsStack)["name"];
		echo $_input->getControlPart()->attributes() ?>><br>
    Cena: <input<?php
		$_input = end($this->global->formsStack)["price"];
		echo $_input->getControlPart()->attributes() ?>><br>
    <input value='Odebrat zboží'<?php
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

<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/presenters/templates/Sign/in.latte

use Latte\Runtime as LR;

class Templatefb3eea9b1e extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
		'title' => 'blockTitle',
	];

	public $blockTypes = [
		'content' => 'html',
		'title' => 'html',
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
		$this->renderBlock('title', get_defined_vars());
?>

<?php
		/* line 4 */ $_tmp = $this->global->uiControl->getComponent("signInForm");
		if ($_tmp instanceof Nette\Application\UI\IRenderable) $_tmp->redrawControl(NULL, FALSE);
		$_tmp->render();
?>

<p><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("up")) ?>">Don't have an account yet? Sign up.</a></p>
<?php
	}


	function blockTitle($_args)
	{
		extract($_args);
?><h1>Sign In</h1>
<?php
	}

}

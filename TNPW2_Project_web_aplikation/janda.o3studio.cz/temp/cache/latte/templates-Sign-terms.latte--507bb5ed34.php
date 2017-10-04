<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PublicModule/presenters/templates/Sign/terms.latte

use Latte\Runtime as LR;

class Template507bb5ed34 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<h1>Podminky</h1>
Nic nechceme, za nic nerucime, za nic nemuzeme...<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}

<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/presenters/templates/@layout.latte

use Latte\Runtime as LR;

class Template8a00867a86 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}

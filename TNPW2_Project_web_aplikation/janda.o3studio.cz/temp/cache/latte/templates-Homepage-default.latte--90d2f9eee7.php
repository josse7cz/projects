<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/presenters/templates/Homepage/default.latte

use Latte\Runtime as LR;

class Template90d2f9eee7 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">

        <title>Title</title>

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 9 */ ?>/css/homepage.css">
    </head>

    <body>
<?php
		$iterations = 0;
		foreach ($flashes as $flash) {
			?>        <div<?php if ($_tmp = array_filter(['flash', $flash->type])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>><?php
			echo LR\Filters::escapeHtmlText($flash->message) /* line 13 */ ?></div>
<?php
			$iterations++;
		}
?>

        <a href="/sign/up">Registrace</a> 
        <a href="/sign/in">Přihlášení</a>


    </body>
</html>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 13');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}

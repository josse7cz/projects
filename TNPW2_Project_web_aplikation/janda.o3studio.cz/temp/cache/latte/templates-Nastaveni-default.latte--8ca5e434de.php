<?php
// source: /data/web/virtuals/165930/virtual/www/subdom/janda/app/PokladnaModule/presenters/templates/Nastaveni/default.latte

use Latte\Runtime as LR;

class Template8ca5e434de extends Latte\Runtime\Template
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
		if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 16');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Nastavení
    </h1>
    <ol class="breadcrumb">
        <li><a href="/pokladna"><i class="fa fa-tablet"></i> Pokladna</a></li>
        <li class="active">Nastavení</li>
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
        <?php echo LR\Filters::escapeHtmlText($flash->message) /* line 19 */ ?>

    </div>
<?php
			$iterations++;
		}
?>
    <h2>Uživatelské údaje</h2>
    <!-- form start -->
<?php
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["changeSettingsForm"];
		?>    <form role="form"<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'role' => NULL,
		), FALSE) ?>>
        <div class="box-body">
            <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <input  type="email" class="form-control" id="exampleInputEmail1" placeholder="Váš email"<?php
		$_input = end($this->global->formsStack)["email"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
            </div>
            <div class="form-group">
                <label for="exampleInputICO">IČO</label>
                <input   type="text" class="form-control" id="exampleInputICO" placeholder="Vaše IČO"<?php
		$_input = end($this->global->formsStack)["ico"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
            </div>
            <div class="form-group">
                <label for="exampleInputDIC">DIČ (Včetně CZ!)</label>
                <input   type="text" class="form-control" id="exampleInputDIC" placeholder="Vaše DIČ"<?php
		$_input = end($this->global->formsStack)["dic_popl"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
            </div>
            <div class="form-group">
                <label for="exampleInputIDP">ID provozovny</label>
                <input   type="text" class="form-control" id="exampleInputIDP" placeholder=""<?php
		$_input = end($this->global->formsStack)["id_provoz"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		'id' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
            </div>
            <div class="form-group">
                <label for="exampleInputIDPP">ID pokladny</label>
                <input   type="text" class="form-control" id="exampleInputIDPP" placeholder=""<?php
		$_input = end($this->global->formsStack)["id_pokl"];
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
		))->attributes() ?>>Uložit</button>
        </div>
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), FALSE);
?>    </form>
    <h2>Změna hesla</h2>
<?php
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["changePasswordForm"];
		?>    <form role="form"<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'role' => NULL,
		), FALSE) ?>>
        <div class="box-body">
            <div class="form-group">
                <label for="pass1">Heslo</label>
                <input id="pass1"  type="password" class="form-control" id="exampleInputEmail1" placeholder="heslo"<?php
		$_input = end($this->global->formsStack)["password"];
		echo $_input->getControlPart()->addAttributes(array (
		'id' => NULL,
		'type' => NULL,
		'class' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
            </div>
            <div class="form-group">
                <label for="pass2">Znovu heslo</label>
                <input type="password" class="form-control" id="pass2" placeholder="znovu heslo">
            </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary" onclick="return validatePass();"<?php
		$_input = end($this->global->formsStack)["submit"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'class' => NULL,
		'onclick' => NULL,
		))->attributes() ?>>Změnit heslo</button>
        </div>
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), FALSE);
?>    </form>
</div>
<!-- /.box -->

</section>
<!-- /.content -->

<?php
	}


	function blockScriptsExtra($_args)
	{
		extract($_args);
?>
<script>
    function validatePass()
    {
        if ($("#pass1").val() === $("#pass2").val())
        {
            return true;
        } else
        {
            alert("Hesla se neshodují!");
            return false;
        }

    }
</script>
<?php
	}

}

<?php /* Smarty version 2.6.22, created on 2009-11-23 19:00:22
         compiled from itemMaint.tpl */ ?>
<?php if ($this->_tpl_vars['displayupdates']): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'itemDisplay.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><hr /><?php endif; ?>
<form action="" method=post>
<input name=upc type=text id=upc> Enter UPC/PLU or product name here<br><br>
<input name='submit' type=submit value="Search">
</form>
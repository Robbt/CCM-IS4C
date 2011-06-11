<?php /* Smarty version 2.6.22, created on 2009-11-23 19:04:43
         compiled from itemMulti.tpl */ ?>
<div>More than 1 item found for: <h3><?php echo $this->_tpl_vars['match']; ?>
</h3></div>
<div>
<?php $_from = $this->_tpl_vars['matches']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row'] => $this->_tpl_vars['v']):
?>
<li><?php echo $this->_tpl_vars['row']; ?>
: <a href="?q=<?php echo $this->_tpl_vars['v']['upc']; ?>
"><?php echo $this->_tpl_vars['v']['description']; ?>
</a></li>
<?php endforeach; endif; unset($_from); ?>
</div>
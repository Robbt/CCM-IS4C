<?php /* Smarty version 2.6.22, created on 2011-02-24 15:26:49
         compiled from itemEdit.tpl */ ?>
<form name=pickSubDepartment action="" method=post>
<div id='box'>
	<table border=0 cellpadding=5 cellspacing=0>
		<tr>
		<td align=right><b>UPC</b></td>
		<td><font color='red'><?php echo $this->_tpl_vars['upc']; ?>
</font><input type=hidden value='<?php echo $this->_tpl_vars['upc']; ?>
' name=upc></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td></tr>
		<tr>
			<td><b>Description</b></td>
			<td><input type=text size=30 value='<?php echo $this->_tpl_vars['description']; ?>
' name=descript></td>
        		<td><b>Price</b></td>
			<td>$<input type=text value='<?php echo $this->_tpl_vars['normal_price']; ?>
' name=price size=10><input type=hidden value='<?php echo $this->_tpl_vars['normal_price']; ?>
' name=currentprice></td>
		</tr>
		<tr>
			<td><b>Brand</b></td>
			<td><input type=text value='<?php echo $this->_tpl_vars['brand']; ?>
' name=brand id="brandselect" autocomplete=off></td>
			<td align="right">Wholesale Cost</td>
			<td>$<input type=text value='<?php echo $this->_tpl_vars['wholesale_cost']; ?>
' name=wholesalecost size=10></td>
		</tr>
		<tr>
			<td><b>Size</b></td>
			<td><input type=text value='<?php echo $this->_tpl_vars['size']; ?>
' name=size size=4></td>
			<td><b>Vendor</b></td>
			<td><input type=text value='<?php echo $this->_tpl_vars['vendor']; ?>
' name=vendor size=20 id="vendorselect" autocomplete=off></td>

		</tr>
			<?php if ($this->_tpl_vars['special_price'] != 0): ?>
                               <tr>
					<td><font color=green><b>Sale Price:</b></font></td>
					<td><font color=green><?php echo $this->_tpl_vars['special_price']; ?>
</font></td>
					<td><font color=green>End Date:</td>
					<td><font color=green><?php echo $this->_tpl_vars['end_date']; ?>
</font></td>
				<tr>
				<tr>
					<td><font color=green>Batch Name</td>
					<td><font color=green><?php echo $this->_tpl_vars['salesbatch']['batchName']; ?>
</td>
					<td colspan = 2><font color=green><a href="http://<?php echo $_SERVER['SERVER_ADDR']; ?>
/batches/display.php?batchID=<?php echo $this->_tpl_vars['salesbatch']['batchID']; ?>
">Open This Batch</a></td>
				</tr>
                        <?php endif; ?>
 		<tr>
		<td><b>Notes</b></td>
		<td><textarea name=notes cols=30 rows=3><?php echo $this->_tpl_vars['notes']; ?>
</textarea></td>
		<td><b># of shelf tags to print</b></td>
		<td><input type=text name=label_prints value='<?php if (isset ( $this->_tpl_vars['label_prints'] )): ?> <?php echo $this->_tpl_vars['label_prints']; ?>
 <?php else: ?> 1 <?php endif; ?>' size=2></td>
		</tr>
		<tr>
		<td><b>Front Stock</b></td>	
		<td><input type=text value='<?php echo $this->_tpl_vars['frontstock']; ?>
' name=frontstock size=10 id="frontstock"></td>
	   	<td><b>Back Stock</b></td>	
		<td><input type=text value='<?php echo $this->_tpl_vars['backstock']; ?>
' name=backstock size=10 id="backstock"></td>	
	</table></div>
	<div id='box'>
        <table border=0 cellpadding=5 cellspacing=0 width='100%'>
	<tr>
        	<th>Dept & SubDept</th>
		<th>Tax</th>
		<th>FoodStamp</th>
		<th>Scale</th>
		<th>QtyFrc</th>
		<th>NoDisc</th>
		<th>In Use</th>
        </tr>
        <tr align=top>
        	<td align=left>
       			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "chainedSelector.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      	  	</td>
		<input type=hidden value=1 name="NoDisc">
		<td align=center><input type=checkbox value=1 name=tax <?php if ($this->_tpl_vars['tax'] == 1): ?>checked<?php endif; ?>></td>
		<td align=center><input type=checkbox value=1 name=FS <?php if ($this->_tpl_vars['foodstamp'] == 1): ?>checked<?php endif; ?>></td>
		<td align=center><input type=checkbox value=1 name=Scale <?php if ($this->_tpl_vars['scale'] == 1): ?>checked<?php endif; ?>></td>
		<td align=center><input type=checkbox value=1 name=QtyFrc <?php if ($this->_tpl_vars['qttyEnforced'] == 1): ?>checked<?php endif; ?>></td>
		<td align=center><input type=checkbox value=0 name=NoDisc <?php if (isset ( $this->_tpl_vars['discount'] ) && $this->_tpl_vars['discount'] == 0): ?>checked<?php endif; ?>></td>
		<td align=center><input type=checkbox value=1 name=inUse <?php if ($this->_tpl_vars['inUse'] == 1): ?>checked<?php endif; ?>></td>
		<td align=center></td>
	</tr>
	<tr>
		<td><input type='submit' name='submit' value='<?php echo $this->_tpl_vars['buttontext']; ?>
'>&nbsp;<a href='index.php'><font size='-1'>cancel</font></a></td>
		<td colspan=5>&nbsp;</td>
	</tr>
</table>
</div> 
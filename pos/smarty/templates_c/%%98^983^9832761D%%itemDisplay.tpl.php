<?php /* Smarty version 2.6.22, created on 2010-12-07 13:50:00
         compiled from itemDisplay.tpl */ ?>
<table border=0>
	<tr>
		<td align=left><b>UPC</b></td>
		<td><span style="color:blue"><?php echo $this->_tpl_vars['upc']; ?>
</span></td>
        </tr>
	<tr>
		<td><b>Description</b></td>
		<td><?php echo $this->_tpl_vars['description']; ?>
</td>
		<td><b>Price</b></td>
		<td><?php echo $this->_tpl_vars['normal_price']; ?>
</td>
	</tr>
	<tr>
		<td colspan=2>&nbsp;</td>
		<td><b>Wholesale Cost</b></td>
		<td><?php echo $this->_tpl_vars['wholesale_cost']; ?>
</td>
	</tr>
	<tr>
	        <td><b>Vendor</b></td>
		<td><?php echo $this->_tpl_vars['vendor']; ?>
</td>
		<td></td>
     	<tr>
		<td><b>Brand</b></td>
		<td><?php echo $this->_tpl_vars['brand']; ?>
</td>
		<td></td>
	</tr>
        <tr>
              <td><b># of Labels to print</b></td>
              <td><?php echo $this->_tpl_vars['label_prints']; ?>
</td>
        </tr>


	<tr>
		<td><b>Notes</b></td>
		<td colspan=2><?php echo $this->_tpl_vars['notes']; ?>
</td>
	</tr>
	<tr>
		<td><b>Front Stock</b></td>
		<td><?php echo $this->_tpl_vars['frontstock']; ?>
</td>
		<td><b>Back Stock</b></td>
		<td><?php echo $this->_tpl_vars['backstock']; ?>
</td>
	</tr>
</table>
<table border=0>
	<tr>
		<th>Deptartment/SubDepartment</th>
		<th>Tax</th>
		<th>FS</th>
		<th>Scale</th>
		<th>QtyFrc</th>
		<th>NoDisc</th>
		<th>inUse</th>
		<th>deposit</th>
	</tr>
	<tr>
		<td><?php echo $this->_tpl_vars['deptname']; ?>
<br /><?php echo $this->_tpl_vars['subdeptname']; ?>
</td>
		<td><?php if ($this->_tpl_vars['tax']): ?><img src="/images/greencheckmark.png"><?php else: ?><img src="/images/xed.png"><?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['foodstamp']): ?><img src="/images/greencheckmark.png"><?php else: ?><img src="/images/xed.png"><?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['scale']): ?><img src="/images/greencheckmark.png"><?php else: ?><img src="/images/xed.png"><?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['qttyEnforced']): ?><img src="/images/greencheckmark.png"><?php else: ?><img src="/images/xed.png"><?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['discount']): ?><img src="/images/xed.png"><?php else: ?><img src="/images/greencheckmark.png"><?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['inUse']): ?><img src="/images/greencheckmark.png"><?php else: ?><img src="/images/xed.png"><?php endif; ?></td>
		<td><?php echo $this->_tpl_vars['deposit']; ?>
</td>
	</tr>
</table>
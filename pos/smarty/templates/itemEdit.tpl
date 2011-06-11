<form name=pickSubDepartment action="" method=post>
<div id='box'>
	<table border=0 cellpadding=5 cellspacing=0>
		<tr>
		<td align=right><b>UPC</b></td>
		<td><font color='red'>{$upc}</font><input type=hidden value='{$upc}' name=upc></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td></tr>
		<tr>
			<td><b>Description</b></td>
			<td><input type=text size=30 value='{$description}' name=descript></td>
        		<td><b>Price</b></td>
			<td>$<input type=text value='{$normal_price}' name=price size=10><input type=hidden value='{$normal_price}' name=currentprice></td>
		</tr>
		<tr>
			<td><b>Brand</b></td>
			<td><input type=text value='{$brand}' name=brand id="brandselect" autocomplete=off></td>
			<td align="right">Wholesale Cost</td>
			<td>$<input type=text value='{$wholesale_cost}' name=wholesalecost size=10></td>
		</tr>
		<tr>
			<td><b>Size</b></td>
			<td><input type=text value='{$size}' name=size size=4></td>
			<td><b>Vendor</b></td>
			<td><input type=text value='{$vendor}' name=vendor size=20 id="vendorselect" autocomplete=off></td>

		</tr>
			{if $special_price != 0 }
                               <tr>
					<td><font color=green><b>Sale Price:</b></font></td>
					<td><font color=green>{$special_price}</font></td>
					<td><font color=green>End Date:</td>
					<td><font color=green>{$end_date}</font></td>
				<tr>
				<tr>
					<td><font color=green>Batch Name</td>
					<td><font color=green>{$salesbatch.batchName}</td>
					<td colspan = 2><font color=green><a href="http://{$smarty.server.SERVER_ADDR}/batches/display.php?batchID={$salesbatch.batchID}">Open This Batch</a></td>
				</tr>
                        {/if}
 		<tr>
		<td><b>Notes</b></td>
		<td><textarea name=notes cols=30 rows=3>{$notes}</textarea></td>
		<td><b># of shelf tags to print</b></td>
		<td><input type=text name=label_prints value='{if isset($label_prints)} {$label_prints} {else} 1 {/if}' size=2></td>
		</tr>
		<tr>
		<td><b>Front Stock</b></td>	
		<td><input type=text value='{$frontstock}' name=frontstock size=10 id="frontstock"></td>
	   	<td><b>Back Stock</b></td>	
		<td><input type=text value='{$backstock}' name=backstock size=10 id="backstock"></td>	
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
       			{include file="chainedSelector.tpl"}
      	  	</td>
		<input type=hidden value=1 name="NoDisc">
		<td align=center><input type=checkbox value=1 name=tax {if $tax == 1}checked{/if}></td>
		<td align=center><input type=checkbox value=1 name=FS {if $foodstamp == 1}checked{/if}></td>
		<td align=center><input type=checkbox value=1 name=Scale {if $scale == 1}checked{/if}></td>
		<td align=center><input type=checkbox value=1 name=QtyFrc {if $qttyEnforced == 1}checked{/if}></td>
		<td align=center><input type=checkbox value=0 name=NoDisc {if isset($discount) && $discount == 0}checked{/if}></td>
		<td align=center><input type=checkbox value=1 name=inUse {if $inUse == 1}checked{/if}></td>
		<td align=center></td>
	</tr>
	<tr>
		<td><input type='submit' name='submit' value='{$buttontext}'>&nbsp;<a href='index.php'><font size='-1'>cancel</font></a></td>
		<td colspan=5>&nbsp;</td>
	</tr>
</table>
</div> 

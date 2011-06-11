<table border=0>
	<tr>
		<td align=left><b>UPC</b></td>
		<td><span style="color:blue">{$upc}</span></td>
        </tr>
	<tr>
		<td><b>Description</b></td>
		<td>{$description}</td>
		<td><b>Price</b></td>
		<td>{$normal_price}</td>
	</tr>
	<tr>
		<td colspan=2>&nbsp;</td>
		<td><b>Wholesale Cost</b></td>
		<td>{$wholesale_cost}</td>
	</tr>
	<tr>
	        <td><b>Vendor</b></td>
		<td>{$vendor}</td>
		<td></td>
     	<tr>
		<td><b>Brand</b></td>
		<td>{$brand}</td>
		<td></td>
	</tr>
        <tr>
              <td><b># of Labels to print</b></td>
              <td>{$label_prints}</td>
        </tr>


	<tr>
		<td><b>Notes</b></td>
		<td colspan=2>{$notes}</td>
	</tr>
	<tr>
		<td><b>Front Stock</b></td>
		<td>{$frontstock}</td>
		<td><b>Back Stock</b></td>
		<td>{$backstock}</td>
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
		<td>{$deptname}<br />{$subdeptname}</td>
		<td>{if $tax}<img src="/images/greencheckmark.png">{else}<img src="/images/xed.png">{/if}</td>
		<td>{if $foodstamp}<img src="/images/greencheckmark.png">{else}<img src="/images/xed.png">{/if}</td>
		<td>{if $scale}<img src="/images/greencheckmark.png">{else}<img src="/images/xed.png">{/if}</td>
		<td>{if $qttyEnforced}<img src="/images/greencheckmark.png">{else}<img src="/images/xed.png">{/if}</td>
		<td>{if $discount}<img src="/images/xed.png">{else}<img src="/images/greencheckmark.png">{/if}</td>
		<td>{if $inUse}<img src="/images/greencheckmark.png">{else}<img src="/images/xed.png">{/if}</td>
		<td>{$deposit}</td>
	</tr>
</table>

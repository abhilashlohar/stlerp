<?php 

	$date= date("d-m-Y"); 
	$time=date('h:i:a',time());

	$filename="outstanding_report_customer_".$date.'_'.$time;

	header ("Expires: 0");
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-type: application/vnd.ms-excel");
	header ("Content-Disposition: attachment; filename=".$filename.".xls");
	header ("Content-Description: Generated Report" );

?>



<table border="1">
	<thead>
		<tr>
			<td colspan="12" align="center">
				<b> Outstanding report Customer
			</td>
		</tr>	
		<tr>
			<th>#</th>
			<th>Customer</th>
			<th>Payment Term</th>
			<th><?php echo $to_send['range0'].' - '.$to_send['range1'].' Days'; ?></th>
			<th><?php echo $to_send['range2'].' - '.$to_send['range3'].' Days'; ?></th>
			<th><?php echo $to_send['range4'].' - '.$to_send['range5'].' Days'; ?></th>
			<th><?php echo $to_send['range6'].' - '.$to_send['range7'].' Days'; ?></th>
			<th><?php echo ' > '.$to_send['range7'].' Days'; ?></th>
			<th>On Account</th>
			<th>Total Outstanding</th>
			<th>No-Due</th>
			<th>Closing Balance</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sr=0; $ClosingBalance=0; 
		$ColumnOnAccount=0; $ColumnOutStanding=0; $ColumnNoDue=0; $ColumnClosingBalance=0;
		foreach($LedgerAccounts as $LedgerAccount){ ?>
		<tr>
			<td><?php echo ++$sr; ?></td>
			<td style=" white-space: normal; width: 200px; "><?php echo $LedgerAccount->name.' <br/> '.$LedgerAccount->alias; ?></td>
			<td><?php echo $CustmerPaymentTerms[$LedgerAccount->id].' Days'; ?></td>
			<td>
				<?php if(@$Outstanding[$LedgerAccount->id]['Slab1'] > 0){
					echo '<span class="clrRed">'.@$Outstanding[$LedgerAccount->id]['Slab1'].'</span>';
				}else{
					echo '<span>'.@$Outstanding[$LedgerAccount->id]['Slab1'].'</span>';
				} ?>
			</td>
			<td>
				<?php if(@$Outstanding[$LedgerAccount->id]['Slab2'] > 0){
					echo '<span class="clrRed">'.@$Outstanding[$LedgerAccount->id]['Slab2'].'</span>';
				}else{
					echo '<span>'.@$Outstanding[$LedgerAccount->id]['Slab2'].'</span>';
				} ?>
			</td>
			<td>
				<?php if(@$Outstanding[$LedgerAccount->id]['Slab3'] > 0){
					echo '<span class="clrRed">'.@$Outstanding[$LedgerAccount->id]['Slab3'].'</span>';
				}else{
					echo '<span>'.@$Outstanding[$LedgerAccount->id]['Slab3'].'</span>';
				} ?>
			</td>
			<td>
				<?php if(@$Outstanding[$LedgerAccount->id]['Slab4'] > 0){
					echo '<span class="clrRed">'.@$Outstanding[$LedgerAccount->id]['Slab4'].'</span>';
				}else{
					echo '<span>'.@$Outstanding[$LedgerAccount->id]['Slab4'].'</span>';
				} ?>
			</td>
			<td>
				<?php if(@$Outstanding[$LedgerAccount->id]['Slab5'] > 0){
					echo '<span class="clrRed">'.@$Outstanding[$LedgerAccount->id]['Slab5'].'</span>';
				}else{
					echo '<span>'.@$Outstanding[$LedgerAccount->id]['Slab5'].'</span>';
				} ?>
			</td>
			
			<td>
			<?php 
				echo @$Outstanding[$LedgerAccount->id]['OnAccount']; 
				@$ColumnOnAccount+=@$Outstanding[$LedgerAccount->id]['OnAccount'];
			?>
			</td>
			<td>
			<?php $TotalOutStanding=@$Outstanding[$LedgerAccount->id]['Slab1']+@$Outstanding[$LedgerAccount->id]['Slab2']+@$Outstanding[$LedgerAccount->id]['Slab3']+@$Outstanding[$LedgerAccount->id]['Slab4']+@$Outstanding[$LedgerAccount->id]['Slab5']+@$Outstanding[$LedgerAccount->id]['OnAccount']; ?>
			<?php 
			if($TotalOutStanding>0){
				echo '<span class="clrRed"><b>'.@$TotalOutStanding.'</b></span>';
			}elseif($TotalOutStanding<0){
				echo '<span>'.@$TotalOutStanding.'</span>';
			} ?>
			<?php
				@$ColumnOutStanding+=@$TotalOutStanding;
			?>
			</td>
			<td>
				<?php 
				echo @$Outstanding[$LedgerAccount->id]['NoDue'];
				@$ColumnNoDue+=@$Outstanding[$LedgerAccount->id]['NoDue'];
				?>
			</td>
			<td>
			<?php $ClosingBalance=@$Outstanding[$LedgerAccount->id]['Slab1']+@$Outstanding[$LedgerAccount->id]['Slab2']+@$Outstanding[$LedgerAccount->id]['Slab3']+@$Outstanding[$LedgerAccount->id]['Slab4']+@$Outstanding[$LedgerAccount->id]['Slab5']+@$Outstanding[$LedgerAccount->id]['NoDue']+@$Outstanding[$LedgerAccount->id]['OnAccount']; ?>
			<?php if($ClosingBalance!=0){
				echo $ClosingBalance;
			} ?>
			<?php
				@$ColumnClosingBalance+=$ClosingBalance;
			?>
			</td>
		</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="8"><div  align="right">TOTAL</div></th>
				<th><?php echo $ColumnOnAccount; ?></th>
				<th><?php echo $ColumnOutStanding; ?></th>
				<th><?php echo $ColumnNoDue; ?></th>
				<th><?php echo $ColumnClosingBalance; ?></th>
			</tr>
		</tfoot>
</table>
			
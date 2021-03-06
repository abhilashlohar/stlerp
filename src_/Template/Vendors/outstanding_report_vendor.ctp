<style>
table td, table th{
    white-space: nowrap;
	font-size:12px !important;
}
.clrRed{
	color:red;
}
</style>
<div class="portlet box red">
	<div class="portlet-title">
		<div class="caption">Outstandings for Vendor for <?php echo $to_send['tdate']; ?></div>
	</div>
	
	<div class="portlet-body">
	<form method="GET" >
		<table width="100%">
			<tbody>
				<tr>
					<td width="15%">
							<?php 
							$options = [];
							$options = [['text'=>'All','value'=>'All'],['text'=>'Zero','value'=>'Zero'],['text'=>'Negative','value'=>'Negative'],['text'=>'Positive','value'=>'Positive']];
							echo $this->Form->input('total1', ['options' => $options,'label' => false,'class' => 'form-control input-sm select2me stock1','placeholder'=>'Sub-Group','value'=> h(@$amountType)]); ?>
						</td>
						<td>
								<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
							</td>
					   <td align="right" width="10%"><input type="text" class="form-control input-sm pull-right" placeholder="Search..." id="search3"  style="width: 100%;"></td>
					   <td align="right" width="8%">
							<?php $url=json_encode($to_send,true);
							 echo $this->Html->link( '<i class="fa fa-file-excel-o"></i> Excel', '/Vendors/Vendor-Export-Excel/'.$url.'?total1='.$amountType,['class' =>'btn  green tooltips','target'=>'_blank','escape'=>false,'data-original-title'=>'Download as excel']); ?>
					   </td>
				</tr>
			</tbody>
		</table>
	</form>
		<div class="table-toolbar">
			<div class="row">
				<div class="col-md-6"></div>
				<div class="col-md-6" >
					
				</div>
			</div>
		</div>
		<div class="table-responsive" >
			<table class="table table-bordered" id="main_tble">
			<thead>
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
			$ClosingBalanceLedgerWise=[];
			foreach($LedgerAccounts as $LedgerAccount){
				
					$ttlamt=round(@$Outstanding[$LedgerAccount->id]['Slab1']+@$Outstanding[$LedgerAccount->id]['Slab2']+@$Outstanding[$LedgerAccount->id]['Slab3']+@$Outstanding[$LedgerAccount->id]['Slab4']+@$Outstanding[$LedgerAccount->id]['Slab5']+@$Outstanding[$LedgerAccount->id]['NoDue']+@$Outstanding[$LedgerAccount->id]['OnAccount'],2);
					
					if($amountType=='Zero' && $ttlamt==0){
						$ClosingBalanceLedgerWise[$LedgerAccount->id]= "Yes";
					}else if($amountType=='Positive' && $ttlamt > 0 ){ 
						$ClosingBalanceLedgerWise[$LedgerAccount->id]= "Yes";
					}else if($amountType=='Negative' && $ttlamt < 0 ){
						//$ClosingBalanceLedgerWise[$LedgerAccount->id]= $ttlamt;
						$ClosingBalanceLedgerWise[$LedgerAccount->id]= "Yes";
					}else if($amountType=='All'){
						//$ClosingBalanceLedgerWise[$LedgerAccount->id]= $ttlamt;
						$ClosingBalanceLedgerWise[$LedgerAccount->id]= "Yes";
					}else{
						$ClosingBalanceLedgerWise[$LedgerAccount->id]= "No";
					}
				
			}
			
			$sr=0; $ClosingBalance=0; 
			$ColumnOnAccount=0; $ColumnOutStanding=0; $ColumnNoDue=0; $ColumnClosingBalance=0;
			foreach($LedgerAccounts as $LedgerAccount){ 
				if($ClosingBalanceLedgerWise[$LedgerAccount->id]=="Yes"){
			?>
			<tr>
				<td><?php echo ++$sr; ?></td>
				<td style=" white-space: normal; width: 200px; ">
				<?php if(!empty($LedgerAccount->alias)){ ?>
				<?php echo $this->Html->link($LedgerAccount->name.' ( '.$LedgerAccount->alias.')',[
							'controller'=>'Ledgers','action' => 'AccountStatementRefrence?status=completed&ledgerid='.$LedgerAccount->id],array('target'=>'_blank')); 
				}else{ 
					echo $this->Html->link($LedgerAccount->name,[
							'controller'=>'Ledgers','action' => 'AccountStatementRefrence?status=completed&ledgerid='.$LedgerAccount->id],array('target'=>'_blank'));
					
				}		?></td>
				<td><?php echo $VendorPaymentTerms[$LedgerAccount->id].' Days'; ?></td>
				<td>
					<?php if(@$Outstanding[$LedgerAccount->id]['Slab1'] > 0){
						echo '<span class="clrRed">'.round(@$Outstanding[$LedgerAccount->id]['Slab1'],2).'</span>';
					}else{
						echo '<span>'.@$Outstanding[$LedgerAccount->id]['Slab1'].'</span>';
					} ?>
				</td>
				<td>
					<?php if(@$Outstanding[$LedgerAccount->id]['Slab2'] > 0){
						echo '<span class="clrRed">'.round(@$Outstanding[$LedgerAccount->id]['Slab2'],2).'</span>';
					}else{
						echo '<span>'.round(@$Outstanding[$LedgerAccount->id]['Slab2'],2).'</span>';
					} ?>
				</td>
				<td>
					<?php if(@$Outstanding[$LedgerAccount->id]['Slab3'] > 0){
						echo '<span class="clrRed">'.round(@$Outstanding[$LedgerAccount->id]['Slab3'],2).'</span>';
					}else{
						echo '<span>'.round(@$Outstanding[$LedgerAccount->id]['Slab3'],2).'</span>';
					} ?>
				</td>
				<td>
					<?php if(@$Outstanding[$LedgerAccount->id]['Slab4'] > 0){
						echo '<span class="clrRed">'.round(@$Outstanding[$LedgerAccount->id]['Slab4'],2).'</span>';
					}else{
						echo '<span>'.round(@$Outstanding[$LedgerAccount->id]['Slab4'],2).'</span>';
					} ?>
				</td>
				<td>
					<?php if(@$Outstanding[$LedgerAccount->id]['Slab5'] > 0){
						echo '<span class="clrRed">'.round(@$Outstanding[$LedgerAccount->id]['Slab5'],2).'</span>';
					}else{
						echo '<span>'.round(@$Outstanding[$LedgerAccount->id]['Slab5'],2).'</span>';
					} ?>
				</td>
				
				<td>
				<?php 
					echo round(@$Outstanding[$LedgerAccount->id]['OnAccount'],2); 
					@$ColumnOnAccount+=@$Outstanding[$LedgerAccount->id]['OnAccount'];
				?>
				</td>
				<td>
				<?php $TotalOutStanding=@$Outstanding[$LedgerAccount->id]['Slab1']+@$Outstanding[$LedgerAccount->id]['Slab2']+@$Outstanding[$LedgerAccount->id]['Slab3']+@$Outstanding[$LedgerAccount->id]['Slab4']+@$Outstanding[$LedgerAccount->id]['Slab5']+@$Outstanding[$LedgerAccount->id]['OnAccount']; ?>
				<?php 
				if($TotalOutStanding>0){
					echo '<span class="clrRed" id="outstnd">'.round(@$TotalOutStanding,2).'</span>';
				}elseif($TotalOutStanding<0){
					echo '<span id="outstnd">'.round(@$TotalOutStanding,2).'</span>';
				} ?>
				<?php
					@$ColumnOutStanding+=@$TotalOutStanding;
				?>
				</td>
				<td>
					<?php 
					echo round(@$Outstanding[$LedgerAccount->id]['NoDue'],2);
					@$ColumnNoDue+=@$Outstanding[$LedgerAccount->id]['NoDue'];
					?>
				</td>
				<td>
				<?php $ClosingBalance=@$Outstanding[$LedgerAccount->id]['Slab1']+@$Outstanding[$LedgerAccount->id]['Slab2']+@$Outstanding[$LedgerAccount->id]['Slab3']+@$Outstanding[$LedgerAccount->id]['Slab4']+@$Outstanding[$LedgerAccount->id]['Slab5']+@$Outstanding[$LedgerAccount->id]['NoDue']+@$Outstanding[$LedgerAccount->id]['OnAccount']; ?>
				<?php if($ClosingBalance!=0){
					echo round($ClosingBalance,2);
				}else{
					echo "0";
				} ?>
				<?php
					@$ColumnClosingBalance+=$ClosingBalance;
				?>
				</td>
			</tr>
			<?php } }?>
			</tbody>
			<tfoot id='tf'>
				<tr>
					<th colspan="8"><div  align="right">TOTAL</div></th>
					<th class="oa"><?php echo round($ColumnOnAccount,2); ?></th>
					<th class="os"><?php echo round($ColumnOutStanding,2); ?></th>
					<th class="nd"><?php echo round($ColumnNoDue,2); ?></th>
					<th class="cb"><?php echo round($ColumnClosingBalance,2); ?></th>
				</tr>
			</tfoot>
			</table>
		</div>
	</div>
</div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script>
$(document).ready(function() {
var $rows = $('#main_tble tbody tr');
	$('#search3').on('keyup',function() {
	
			var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
    		var v = $(this).val();
    		if(v){ 
    			$rows.show().filter(function() {
    				var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
		
    				return !~text.indexOf(val);
    			}).hide();
    		}else{
    			$rows.show();
    		}
    	});
	/////
	$('.stock').die().live("change",function(){
		var stock = $(this).val();
			var total_closing_bal=0;
			var total_nodue=0;
			var total_out=0;
			var total_on_acc=0;
			$("#main_tble tbody tr").each(function(){
				var closing_bal = $(this).find("td:nth-child(12)").html();
				var no_due = $(this).find("td:nth-child(11)").html();
				var total_outstanding = 
				$(this).find("td:nth-child(10) #outstnd").html();
				
				var on_acc = $(this).find("td:nth-child(9)").html();
				
				if(stock =='Positive' && closing_bal > 0){
					$(this).show();
					total_closing_bal=parseFloat(total_closing_bal)+parseFloat(closing_bal);
					total_nodue=parseFloat(total_nodue)+parseFloat(no_due);
					total_out=parseFloat(total_out)+parseFloat(total_outstanding);
					total_on_acc=parseFloat(total_on_acc)+parseFloat(on_acc);
					total_nodue=round(total_nodue,2);
					total_on_acc=round(total_on_acc,2);
					total_closing_bal=round(total_closing_bal,2);
					$("#main_tble #tf tr th.oa").html('');
					$("#main_tble #tf tr th.os").html('');
					$("#main_tble #tf tr th.nd").html('');
					$("#main_tble #tf tr th.cb").html('');
					if(total_on_acc == 0){
						$("#main_tble #tf tr th.oa").html(0);
					}else{
						$("#main_tble #tf tr th.oa").html('');
						$("#main_tble #tf tr th.oa").html(total_on_acc);
					}
					if(total_out == 0){
						$("#main_tble #tf tr th.os").html(0);
					}else{
						$("#main_tble #tf tr th.os").html('');
						$("#main_tble #tf tr th.os").html(total_out);
					}
					if(total_nodue==0){
						$("#main_tble #tf tr th.nd").html(0);
					}else{
						$("#main_tble #tf tr th.nd").html('');
						$("#main_tble #tf tr th.nd").html(total_nodue);
					}	
					if(total_closing_bal==0){
						$("#main_tble #tf tr th.cb").html(0);
					}else{
						$("#main_tble #tf tr th.cb").html('');
						$("#main_tble #tf tr th.cb").html(total_closing_bal);
					}	
					
				}else if(stock =='Negative' && closing_bal < 0){
					$(this).show(); 
					total_closing_bal=parseFloat(total_closing_bal)+parseFloat(closing_bal);
					total_nodue=parseFloat(total_nodue)+parseFloat(no_due);
					total_out=parseFloat(total_out)+parseFloat(total_outstanding);
					total_on_acc=parseFloat(total_on_acc)+parseFloat(on_acc);
					total_nodue=round(total_nodue,2);
					total_on_acc=round(total_on_acc,2);
					total_closing_bal=round(total_closing_bal,2);
					
					
				
					if(total_on_acc == 0){
						$("#main_tble #tf tr th.oa").html(0);
					}else{
						$("#main_tble #tf tr th.oa").html('');
						$("#main_tble #tf tr th.oa").html(total_on_acc);
					}
					if(total_out == 0){
						$("#main_tble #tf tr th.os").html(0);
					}else{
						$("#main_tble #tf tr th.os").html('');
						$("#main_tble #tf tr th.os").html(total_out);
					}
					if(total_nodue==0){
						$("#main_tble #tf tr th.nd").html(0);
					}else{
						$("#main_tble #tf tr th.nd").html('');
						$("#main_tble #tf tr th.nd").html(total_nodue);
					}	
					if(total_closing_bal==0){
						$("#main_tble #tf tr th.cb").html(0);
					}else{
							$("#main_tble #tf tr th.cb").html('');
						$("#main_tble #tf tr th.cb").html(total_closing_bal);
					}	
				}else if(stock =='Zero' && closing_bal == 0){
					$(this).show();
					$("#main_tble #tf tr th.os").html(0);
					$("#main_tble #tf tr th.cb").html(0);
					$("#main_tble #tf tr th.oa").html(0);
					$("#main_tble #tf tr th.nd").html(0);
				}else{
					$(this).hide();
				}
				
			});
		
	});	
});
		
</script>
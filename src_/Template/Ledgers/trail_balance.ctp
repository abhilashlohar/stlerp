<?php 
$url_excel="/?".$url;



?>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Trial Balance</span>
		</div>
		<div class="actions">
			<?php  echo $this->Html->link( '<i class="fa fa-file-excel-o"></i> Excel', '/Ledgers/excelTb/'.$url_excel.'',['class' =>'btn btn-sm green tooltips','target'=>'_blank','escape'=>false,'data-original-title'=>'Download as excel']); ?>
		</div>
		<div class="portlet-body form">
	<form method="GET" >
				<table class="table table-condensed" >
				<tbody>
					<tr>
					<td>
						<div class="row">
							
							<div class="col-md-3">
								<?php echo $this->Form->input('From', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker from_date','data-date-format' => 'dd-mm-yyyy','value' => @date('d-m-Y', strtotime($from_date)),'data-date-start-date' => date("d-m-Y",strtotime($financial_year->date_from)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to))]); ?>
								
							</div>
							<div class="col-md-3">
								<?php echo $this->Form->input('To', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker to_date','data-date-format' => 'dd-mm-yyyy','value' => @date('d-m-Y', strtotime($to_date)),'data-date-start-date' => date("d-m-Y",strtotime($financial_year->date_from)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to))]); ?>
							</div>
							<div class="col-md-3">
							<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
							</div>
							
						</div>
					</td>
					</tr>
				</tbody>
			</table>
	</form>
		<!-- BEGIN FORM-->
<?php if(!empty($ClosingBalanceForPrint)){?>
<table class="table table-bordered table-hover table-condensed" width="100%">
					<thead>
						<tr>
							<th scope="col"></th>
							<th scope="col" colspan="2" style="text-align:center";><b>Opening Balance</th>
							<th scope="col" colspan="2" style="text-align:center";><b>Transactions</th>
							<th scope="col" colspan="2" style="text-align:center";><b>Closing balance</th>
						</tr>
						<tr>
							<th scope="col"><b>Ledgers</th>
							<th scope="col" style="text-align:center";><b>Debit</th>
							<th scope="col" style="text-align:center";><b>Credit</th>
							<th scope="col" style="text-align:center";><b>Debit</th>
							<th scope="col" style="text-align:center";><b>Credit</th>
							<th scope="col" style="text-align:center";><b>Debit</th>
							<th scope="col" style="text-align:center";><b>Credit</th>
						</tr>
					</thead>
					<tbody>
							<?php $i=1; $totalDr=0; $totalCr=0; foreach($ClosingBalanceForPrint as $key=>$data){  ?>
							<tr>		
								<td>
									<a href="#" role='button' status='close' class="group_name" group_id='<?php  echo $key; ?>' style='color:black;'>
									<?php echo $data['name']; ?>
									</a>
								</td>
								<?php if($OpeningBalanceForPrint[$key]['balance'] > 0){ ?>
								<td><?php echo $OpeningBalanceForPrint[$key]['balance'];
										?></td>
								<td><?php echo "-" ?></td>
								<?php }else{ ?>
								<td><?php echo "-"; ?></td>
								<td><?php echo abs($OpeningBalanceForPrint[$key]['balance']);  ?></td>
								<?php } ?>


								
								<td><?php echo abs($TransactionDr[$key]['balance']);
										?></td>
								
								<td><?php echo abs($TransactionCr[$key]['balance']);
									  ?></td>
								
								


								<?php if($data['balance'] > 0){ ?>
								<td><?php echo $data['balance'];
									@$totalDr=@$totalDr+$data['balance'];	?></td>
								<td><?php echo "-" ?></td>
								<?php }else{ ?>
								<td><?php echo "-"; ?></td>
								<td><?php echo abs($data['balance']);  
									@$totalCr=@$totalCr+abs($data['balance']);  ?></td>
								<?php } ?>
								
							</tr>
							<?php } ?>
							<tr>
								<td colspan="5">Opening Stocks</td>
								<td  scope="col" align="left"><?php echo round($itemOpeningBalance,2); ?></td>
								<td></td>
							</tr>
							<tr style="color:red;">
								<td colspan="5">Diff. In Opening Balance</td>
								<?php if($differenceInOpeningBalance > 0){ ?>
									<td></td>
									<td  scope="col" align="left"><?php echo round($differenceInOpeningBalance,2); ?></td>
									
								<?php } else { ?>
										
									<td  scope="col" align="left"><?php 
									$differenceInOpeningBalance=abs($differenceInOpeningBalance);
									echo round($differenceInOpeningBalance,2); ?></td>
									<td></td>
								<?php } ?>
								
							</tr>
							<tr>
								<td colspan="5">Total</td>
								<?php if($differenceInOpeningBalance < 0){ ?>
								<td  scope="col" align="left"><?php echo round($totalDr+$itemOpeningBalance,2); ?></td>
								<td  scope="col" align="left"><?php echo round($totalCr+$differenceInOpeningBalance,2); ?></td>
								<?php } else { ?>
								<td  scope="col" align="left"><?php echo round($totalDr+$differenceInOpeningBalance+$itemOpeningBalance,2); ?></td>
								<td  scope="col" align="left"><?php echo round($totalCr,2); ?></td>
								<?php } ?>
							</tr>
							
					</tbody>
				<tfoot>
			</tfoot>
		</table>
				
<?php } ?>
</div></div>
</div>

<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script>
$(document).ready(function() {
	$(".group_name").die().live('click',function(e){
	   var current_obj=$(this);
	   var group_id=$(this).attr('group_id');
	  
	  if(current_obj.attr('status') == 'open')
	   {
			$('tr.row_for_'+group_id+'').remove();
			current_obj.attr('status','close');
		   $('table > tbody > tr > td> a').removeClass("group_a");
		   $('table > tbody > tr > td> span').removeClass("group_a");

		}
	   else
	   {  
		   var from_date = $('.from_date').val();
		   var to_date = $('.to_date').val();
		   
		   var url="<?php echo $this->Url->build(['controller'=>'Ledgers','action'=>'firstSubGroupsTb']); ?>";
		   url=url+'/'+group_id +'/'+from_date+'/'+to_date,
//alert(url);
			$.ajax({
				url: url,
			}).done(function(response) {
				current_obj.attr('status','open');
				 current_obj.addClass("group_a");
				current_obj.closest('tr').find('span').addClass("group_a");
				$('<tr class="append_tr row_for_'+group_id+'"><td colspan="7">'+response+'</td></tr>').insertAfter(current_obj.closest('tr'));
			});			   
		}   
	});	
	
	$(".first_grp_name").die().live('click',function(e){ 
	   var current_obj=$(this);
	   var first_grp_id=$(this).attr('first_grp_id');
	  
	  if(current_obj.attr('status') == 'open')
	   {
			$('tr.row_for_'+first_grp_id+'').remove();
			current_obj.attr('status','close');
		   $('table > tbody > tr > td> a').removeClass("group_a");
		   $('table > tbody > tr > td> span').removeClass("group_a");

		}
	   else
	   {  
		   var from_date = $('.from_date').val();
		   var to_date = $('.to_date').val();
		   var url="<?php echo $this->Url->build(['controller'=>'Ledgers','action'=>'secondSubGroupsTb']); ?>";
		   url=url+'/'+first_grp_id +'/'+from_date+'/'+to_date,
			$.ajax({
				url: url,
			}).done(function(response) {
				current_obj.attr('status','open');
				 current_obj.addClass("group_a");
				current_obj.closest('tr').find('span').addClass("group_a");
				$('<tr class="append_tr row_for_'+first_grp_id+'"><td colspan="7">'+response+'</td><td></td></tr>').insertAfter(current_obj.closest('tr'));
			});			   
		}   
	});	

	$(".second_grp_name").die().live('click',function(e){ 
	   var current_obj=$(this);
	   var second_grp_id=$(this).attr('second_grp_id');
	  
	  if(current_obj.attr('status') == 'open')
	   {
			$('tr.row_for_'+second_grp_id+'').remove();
			current_obj.attr('status','close');
		   $('table > tbody > tr > td> a').removeClass("group_a");
		   $('table > tbody > tr > td> span').removeClass("group_a");

		}
	   else
	   {  
		   var from_date = $('.from_date').val();
		   var to_date = $('.to_date').val();
		   var url="<?php echo $this->Url->build(['controller'=>'Ledgers','action'=>'ledgerAccountDataTb']); ?>";
		   url=url+'/'+second_grp_id +'/'+from_date+'/'+to_date,
			
			$.ajax({
				url: url,
			}).done(function(response) {
				current_obj.attr('status','open');
				 current_obj.addClass("group_a");
				current_obj.closest('tr').find('span').addClass("group_a");
				$('<tr class="append_tr row_for_'+second_grp_id+'"><td colspan="7">'+response+'</td></tr>').insertAfter(current_obj.closest('tr'));
			});			   
		}   
	});
});	
</script>
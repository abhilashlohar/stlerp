<?php  
if(!empty($status)){
		$url_excel=$status."/?".$url;
	}else{
		$url_excel="/?".$url;
	}	
?>
<?php 	
	$first="01";
	$last="31";
	$start_date=$first.'-'.$financial_month_first->month;
	$end_date=$last.'-'.$financial_month_last->month;
	//pr($end_date);
?>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Quotations</span> 
			<?php if($pull_request=="true"){ ?>
			: Select a Quotation to convert into sales-order
			<?php } elseif($gst_pull_request=="true"){ ?>
			: Select a Quotation to convert into GST Sales-Order	
			<?php } elseif($copy_request=="copy"){?>
			: Select a Quotation to Copy
			<?php } ?>
		</div>
		<div class="actions">
			<?php
			
			
			if($status==null or $status=='Pending'){ $class1='btn btn-primary'; }else{ $class1='btn btn-default'; }
			if($status=='Converted into Sales Order'){ $class2='btn btn-primary'; }else{ $class2='btn btn-default'; }
			if($status=='Closed'){ $class3='btn btn-primary'; }else{ $class3='btn btn-default'; }
			?>
			<?php if($pull_request!="true" and $gst_pull_request!="true" and $copy_request!="copy"){ ?>
				<?= $this->Html->link(
					'Pending',
					'/Quotations/index/Pending',
					['class' => $class1]
				); ?>
				<?= $this->Html->link(
					'Converted in Sales Order',
					'/Quotations/index/Converted into Sales Order',
					['class' => $class2]
				); ?>
				
				<?= $this->Html->link(
					'Closed',
					'/Quotations/index/Closed',
					['class' => $class3]
				); ?>
				<?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i> Excel','/Quotations/Export-Excel/'.$url_excel,['class' =>'btn  green tooltips','target'=>'_blank','escape'=>false,'data-original-title'=>'Download as excel']); ?>
				<?php }  ?>
			
		</div>	
	
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-12">
				<form method="GET" >
				<input type="hidden" name="pull-request" value="<?php echo @$pull_request; ?>">
				<input type="hidden" name="copy-request" value="<?php echo @$copy_request;?>">
				<input type="hidden" name="status" value="<?php echo @$status;?>">
				<input type="hidden" name="gst-pull-request" value="<?php echo @$gst_pull_request;?>">
				<table class="table table-condensed">
					<tbody> 
						<tr>
							<td width="14%">
								<div class="input-group" style="" id="pnf_text">
											<span class="input-group-addon">QT-</span><input type="text" name="qt2" class="form-control input-sm" placeholder="Quotation No" value="<?php echo @$qt2; ?>">
								</div>
							</td>
							<td width="8%">
								<input type="text" name="file" class="form-control input-sm" placeholder="File" value="<?php echo @$file; ?>">
							</td>
							<td width="15%">
								<input type="text" name="customer" class="form-control input-sm" placeholder="Customer" value="<?php echo @$customer; ?>">
							</td>
							<?php
							$EMP_ID =[23,16,17];
							if(in_array($s_employee_id,$EMP_ID)){ ?>
								<td width="15%"><?php echo $this->Form->input('salesman', ['empty'=>'--SalesMans--','options' => $SalesMans,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'SalesMan Name','value'=> h(@$salesman) ]); ?></td>
							<?php }else{ ?>
								<td width="15%"><?php echo $this->Form->input('salesman', ['empty'=>'--SalesMans--','options' => $SalesMans,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'SalesMan Name','value'=> h(@$s_employee_id) ]); ?></td>
							<?php } ?>
							<td width="15%">
								<input type="text" name="product" class="form-control input-sm" placeholder="Product" value="<?php echo @$product; ?>">
							</td>
							
							
							
								
							
							
						</tr>
					</tbody>
				</table>
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td  width="24%">
								<?php echo $this->Form->input('items', ['empty'=>'--Items--','options' => $Items,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Category','value'=> h(@$items) ]); ?>
							</td>
							<td  width="19%">
								<?php echo $this->Form->input('item_group', ['empty'=>'---Group---','options'=>$ItemGroups,'label' => false,'class' => 'form-control input-sm item_group select2me','placeholder'=>'Item Name','value'=> h(@$item_group) ]); ?>
							</td>
							<td  width="19%">
								<?php echo $this->Form->input('item_subgroup', ['empty'=>'---Sub Group---','options'=>$ItemSubGroups,'label' => false,'class' => 'form-control input-sm item_subgroup select2me','placeholder'=>'Item Name','value'=> h(@$item_subgroup) ]); ?>
							</td>
							<?php if($pull_request=="true"){ ?>
							<td width="15%">
								<input type="text" name="q_dateFrom" class="form-control input-sm date-picker" placeholder="From" value="<?php echo @$q_dateFrom; ?>"  data-date-format="dd-mm-yyyy" >
							</td>
							<td width="15%">
										<input type="text" name="q_dateTo" class="form-control input-sm date-picker" placeholder="To" value="<?php echo @$q_dateTo; ?>"  data-date-format="dd-mm-yyyy" >
							</td>
							<?php }else{ ?>
								<td width="15%">
									
										<input type="text" name="From" class="form-control input-sm date-picker" placeholder="From" value="<?php echo @$From; ?>"  data-date-format="dd-mm-yyyy" >
								</td>
								<td width="15%">
										<input type="text" name="To" class="form-control input-sm date-picker" placeholder="To" value="<?php echo @$To; ?>"  data-date-format="dd-mm-yyyy" >
									
								</td>
							<?php } ?>
							
							<td><button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button></td>
						</tr>
					</tbody>
				</table>
				</form>
				<?php $page_no=$this->Paginator->current('Quotations'); $page_no=($page_no-1)*20; 
					
				?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="5%">Sr. No.</th>
							<th width="15%">Ref. No.</th>
							<th width="15%">Customer</th>
							<th width="15%">Salesman</th>
							<th width="15%">Product</th>
							<th width="10%">Items Name</th>
							<th width="10%">Amount</th>
							<?php if($pull_request=="true"){ ?>
							<th width="10%">Quotation Date</th>
							<?php }else{ ?>
							<th width="10%">Finalisation Date</th>
							<?php } ?>
							<?php if($status=="Closed"){ ?>
							<th width="10%">Closing Reason</th>
							<th width="10%">Closing Date</th>
							<?php } ?>
							<th width="10%" class="actions"><?= __('Actions') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $total_amount=0; $i=0; foreach ($quotations as $quotation):  $i++;
						$quotation_id = $quotation->id;
						$quotation->id = $EncryptingDecrypting->encryptData($quotation->id);
						if($quotation->status=='Converted Into Sales Order'){ $tr_color='#f4f4f4'; }
						if($quotation->status=='Pending'){ $tr_color='#FFF'; }
						if($quotation->status=='Closed'){ $tr_color='#FFF'; }
						//pr($allowed_acc);
						 if((in_array($quotation->customer->employee_id,$allowed_emp) || in_array($quotation->customer->employee_id,$allowed_acc)) || $quotation->created_by == $s_employee_id || $quotation->employee_id == $s_employee_id || $gst_pull_request=='true'){
						?>
						<tr>
							<td><?= h(++$page_no) ?>
							<?php if($quotation->revision > 0) { ?>
							<button type="button" class="btn btn-xs tooltips revision_show" value="<?=$quotation->id ?>" style="margin-left:5px;" data-original-title="Revisions"><i class="fa fa-plus-circle"></i></button>
							<button type="button" class="btn btn-xs tooltips revision_hide" id="revision_hide" value="<?=$quotation->id ?>" style="margin-left:5px; display:none;"><i class="fa fa-minus-circle"></i></button><?php } ?></td>
							
							<td><?= h(($quotation->qt1.'/QT-'.str_pad($quotation->qt2, 3, '0', STR_PAD_LEFT).'/'.$quotation->qt3.'/'.$quotation->qt4)) ?><?php if($quotation->revision > 0) { ?><?php echo ' (#R'.$quotation->revision.' )'; ?><?php } ?></td>
							<td><?= h($quotation->customer->customer_name).'('.h($quotation->customer->alias).')' ?></td>
							<td><?= h($quotation->employee->name) ?></td>
							<td><?= h($quotation->item_group->name) ?></td>
							<td>
								<div class="btn-group">
									<button id="btnGroupVerticalDrop5" type="button" class="btn  btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Items <i class="fa fa-angle-down"></i></button>
										<ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop5">
										<?php foreach($quotation->quotation_rows as $quotation_rows){ 
											if($quotation_rows->quotation_id == $quotation_id){?>
											<li><p><?= h($quotation_rows->item->name) ?></p></li>
											<?php }}?>
										</ul>
								</div>
							</td>
							<td align="center"><?= h($this->Money->indianNumberFormat($quotation->total)) ?><?php $total_amount=$total_amount+$quotation->total; ?></td>
							
							<?php if($pull_request=="true" ){ ?>
							<td><?php echo date("d-m-Y",strtotime($quotation->created_on)); ?></td>
							<?php }else{ ?>
							<td><?php echo date("d-m-Y",strtotime($quotation->finalisation_date)); ?></td>
							<?php } ?>
							<?php if($quotation->status=='Closed'){ ?>
							<td><?= h($quotation->reason) ?> </td>
							<td><?php 
									echo date("d-m-Y",strtotime($quotation->closing_date));
								?></td>	
							<?php } ?>
							<td class="actions">
							<?php if($status=='Converted into Sales Order' && in_array(21,$allowed_pages)){?>
								<?php echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'confirm', $quotation->id],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs yellow tooltips','data-original-title'=>'View as PDF')); 
							} ?>
							<?php //if(in_array($quotation->customer->employee_id,$allowed_emp)){ 
								
							?>
							
							<?php  if(date("d-m-Y",strtotime($quotation->created_on)) >= $start_date || date("d-m-Y",strtotime($quotation->created_on)) <= $end_date)  {  ?>
								<?php if($quotation->status=='Pending' and $gst_pull_request!="true"  and $pull_request!="true" && $copy_request!="copy"){ ?>
								<?php if(in_array(21,$allowed_pages)){ ?>
								<?php echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'confirm', $quotation->id],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs yellow tooltips','data-original-title'=>'View as PDF')); ?>
								
							<?php } ?>	
							<a href="#" class="btn btn-xs blue tooltips  select_term_condition" qwerty="<?php echo $quotation_id; ?>" data-original-title="Pending Item"><i class="fa fa-eye"></i></a>
								<?php
								 if(!in_array(date("m-Y",strtotime($quotation->created_on)),$closed_month) && $st_year_id==$quotation->financial_year_id and in_array(2,$allowed_pages))
								 { ?>
								<div class="btn-group dropup">
									<button type="button" class="btn btn-xs blue dropdown-toggle" data-toggle="dropdown"><i class="fa fa-pencil-square-o"></i></button>
									<ul class="dropdown-menu" role="menu" style=" margin-left: -82px; ">
										<li>
											<?php	echo $this->Html->link('<i class="fa fa-file-o"></i>Save as Revision ','/Quotations/Add?revision='.$quotation->id,array('escape'=>false,'class'=>''));
											 ?>
										</li>
										<li>
											<?php echo $this->Html->link('<i class="fa fa-repeat"></i>Save as old version ',['action' => 'edit', $quotation->id],array('escape'=>false,'class'=>'')); ?>
										</li>
									</ul>
								</div>
							<?php } } } 
							//}
							?>
								
								<?php if($status == "Closed" && $pull_request=="true" && $close_status != "close"){
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into Sales Order','/Sales-Orders/Add?quotation='.$quotation->id,array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));

								}elseif($close_status == "close" && $gst_pull_request != "true"){
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into Sales Order','/Sales-Orders/Add?quotation='.$quotation->id.'&&'.'status='.'close',array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
									
								}elseif($gst_pull_request == "true" && $close_status == "open"){ 
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into Sales Order','/Sales-Orders/gstSalesOrderAdd?quotation='.$quotation->id.'&&'.'status='.'open',array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
									
								}elseif($gst_pull_request == "true"){ 
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into Sales Order','/Sales-Orders/gstSalesOrderAdd?quotation='.$quotation->id.'&&'.'status='.'close',array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
									
								}elseif($pull_request == "true" && $close_status == "open"){
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into Sales Order','/Sales-Orders/Add?quotation='.$quotation->id.'&&'.'status='.'open',array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
									
								}elseif($pull_request == "true"){
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into Sales Order','/Sales-Orders/Add?quotation='.$quotation->id,array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
									
								} ?>
								<?php //if(in_array($quotation->created_by,$allowed_emp)){ ?>
								<?php 
								if(in_array(30,$allowed_pages)){
								if($quotation->status=='Pending' && $copy_request!="copy" && $pull_request!="true" && $gst_pull_request!="true" && $st_year_id==$quotation->financial_year_id){
								echo $this->Html->link('<i class="fa fa-minus-circle"></i> ',['action' => '#'],array('escape'=>false,'class'=>'btn btn-xs red tooltips close_btn','data-original-title'=>'Close','role'=>'button','quote_id'=>$quotation_id));
								
								} }?>
								
								
								<?php if($copy_request=="copy"){
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Copy','/Quotations/Add?copy='.$quotation->id,array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
								} ?>
								<?php if($quotation->status=='Closed' ){
								echo $this->Form->postLink('<i class="fa fa-minus-circle"></i> ',['action' =>'reopen', $quotation->id],['escape' => false,'class' => 'btn btn-xs green tooltips','data-original-title'=>'Reopen','confirm' => __('Are you sure, you want to reopen ?', $quotation->id)]
								);
								} 
								//}
								?>
								
							</td>
						</tr>
						
						 <?php 
						 }
						 endforeach; ?>
					</tbody>
					
					<tfoot>
						<tr>
							<td colspan="6" style="text-align:right"><b>Total</b></td>
							<td style="text-align:right"><b><?php echo $this->Money->indianNumberFormat($total_amount);?></b></td>
							<td style="text-align:right"><b></b></td>
							<td style="text-align:right"><b></b></td>
							<?php 
								if($status == "Closed"){ ?>
								<td></td>
								<td></td>
							<?php	}
							?>		
						</tr>
					</tfoot>
				</table>
				
			</div>
		</div>
	</div>
</div>

<div id="quotation_close_reason" style="display:none;"></div>

<div id="close_reason" style="display:none;"></div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<style>
#sortable li{
	cursor: -webkit-grab;
}
</style>
<?php echo $this->Html->css('/drag_drop/jquery-ui.css'); ?>
<?php echo $this->Html->script('/drag_drop/jquery-1.12.4.js'); ?>
<?php echo $this->Html->script('/drag_drop/jquery-ui.js'); ?>
<script>
$( function() {
$( "#sortable" ).sortable();
$( "#sortable" ).disableSelection();
} );
</script>
<script>
$(document).ready(function() {
	$.uniform.update();

	$('#close_popup_btn').die().live("click",function() {
		var quote_id=$(this).attr('quote_id');
		var reason_id=$('.radio_text:checked').val();
		
		if((quote_id) && (reason_id)){
			var url="<?php echo $this->Url->build(['controller'=>'Quotations','action'=>'Close']); 
			?>";
			url=url+'/'+quote_id+'/'+reason_id,
			
			$.ajax({
				url: url,
			}).done(function(response) {
				location.reload();
			});
			
		}else{
			alert("Please Select Atleast one reason For Close Quotaion");		
		}		
    });
	
	$('.close_btn').die().live("click",function() {
		var quote_id=$(this).attr('quote_id');
		var addr=$(this).text();
		$("#myModal2").show();
		$("#close_popup_btn").attr('quote_id',quote_id);
    });
	
	$('.closebtn2').on("click",function() { 
		$("#myModal2").hide();
    });
	
	$('.revision_show').die().live("click",function() {
		//$("#revision_hide").show();
		//$("#revision_show").hide();
		$(this).hide();
		$(this).closest('td').find(".revision_hide").show();
		var entity=$(this).closest('tr');
		var quote_id=$(this).val();
		var url="<?php echo $this->Url->build(['controller'=>'Quotations','action'=>'Revision']);
		?>";
		url=url+'/'+quote_id+'/'+quote_id,
		
		$.ajax({
			url: url,
		}).done(function(response) {
			entity.after(response);
			//$("#view_revision").html(response);
		});		
    });
	
	$('.revision_hide').die().live("click",function() {
		$(this).closest('tr').next().remove();
		$(this).hide();
		$(this).closest('td').find(".revision_show").show();
	});
});	
</script>
<script>
$(document).ready(function() {
	
	$('.select_term_condition').die().live("click",function() {  
		var sid=$(this).attr('qwerty');
		
		open_address(sid);
    });
	$('.closebtn2').on("click",function() {  
		$("#myModal2").hide();
	});
	function open_address(sid){
			
			var url="<?php echo $this->Url->build(['controller'=>'Quotations','action'=>'showPendingItem']); ?>";
			var url1=url+'/'+sid; //alert(url1);
			url=url+'/'+sid,
			$.ajax({
				url: url,
			}).done(function(response) {
				$("#show_model").html(response);
			});
		}
		
    });
	
</script>

<div id="show_model">

</div>

<div id="myModal2" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="false" style="display: none; padding-right: 12px;"><div class="modal-backdrop fade in" ></div>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body" id="result_ajax">
			<h4>Closing Reason</h4>
				<div style=" overflow: auto; height: 450px;">
				
				<table class="table table-hover tabl_tc">
				
				<?php foreach ($closeReasons as $closeReason): ?>
					<tr>
						<td width="10">
						<div class="radio-list">
						<?php echo $this->Form->radio('reason', [
										['value' => $closeReason->id, 'text' => $closeReason->reason,'class'=>'radio_text']]); ?>
						</div>
						</td>
					</tr>
					
				</div>
				<?php endforeach; ?>
				</table>
				</div>
				
			</div>
			<div class="modal-footer">
				<button class="btn default closebtn2">Close</button>
				<button class="btn red close_rsn" id="close_popup_btn">Close Quotation</button>
			</div>
		</div>
	</div>
</div>

<?php $url_excel="/?".$url; ?>

<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Invoice</span> 
			<?php if($inventory_voucher=="true"){ echo ": Select Invoice to create Inventory Voucher"; } ?>
			<?php if($sales_return=="true"){ echo ": Select Invoice to create Sales Return"; } ?>
		</div>
		<div class="actions">
		<?php if($inventory_voucher!="true"){ ?>
		<?php
		if($status=='Pending' || $status==''){ $class1='btn btn-primary'; }else{ $class1='btn btn-default'; }
		if($status=='Cancel'){ $class3='btn btn-primary'; }else{ $class3='btn btn-default'; }
		?>
			
			<?= $this->Html->link(
				'Pending',
				'/Invoices/index/Pending',
				['class' => $class1]
			); ?>
			
			<?php echo $this->Html->link( '<i class="fa fa-file-excel-o"></i> Excel', '/Invoices/Excel-Export/'.$url_excel.'',['class' =>'btn  green tooltips','target'=>'_blank','escape'=>false,'data-original-title'=>'Download as excel']); ?>
			
		<?php } ?>
		</div>
	
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-12">
				<form method="GET" >
				<input type="hidden" name="inventory_voucher" value="<?php echo @$inventory_voucher; ?>">
				<table class="table table-bordered table-striped table-hover">
					<tbody>
						<tr>
							<td width="18%">  
								<input type='hidden' name='sales_return' value='<?php echo $sales_return; ?>' />
								<div class="input-group" style="" id="pnf_text">
									<span class="input-group-addon">IN-</span><input type="text" name="invoice_no" class="form-control input-sm" placeholder="Invoice No" value="<?php echo @$invoice_no; ?>">
								</div>
							</td>
							<td width="15%">
								<input type="text" name="file" class="form-control input-sm" placeholder="File" value="<?php echo @$file; ?>">
							</td>
							<td width="18%">
								<input type="text" name="customer" class="form-control input-sm" placeholder="Customer" value="<?php echo @$customer; ?>">
							</td>
							<td width="10%">
								<?php echo $this->Form->input('items', ['empty'=>'--Items--','options' => $Items,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Category','value'=> h(@$items) ]); ?>
							</td>
							<td width="10%">
								<input type="text" name="From" class="form-control input-sm date-picker" placeholder="Date From" value="<?php echo @$From; ?>" data-date-format="dd-mm-yyyy">
							</td>
							<td width="10%">
								<input type="text" name="To" class="form-control input-sm date-picker" placeholder="Date To" value="<?php echo @$To; ?>" data-date-format="dd-mm-yyyy" >
							</td>
							<td width="10%">
								<input type="text" name="total_From" class="form-control input-sm" placeholder="Total" value="<?php echo @$total_From; ?>">
							</td>
								
							</td >
							<td><button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button></td>
						</tr>
					</tbody>
				</table>
				</form>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th width="5%">Sr. No.</th>
							<th width="10%">Invoice No.</th>
							<th width="10%">Sales Order No.</th>
							<th width="12%">Customer</th>
							<th width="8%">Items</th>
							<th width="8%">Created Date</th>
							<th style="text-align:right;" width="10%">Total</th>
							<th width="10%">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php $i=0; foreach ($invoices as $invoice): 
						$invoice_id = $EncryptingDecrypting->encryptData($invoice->id);
						if($invoice->status=='Pending'){ $tr_color='#FFF'; }
						if($invoice->status=='Cancel'){ $tr_color='#FFF'; }
						?>
						<?php if(sizeof($invoice->invoice_rows) > 0){ ?>
						<tr>
							<td><?php echo ++$i; ?></td>
							<td>
							
							<?= h(($invoice->in1.'/IN-'.str_pad($invoice->in2, 3, '0', STR_PAD_LEFT).'/'.$invoice->in3.'/'.$invoice->in4)) ?></td>
							<?php if($invoice->sales_order_id != 0){ ?>
							<td>
							
							<?php if(in_array($invoice->customer->employee_id,$allowed_emp)){
							$sales_order_id = $EncryptingDecrypting->encryptData($invoice->sales_order->id);	
							if($invoice->sales_order->gst == 'yes'){
								echo $this->Html->link( $invoice->sales_order->so1.'/SO-'.str_pad($invoice->sales_order->so2, 3, '0', STR_PAD_LEFT).'/'.$invoice->sales_order->so3.'/'.$invoice->sales_order->so4,[
							'controller'=>'SalesOrders','action' => 'gstConfirm',$sales_order_id],array('target'=>'_blank')); 
							}else{
								echo $this->Html->link( $invoice->sales_order->so1.'/SO-'.str_pad($invoice->sales_order->so2, 3, '0', STR_PAD_LEFT).'/'.$invoice->sales_order->so3.'/'.$invoice->sales_order->so4,[
							'controller'=>'SalesOrders','action' => 'confirm',$sales_order_id],array('target'=>'_blank')); 
							} }?>
							</td>
							<?php }else{?>
							<td></td><?php } ?>
							
							
							<td><?php echo $invoice->customer->customer_name.'('.$invoice->customer->alias.')' ?></td>
							<td>
								<div class="btn-group">
									<button id="btnGroupVerticalDrop5" type="button" class="btn  btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Items <i class="fa fa-angle-down"></i></button>
										<ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop5">
										<?php  foreach($invoice->invoice_rows as $invoice_row){
											if($invoice_row->invoice_id == $invoice->id){?>
											<li><p><?= h($invoice_row->item->name) ?></p></li>
											<?php }}?>
										</ul>
								</div>
							</td>
							<td><?php echo date("d-m-Y",strtotime($invoice->date_created)); ?></td>
							<td align="right"><?= h($this->Money->indianNumberFormat($invoice->total_after_pnf)) ?></td>
							<td class="actions">
							<?php
							if(in_array($invoice->customer->employee_id,$allowed_emp)){
								if($invoice->invoice_type=='GST'){ ?>
									<?php 
									if(in_array(27,$allowed_pages)){
									echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'GstConfirm', $invoice_id],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs yellow tooltips','data-original-title'=>'View as PDF')); ?>
									<?php }} else {?>
									<?php 
									if(in_array(27,$allowed_pages)){
										echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'confirm', $invoice_id],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs yellow tooltips','data-original-title'=>'View as PDF')); ?>
									<?php }} ?>
								<?php if($invoice->status !='Cancel' and $sales_return!="true" and $inventory_voucher!="true" and in_array(8,$allowed_pages)){
									
								if($invoice->invoice_type=='GST' and !in_array(date("m-Y",strtotime($invoice->date_created)),$closed_month))
								 { 
								echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'GstEdit', $invoice_id,],array('escape'=>false,'class'=>'btn btn-xs blue tooltips','data-original-title'=>'Edit'));  
								
								 }else {
									echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'edit', $invoice_id,],array('escape'=>false,'class'=>'btn btn-xs blue tooltips','data-original-title'=>'Edit')); 
								 }
								 
								
								} ?>
								<?php if($invoice->send_emails){ ?>
								<?php echo $this->Html->link('<i class="fa fa-download"></i>',['action' => 'dispatch_download', $invoice_id,],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs blue tooltips','data-original-title'=>'View Dispatch Document'));  
								?><?php } 
								
								}?>
								<?php
								if($inventory_voucher=="true" ){
								echo $this->Html->link('<i class="fa fa-repeat"></i>  Create Inventory Voucher','/Ivs/add/'.$invoice_id,array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
								
								} ?><?php 
								if($sales_return=="true" && $invoice->sale_return_status=='No'){
								echo $this->Html->link('<i class="fa fa-repeat"></i>  Sale Return','/SaleReturns/Add?invoice='.$invoice_id,array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
								} ?>
								
							</td>
						</tr>
							<?php }  endforeach; ?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
				
			</div>
		</div>

<script>
$( function() {
$( "#sortable" ).sortable();
$( "#sortable" ).disableSelection();
} );
</script>
<?php echo $this->Html->css('/drag_drop/jquery-ui.css'); ?>
<?php echo $this->Html->script('/drag_drop/jquery-1.12.4.js'); ?>
<?php echo $this->Html->script('/drag_drop/jquery-ui.js'); ?>

<script>
$(document).ready(function() { 
	
	$('#close_popup_btn').die().live("click",function() {
		var invoice_id=$(this).attr('invoice_id');
		var url="<?php echo $this->Url->build(['controller'=>'Invoices','action'=>'Cancel']); 
		?>";
		
		url=url+'/'+invoice_id
		$.ajax({
			url: url,
		}).done(function(response) {
			location.reload();
		});		
		
    });

	
	$('.close_btn').die().live("click",function() {
		var invoice_id=$(this).attr('invoice_id');
		$("#myModal2").show();
		$("#close_popup_btn").attr('invoice_id',invoice_id);
    });
	
	$('.closebtn2').on("click",function() { 
		$("#myModal2").hide();
    });
	
	
	
	
});	
</script>

<div id="myModal2" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="false" style="display: none; padding-right: 12px;"><div class="modal-backdrop fade in" ></div>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
			
				Are you sure you want to cancel
				
			</div>
			<div class="modal-footer">
				<button class="btn default closebtn2">Close</button>
				<button class="btn red close_rsn" id="close_popup_btn">Close Invoice</button>
			</div>
		</div>
	</div>
</div>

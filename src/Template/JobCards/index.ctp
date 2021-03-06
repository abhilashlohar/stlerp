<?php 

	if(!empty($status)){
		$url_excel=$status."/?".$url;
	}else{
		$url_excel="/?".$url;
	}
	
	$jobCardStatus=$status;

?>
<div class="portlet light bordered">
<div class="portlet-title">
	<div class="caption">
		<i class="icon-globe font-blue-steel"></i>
		<span class="caption-subject font-blue-steel uppercase">Job Cards</span>
	</div>
	<div class="actions">
		<?php 
			if($status==null or $status=='Pending'){ $class1='btn btn-primary'; }else{ $class1='btn btn-default'; }
			if($status=='Closed'){ $class3='btn btn-primary'; }else{ $class3='btn btn-default'; }
			 ?>
		<?= $this->Html->link(
			'Pending',
			'/JobCards/index/Pending',
			['class' => $class1]
		); ?>
		<?= $this->Html->link(
			'Closed',
			'/JobCards/index/Closed',
			['class' => $class3]
		); ?>
		<?php echo $this->Html->link( '<i class="fa fa-file-excel-o"></i> Excel', '/JobCards/Export-Excel/'.$url_excel.'',['class' =>'btn  green tooltips','target'=>'_blank','escape'=>false,'data-original-title'=>'Download as excel']); ?>
	</div>
<div class="portlet-body">
	<div class="row">
		<div class="col-md-12">
			<form method="GET" >
				<input type="hidden">
				<table class="table table-condensed">
					<tbody>
					<tr>
							<td>
								<div class="input-group" >
											<span class="input-group-addon">JC-</span><input type="text" name="jc_no" class="form-control input-sm" placeholder="JC No" value="<?php echo @$jc_no; ?>"></div>
										
									</td>
									<td>
										<input type="text" name="jc_file_no" class="form-control input-sm" placeholder="File" value="<?php echo @$jc_file_no; ?>">
									</td>
									
							<td>
										<div class="input-group" >
											<span class="input-group-addon">SO-</span><input type="text" name="so_no" class="form-control input-sm" placeholder="SO No" value="<?php echo @$so_no; ?>">
										</div>
									</td>
									<td>
										<input type="text" name="so_file_no" class="form-control input-sm" placeholder="File" value="<?php echo @$so_file_no; ?>">
									</td>
									<td width="15%">
											<?php echo $this->Form->input('customers', ['empty'=>'--Customer--','options' => $Customers,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Category','value'=> h(@$customer_id) ]); ?>
									</td>
									<td width="12%">
										<?php echo $this->Form->input('items', ['empty'=>'--Items--','options' => $Items,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Category','value'=> h(@$items) ]); ?>
									</td>
								<td>
										<input type="text" name="Required_From" class="form-control input-sm date-picker" placeholder="Required From" value="<?php echo @$Required_From; ?>" data-date-format="dd-mm-yyyy" >
									</td><td>
										<input type="text" name="Required_To" class="form-control input-sm date-picker" placeholder="Required To" value="<?php echo @$Required_To; ?>" data-date-format="dd-mm-yyyy" >
									</td>
							<td>
								
										<input type="text" name="Created_From" class="form-control input-sm date-picker" placeholder="Created From" value="<?php echo @$Created_From; ?>" data-date-format="dd-mm-yyyy" >
									</td>
									<td>
										<input type="text" name="Created_To" class="form-control input-sm date-picker" placeholder="Created To" value="<?php echo @$Created_To; ?>" data-date-format="dd-mm-yyyy" >
									
							</td>
							
							<td><button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button></td>
						</tr>
					</tbody>
				</table>
			</form>
		
			<?php  $page_no=$this->Paginator->current('JobCards'); $page_no=($page_no-1)*20; ?>	 
			<table class="table table-bordered table-striped table-hover ">
				<thead>
				<tr>
					<th >Sr.No.</th>
					<th>Job Card No.</th>
					<th>Sales Order</th>
					<th width="10%">Items Name</th>
					<th>Required Date</th>
					<th>Created Date</th>
					<th>Action</th>
				</tr>
				</thead>
				<tbody>
		    <?php    foreach ($jobCards as $jobCard): 
			
			$so=@$SalesOrderQty[@$jobCard->sales_order_id];
			$in=@$InvoiceQty[@$jobCard->sales_order_id];
			$iv=@$InventoryVoucherQty[@$jobCard->sales_order_id];
			$jobCard->id = $EncryptingDecrypting->encryptData($jobCard->id);
			if(($jobCardStatus==null || $jobCardStatus=='Pending')){ 
				if($so != $in || $so != $iv || $in != $iv ){
					
					$jobCard_id = $jobCard->id;
			?>
				<tr>
					<td><?= h(++$page_no) ?></td>
					<td><?= h(($jobCard->jc1.'/JC-'.str_pad($jobCard->jc2, 3, '0', STR_PAD_LEFT).'/'.$jobCard->jc3.'/'.$jobCard->jc4))?></td>
					<td><?= h(($jobCard->sales_order->so1.'/SO-'.str_pad($jobCard->sales_order->so2, 3, '0', STR_PAD_LEFT).'/'.$jobCard->sales_order->so3.'/'.$jobCard->sales_order->so4))?></td> 
					<td>
								<div class="btn-group">
									<button id="btnGroupVerticalDrop5" type="button" class="btn  btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Items <i class="fa fa-angle-down"></i></button>
										<ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop5">
										<?php  foreach($jobCard->job_card_rows as $job_card_rows){ ?>
											<li><p><?= h($job_card_rows->item->name) ?></p></li>
											<?php }?>
										</ul>
								</div>
							</td>
 					<td><?= date("d-m-Y",strtotime($jobCard->required_date));?></td>
					<td><?= date("d-m-Y",strtotime($jobCard->created_on));?></td>
					<td class="actions">
					
					<?php if(in_array(24,$allowed_pages)){ ?>
					<?php echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'view', $jobCard->id],array('escape'=>false,'class'=>'btn btn-xs yellow tooltips','target'=>'blank','data-original-title'=>'View')); ?>
					<?php } ?>
					<?php if(in_array(6,$allowed_pages)){  ?>
					<?php
					if(!in_array(date("m-Y",strtotime($jobCard->created_on)),$closed_month))
					{ 
					echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'edit', $jobCard->id],array('escape'=>false,'class'=>'btn btn-xs blue tooltips','data-original-title'=>'Edit')); ?>
				<?php } }  ?>
				<?php if(in_array(34,$allowed_pages)){ ?>
					<?php
						echo $this->Html->link('<i class="fa fa-minus-circle"></i> ',['action' => '#'],array('escape'=>false,'class'=>'btn btn-xs red tooltips close_btn','data-original-title'=>'Close','role'=>'button','jobcard_id'=>$jobCard_id));

					?>
				<?php } ?>
					<!-- // if(in_array(34,$allowed_pages)) { 

					 //if($status==null or $status=='Pending'){ 
					 //$this->Form->postLink('Close',
						//['action' => 'close', $jobCard->id], 
						//[
						//	'escape' => false,
						//	'class'=>'btn btn-xs red tooltips','data-original-title'=>'Close',
//'confirm' => __('Are you sure ?')
					//	]
					//) 
					// } }  -->
					<?php } ?>
					</td>
				</tr>
		    <?php }  else if($jobCardStatus=='Closed'){
					if((($so == $in) && ($so == $iv) && ($so == $iv) )  || $jobCard->status=="Closed"){
			?>
			<tr>
					<td><?= h(++$page_no) ?></td>
					<td><?= h(($jobCard->jc1.'/JC-'.str_pad($jobCard->jc2, 3, '0', STR_PAD_LEFT).'/'.$jobCard->jc3.'/'.$jobCard->jc4))?></td>
					<td><?= h(($jobCard->sales_order->so1.'/SO-'.str_pad($jobCard->sales_order->so2, 3, '0', STR_PAD_LEFT).'/'.$jobCard->sales_order->so3.'/'.$jobCard->sales_order->so4))?></td> 
					<td>
								<div class="btn-group">
									<button id="btnGroupVerticalDrop5" type="button" class="btn  btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Items <i class="fa fa-angle-down"></i></button>
										<ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop5">
										<?php  foreach($jobCard->job_card_rows as $job_card_rows){ ?>
											<li><p><?= h($job_card_rows->item->name) ?></p></li>
											<?php }?>
										</ul>
								</div>
							</td>
 					<td><?= date("d-m-Y",strtotime($jobCard->required_date));?></td>
					<td><?= date("d-m-Y",strtotime($jobCard->created_on));?></td>
					<td class="actions">
				
					<?php if(in_array(24,$allowed_pages)){ ?>
					<?php echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'view', $jobCard->id],array('escape'=>false,'class'=>'btn btn-xs yellow tooltips','target'=>'_blank','data-original-title'=>'View')); ?>
					<?php }  ?>
					<!--if(in_array(34,$allowed_pages)) {

					 if($status==null or $status=='Pending'){ 
					$this->Form->postLink('Close',
						['action' => 'close', $jobCard->id], 
						[
							'escape' => false,
							'class'=>'btn btn-xs red tooltips','data-original-title'=>'Close',
							'confirm' => __('Are you sure ?')
						]
					) ?>
					} } ?>-->
					</td>
				</tr>
			
			<?php } }  endforeach; ?>
			 </tbody>
			</table>
		</div>
	</div>
</div>

</div>
</div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<?php echo $this->Html->css('/drag_drop/jquery-ui.css'); ?>
<?php echo $this->Html->script('/drag_drop/jquery-1.12.4.js'); ?>
<?php echo $this->Html->script('/drag_drop/jquery-ui.js'); ?>
<script>
$(document).ready(function() {
	$('.close_btn').die().live("click",function() {
		var jobcard_id=$(this).attr('jobcard_id');
		var addr=$(this).text();
		$("#myModal2").show();
		$("#close_popup_btn").attr('jobcard_id',jobcard_id);
    });
	
	$('#close_popup_btn').die().live("click",function() {
		var jobcard_id=$(this).attr('jobcard_id');
		var jobcardreason=$('.jobcardreason').val();
		
		if((jobcard_id) && (jobcardreason)){
			var url="<?php echo $this->Url->build(['controller'=>'JobCards','action'=>'CloseJobCards']); 
			?>";
			url=url+'/'+jobcard_id+'/'+jobcardreason,
			
			$.ajax({
				url: url,
			}).done(function(response) {
				
				location.reload();
			});
			
		}else{
			alert("Please Select Atleast one reason For Close Quotaion");		
		}		
    });
	
	$('.closebtn2').on("click",function() { 
		$("#myModal2").hide();
    });
	
	});
</script>	
<div id="myModal2" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="false" style="display: none; padding-right: 12px;"><div class="modal-backdrop fade in" ></div>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body" id="result_ajax">
			<h4>Closing Reason</h4>
				<div class="row">
					<div class="col-md-12">
						<textarea cols="30" rows="5" name="reason" class="form-control jobcardreason"></textarea>
					</div>
				</div>
				
			</div>
			<div class="modal-footer">
				<button class="btn default closebtn2">Close</button>
				<button class="btn red close_rsn" id="close_popup_btn">Close JobCards</button>
			</div>
		</div>
	</div>
 
 
<style>
.help-block-error{
	font-size: 10px;
}

.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
	vertical-align: top !important;
}
</style>
<?php 	$first="01";
		$last="31";
		$start_date=$first.'-'.$financial_month_first->month;
		$end_date=$last.'-'.$financial_month_last->month;
		$start_date=strtotime(date("Y-m-d",strtotime($start_date)));
		$transaction_date=strtotime($inventoryTransferVouchers->transaction_date);
if($transaction_date <  $start_date ) {
	echo "Financial Month has been Closed";
} else { ?>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption" >
			<i class="icon-globe font-blue-steel"></i>
				<span class="caption-subject font-blue-steel uppercase">Inventory Transfer Voucher In</span>
		</div>
	
	</div>
	<div class="portlet-body form">
	<?= $this->Form->create($inventoryTransferVouchers,['id'=>'form_sample_3']) ?>
	<?php 	$first="01";
				$last="31";
				$start_date=$first.'-'.$financial_month_first->month;
				$end_date=$last.'-'.$financial_month_last->month;
				//pr($start_date); exit;
		?>
	<div class="row">
		<div class="form-group">
			<div class="col-md-3">
				<?php echo $this->Form->radio(
									'cust_supplier_mode',
									[
										['value' => 'customer', 'text' => 'Customers','checked'],
										['value' => 'supplier', 'text' => 'Supplier']
									]
								); ?>
			</div>
			<div id="customerData">
				<div class="col-md-3">
					<label>Customers</label>
					<?php
						$options=array();
								foreach($customers as $customer){
									if(empty($customer->alias)){
										$merge=$customer->customer_name;
									}else{
										$merge=$customer->customer_name.'	('.$customer->alias.')';
									}
									
									$options[]=['text' =>$merge, 'value' => $customer->id];
								}

					?>
					<?php echo $this->Form->input('customer_id',['empty'=>'---Select Customer ---','options'=>$options,'class'=>'form-control select2me customer_id','label'=>false]); ?>
				</div>
				<div class="col-md-3">
					<label>File No.</label>
					<div  id="so3_div">
						<?php echo $this->Form->input('file_no', ['empty'=>'--- Select ---','label' => false,'class' => 'form-control  file_no','id'=>'file_no']); ?>
					</div>
				</div>
			</div>
			<div class="col-md-3" id="supplierData">
				<label>Supplier</label>
				<?php
					foreach($vendor as $vendors){
								if(empty($vendors->alias)){
									$merge1=$vendors->company_name;
								}else{
									$merge1=$vendors->company_name.'('.$vendors->alias.')';
								}
								
								$options1[]=['text' =>$merge1, 'value' => $vendors->id];

							}
							echo $this->Form->input('vendor_id', ['empty' => "--Select--",'label' => false,'options' => $options1,'class' => 'form-control select2me vendor_id','value' => @$vendor->id]); ?>

			</div>
			<div class="col-md-3">
				<label>Transaction Date</label>
				<?php echo $this->Form->input('transaction_date', ['label' => false,'class' => 'form-control  date-picker','data-date-format'=>'dd-mm-yyyy','placeholder'=>'dd-mm-yyyy','type' => 'text','value'=>date("d-m-Y",strtotime($inventoryTransferVouchers->transaction_date)),'data-date-start-date' => $start_date,'data-date-end-date' => $end_date]); ?>
			</div>
		</div>	
	</div><br/>	
	
		<div class="row">
		<div class="col-md-12">
			<h5>For In -</h5>

				<table id="main_table_1" width="100%"  class="table table-condensed">
					<thead>
						<tr>
							<th>Item</th>
							<th >Quantity</th>
							<th >Serial Number</th>
							<th >Rate</th>
							<th>Narration</th>
							<th></th>
						</tr>
					</thead>
					<tbody id="maintbody_1">
						<tr>
								<td><a class="btn btn-xs btn-default addrow_1" href="#" role='button'>Add row</a></td>
							</tr>
						<?php $options1= [];	foreach($inventoryTransferVouchers->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row_in){ 
						?>
							<tr class="main">
								<td  width="25%">
									<?php echo $inventory_transfer_voucher_row_in->item->name;
									echo $this->Form->input('q', ['type'=>'hidden','readonly','value'=>$inventory_transfer_voucher_row_in->item->id,'label' => false,'item_sr'=>$inventory_transfer_voucher_row_in->item->item_companies[0]->serial_number_enable,'class' => 'form-control input-sm  ']); ?>
								</td>
								<td  width="10%"> 
									<?php echo $this->Form->input('q', ['type' => 'text','label' => false,'value'=>$inventory_transfer_voucher_row_in->quantity,'class' => 'form-control input-sm qty_bx_in','placeholder' => 'Quantity','old_qty'=>$inventory_transfer_voucher_row_in->quantity]); ?>
									
									<?php echo $this->Form->input('q', ['type' => 'hidden','label' => false,'class' => 'form-control input-sm status','placeholder' => 'Quantity','value'=>'In']); ?>
								</td>
								
								<td width="30%">
									<div class="row" style="padding-left:2%">
										
									<?php 
									//pr($inventory_transfer_voucher_row_in); exit;
									$i=1; $p=0; foreach($inventory_transfer_voucher_row_in->item->serial_numbers as $item_serial_number){
										if($item_serial_number->itv_row_id == $inventory_transfer_voucher_row_in->id 
										&& $item_serial_number->status=='In'){ ?>
											<?php echo $this->Form->input('q', ['label' => false,'type'=>'text','style'=>'width: 120px;','value' => $item_serial_number->name]); ?>
										<?php 
										if(@$parentSerialNo[$item_serial_number->id]!=$item_serial_number->id){ $p++; ?>
											<?=  $this->Html->link('<i class="fa fa-trash"></i> ',
														['action' => 'DeleteSerialNumberIn', $item_serial_number->id, $inventory_transfer_voucher_row_in->id,$inventory_transfer_voucher_row_in->inventory_transfer_voucher_id,$inventory_transfer_voucher_row_in->item_id], 
														[
															'escape' => false,
															'class' => 'btn btn-xs red',
															'confirm' => __('Are you sure, you want to delete {0}?', $item_serial_number->id)
														]
													) ?>
												<?php } ?>
										<?php echo "<br>"; $i++; } }  ?>
										<div class="col-md-6 offset sr_container"></div>				
									</div>
									
								</td>
								<td width="10%">
									<?php echo $this->Form->input('amount', ['type' => 'text','label' => false,'value'=>$inventory_transfer_voucher_row_in->amount,'class' => 'form-control input-sm amount','placeholder' => 'Rate']); ?>
									
									<?php echo $this->Form->input('q', ['type' => 'hidden','label' => false,'class' => 'form-control input-sm itvrowid','placeholder' => 'Quantity','value'=>$inventory_transfer_voucher_row_in->id]); ?>
								</td>
								<td width="30%">
									<?php echo $this->Form->input('amount', ['type' => 'textarea','label' => false,'value'=>$inventory_transfer_voucher_row_in->narration,'class' => 'form-control input-sm ','placeholder' => 'Narration']); ?>
								</td>
								<td>
								<?php if($inventory_transfer_voucher_row_in->item->item_companies[0]->serial_number_enable != 1 || $inventory_transfer_voucher_row_in->quantity==$p){ ?>
								<?= $this->Html->link('<i class="fa fa-trash"></i> ',
														['action' => 'DeleteRow', $inventory_transfer_voucher_row_in->id,$inventory_transfer_voucher_row_in->inventory_transfer_voucher_id,$inventory_transfer_voucher_row_in->item_id], 
														[
															'escape' => false,
															'class' => 'btn btn-xs red',
															'confirm' => __('Are you sure, you want to delete {0}?', $inventory_transfer_voucher_row_in->item->name)
														]
													) ?>
								<?php } ?>
								</td>
							</tr>
						<?php }?>
						</tbody>
					
				</table>
			</div>
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
<?= $this->Form->end() ?>		
	</div>
</div>	
<?php } ?>

<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>

<script>
$(document).ready(function() {
	jQuery.validator.addMethod("notEqualToGroup", function (value, element, options) {
		// get all the elements passed here with the same class
		var elems = $(element).parents('form').find(options[0]);
		// the value of the current element
		var valueToCompare = value;
		// count
		var matchesFound = 0;
		// loop each element and compare its value with the current value
		// and increase the count every time we find one
		jQuery.each(elems, function () {
			thisVal = $(this).val();
			if (thisVal == valueToCompare) {
				matchesFound++;
			}
		});
		// count should be either 0 or 1 max
		if (this.optional(element) || matchesFound <= 1) {
			//elems.removeClass('error');
			return true;
		} else {
			//elems.addClass('error');
		}
	}, jQuery.format(""))
	//--------- FORM VALIDATION
	var form3 = $('#form_sample_3');
	var error3 = $('.alert-danger', form3);
	var success3 = $('.alert-success', form3);
	form3.validate({
		errorElement: 'span', //default input error message container
		errorClass: 'help-block help-block-error', // default input error message class
		focusInvalid: true, // do not focus the last invalid input
		rules: {
				transaction_date:{
					required: true,
				},
				item_id :{
							required: true,
						  },
				quantity :{
							required: true,
						  }
			},

		messages: { // custom messages for radio buttons and checkboxes
			
		},

		errorPlacement: function (error, element) { // render error placement for each input type
			if (element.parent(".input-group").size() > 0) {
				error.insertAfter(element.parent(".input-group"));
			} else if (element.attr("data-error-container")) { 
				error.appendTo(element.attr("data-error-container"));
			} else if (element.parents('.radio-list').size() > 0) { 
				error.appendTo(element.parents('.radio-list').attr("data-error-container"));
			} else if (element.parents('.radio-inline').size() > 0) { 
				error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
			} else if (element.parents('.checkbox-list').size() > 0) {
				error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
			} else if (element.parents('.checkbox-inline').size() > 0) { 
				error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
			} else {
				error.insertAfter(element); // for other inputs, just perform default behavior
			}
		},

		invalidHandler: function (event, validator) { //display error alert on form submit   
			success3.hide();
			error3.show();
			
			//Metronic.scrollTo(error3, -200);
		},

		highlight: function (element) { // hightlight error inputs
		   $(element)
				.closest('.form-group').addClass('has-error'); // set error class to the control group
		},

		unhighlight: function (element) { // revert the change done by hightlight
			$(element)
				.closest('.form-group').removeClass('has-error'); // set error class to the control group
		},

		success: function (label) {
			label
				.closest('.form-group').removeClass('has-error'); // set success class to the control group
		},

		submitHandler: function (form) {
			validate_serial();
			success3.show();
			error3.hide();
			form[0].submit(); // submit the form
		}

	});
	
	
	//add_row_in();
	

	$('.addrow_1').die().live("click",function() { 
		add_row_in();
	});


	$('.deleterow_1').live("click",function() {
		var l=$(this).closest("table tbody").find("tr").length;
		if (confirm("Are you sure to remove row ?") == true) {
			if(l>1){
				var row_no=$(this).closest("tr").attr("row_no");
				var del=$(this).closest("tr");
				$(del).remove();
				rename_rows_in();
			}
		} 
	});
	
	function add_row_in(){
		var tr2=$("#sampletable_1 tbody tr").clone();
		$("#main_table_1 tbody#maintbody_1").append(tr2);
		rename_rows_in();
		
	}

	
	$('.select_item_in').die().live("change",function() { 
		var tr_obj=$(this).closest('tr');
		sr_nos(tr_obj);
		rename_rows_in();
	});
	
	$('.qty_bx_in').die().live("blur",function() { 
		var tr_obj=$(this).closest('tr');  
		
			var len=tr_obj.find("td:nth-child(1) select").length;
			if(len>0){
				var serial_number_enable=tr_obj.find('td:nth-child(1) select option:selected').attr('serial_number_enable');
				var item_id=tr_obj.find('td:nth-child(1) select option:selected').val();
				var old_qty=0;
			}else{
				var item_id=tr_obj.find('td:nth-child(1) input').val()
				var serial_number_enable=tr_obj.find('td:nth-child(1) input').attr('item_sr');
				var old_qty=tr_obj.find('td:nth-child(2) input').attr('old_qty');
				tr_obj.find('td:nth-child(2) input').attr('min',old_qty);
			}
		
		if(item_id > 0){ 
		if(serial_number_enable == '1'){
				var quantity=tr_obj.find('td:nth-child(2) input').val();
				 if(quantity.search(/[^0-9]/) != -1)
					{
						alert("Item serial number is enabled !!! Please Enter Only Digits")
						tr_obj.find('td:nth-child(2) input').val("");
					}
				sr_nos(tr_obj,serial_number_enable,old_qty);
			}
		
		}
    });
	
	function sr_nos(tr_obj,serial_number_enable,old_qty){  
		if(serial_number_enable==1){ 
			var OriginalQty=tr_obj.find('td:nth-child(2) input').val();
			Quantity = OriginalQty.split('.'); qty=Quantity[0];
			var row_no=tr_obj.attr('row_no');
			tr_obj.find('td:nth-child(3) div.sr_container').html('');
			for(var w=1; w<=qty-old_qty; w++){ 
				tr_obj.find('td:nth-child(3) div.sr_container').append('<input type="text" name="inventory_transfer_voucher_rows['+row_no+'][sr_no]['+w+']" id="inventory_transfer_voucher_rows-in-'+row_no+'-sr_no-'+w+'" required="required" placeholder="serial number '+w+'" class="sr_no" style=" width: 120px;" />');
			}
			rename_input();
		}else{
			tr_obj.find('td:nth-child(3) div.sr_container').html('');
		}
		
	}
	
	//rename_input();
	
	function rename_input()
	{
		var q=0;
		$("#main_table_1 tbody#maintbody_1 tr.main").each(function(){
			var row_no = $(this).attr('row_no');
				var i=0;
				$(this).find("td:nth-child(3) .sr_no").each(function()
				{
					$(this).attr({name:"inventory_transfer_voucher_rows["+q+"][sr_no]["+i+"]"}).rules("add", "required");
					i++;
				});
				q++;
			});
	}
	
rename_rows_in();
	function rename_rows_in(){ 
		var j=0;
		$("#main_table_1 tbody#maintbody_1 tr.main").each(function(){
			
			$(this).attr('row_no',j);
			var len=$(this).find("td:nth-child(1) select").length;
			if(len>0){
			$(this).find("td:nth-child(1) select").select2().attr({name:"inventory_transfer_voucher_rows["+j+"][item_id]", id:"inventory_transfer_voucher_rows-"+j+"-item_id"}).rules("add",
					{ 
						required: true
					});
			}else{
				$(this).find('td:nth-child(1) input').attr({name:"inventory_transfer_voucher_rows["+j+"][item_id]", id:"inventory_transfer_voucher_rows-"+j+"-item_id"}).rules("add", "required");
			}
			
			$(this).find('td:nth-child(2) input').attr({name:"inventory_transfer_voucher_rows["+j+"][quantity]", id:"inventory_transfer_voucher_rows-"+j+"-quantity", row:j}).rules("add", "required");
		
			$(this).find('td:nth-child(2) input.status').attr({name:"inventory_transfer_voucher_rows["+j+"][status]", id:"inventory_transfer_voucher_rows-"+j+"-status", row:j});
			
			$(this).find('td:nth-child(4) input.amount').attr({name:"inventory_transfer_voucher_rows["+j+"][amount]", id:"inventory_transfer_voucher_rows-"+j+"-amount"}).rules("add", "required");
			
			$(this).find('td:nth-child(4) input.itvrowid').attr({name:"inventory_transfer_voucher_rows["+j+"][id]", id:"inventory_transfer_voucher_rows-"+j+"-id"});
			
			$(this).find('td:nth-child(5) textarea').attr({name:"inventory_transfer_voucher_rows["+j+"][narration]", id:"inventory_transfer_voucher_rows-"+j+"-narration"}).rules("add", "required");
			j++; 
	   });
	}	

	rename_input();
	$('.qty_bx').die().live("keyup",function() {
		validate_serial();
    });
	
	function validate_serial(){
		$("#main_table tbody#maintbody tr.main").each(function(){
			var OriginalQty=$(this).find('td:nth-child(2) input').val();
			Quantity = OriginalQty.split('.'); qty=Quantity[0];
			if($(this).find('td:nth-child(3) select').length>0){
				$(this).find('td:nth-child(3) select').attr('test',qty).rules('add', {
							required: true,
							minlength: qty,
							maxlength: qty,
							messages: {
								maxlength: "select serial number equal to quantity.",
								minlength: "select serial number equal to quantity."
							}
					});
			}
		});	
	}
	
	$('input[type=radio]').die().live("click",function() { 
		var selectedValue = $('input[type=radio]:checked').val();
		if(selectedValue == "supplier"){
			$('#supplierData').show();
			$('#customerData').hide();
			$('.vendor_id').attr('required','required');
			$('.file_no').removeAttr('required');
			$('.customer_id ').removeAttr('required');
			$('.file_no').select2('data',null);
			$('.customer_id').select2('data',null);
		}else if(selectedValue == "customer"){
			$('#supplierData').hide();
			$('#customerData').show();
			$('.customer_id').attr('required','required');
			$('.file_no').attr('required','required');
			$('.vendor_id ').removeAttr('required');
			$('.vendor_id').select2('data',null);
		}
	});
	var selectedValue = $('input[type=radio]:checked').val();
		if(selectedValue == "supplier"){
			$('#supplierData').show();
			$('#customerData').hide();
			$('.vendor_id').attr('required','required');
			$('.file_no').removeAttr('required');
			$('.customer_id ').removeAttr('required');
			
		}else if(selectedValue == "customer"){
			$('#supplierData').hide();
			$('#customerData').show();
			$('.customer_id').attr('required','required');
			$('.file_no').attr('required','required');
			$('.vendor_id ').removeAttr('required');
		}
	
	$('select[name="customer_id"]').on("change",function() {
		var customer_id=$('select[name="customer_id"] option:selected').val();
		$("#so3_div").html('Loading...');
		var url="<?php echo $this->Url->build(['controller'=>'Filenames','action'=>'listFilename']); ?>";
		url=url+'/'+customer_id+'/so',
		$.ajax({
			url: url,
		}).done(function(response) {
			$("#so3_div").html(response);
			$('select[name="qt3"]').attr('name','so3').select2();
		});
	});
});

	
	
</script>	

<table id="sampletable_1" style="display:none;">
	<tbody>
		<tr class="main">
			<td width="25%">
				<?php 
				$item_option=[];
				foreach($display_items as $Item){  
					if(sizeof($Item->item_companies) > 0 ){
						$item_option[]=['text' =>$Item->name, 'value' => $Item->id, 'serial_number_enable' => (int)@$Item->item_companies[0]->serial_number_enable];
					}
				}
				echo $this->Form->input('q', ['empty'=>'Select','options' => $item_option,'label' => false,'style'=>'width: 100%; display: block;','class' => 'form-control input-sm select_item_in item_id']); ?>
			</td>
			<td width="10%"> 
				<?php echo $this->Form->input('q', ['type' => 'text','label' => false,'class' => 'form-control input-sm qty_bx_in','placeholder' => 'Quantity']); ?>
			</td>
			<td width="20%" ><div class="sr_container"></div></td>
			<td width="10%">
				<?php echo $this->Form->input('amount', ['type' => 'text','label' => false,'class' => 'form-control input-sm amount','placeholder' => 'Rate']); ?>
			</td>
			<td width="30%">
				<?php echo $this->Form->input('narration', ['type' => 'textarea','label' => false,'class' => 'form-control input-sm ','placeholder' => 'Narration']); ?>
			</td>
			<td width="10%"><a class="btn btn-xs btn-default addrow_1" href="#" role='button'><i class="fa fa-plus"></i></a><a class="btn btn-xs btn-default deleterow_1" href="#" role='button'><i class="fa fa-times"></i></a></td>
		</tr>
	</tbody>
</table>
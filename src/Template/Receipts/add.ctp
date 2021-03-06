<style>
table > thead > tr > th, table > tbody > tr > th, table > tfoot > tr > th, table > thead > tr > td, table > tbody > tr > td, table > tfoot > tr > td{
	vertical-align: top !important;
	border-bottom:solid 1px #CCC;
}
.page-content-wrapper .page-content {
    padding: 5px;
}
.portlet.light {
    padding: 4px 10px 4px 10px;
}
.help-block-error{
	font-size: 10px;
}
</style>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption" >
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Add Receipt Voucher</span>
		</div>
	</div>
	<div class="portlet-body form">
    <?= $this->Form->create($receipt,['id'=>'form_sample_3']) ?>
	<?php 	$first="01";
				$last="31";
				$start_date=$first.'-'.$financial_month_first->month;
				$end_date=$last.'-'.$financial_month_last->month;
				$default_date='';
				$cur_date=date('Y-m-d');
				$start_date1=date('Y-m-d',strtotime($start_date));
				$end_date1=date('Y-m-d',strtotime($end_date));
				if($start_date1 <= $cur_date && $end_date1 >= $cur_date){
					$default_date=date('d-m-Y');
				}else{
					$default_date=date('d-m-Y',strtotime($end_date));
				}
		if($st_company_id == 25){
			$slected_bank=145;
		}else{
			$slected_bank='';
		}	
		?>
        <div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Transaction Date<span class="required" aria-required="true">*</span></label>
					<?php echo $this->Form->input('transaction_date', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','value' => $default_date,'data-date-start-date'=>date("d-m-Y",strtotime($start_date)) ,'data-date-end-date' => date("d-m-Y",strtotime($end_date)),'required']); ?>
				</div>

					<span style="color: red;">
						<?php if($chkdate == 'Not Found'){  ?>
							You are not in Current Financial Year
						<?php } ?>
					</span>				

			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Bank/Cash Account<span class="required" aria-required="true">*</span></label>
					<?php echo $this->Form->input('bank_cash_id', ['empty'=>'--Select-','label' => false,'class' => 'form-control input-sm select2me','value'=>$slected_bank]); ?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Mode of Payment<span class="required" aria-required="true">*</span></label>
					<div class="radio-list">
						<div class="radio-inline" >
						<?php echo $this->Form->radio(
							'payment_mode',
							[
								['value' => 'Cheque', 'text' => 'Cheque','checked'],
								['value' => 'Cash', 'text' => 'Cash'],
								['value' => 'NEFT/RTGS', 'text' => 'NEFT/RTGS']
							]
						); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group" id="chq_no">
					<label class="control-label">Cheque No<span class="required" aria-required="true">*</span></label>
					<?php echo $this->Form->input('cheque_no', ['label' => false,'class' => 'form-control input-sm','placeholder'=>'Cheque No']); ?>
				</div>
			</div>
		</div>
		
		<div style="overflow: auto;">
		<table width="100%" id="main_table">
			<thead>
				<th width="25%"><label class="control-label">Received From</label></th>
				<th width="20%"><label class="control-label">Amount</label></th>
				<th></th>
				<th width="3%"></th>
			</thead>
			<tbody id="main_tbody">
			
			</tbody>
			<tfoot>
				<td><a class="btn btn-xs btn-default addrow" href="#" role="button"><i class="fa fa-plus"></i> Add row</a></td>
				<td id="receipt_amount" style="font-size: 14px;font-weight: bold;"></td>
				<td><?php echo $this->Form->input('narration', ['type'=>'textarea','label' => false,'class' => 'form-control input-sm','placeholder'=>'Narration','required']); ?></td>
				<td style="vertical-align: bottom !important;"></td>
				<td></td>
			</tfoot>
		</table>
				<?php if($chkdate == 'Not Found'){  ?>
					<label class="btn btn-danger"> You are not in Current Financial Year </label>
				<?php } else { ?>
					<button id='submitbtn' type="submit" class="btn btn-primary" >CREATE RECEIPT</button>
				<?php } ?>	
		
		</div>
		
    
    <?= $this->Form->end() ?>
	</div>
</div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>

<script>
$(document).ready(function() {
	//--------- FORM VALIDATION
	var form3 = $('#form_sample_3');
	var error3 = $('.alert-danger', form3);
	var success3 = $('.alert-success', form3);
	form3.validate({
		errorElement: 'span', //default input error message container
		errorClass: 'help-block help-block-error', // default input error message class
		focusInvalid: true, // do not focus the last invalid input
		rules: {
				bank_cash_id:{
					required: true,
				},
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
			
			$('#submitbtn').prop('disabled', true);
			$('#submitbtn').text('Submitting.....');
			success3.show();
			error3.hide();
			form[0].submit(); // submit the form
		}

	});
	//--	 END OF VALIDATION
	
	$('input[name="payment_mode"]').die().live("click",function() {
		var payment_mode=$(this).val();
		
		if(payment_mode=="Cheque"){
			$("#chq_no").show();
		}else{
			$("#chq_no").hide();
		}
	});
	
	add_row();
	function add_row(){
		var tr=$("#sample_table tbody tr").clone();
		$("#main_table tbody#main_tbody").append(tr);
		rename_rows();
	}
	
	function rename_rows(){
		var i=0;
		$("#main_table tbody#main_tbody tr.main_tr").each(function(){
			$(this).find("td:eq(0) select.received_from").select2().attr({name:"receipt_rows["+i+"][received_from_id]", id:"receipt_rows-"+i+"-received_from_id"}).rules('add', {
						required: true
					});
			$(this).find("td:eq(0) .row_id").val(i);
			$(this).find("td:eq(1) input").attr({name:"receipt_rows["+i+"][amount]", id:"receipt_rows-"+i+"-amount"}).rules('add', {
						required: true,
						min: 0.01
					});
			$(this).find("td:eq(1) select").attr({name:"receipt_rows["+i+"][cr_dr]", id:"receipt_rows-"+i+"-cr_dr"});
			i++;
		});
	}
	
	$('.addrow').live("click",function() {
		add_row();
	});
	$('.deleterow').live("click",function() {
		$(this).closest("tr").remove();
		do_mian_amount_total();
		do_ref_total();
	});
	
	$('.addrefrow').live("click",function() {
		var sel=$(this).closest('tr.main_tr');
		var received_from_id=$(this).closest('tr.main_tr').find('td:nth-child(1) select').val();
		add_ref_row(sel,received_from_id);
	});
	
	function add_ref_row(sel,received_from_id){
		var tr=$("#sample_ref table.ref_table tbody tr").clone();
		sel.find("table.ref_table tbody").append(tr);
		
		rename_ref_rows(sel,received_from_id);
		
	}
	
	function rename_ref_rows(sel,received_from_id){
		var i=0;
		var row_id=0;
		$(sel).find("table.ref_table tbody tr").each(function(){
			
			row_id=$(this).closest('tr.main_tr').find('td:eq(0) .row_id').val();
			
			$(this).find("td:nth-child(1) select").attr({name:"receipt_rows["+row_id+"][ref_rows]["+i+"][ref_type]", id:"ref_rows-"+row_id+"-"+i+"-ref_type"}).rules("add", "required");
			var is_select=$(this).find("td:nth-child(2) select").length;
			var is_input=$(this).find("td:nth-child(2) input").length;
			
			if(is_select){
				$(this).find("td:nth-child(2) select").attr({name:"receipt_rows["+row_id+"][ref_rows]["+i+"][ref_no]", id:"ref_rows-"+row_id+"-"+i+"-ref_no"}).rules("add", "required");
			}else if(is_input){
				$(this).find("td:nth-child(2) input").attr({name:"receipt_rows["+row_id+"][ref_rows]["+i+"][ref_no]", id:"ref_rows-"+row_id+"-"+i+"-ref_no", class:"form-control input-sm ref_number-"+row_id}).rules("add", "required");
			}
			
			$(this).find("td:nth-child(3) input").attr({name:"receipt_rows["+row_id+"][ref_rows]["+i+"][ref_amount]", id:"ref_rows-"+row_id+"-"+i+"-ref_amount"}).rules("add", "required");
			
			$(this).find("td:nth-child(4) select").attr({name:"receipt_rows["+row_id+"][ref_rows]["+i+"][ref_cr_dr]", id:"ref_rows-"+row_id+"-"+i+"-ref_cr_dr"}).rules("add", "required");
			
			i++;
		});
		
		$(sel).find("table.ref_table tfoot tr:nth-child(1) td:nth-child(3) input").attr({name:"receipt_rows["+row_id+"][on_acc]", id:"ref_rows-"+row_id+"-"+i+"-ref_cr_dr"}).rules("add", "required");
		
		$(sel).find("table.ref_table tfoot tr:nth-child(1) .on_account_dr_cr").attr({name:"receipt_rows["+row_id+"][on_acc_dr_cr]", id:"ref_rows-"+row_id+"-"+i+"-ref_cr_dr"}).rules("add", "required");
		///var a=$(this).find("table.ref_table tfoot tr:nth-child(1) td:nth-child(3) input").val();
		
		
	}
	
	$('.deleterefrow').live("click",function() {
		var l=$(this).closest("table.ref_table tbody").find("tr").length;
			if(l>1){
				$(this).closest("tr").remove();
			}
		do_ref_total();
	});
	
	$('.received_from').live("change",function() {
		var sel=$(this);
		load_ref_section(sel);
	});
	
	/* $('.cr_dr').live("change",function() {
		var sel=$(this);
		load_ref_section(sel);
		do_mian_amount_total();
	});
	 */
	function load_ref_section(sel){
		$(sel).closest("tr.main_tr").find("td:nth-child(3)").html("Loading...");
		var sel2=$(sel).closest('tr.main_tr');
		var received_from_id=$(sel).closest("tr.main_tr").find("td:nth-child(1) select").find('option:selected').val();
		var url="<?php echo $this->Url->build(['controller'=>'LedgerAccounts','action'=>'checkBillToBillAccountingStatus']); ?>";
		url=url+'/'+received_from_id,
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'text'
		}).done(function(response) {
			if(response.trim()=="Yes"){
				var ref_table=$("#sample_ref div.ref").clone();
				$(sel).closest("tr").find("td:nth-child(3)").html(ref_table);
			}else{
				$(sel).closest("tr").find("td:nth-child(3)").html("");
			}
			rename_ref_rows(sel2,received_from_id);
		});
	}
	
	$('.ref_type').live("change",function() {
		var current_obj=$(this);
		
		var sel3=$(this).closest('tr.main_tr');
		var cr_dr=$(this).closest('tr.main_tr').find('td:nth-child(2) select').val();
		var ref_type=$(this).find('option:selected').val();
		var received_from_id=$(this).closest('tr.main_tr').find('td select:eq(0)').val();
		if(ref_type=="Against Reference"){
			var url="<?php echo $this->Url->build(['controller'=>'ReferenceDetails','action'=>'listRef']); ?>";
			url=url+'/'+received_from_id,
			$.ajax({
				url: url,
				type: 'GET',
			}).done(function(response) { 
				current_obj.closest('tr').find('td:eq(1)').html(response);
				rename_ref_rows(sel3,received_from_id);
			});
		}else if(ref_type=="New Reference" || ref_type=="Advance Reference"){
			current_obj.closest('tr').find('td:eq(1)').html('<input type="text" class="form-control input-sm" placeholder="Ref No." >');
			rename_ref_rows(sel3,received_from_id);
		}else{
			current_obj.closest('tr').find('td:eq(1)').html('');
		}
	});
	
	$('.ref_list').live("change",function() {
		var current_obj=$(this);
		var due_amount=$(this).find('option:selected').attr('amt');
		$(this).closest('tr').find('td:eq(2) input').val(due_amount);
		do_ref_total();
	});
	
	$('.ref_list').live("change",function() {
		do_ref_total();
	});
	$('.ref_amount_textbox').live("keyup",function() {
		do_ref_total();
	});
	
	$('.cr_dr').live("change",function() { 
		do_ref_total();
		do_mian_amount_total();
	});
	
	$('.drcrChange').live("change",function() { 
		do_ref_total();
	});
	
	
	
	function do_ref_total(){  
		$("#main_table tbody#main_tbody tr.main_tr").each(function(){
			var main_amount=$(this).find('td:nth-child(2) input').val();
			var total_ref_cr=0;
			var total_ref_dr=0;
			
			$(this).find("table.ref_table tbody tr").each(function(){
				var am=parseFloat($(this).find('td:nth-child(3) input').val());
				var cr_dr=$(this).find('td:nth-child(4) select').val();
				//alert(cr_dr);
				if(!am){ am=0; }
				if(cr_dr=="Dr"){
					total_ref_dr=total_ref_dr+am;
				}else{
					total_ref_cr=total_ref_cr+am;
				}
			});
			
			
			var main_dr_cr=$(this).closest("#main_table tbody#main_tbody tr.main_tr").find('.cr_dr').val();
			var onAcc_dr_cr="";
			var onAcc=0;
			var afterCal=0;
			if(main_dr_cr=="Dr"){
				var main_amt=parseFloat($(this).closest("#main_table tbody#main_tbody tr.main_tr").find('td:nth-child(2) input').val());
				if(total_ref_dr > total_ref_cr){
					afterCal=total_ref_dr-total_ref_cr;
					onAcc=main_amt-afterCal;
					onAcc_dr_cr="Dr";
				}else if(total_ref_dr < total_ref_cr){
					afterCal=total_ref_dr-total_ref_cr;
					 onAcc=main_amt-afterCal;
					onAcc_dr_cr="Dr";
				}else{
					onAcc=main_amt;
					onAcc_dr_cr="Dr";
				}
				if(onAcc>=0){
					onAcc=Math.abs(onAcc);
					onAcc=round(onAcc,2);
					$(this).find("table.ref_table tfoot tr:nth-child(1) td:nth-child(3) input").val(onAcc);
					$(this).find("table.ref_table tfoot tr:nth-child(1) .on_account_dr_cr").val(onAcc_dr_cr);
				}else{
				//	onAcc=Math.abs(onAcc);
					onAcc=round(onAcc,2);
					$(this).find("table.ref_table tfoot tr:nth-child(1) td:nth-child(3) input").val(Math.abs(onAcc));
					$(this).find("table.ref_table tfoot tr:nth-child(1) .on_account_dr_cr").val("Cr");
				}
				
				var total_amt_ref=0;
				if(onAcc_dr_cr=="Dr"){
					var total_amt_ref=(onAcc+total_ref_dr)-total_ref_cr;
				}else{
					var total_amt_ref=(onAcc+total_ref_cr)-total_ref_dr;
				}
				$(this).find("table.ref_table tfoot tr:nth-child(2) td:nth-child(2) input").val(round(total_amt_ref,2));
				
			}else{
				var main_amt=parseFloat($(this).closest("#main_table tbody#main_tbody tr.main_tr").find('td:nth-child(2) input').val());
				if(total_ref_dr < total_ref_cr){
					afterCal=total_ref_cr-total_ref_dr;
					onAcc=main_amt-afterCal;
					onAcc_dr_cr="Cr";
				}else if(total_ref_dr > total_ref_cr){
					afterCal=total_ref_cr-total_ref_dr;
					onAcc=main_amt-afterCal;
					onAcc_dr_cr="Cr";
				}else{
					onAcc=main_amt;
					onAcc_dr_cr="Cr";
				}
				
				//alert(onAcc);
				if(onAcc>=0){
					onAcc=Math.abs(onAcc);
					onAcc=round(onAcc,2);
				$(this).find("table.ref_table tfoot tr:nth-child(1) td:nth-child(3) input").val(onAcc);
				$(this).find("table.ref_table tfoot tr:nth-child(1) .on_account_dr_cr").val(onAcc_dr_cr);
				//total_ref_cr=total_ref_cr+on_acc;
				}else{
					//onAcc=Math.abs(onAcc);
					onAcc=round(onAcc,2);
					$(this).find("table.ref_table tfoot tr:nth-child(1) td:nth-child(3) input").val(Math.abs(onAcc));
					$(this).find("table.ref_table tfoot tr:nth-child(1) .on_account_dr_cr").val("Dr");
				}
				
				var total_amt_ref=0;
				
				if(onAcc_dr_cr=="Dr"){
					var total_amt_ref=(onAcc+total_ref_dr)-total_ref_cr;
				}else{ 
					var total_amt_ref=(onAcc+total_ref_cr)-total_ref_dr;
					
				}
				
				//$(this).find("table.ref_table tfoot tr:nth-child(2) td:nth-child(2) input").val(total_amt_ref.toFixed(2));
			}
		});
	}
	
	$('.mian_amount').live("blur",function() {
		var v=parseFloat($(this).val());
		if(!v){ v=0; }
		$(this).val(round(v,2));
	});
	
	$('.mian_amount').live("keyup",function() {
		do_mian_amount_total();
		do_ref_total();
	});
	
	function do_mian_amount_total(){
		var mian_amount_total_cr=0; var mian_amount_total_dr=0;
		$("#main_table tbody#main_tbody tr.main_tr").each(function(){
			var v=parseFloat($(this).find("td:nth-child(2) input").val());
			var cr_dr=($(this).find("td:nth-child(2) select").val());
			if(!v){ v=0; }
			if(cr_dr=="Cr"){
				mian_amount_total_cr=mian_amount_total_cr+v;
			}else{
				mian_amount_total_dr=mian_amount_total_dr+v;
			}
			
			mian_amount_total=mian_amount_total_cr-mian_amount_total_dr;
			$('#receipt_amount').text(round(mian_amount_total,2));
		});
	}
	
	
	
});
</script>

<table id="sample_table" style="display:none;">
	<tbody>
		<tr class="main_tr">
			<td><?php echo $this->Form->input('received_from_id', ['empty'=>'--Select-','options'=>$receivedFroms,'label' => false,'class' => 'form-control input-sm received_from']); ?>
			<?php echo $this->Form->input('row_id', ['type'=>'hidden','label' => false,'class' => 'form-control input-sm row_id']); ?>
			</td>
			<td>
				<div class="row">
					<div class="col-md-7" style="padding-right: 0;">
						<?php echo $this->Form->input('amount', ['label' => false,'class' => 'form-control input-sm mian_amount','placeholder'=>'Amount']); ?>
					</div>
					<div class="col-md-5  " style="padding-left: 0;">
						<select name="cr_dr" class="form-control input-sm cr_dr" >
							<option value="Cr">Cr</option>
							<option value="Dr">Dr</option>
						</select>
					</div>
				</div>
			</td>
			<td></td>
			<td><a class="btn btn-xs btn-default deleterow" href="#" role="button"><i class="fa fa-times"></i></a></td>
		</tr>
	</tbody>
</table>

<?php $ref_types=['New Reference'=>'New Ref','Against Reference'=>'Agst Ref','Advance Reference'=>'Advance']; ?>
<div id="sample_ref" style="display:none;">
	<div class="ref" style="padding:4px;">
	<table width="100%" class="ref_table">
		<thead>
			<tr>
				<th width="20%">Ref Type</th>
				<th width="35%">Ref No.</th>
				<th width="28%">Amount</th>
				<th width="30%">Cr/Dr</th>
				<th width="5%"></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $this->Form->input('ref_types', ['empty'=>'--Select-','options'=>$ref_types,'label' => false,'class' => 'form-control input-sm ref_type']); ?></td>
				<td class="ref_no"></td>
				<td><?php echo $this->Form->input('amount', ['label' => false,'class' => 'form-control input-sm ref_amount_textbox','placeholder'=>'Amount']); ?></td>
				<td><?php echo $this->Form->input('ref_cr_dr', ['options'=>['Cr'=>'Cr','Dr'=>'Dr'],'label' => false,'class' => 'form-control input-sm  calculation drcrChange','value'=>'Cr']); ?></td>
				<td><a class="btn btn-xs btn-default deleterefrow" href="#" role="button"><i class="fa fa-times"></i></a></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td align="center" style="vertical-align: middle !important;">On Account</td>
				<td></td>
				<td><?php echo $this->Form->input('on_account', ['label' => false,'class' => 'form-control input-sm on_account','placeholder'=>'Amount','readonly']); ?></td>
				<td><?php echo $this->Form->input('on_account_dr_cr', ['label' => false,'class' => 'form-control input-sm on_account_dr_cr','readonly']); ?></td>
				
			</tr>
			<tr>
				<td colspan="2"><a class="btn btn-xs btn-default addrefrow" href="#" role="button"><i class="fa fa-plus"></i> Add row</a></td>
				<td colspan="2"></td>
				
			</tr>
		</tfoot>
	</table>
	</div>
</div>

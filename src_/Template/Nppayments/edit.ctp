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
<?php 	$first="01";
		$last="31";
		$start_date=$first.'-'.$financial_month_first->month;
		$end_date=$last.'-'.$financial_month_last->month;
		$start_date=strtotime(date("Y-m-d",strtotime($start_date)));
		$transaction_date=strtotime($nppayment->transaction_date);
if($transaction_date <  $start_date ) {
	echo "Financial Month has been Closed";
} else { ?>
<?php $ref_types=['New Reference'=>'New Ref','Against Reference'=>'Agst Ref','Advance Reference'=>'Advance']; ?>
<?php $cr_dr_options=['Dr'=>'Dr','Cr'=>'Cr']; ?>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption" >
            <i class="icon-globe font-blue-steel"></i>
            <span class="caption-subject font-blue-steel uppercase">Edit NonPrint Payment Voucher</span>
        </div>
    </div>
    <div class="portlet-body form">
    <?= $this->Form->create($nppayment,['id'=>'form_sample_3']) ?>
	<?php 	$first="01";
				$last="31";
				$start_date=$first.'-'.$financial_month_first->month;
				$end_date=$last.'-'.$financial_month_last->month;
				//pr($start_date); exit;
		?>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Transaction Date<span class="required" aria-required="true">*</span></label>
                    <?php echo $this->Form->input('transaction_date', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','value' => date("d-m-Y",strtotime($nppayment->transaction_date)),'data-date-start-date' => $start_date,'data-date-end-date' => $end_date,'required']); ?>
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
                    <?php echo $this->Form->input('bank_cash_id', ['empty'=>'--Select-','label' => false,'class' => 'form-control input-sm select2me']); ?>
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
                <th  width="25%" ><label class="control-label">Paid To</label></th>
                <th width="20%"><label class="control-label">Amount</label></th>
				<th width="40%"></th>
                <th width="10%"><label class="control-label">Narration</label></th>
				<th width="3%"></th>
            </thead>
            <tbody id="main_tbody">
            <?php

			//pr($nppayment->nppayment_rows);exit;
			foreach($nppayment->nppayment_rows as $nppayment_row){ ?> 
                <tr class="main_tr" old_received_from_id="<?php echo $nppayment_row->received_from_id; ?>">
                   <td width="20%">
					<?php echo $this->Form->input('id', ['type' => 'hidden','class' => 'form-control input-sm nppayment_row_id','value'=>$nppayment_row->id]); ?>
					<?php echo $this->Form->input('row_id', ['type'=>'hidden','label' => false,'class' => 'form-control input-sm row_id']); ?>
					
					<?php 
					
					echo $this->Form->input('bill_to_bill_account', ['type'=>'hidden','label' => false,'class' => 'form-control input-sm bill_to_bill_account','value'=>$nppayment_row->ReceivedFrom->bill_to_bill_account]); ?>
					
					<?php echo $this->Form->input('received_from_id', ['empty'=>'--Select-','options'=>$receivedFroms,'label' => false,'class' => 'form-control input-sm  received_from','value'=>$nppayment_row->received_from_id,'style'=>'width: 266px; display: block;']); ?>
					<div class="show_result">
					<?php if($nppayment_row->ledger_account->expense_tracking=='yes' && $nppayment_row->ledger_account->grn_invoice=='grn'){
							 $option=[];
							foreach($grn as $grn1) 
								{ 
									$grnIds = explode(',',$nppayment_row->grn_ids);  
									if(in_array($grn1->id, $grnIds))
									{   
										$grn_no=$grn1->grn1.'/GRN-'.str_pad($grn1->grn2, 3, '0', STR_PAD_LEFT).'/'.$grn1->grn3.'/'.$grn1->grn4;

										$value[]=$grn1->id;  //pr($value);
									}
									
									$grn_no=$grn1->grn1.'/GRN-'.str_pad($grn1->grn2, 3, '0', STR_PAD_LEFT).'/'.$grn1->grn3.'/'.$grn1->grn4;
									$option[]=['text' =>$grn_no, 'value' => $grn1->id];
								}

					echo $this->Form->input('q[]', ['label'=>false,'options' => $option,'multiple' => 'multiple','class'=>'form-control  grns select2me','style'=>'width:100%','value'=>$value]);
					}
					elseif($nppayment_row->ledger_account->expense_tracking=='yes' && $nppayment_row->ledger_account->grn_invoice=='invoice'){
							 $option=[];
							foreach($invoice as $invoice1)
								{ 
									$invoiceIds = explode(',',$nppayment_row->invoice_ids);
									if(in_array($invoice1->id, $invoiceIds))
									{   
										$invoice_no=$invoice1->in1.'/IN-'.str_pad($invoice1->in2, 3, '0', STR_PAD_LEFT).'/'.$invoice1->in3.'/'.$invoice1->in4;
										$option[]=['text' =>$invoice_no, 'value' => $invoice1->id, 'selected'];
									}
									else
									{
										if($invoice1->sales_thela_bhada_status=='no')
										{
											$invoice_no=$invoice1->in1.'/IN-'.str_pad($invoice1->in2, 3, '0', STR_PAD_LEFT).'/'.$invoice1->in3.'/'.$invoice1->in4;
											$option[]=['text' =>$invoice_no, 'value' => $invoice1->id];
										}
									} 

								}
					echo $this->Form->input('q[]', ['label'=>false,'options' => $option,'multiple' => 'multiple','class'=>'form-control invoices select2me','style'=>'width:100%']);
					}
					
					?>
					</div>
					</td>
                    <td width="30%">
                    <div class="row">
                        <div class="col-md-7" style="padding-right: 0;">
                            <?php echo $this->Form->input('amount', ['label' => false,'class' => 'form-control input-sm mian_amount','placeholder'=>'Amount','value'=>$nppayment_row->amount]); ?>
                        </div>
                        <div class="col-md-5"style="padding-left: 0;">
                            <?php echo $this->Form->input('cr_dr', ['label' => false,'options'=>$cr_dr_options,'class' => 'form-control input-sm cr_dr','value'=>$nppayment_row->cr_dr]); ?>
                        </div>
                    </div>
                    </td>
                    <td width="60%">
                    <?php if($nppayment_row->ReceivedFrom->bill_to_bill_account=="Yes"){ ?>
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
                            <?php foreach($nppayment_row->reference_details as $reference_detail){
                               	if($reference_detail->reference_type!='On_account'){
								?>
								<tr>
									<td><?php echo $this->Form->input('ref_types', ['empty'=>'--Select-','options'=>$ref_types,'label' => false,'class' => 'form-control input-sm ref_type','value'=>$reference_detail->reference_type]); ?></td>
									<td class="ref_no">
									<?php if($reference_detail->reference_type=="Against Reference"){
									echo $this->requestAction('/ReferenceDetails/listRefEdit?ledger_account_id='.$nppayment_row->received_from_id.'&ref_name='.$reference_detail->reference_no);
									}else{
										echo $this->Form->input('ref_cr_dr', ['label' => false,'class' => 'form-control input-sm ','value'=>$reference_detail->reference_no]);
									} ?> 
									</td>
									<td><?php 
									if(!empty($reference_detail->credit)){
										$amount=$reference_detail->credit;
										$dr_cr="Cr";
									}else{
										$amount=$reference_detail->debit;
										$dr_cr="Dr";
									}
									echo $this->Form->input('amount', ['label' => false,'class' => 'form-control input-sm ref_amount_textbox','placeholder'=>'Amount','value'=>$amount]); ?></td>
									<td><?php echo $this->Form->input('ref_cr_dr', ['options'=>['Dr'=>'Dr','Cr'=>'Cr'],'label' => false,'class' => 'form-control input-sm  calculation drcrChange','value'=>$dr_cr]); ?></td>
									<td><a class="btn btn-xs btn-default deleterefrow" href="#" role="button"><i class="fa fa-times"></i></a></td>
								</tr>
								<?php } } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td align="center" style="vertical-align: middle !important;">On Account</td>
                                    <td></td>
                                   <td><?php echo $this->Form->input('on_account', ['label' => false,'class' => 'form-control input-sm on_account','placeholder'=>'Amount','readonly']); ?></td>
									
									<td><?php echo $this->Form->input('on_account_dr_cr', ['label' => false,'class' => 'form-control input-sm on_account_dr_cr','readonly']); ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><a class="btn btn-xs btn-default addrefrow" href="#" role="button"><i class="fa fa-plus"></i> Add row</a></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        </div>
                       <?php } ?>  
                    </td>
                    <td width="5%"><?php echo $this->Form->input('narration', ['type'=>'textarea','label' => false,'class' => 'form-control input-sm narration','placeholder'=>'Narration','value'=>$nppayment_row->narration]); ?></td>
                    <td><a class="btn btn-xs btn-default deleterow" href="#" role="button"><i class="fa fa-times"></i></a></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
                <td><a class="btn btn-xs btn-default addrow" href="#" role="button"><i class="fa fa-plus"></i> Add row</a></td>
                <td id="receipt_amount" style="font-size: 14px;font-weight: bold;"></td>
                <td></td>
                <td>
					<?php if($chkdate == 'Not Found'){  ?>
						<label class="btn btn-danger"> You are not in Current Financial Year </label>
					<?php } else { ?>
						<button type="submit" class="btn btn-primary" id='submitbtn'>EDIT NON PRINT PAYMENT VOUCHER</button>
					<?php } ?>	
				</td>
                <td></td>
            </tfoot>
        </table>
        </div>
    <?= $this->Form->end() ?>
    </div>
</div>
<?php } ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>

<script>
$(document).ready(function() {
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
	
	
		var nppayment_mode= $('input[name="payment_mode"]:checked').val();
        //alert(nppayment_mode);
        if(nppayment_mode=="Cheque"){
            $("#chq_no").show();
        }else{
            $("#chq_no").hide();
        }
	
	
    $('input[name="payment_mode"]').die().live("click",function() {
        var nppayment_mode=$(this).val();
        
        if(nppayment_mode=="Cheque"){
            $("#chq_no").show();
        }else{
            $("#chq_no").hide();
        }
    });
	
	$('.addrow').live("click",function() {
        add_row();
    });
	
	    function add_row(){
        var tr=$("#sample_table tbody tr").clone();
        $("#main_table tbody#main_tbody").append(tr);
        rename_rows();
    }
	
	
		$("#main_table tbody#main_tbody tr.main_tr").each(function(){ 
		var sel2=$(this);
		var received_from_id=$(this).find("td:nth-child(1) select").find('option:selected').val();
		var url="<?php echo $this->Url->build(['controller'=>'LedgerAccounts','action'=>'checkBillToBillAccountingStatus']); ?>";
		url=url+'/'+received_from_id,
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'text'
		}).done(function(response) {
			if(response.trim()=="Yes"){
				
			}else{
				$(sel2).find("td:nth-child(3)").html("");
			}
			rename_ref_rows(sel2,received_from_id);
		});
	});
    rename_rows();
	function rename_rows(){
		var i=0;
		$("#main_table tbody#main_tbody tr.main_tr").each(function(){ 
			$(this).find("td:eq(0) .nppayment_row_id").attr({name:"nppayment_rows["+i+"][id]", id:"nppayment_rows-"+i+"-id"});
		
			$(this).find("td:eq(0) select.received_from").select2().attr({name:"nppayment_rows["+i+"][received_from_id]", id:"quotation_rows-"+i+"-received_from_id"}).rules('add', {
						required: true
					});

				var thela_type=$(this).find('td:nth-child(1) select.received_from  option:selected').attr('thelatype');

				if(thela_type)
				{ 
					if(thela_type=='grn')
					{				
						$(this).find("td:eq(0) select.grns").attr({name:"nppayment_rows["+i+"][grn_ids][]", id:"nppayment_rows-"+i+"-grn_ids"}).rules('add', {
							required: true,
							notEqualToGroup: ['.grns'],
							messages: {
								notEqualToGroup: "Do not select same grn again."
							}
						});
					}
					if(thela_type=='invoice')
					{				
						$(this).find("td:eq(0) select.invoices").attr({name:"nppayment_rows["+i+"][invoice_ids][]", id:"nppayment_rows-"+i+"-invoice_ids"}).rules('add', {
							required: true,
							notEqualToGroup: ['.invoices'],
							messages: {
								notEqualToGroup: "Do not select same invoice again."
							}
						});
					}
				}				


    		$(this).find("td:eq(0) .row_id").val(i);
			$(this).find("td:eq(1) input").attr({name:"nppayment_rows["+i+"][amount]", id:"nppayment_rows-"+i+"-amount"}).rules('add', {
						required: true,
						min: 0.01
					});
			$(this).find("td:eq(1) select").attr({name:"nppayment_rows["+i+"][cr_dr]", id:"nppayment_rows-"+i+"-cr_dr"});
			$(this).find("td:nth-child(4) textarea").attr({name:"nppayment_rows["+i+"][narration]", id:"nppayment_rows-"+i+"-narration"}).rules('add', {
						required: true
					});
			
			i++;
		});
	}
	
	
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
	

	
	function rename_ref_rows(sel,received_from_id){  //alert(sel);alert(received_from_id);
		var i=0;
		var row_id=0;
		$(sel).find("table.ref_table tbody tr").each(function(){ 
			
			row_id=$(this).closest('tr.main_tr').find('td:eq(0) .row_id').val();
			$(this).find("td:nth-child(1) select").attr({name:"nppayment_rows["+row_id+"][ref_rows]["+i+"][ref_type]", id:"ref_rows-"+row_id+"-"+i+"-ref_type"}).rules("add", "required");
			var is_select=$(this).find("td:nth-child(2) select").length;
			var is_input=$(this).find("td:nth-child(2) input").length;
			
			if(is_select){
				//$(this).find("td:nth-child(2) input").rules("remove", "required");
				$(this).find("td:nth-child(2) select").attr({name:"nppayment_rows["+row_id+"][ref_rows]["+i+"][ref_no]", id:"ref_rows-"+row_id+"-"+i+"-ref_no"}).rules("add", "required");
			}else if(is_input){
				
				$(this).find("td:nth-child(2) input").attr({name:"nppayment_rows["+row_id+"][ref_rows]["+i+"][ref_no]", id:"ref_rows-"+row_id+"-"+i+"-ref_no", class:"form-control input-sm ref_number-"+row_id}).rules("add", "required");
			}
			
			$(this).find("td:nth-child(3) input").attr({name:"nppayment_rows["+row_id+"][ref_rows]["+i+"][ref_amount]", id:"ref_rows-"+row_id+"-"+i+"-ref_amount"}).rules("add", "required");
			
			$(this).find("td:nth-child(4) select").attr({name:"nppayment_rows["+row_id+"][ref_rows]["+i+"][ref_cr_dr]", id:"ref_rows-"+row_id+"-"+i+"-ref_cr_dr"}).rules("add", "required");
			i++;
		});
		
		$(sel).find("table.ref_table tfoot tr:nth-child(1) .on_account").attr({name:"nppayment_rows["+row_id+"][on_acc]", id:"ref_rows-"+row_id+"-"+i+"-ref_cr_dr"}).rules("add", "required");
		
		$(sel).find("table.ref_table tfoot tr:nth-child(1) .on_account_dr_cr").attr({name:"nppayment_rows["+row_id+"][on_acc_dr_cr]", id:"ref_rows-"+row_id+"-"+i+"-ref_cr_dr"}).rules("add", "required");
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
		$(this).closest('div.select').css("width","235px");
        load_ref_section(sel);
    });
    
    $('.cr_dr').live("change",function() {
       // var sel=$(this);
       // load_ref_section(sel);
      //  do_mian_amount_total();
    });
    
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
		
		var url="<?php echo $this->Url->build(['controller'=>'LedgerAccounts','action'=>'checkExpenseTrackingStatus']); ?>";
		url=url+'/'+received_from_id,
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'text'
		}).done(function(response) { 
			$(sel).closest('tr.main_tr').find('.show_result').html(response);
			//$(sel).closest('tr.main_tr').find('select.grns').select2();
			rename_rows();
		});
		
		/* var url="<?php echo $this->Url->build(['controller'=>'LedgerAccounts','action'=>'loadGrns']); ?>";
		url=url+'/'+received_from_id;
		
		if(received_from_id=='101' || received_from_id=='165' || received_from_id=='313')
		{  
	       $.ajax({
				url: url,
				type: 'GET',
				dataType: 'text'
			}).done(function(response) { 
				$(sel).closest('tr.main_tr').find('.show_result').html(response);
				$(sel).closest('tr.main_tr').find('select.grns').select2();
				rename_rows();
			});
		}
		else
		{
			$(sel).closest('tr.main_tr').find('.show_result').html('');
		}
		
		var url="<?php echo $this->Url->build(['controller'=>'LedgerAccounts','action'=>'loadInvoices']); ?>";
		url=url+'/'+received_from_id;
		if(received_from_id=='105' || received_from_id=='168' || received_from_id=='316')
		{
	       $.ajax({
				url: url,
				type: 'GET',
				dataType: 'text'
			}).done(function(response) {  
				$(sel).closest('tr.main_tr').find('.show_result').html(response);
				rename_rows(); 
			});
		}
		else
		{
			$(sel).closest('tr.main_tr').find('.show_result').html('');
		} */
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
    
    $('.ref_amount_textbox').live("keyup",function() {
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
    do_ref_total();
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
				//total_ref_cr=total_ref_cr+on_acc;
				}else{
					onAcc=Math.abs(onAcc);
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
				$(this).find("table.ref_table tfoot tr:nth-child(2) td:nth-child(2) input").val(total_amt_ref.toFixed(2));
				
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
        $(this).val(v.toFixed(2));
    });
    
    $('.mian_amount').live("keyup",function() {
        do_mian_amount_total();
        do_ref_total();
    });
    do_mian_amount_total();
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
            
            mian_amount_total=mian_amount_total_dr-mian_amount_total_cr;
            $('#receipt_amount').text(mian_amount_total.toFixed(2));
        });
    }
    
	
    
});
</script>


<table id="sample_table" style="display:none;">
    <tbody>
        <tr class="main_tr">
            <td width="25%"><?php echo $this->Form->input('received_from_id', ['empty'=>'--Select-','options'=>$receivedFroms,'label' => false,'class' => 'form-control input-sm received_from']); ?>
			<?php echo $this->Form->input('row_id', ['type'=>'hidden','label' => false,'class' => 'form-control input-sm row_id']); ?>
			<div class="show_result"></div>
			</td>
            <td width="20%">
            <div class="row">
                <div class="col-md-7" style="padding-right: 0;">
                    <?php echo $this->Form->input('amount', ['label' => false,'class' => 'form-control input-sm mian_amount','placeholder'=>'Amount']); ?>
                </div>
                <div class="col-md-5"style="padding-left: 0;">
                    <select name="cr_dr" class="form-control input-sm cr_dr" >
                        <option value="Dr">Dr</option>
                        <option value="Cr">Cr</option>
                    </select>
                </div>
            </div>
            </td>
            <td width="57%"></td>
            <td width="5%"><?php echo $this->Form->input('narration', ['type'=>'textarea','label' => false,'class' => 'form-control input-sm','placeholder'=>'Narration']); ?></td>
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
				<td><?php echo $this->Form->input('ref_cr_dr', ['options'=>['Dr'=>'Dr','Cr'=>'Cr'],'label' => false,'class' => 'form-control input-sm  calculation drcrChange','value'=>'Dr']); ?></td>
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
				
				
			</tr>
		</tfoot>
	</table>
	</div>
</div>

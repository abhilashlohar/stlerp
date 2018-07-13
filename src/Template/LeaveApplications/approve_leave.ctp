<style>
.disabledbutton {
    pointer-events: none;
    //opacity: 0.4;
}
</style>

<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-purple-intense ">Approve Leave Application</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-12">
				<table width="70%">
					<tr>
						<td width="15%"><b>Employee: </b></td>
						<td><?php echo $LeaveApplication->employee->name; ?></td>
						<td width="15%"><b>No. of leaves: </b></td>
						<td><?php echo $LeaveApplication->day_no; ?></td>
					</tr>
					<tr>
						<td width="15%"><b>Leave Dates: </b></td>
						<td>
							From <?php echo $LeaveApplication->from_leave_date->format('d-m-Y'); ?> 
							<?php if($LeaveApplication->from_full_half!='Full Day'){ echo '('.$LeaveApplication->from_full_half.')'; } ?> 
							To <?php echo $LeaveApplication->to_leave_date->format('d-m-Y'); ?>
							<?php if($LeaveApplication->to_full_half!='Full Day'){ echo '('.$LeaveApplication->to_full_half.')'; } ?>
						</td>
						<td width="15%"><b>Leave Type: </b></td>
						<td><?php echo $LeaveApplication->leave_type->leave_name; ?></td>
					</tr>
					<tr>
						<td width="15%"><b>Reason for leave: </b></td>
						<td><?php echo $LeaveApplication->leave_reason; ?></td>
						<td width="15%"></td>
						<td></td>
					</tr>
				</table>
			
			</div>
		</div>
		<hr/>
		
		<form method="post">
			<div class="row">
				<div class="col-md-5"></div>
				<div class="col-md-6">
					<?php 
					if($LeaveApplication->single_multiple == "Single"){
						echo $this->Form->radio(
						'approve_single_multiple',
						[
							['value' => 'Single', 'text' => 'Single Day'],
							
						],['value'=>$LeaveApplication->single_multiple]
					);
					}else if($LeaveApplication->single_multiple == "Multiple"){
						echo $this->Form->radio(
						'approve_single_multiple',
						[
							['value' => 'Multiple', 'text' => 'Multiple Days']
						],['value'=>$LeaveApplication->single_multiple]
					);
					}else{
						echo $this->Form->radio(
						'approve_single_multiple',
						[
							['value' => 'Single', 'text' => 'Single Day'],
							['value' => 'Multiple', 'text' => 'Multiple Days']
						],['value'=>$LeaveApplication->single_multiple]
					);
					}
					 ?>
				</div>
			</div>
			<div class="row">
				<div id="date_from">
					<div class="col-md-3">
						<div class="form-group" >
							<label class="control-label  label-css">Date of Leave Required (From)</label>   
							<?php 
							
							echo $this->Form->input('approve_leave_from', ['type'=>'text','label' => false,'placeholder'=>'dd-mm-yyyy','class'=>'form-control input-sm date-picker disabledbutton','data-date-format'=>'dd-mm-yyyy','value'=>$LeaveApplication->from_leave_date->format('d-m-Y')]); ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" id="from_half">
							<label class="control-label  label-css">&nbsp;</label>  
							<?php
							
							$options[]=['text' =>'Full Day', 'value' => 'Full Day'];
							$options[]=['text' =>'First Half Day', 'value' => 'First Half Day'];
							$options[]=['text' =>'Second Half Day', 'value' => 'Second Half Day'];
							echo $this->Form->input('approve_full_half_from', ['label' => false,'options' => $options,'class' => 'form-control input-sm disabledbutton','value' => $LeaveApplication->from_full_half]); ?>
						</div>
					</div>
				</div>	
				<div id="date_to">
					<div class="col-md-3">
						<div class="form-group" >
							<label class="control-label  label-css">Date of Leave Required (To)</label>   
							<?php 
							
							echo $this->Form->input('approve_leave_to', ['type'=>'text','label' => false,'placeholder'=>'dd-mm-yyyy','class'=>'form-control input-sm date-picker disabledbutton','data-date-format'=>'dd-mm-yyyy','value'=>$LeaveApplication->to_leave_date->format('d-m-Y')]); ?>
						</div>
					</div>
				   <div class="col-md-3">
						<div class="form-group" id="to_half">
							<label class="control-label  label-css">.</label>  
							<?php 
							
							echo $this->Form->input('approve_full_half_to', ['label' => false,'options' => $options,'class' => 'form-control input-sm disabledbutton','value' => $LeaveApplication->to_full_half]); ?>
						</div>
					</div>
				</div>
			</div>
			
			<table width="100%" >
				<tr>
					
					<td align="center">
						<div id="qwerty"></div>
						<table class="table">
							<tr>
								<td>
									<label class="control-label  label-css">Intimated  Leaves </label>   
									
								</td>
								<td>
									<label class="control-label  label-css">Prior Approval </label>   
									<?php echo $this->Form->input('prior_approval', ['type'=>'text','label' => false,'class'=>'form-control input-sm intimated_leave','value'=>@$LeaveApplication->intimated_leave]); ?>
								</td>
								<td>
									<label class="control-label  label-css">Without Prior Approval </label>   
									<?php echo $this->Form->input('without_prior_approval', ['type'=>'text','label' => false,'class'=>'form-control input-sm intimated_leave','value'=>@$LeaveApplication->intimated_leave]); ?>
								</td>
								
								<td>
									
								</td>
							</tr>
							<!--<tr>
								<td>
									<label class="control-label  label-css">Unintimated Leaves </label>   
									
								</td>
								<td><?php echo $this->Form->input('unintimated_leave', ['type'=>'text','label' => false,'class'=>'form-control input-sm unintimated_leave','readonly']); ?></td>
								<td></td>
							</tr>-->
							<tr>
								<td>
									<label class="control-label  label-css">Paid Leaves </label>   
									<?php echo $this->Form->input('paid_leaves', ['type'=>'text','label' => false,'class'=>'form-control input-sm','readonly']); ?>
								</td>
								<td>
									<label class="control-label  label-css">Unpaid Leaves </label>   
									<?php echo $this->Form->input('unpaid_leaves', ['type'=>'text','label' => false,'class'=>'form-control input-sm','readonly']); ?>
								</td>
								<td>
									<label class="control-label  label-css">Total Approved Leaves </label>   
									<?php echo $this->Form->input('total_approved_leaves', ['type'=>'text','label' => false,'class'=>'form-control input-sm','readonly']); ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			
			
			
			<button type="submit" class="btn blue sub">Approve</button>
		</form>
	</div>
</div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script>
$(document).ready(function(){
	

	
	$('input[name=without_prior_approval]').live("keyup",function(){
		countLeaves();
	});
	$('input[name=prior_approval]').live("keyup",function(){
		countLeaves();
	});
	$('input[name=approve_leave_to]').live("blur",function(){
		countLeaves();
	});
	
	$('input[name=approve_single_multiple]').live("click",function(){
		var single_multiple=$(this).val();
		expandHalfDay(single_multiple);
		if(single_multiple=="Single"){
			var q=$('input[name=approve_leave_from]').val();
			$('input[name=approve_leave_to]').val(q);
		}
		countLeaves();
	});
	
	var single_multiple=$('input[name=approve_single_multiple]:checked').val();
	expandHalfDay(single_multiple);
	
	function expandHalfDay(single_multiple){
		if(single_multiple=="Single"){
			$('#date_to').hide();
			
			$('#from_half').find('select option[value="First Half Day"]').removeAttr('disabled','disabled');
			//$('#from_half').find('select option[value="Full Day"]').attr('selected','selected');
		}else{
			$('#date_to').show();
			
			var q=$('#from_half').find('select option:selected').val();
			if(q=='First Half Day'){
				$('#from_half').find('select option[value="Full Day"]').attr('selected','selected');
			}
			
			$('#to_half').find('select option[value="Second Half Day"]').attr('disabled','disabled');
			//$('#to_half').find('select option[value="Full Day"]').attr('selected','selected');
			
			$('#from_half').find('select option[value="First Half Day"]').attr('disabled','disabled');
			//$('#from_half').find('select option[value="Full Day"]').attr('selected','selected');
		}
	}
	countLeaves();
	function countLeaves(){ 
		var employee_id='<?php echo $LeaveApplication->employee_id; ?>';
		var leaveAppId='<?php echo $LeaveApplication->id; ?>';
		var url="<?php echo $this->Url->build(['controller'=>'LeaveApplications','action'=>'leaveInfoEmployees']); ?>";
        url=url+'/'+employee_id+'/'+leaveAppId; 
		//alert(url);
		 $.ajax({
            url: url,
            type: 'GET',
        }).done(function(response) { // alert(response);
			var res = response.split(",");
			var paid_leave = res[0];
			var unpaid_leave = res[1];
			var un_initimate_leave = res[2];
			var prior_leave = res[3];
			var without_prior_leave = res[4];
			var day_no = res[5];
			var total_past_paid_leave = res[6];
			var total_past_unpaid_leave = res[7];
			
			$('div#qwerty').html('ML:'+30+', PPL:'+total_past_paid_leave+', PUL:'+total_past_unpaid_leave);
			
			$('input[name="paid_leaves"]').val(paid_leave);
			$('input[name="unpaid_leaves"]').val(unpaid_leave);
			$('input[name="unintimated_leave"]').val(un_initimate_leave);
			$('input[name="total_approved_leaves"]').val(day_no);
			if(total_past_unpaid_leave > 0){
				var unpaid_leave_tot = parseFloat(total_past_unpaid_leave)+parseFloat(paid_leave);
				$('input[name="unpaid_leaves"]').val(unpaid_leave_tot);
				$('input[name="paid_leaves"]').val(0);
			}
			var without_prior_approvals = $('input[name="without_prior_approval"]').val();
			if(without_prior_approvals > 0){
				var unpaid_leave_t =$('input[name="unpaid_leaves"]').val(); 
				var abc = parseFloat(without_prior_approvals)+parseFloat(unpaid_leave_t);
				//var total_unpaid = parseFloat(5.0)-parseFloat(unpaid_leave_t); 
				//var tots = parseFloat(without_prior_approvals)+parseFloat(without_prior_leave);
				//var abc = parseFloat(total_unpaid) + parseFloat(tots);
				//$('input[name="without_prior_approval"]').val(tots);
				$('input[name="unpaid_leaves"]').val(abc);
			}
			
		});
	
	
	
	}
	/* function countLeaves(){
		
		var w=$('input[name=approve_leave_to]').val();
		if(w==""){
			var t=$('input[name=approve_leave_from]').val();
			$('input[name=approve_leave_to]').val(t);
		}
		
		var employee_id='<?php echo $LeaveApplication->employee_id; ?>';
		var leaveAppId='<?php echo $LeaveApplication->id; ?>';
		var url="<?php echo $this->Url->build(['controller'=>'LeaveApplications','action'=>'leaveInfo']); ?>";
        url=url+'/'+employee_id+'/'+leaveAppId; 
		//alert(url);
        $.ajax({
            url: url,
            type: 'GET',
        }).done(function(response) {
			//alert(response);
			var res = response.split("-");
			var ML=res[0];
			var PPL=res[1];
			var PUL=res[2];
			var EMPTYPE=res[3];
			
			$('div#qwerty').html('ML:'+ML+', PPL:'+PPL+', PUL'+PUL);
			
			var p=$('input[name="approve_leave_from"]').val().split('-');
			var approve_leave_from = new Date(p[2], p[1] - 1, p[0]);
			
			var p=$('input[name="approve_leave_to"]').val().split('-');
			var approve_leave_to = new Date(p[2], p[1] - 1, p[0]);
			
			var diff = new Date(approve_leave_to - approve_leave_from);
			var days = diff/1000/60/60/24;
			days=days+1;
			
			if(days<0){
				var q=$('input[name="approve_leave_from"]').val();
				$('input[name="approve_leave_to"]').val(q);
			}
			
			var single_multiple=$('input[name="approve_single_multiple"]:checked').val();
			var approve_full_half_from=$('select[name="approve_full_half_from"] option:selected').val();
			var approve_full_half_to=$('select[name="approve_full_half_to"] option:selected').val();
			if(single_multiple=='Single'){
				if(approve_full_half_from!='Full Day'){
					days=0.5;
				}else{
					days=1;
				}
			}else{
				if(approve_full_half_from=='Second Half Day'){
					days-=0.5;
				}
				if(approve_full_half_to=='First Half Day'){
					days-=0.5;
				}
			}
			
			var T=days;
			
			var I=$('input[name="intimated_leave"]').val();
			
			if(I>T){
				I=0;
				$('input[name="intimated_leave"]').val(0);
			}
			var U=T-I;
			
			$('input[name="unintimated_leave"]').val(U);
			
			var PL=0; var UPL=0;
			var R=5-PUL;
			var X=R-U;
			if(EMPTYPE == "probabtion"){ 
				if(X>=0){
					var Q=ML-PPL; 
					var Z=Q-U;
					if(Z>=0){ PL=U; }
					if(Z<0){ PL=Q; UPL=Math.abs(Z); }alert(UPL);alert(PL);
				}else{
					var Q=ML-PPL;
					var Z=Q-R;
					if(Z>=0){ PL=R; UPL=Math.abs(X); }
					if(Z<0){ PL=Q; UPL=Math.abs(Z); UPL+=Math.abs(X); }
				}
			}else{
				if(X>=0){
					var Q=ML-PPL;
					var Z=Q-U;
					if(Z>=0){ PL=U; }
					if(Z<0){ PL=Q; UPL=Math.abs(Z); }
				}else{
					var Q=ML-PPL;
					var Z=Q-R;
					if(Z>=0){ PL=R; UPL=Math.abs(X); }
					if(Z<0){ PL=Q; UPL=Math.abs(Z); UPL+=Math.abs(X); }
				}
			}
			
			
			
			
			
			var B=ML-(parseFloat(PPL)+parseFloat(PL));
			var C=B-I;
			if(C>=0){ PL+=parseFloat(I);  }
			if(C<0){ PL+=B; UPL+=Math.abs(C);  }
			//alert(PL);

			$('input[name="paid_leaves"]').val(PL);
			$('input[name="unpaid_leaves"]').val(UPL);
			$('input[name="total_approved_leaves"]').val(T);
        });
		
		
		
	} */
	
	$('input[name="unpaid_leaves"]').live("keyup",function(){
		countLeaves();
	});
	$('input[name="paid_leaves"]').live("keyup",function(){
		countLeaves();
	});
	$(".sub").mouseover(function(){
		countLeaves();
	});
});
</script>
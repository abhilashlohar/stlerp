<?php 
$pdf_url=$this->Url->build(['controller'=>'SaleReturns','action'=>'GstPdf']);
$list_url=$this->Url->build(['controller'=>'SaleReturns','action'=>'index']);
if($SaleReturns->sale_return_type=="GST"){
	$Edit_url=$this->Url->build(['controller'=>'SaleReturns','action'=>'gstSalesEdit']);
}else{
	$Edit_url=$this->Url->build(['controller'=>'SaleReturns','action'=>'Edit']);
}
$mail_url=$this->Url->build(['controller'=>'SaleReturns','action'=>'sendMail']);
$id = $EncryptingDecrypting->encryptData($id);
//pr($pdf_url); exit;
?>
<table width="100%">
	<tr>
		<td valign="top" style="background: #FFF;">
		<div class="list-group">
			<a href="<?php echo $list_url; ?>" class="list-group-item"><i class="fa fa-chevron-left"></i> Back to SaleReturns </a>
			<a href="#" class="list-group-item select_term_condition"><i class="fa fa-envelope"></i> Email </a>
			<?php if(in_array(8,$allowed_pages)){
				if(!in_array(date("m-Y",strtotime($SaleReturns->date_created)),$closed_month))
				{ 
				?>
			<a href="<?php echo $Edit_url.'/'.$id; ?>" class="list-group-item"><i class="fa fa-edit"></i> Edit </a>
			<?php } } ?>
			<a href="#" class="list-group-item" onclick="window.close()"><i class="fa fa-times"></i> Close </a>
		</div>
		
		</td>
		<td width="80%">
			<object data="<?php echo $pdf_url.'/'.$id; ?>" type="application/pdf" width="100%" height="613px">
			  <p>Wait a while, PDf is loading...</p>
			</object>
		</td>
	</tr>
</table>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script>
$(document).ready(function() {
	$('.quantity').die().live("keyup",function() {
			var asc=$(this).val();
			var numbers =  /^[0-9]*\.?[0-9]*$/;
			if(asc==0)
			{
				$(this).val('');
				return false; 
			}
			else if(asc.match(numbers))  
			{  
			} 
			else  
			{  
				$(this).val('');
				return false;  
			}
	});
	
	$('.select_term_condition').die().live("click",function() { 
		var addr=$(this).text();
		$("#myModal2").show();
    });
	$('.closebtn2').on("click",function() { 
		$("#myModal2").hide();
    });
	
	$('.check_value').die().live("change",function() {
		$(".tabl_tc tbody tr").each(function(){
		var v=$(this).find('td:nth-child(1)  input[type="checkbox"]:checked').val();
		if(v){
			$(this).find('td:nth-child(1)  input[type="text"].term').removeAttr("readonly"); ;
			$(this).find('td:nth-child(1)  input[type="text"].term').focus();
		}else{
				
			$(this).find('td:nth-child(1)  input[type="text"].term').attr('readonly','readonly');
		}
		});
	});
	
	$('.insert_tc').die().live("click",function() {
		$('#sortable').html("");
		var i=0;
		var send_data = [];
		$(".tabl_tc tbody tr").each(function(){
			var v=$(this).find('td:nth-child(1)  input[type="checkbox"]:checked').val();
			var term=$(this).find('td:nth-child(1)  input[type="text"].term').val();
			if(term){
			$(this).find('td:nth-child(1)  input[type="checkbox"]:checked').val(term);
			}
			if(v){
				var tc=$(this).find('td:nth-child(1) .check_value').val(); 
				send_data[i++] = tc;
				//send_data[++i]=tc;
			}
		});
		var textdata=$('.textdata').val(); 
		var json_data=JSON.stringify(send_data);
		
		 var id="<?php echo $id; ?>";
		
		var url="<?php echo $this->Url->build(['controller'=>'Invoices','action'=>'sendMail']); ?>";
		url=url+'?id='+id+'&data='+json_data+'&otherData='+textdata;
		alert(url);
		$.ajax({
			url: url,
			type: "GET",
		}).done(function(response) { 
		//alert(response);
			alert("Email Send successfully")
		}); 
		
		//console.log(send_data);
		$("#myModal2").hide();
    });
});
</script>

<ol id="sortable"></ol>
<div id="myModal2" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="false" style="display: none; padding-right: 12px;"><div class="modal-backdrop fade in" ></div>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body" id="result_ajax">
			<h4>Terms & Conditions</h4>
				<div style=" overflow: auto; height: 450px;">
				<table class="table table-hover tabl_tc">
					
				<?php foreach ($termsConditions as $termsCondition): ?>
					 <tr>
						<td>
						 <div class="checkbox-list">
							<label>
								<input type="checkbox" name="dummy" value="<?= h($termsCondition->id) ?>" class="check_value"><input style="border: none;
								background: transparent;" type="text" size="60%" class="term" name="terms" value="<?php echo $termsCondition->text_line; ?>" readonly>
							</label>
						 </div>
						
						</td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<td>
						 <div class="checkbox-list">
							<label>
								
								<textarea name="delivery_description" class="form-control input-sm textdata" placeholder="Other Description" id="delivery-description" rows="5"></textarea>
							</label> 
						 </div>
						
						</td>
				</tr>
				</table>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn default closebtn2">Close</button>
				<button class="btn btn-primary insert_tc">Send Email</button>
			</div>
		</div>
	</div>
</div>

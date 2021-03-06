<?php if(empty(@$transaction_from_date)){
			$transaction_from_date=" ";
		}else{
			$transaction_from_date=$transaction_from_date;
		} 

		if(empty($transaction_to_date)){
			$transaction_to_date=" ";
		}else{
			$transaction_to_date=$transaction_to_date;
		}

		$closing_balance_ar=[];
		$balance_as_per_bank=[];

		///start new concept of bank reconcilation 7dec2017
		$balance_as_per_book_data=0;$balance_as_per_book_data1=0;
	
		if(!empty(@$balance_as_per_book_amt)){  
			if(@$balance_as_per_book_amt['debit'] > @$balance_as_per_book_amt['credit']){ 
				$balance_as_per_book_data1=@$balance_as_per_book_amt['debit'] - @$balance_as_per_book_amt['credit'];
				$balance_as_per_book_data = $this->Number->format(@$balance_as_per_book_data1.'Dr',[ 'places' => 2])." Dr";
			}
			else { 
				$balance_as_per_book_data1=@$balance_as_per_book_amt['credit'] - @$balance_as_per_book_amt['debit'];
				$balance_as_per_book_data = $this->Number->format(@$balance_as_per_book_data1.'Cr',[ 'places' => 2])." Cr";
			}					
		}
		else {   
				$balance_as_per_book_data = $this->Number->format('0',[ 'places' => 2]); 
			}
			
		if(sizeof(@$Ledgers_Banks) > 0){
			foreach($Ledgers_Banks as $Ledger)
			{
				@$balance_as_per_bank['debit']+=$Ledger->debit;
				@$balance_as_per_bank['credit']+=$Ledger->credit;
			}
		}	
		
		
		
			
		///////
				
	///end new concept of bank reconcilation 7dec2017
	
	if(!empty(@$Ledgers))
	{
		foreach($Ledgers as $Ledger)
		{
			@$closing_balance_ar['debit']+=$Ledger->debit;
			@$closing_balance_ar['credit']+=$Ledger->credit;
		}	
	}

?>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Bank Reconciliation Add</span>
		</div>
		<div align="right">
			<?php 
			if(in_array(127,$allowed_pages)){
			$today =date('d-m-Y');
						echo $this->Html->link('<i class="fa fa-puzzle-piece"></i> Bank Reconcilation View',array('controller'=>'Ledgers','action'=>'bankReconciliationView','From'=>date("d-m-Y",strtotime($financial_year->date_from)),'To'=>$today),array('escape'=>false)); 
			} ?>
		</div>
	
	
	<div class="portlet-body form">
	<form method="GET" >
				<table class="table table-condensed" >
				<tbody>
					<tr>
					<td>
						<div class="row">
							<div class="col-md-4">
									<?php echo $this->Form->input('ledger_account_id', ['empty'=>'--Select--','options' => @$banks,'empty' => "--Select Bank--",'label' => false,'class' => 'bank_data form-control input-sm select2me','required','value'=>@$ledger_account_id]); ?>
							</div>
							<?php if(empty($from)){ ?>
							<div class="col-md-4">
								<?php echo $this->Form->input('From', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','value' => @date('01-04-Y', strtotime($from)),'data-date-start-date' => date("d-m-Y",strtotime($financial_year->date_from)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to))]); ?>
							</div>
							<?php }else{ ?>
							<div class="col-md-4">
								<?php echo $this->Form->input('From', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','value' => $from,'data-date-start-date' => date("d-m-Y",strtotime($financial_year->date_from)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to))]); ?>
							</div>		
							<?php } ?>
							<?php if(empty($To)){ ?>
							<div class="col-md-4">
								<?php echo $this->Form->input('	To', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','value' => @date('d-m-Y', strtotime($To)),'data-date-start-date' => date("d-m-Y",strtotime($financial_year->date_from)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to))]); ?>
								
							</div>
							<?php }else{ ?>
							<div class="col-md-4">
							<?php echo $this->Form->input('To', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','value' => @$To,'data-date-start-date' => date("d-m-Y",strtotime($financial_year->date_from)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to))]); ?>
								
							</div>	
							<?php } ?>
						</div>
					</td>
					<td><button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button></td>
					</tr>
				</tbody>
			</table>
	</form>
	
		<!-- BEGIN FORM-->
<?php if(!empty($Bank_Ledgers)){  ?>
	<div class="row " id="hide_div">
		<!-- <div class="col-md-12">
			<div class="col-md-8"></div>	
			<div class="col-md-4 caption-subject " align="left" style="background-color:#E7E2CB; font-size: 16px;"><b>Opening Balance : 
				<?php 
						$opening_balance_data=0;
	
						if(!empty(@$opening_balance_ar)){  
							if(@$opening_balance_ar['debit'] > @$opening_balance_ar['credit']){ 
								$opening_balance_data=@$opening_balance_ar['debit'] - @$opening_balance_ar['credit'];
								echo $this->Number->format(@$opening_balance_data.'Dr',[ 'places' => 2]);	echo " Dr";
							}
							else { 
								$opening_balance_data=@$opening_balance_ar['credit'] - @$opening_balance_ar['debit'];
								echo $this->Number->format(@$opening_balance_data.'Cr',[ 'places' => 2]); echo " Cr";
							}					
						}
						else {   echo $this->Number->format('0',[ 'places' => 2]); }
						
				?>  
			</b>
			
			</div>
		</div> -->
		<div class="col-md-12"> 
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>Transaction Date</th>
						<th>Source</th>
						<th>Source Path</th>
						<th style="text-align:right;">Dr</th>
						<th style="text-align:right;">Cr</th>
						<th>Reconcilation Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php
				
				$total_balance_acc=0; $total_debit=0; $total_credit=0;
				$balance_as_per_total_debit=0; $balance_as_per_total_credit=0;
				foreach($Bank_Ledgers as $ledger):
				
				$emp_id="No";
				$ledger->voucher_id = $EncryptingDecrypting->encryptData($ledger->voucher_id);
				if($ledger->voucher_source=="Journal Voucher"){
					$Receipt=$url_link[$ledger->id];
					//pr($Receipt->voucher_no); 
					$voucher_no=h('#'.str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/JournalVouchers/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Payment Voucher"){
					$Receipt=$url_link[$ledger->id];
					//pr($Receipt->voucher_no);exit;
					$voucher_no=h('#'.str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/Payments/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Petty Cash Payment Voucher"){
					$Receipt=$url_link[$ledger->id];
					//pr($url_link[$ledger->id]);exit;
					$voucher_no=h('#'.str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/petty-cash-vouchers/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Contra Voucher"){
					$Receipt=$url_link[$ledger->id];
					 $voucher_no=h('#'.str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					 $url_path="/contra-vouchers/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Receipt Voucher"){ 
					$Receipt=$url_link[$ledger->id];
					$voucher_no=h('#'.str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/receipts/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Invoice"){ 
					$invoice=$url_link[$ledger->id];
					$voucher_no=h(($invoice->in1.'/IN-'.str_pad($invoice->in2, 3, '0', STR_PAD_LEFT).'/'.$invoice->in3.'/'.$invoice->in4));
					if($invoice->invoice_type=="GST"){
					$url_path="/invoices/gst-confirm/".$ledger->voucher_id;	
					}else{
					$url_path="/invoices/confirm/".$ledger->voucher_id;
					}
					
					if(in_array($invoice->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
					
					
				}else if($ledger->voucher_source=="Sale Return"){ 
					$salereturn=$url_link[$ledger->id];
					//pr($salereturn->sale_return_type); exit; 
					$voucher_no=h(($salereturn->sr1.'/CR-'.str_pad($salereturn->sr2, 3, '0', STR_PAD_LEFT).'/'.$salereturn->sr3.'/'.$salereturn->sr4));
					if($salereturn->sale_return_type=="GST"){
						$url_path="/sale-returns/gst-confirm/".$ledger->voucher_id;	
					}else{
						$url_path="/sale-returns/confirm/".$ledger->voucher_id;	
					}
					
					
					if(in_array($salereturn->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
					
				}else if($ledger->voucher_source=="Invoice Booking"){
					$ibs=$url_link[$ledger->id];
					//pr($ibs); exit;
					$voucher_no=h(($ibs->ib1.'/IB-'.str_pad($ibs->ib2, 3, '0', STR_PAD_LEFT).'/'.$ibs->ib3.'/'.$ibs->ib4));
					if($ibs->gst=="yes"){
						$url_path="/invoice-bookings/gst-invoice-booking-view/".$ledger->voucher_id;	
					}else{
						$url_path="/invoice-bookings/view/".$ledger->voucher_id;
					}
					
					//$url_path="/invoice-bookings/view/".$ledger->voucher_id;
					
					if(in_array($ibs->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Non Print Payment Voucher"){
					$Receipt=$url_link[$ledger->id];
					$voucher_no=h('#'.str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/nppayments/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Debit Notes"){
				
					$Receipt=$url_link[$ledger->id];
					$voucher=('DN/'.str_pad(@$Receipt->voucher_no, 4, '0', STR_PAD_LEFT)); 
					$s_year_from = date("Y",strtotime(@$Receipt->financial_year->date_from));
					$s_year_to = date("Y",strtotime(@$Receipt->financial_year->date_to));
					$fy=(substr($s_year_from, -2).'-'.substr($s_year_to, -2));
					$voucher_no=$voucher.'/'.$fy;					
					$url_path="/debit-notes/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Credit Notes"){
					
					$Receipt=$url_link[$ledger->id];
					$voucher=('CR/'.str_pad(@$Receipt->voucher_no, 4, '0', STR_PAD_LEFT)); 
					$s_year_from = date("Y",strtotime(@$Receipt->financial_year->date_from));
					$s_year_to = date("Y",strtotime(@$Receipt->financial_year->date_to));
					$fy=(substr($s_year_from, -2).'-'.substr($s_year_to, -2));
					$voucher_no=$voucher.'/'.$fy;					
					$url_path="/credit-notes/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Purchase Return"){
					$Receipt=$url_link[$ledger->id];
					$voucher=('DN/'.str_pad(@$Receipt->voucher_no, 4, '0', STR_PAD_LEFT)); 
					$s_year_from = date("Y",strtotime(@$Receipt->financial_year->date_from));
					$s_year_to = date("Y",strtotime(@$Receipt->financial_year->date_to));
					$fy=(substr($s_year_from, -2).'-'.substr($s_year_to, -2));
					$voucher_no=$voucher.'/'.$fy;
					//$voucher_no=h('DN/',str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					if($Receipt->gst_type=="Gst"){
						$url_path="/PurchaseReturns/gstView/".$ledger->voucher_id;	
					}else{
						$url_path="/PurchaseReturns/View/".$ledger->voucher_id;	
					}
					//$url_path="/purchase-returns/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}
				
				
				
				if(date('Y-m-d',strtotime($ledger->reconciliation_date))=="1970-01-01" or strtotime($ledger->reconciliation_date)>strtotime($transaction_to_date)){
				?>
				<tr class="main_tr">
						<td><?php echo date("d-m-Y",strtotime($ledger->transaction_date));  ?></td>
						<td><?= h($ledger->voucher_source); ?></td>
						<td>
							<?php
							if($emp_id=="Yes"){
								if(!empty($url_path)){
									echo $this->Html->link($voucher_no ,$url_path,['target' => '_blank']);
								}else{
									echo str_pad($ledger->voucher_id,4,'0',STR_PAD_LEFT);
								}
							}else{
								echo $voucher_no;
							}
						?>
						</td>
						<td align="right"><?= $this->Number->format($ledger->debit,[ 'places' => 2]); 
							$total_debit+=$ledger->debit; ?></td>
						<td align="right"><?= $this->Number->format($ledger->credit,[ 'places' => 2]); 
							$total_credit+=$ledger->credit; ?></td>
						<td>
						<?php if(empty($ledger->reconciliation_date)){  ?>
						
							<?php echo $this->Form->input('reconciliation_date', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker reconciliation_date','data-date-format' => 'dd-mm-yyyy','data-date-start-date' => date("d-m-Y",strtotime($ledger->transaction_date)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to)),'placeholder' => 'Reconcilation Date','ledger_id'=>$ledger->id,'required']); 
						}else{  ?>
							<?php echo $this->Form->input('reconciliation_date', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker reconciliation_date','data-date-format' => 'dd-mm-yyyy','data-date-start-date' => date("d-m-Y",strtotime($ledger->transaction_date)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to)),'placeholder' => 'Reconcilation Date','ledger_id'=>$ledger->id,'required','value'=>date('d-m-Y',strtotime($ledger->reconciliation_date))]); ?>
						<?php } ?></td>
						<td>
							<button type="button" ledger_id=<?php echo $ledger->id ?> class="btn btn-primary btn-sm subdate"><i class="fa fa-arrow-right" ></i></button>	
						</td>
				</tr>
				<?php } ?>
				<?php  endforeach; ?>
				<tr>
					<td colspan="2" align="right">Total</td>
					<td align="right" ><?php echo $this->Number->format($total_debit,['places'=>2]) ;?> Dr</td>
					<td align="right" ><?php echo $this->Number->format($total_credit,['places'=>2]); ?> Cr</td>
					<td align="right" ></td>
					<td align="right" ></td>
				<tr>
				</tbody>
			</table>
			</div>
			<!-- <div class="col-md-12">
				<div class="col-md-8"></div>	
				<div class="col-md-4 caption-subject " align="left" style="background-color:#E3F2EE; font-size: 16px;"><b>Closing Balance:  </b>
				<?php 
				/////
				$close_dr=0;$close_cr=0;
				
						if((@$opening_balance_ar['debit'] > 0) || (@$opening_balance_ar['credit'] > 0)){  
							if(@$opening_balance_ar['debit'] > @$opening_balance_ar['credit']){
								
									 $close_dr=@$opening_balance_data-@$total_debit;
									 $close_cr=@$total_credit;
								
							}
							else if(@$opening_balance_ar['credit'] > @$opening_balance_ar['debit']){ 
							
								$close_cr=@$opening_balance_data-@$total_credit;
								$close_dr=@$total_debit;
							 
							}else if($opening_balance_ar['debit']== $opening_balance_ar['credit']){ 
								if(@$closing_balance_ar['debit'] > @$closing_balance_ar['credit']){   
								$close_dr=@$closing_balance_ar['debit'];
								$close_cr=@$closing_balance_ar['credit'];
								}else{
									$close_dr=@$closing_balance_ar['debit'];
									$close_cr=@$closing_balance_ar['credit'];
								}
							}
							}else if((@$opening_balance_ar['debit']== 0) && (@$opening_balance_ar['credit']== 0)){ 
								$close_dr=@$total_debit;
								$close_cr=@$total_credit;
								
							}else if($opening_balance_ar['debit']== $opening_balance_ar['credit']){ 
								if(@$closing_balance_ar['debit'] > @$closing_balance_ar['credit']){ 
								$close_dr=@$closing_balance_ar['debit'];
								$close_cr=@$closing_balance_ar['credit'];
								}else{
									$close_dr=@$closing_balance_ar['debit'];
									$close_cr=@$closing_balance_ar['credit'];
								}
							}
			
				///////
				
				$closing_balance=@$close_dr+@$close_cr;
					
						echo $this->Number->format(abs($closing_balance),['places'=>2]);
						if($closing_balance>0){
							echo 'Dr';
						}else if($closing_balance <0){
							echo 'Cr';
						}
						
				?>
				</div>
			</div> -->
			<div class="col-md-12"><br/></div>
			<div class="col-md-12">
				<div class="col-md-4"></div>
				<div class="col-md-4" align="left" style="background-color:#E7E2CB; font-size: 16px;">
					<b>Balance as per Books : 
						<?php echo @$balance_as_per_book_data; ?>
					</b>
				</div>
				<div class="col-md-4" align="left" style="background-color:#E3F2EE; font-size: 16px;">
					<b>Balance as per Bank : </b>
					
					<?php 
				/////
				$close_dr=0;$close_cr=0;
				
						if((@$opening_balance_ar['debit'] > 0) || (@$opening_balance_ar['credit'] > 0)){  
							if(@$opening_balance_ar['debit'] > @$opening_balance_ar['credit']){
								
									 $close_dr=@$opening_balance_data-@$total_debit;
									 $close_cr=@$total_credit;
								
							}
							else if(@$opening_balance_ar['credit'] > @$opening_balance_ar['debit']){ 
							
								$close_cr=@$opening_balance_data-@$total_credit;
								$close_dr=@$total_debit;
							 
							}else if($opening_balance_ar['debit']== $opening_balance_ar['credit']){ 
								if(@$closing_balance_ar['debit'] > @$closing_balance_ar['credit']){   
								$close_dr=@$closing_balance_ar['debit'];
								$close_cr=@$closing_balance_ar['credit'];
								}else{
									$close_dr=@$closing_balance_ar['debit'];
									$close_cr=@$closing_balance_ar['credit'];
								}
							}
							}else if((@$opening_balance_ar['debit']== 0) && (@$opening_balance_ar['credit']== 0)){ 
								$close_dr=@$total_debit;
								$close_cr=@$total_credit;
								
							}else if($opening_balance_ar['debit']== $opening_balance_ar['credit']){ 
								if(@$closing_balance_ar['debit'] > @$closing_balance_ar['credit']){ 
								$close_dr=@$closing_balance_ar['debit'];
								$close_cr=@$closing_balance_ar['credit'];
								}else{
									$close_dr=@$closing_balance_ar['debit'];
									$close_cr=@$closing_balance_ar['credit'];
								}
							}
			
				///////
				
				$closing_balance=@$close_dr+@$close_cr;
					
						echo $this->Number->format(abs($closing_balance),['places'=>2]);
						if($closing_balance>0){
							echo 'Dr';
						}else if($closing_balance <0){
							echo 'Cr';
						}
						
				?>
				</div>
			</div>
			<!--<div class="col-md-12">
				<div class="col-md-8"></div>
				<div class="col-md-4" align="left" style="font-size: 16px;">
					<b>Balance as per Bank : </b>
				</div>
			</div>	-->
		</div>
<?php } ?>
</div></div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script>
$(document).ready(function() {
	$('.subdate').die().click(function() { 
	var t=$(this);
		var ledger_id=$(this).attr('ledger_id');
		var reconciliation_date=$(this).closest('tr.main_tr').find('.reconciliation_date').val();
		if(reconciliation_date == ""){
			alert("Please Select Reconcilation Date");
		}else{
			var url="<?php echo $this->Url->build(['controller'=>'Ledgers','action'=>'dateUpdate']); ?>";
			url=url+'/'+ledger_id+'/'+reconciliation_date,
			$.ajax({
				url: url,
			}).done(function(response) { 
				t.closest("tr").hide();
				 window.location.reload(true);
			});
		}
		
    });
	
	$('.bank_data').die().live("change",function() { 
		$("#hide_div").hide();
	});

});
</script>

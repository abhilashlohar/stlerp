<style>

</style>
<div style="background-color:#FFF">
<table class="table table-condensed " width="100%">
	<thead>
		<tr>
			<th>Sr. No.</th>
			<th>Transaction Date</th>
			<th>Party</th>
			<th>Voucher Source</th>
			<th>Voucher No.</th>
			<th>In</th>
			<th>Out</th>
			<th style="text-align:right;">Unit Rate</th>
		</tr>
	</thead>
	<tbody>

		<?php $page_no=0; 
		 foreach ($itemLedgers as $itemLedger): 
		 
		
		 
		$rate = $itemLedger->rate;
		$in_out_type=$itemLedger->in_out;
		$party=$itemLedger->party_type;
		$url_path="";
		$data="";
		$source_model=$itemLedger->source_model;
		//@$itemLedger->voucher_info->id = $EncryptingDecrypting->encryptData(@$itemLedger->voucher_info->id);
		//pr($source_model);exit;
		if($party!='Item'){
			  $itemLedger->voucher_info->id = $EncryptingDecrypting->encryptData($itemLedger->voucher_info->id);
		 }
		if($source_model=='Challan')
		{	
			$created_by = $itemLedger->voucher_info->created_by;
			if($itemLedger->party_type=='Vendor'){
				$party_name=$itemLedger->party_info->company_name;
			}else{
				$party_name=$itemLedger->party_info->customer_name;
			}
			$voucher_no=$itemLedger->voucher_info->ch1.'/CH-'.str_pad($itemLedger->voucher_info->ch2, 3, '0', STR_PAD_LEFT).'/'.$itemLedger->voucher_info->ch3.'/'.$itemLedger->voucher_info->ch4;
		}
		
		else if($party=='Customer')
		{ 
			$party_name=$itemLedger->party_info->customer_name;
			$created_by = $itemLedger->voucher_info->created_by;
			$voucher_no=$itemLedger->voucher_info->in1.'/IN-'.str_pad($itemLedger->voucher_info->in2, 3, '0', STR_PAD_LEFT).'/'.$itemLedger->voucher_info->in3.'/'.$itemLedger->voucher_info->in4;
			
			if($itemLedger->voucher_info->invoice_type=='GST'){ 
				$url_path="/Invoices/gst-confirm/".$itemLedger->voucher_info->id;
			
			}else{
				$url_path="/Invoices/confirm/".$itemLedger->voucher_info->id;
			}
			
		}
		else if($party=='Supplier')
		{
			$data=$itemLedger->voucher_info->transaction_date;
			//pr($data);exit;
			$created_by = $itemLedger->voucher_info->created_by;
			$party_name=$itemLedger->party_info->company_name;
			$voucher_no='-';
			
		}
		else if($party=='Item')
		{
			$party_name='-';
			$voucher_no='-';
		}
		else if($source_model=='Purchase Return')
		{
			$created_by = $itemLedger->voucher_info->created_by; 
			$party_name=$itemLedger->party_info->company_name;
			$voucher=('DN/'.str_pad(@$itemLedger->voucher_info->voucher_no, 4, '0', STR_PAD_LEFT)); 
					$s_year_from = date("Y",strtotime(@$itemLedger->voucher_info->financial_year->date_from));
					$s_year_to = date("Y",strtotime(@$itemLedger->voucher_info->financial_year->date_to));
					$fy=(substr($s_year_from, -2).'-'.substr($s_year_to, -2));
					$voucher_no=$voucher.'/'.$fy;
			//$voucher_no= '#'.str_pad($itemLedger->voucher_info->voucher_no, 4, '0', STR_PAD_LEFT); 
			if($itemLedger->voucher_info->gst_type == "Non-GST"){
				$url_path="/PurchaseReturns/view/".$itemLedger->voucher_info->id;
			}else{
				$url_path="/PurchaseReturns/gstView/".$itemLedger->voucher_info->id;
			}
		}
		else if($source_model=='Sale Return')
		{ 
			$party_name=$itemLedger->party_info->customer_name;
			$created_by = $itemLedger->voucher_info->created_by;
			$voucher_no=$itemLedger->voucher_info->sr1.'/CR-'.str_pad($itemLedger->voucher_info->sr2, 3, '0', STR_PAD_LEFT).'/'.$itemLedger->voucher_info->sr3.'/'.$itemLedger->voucher_info->sr4;
			if($itemLedger->voucher_info->sale_return_type == "Non-GST"){
				$url_path="/sale-returns/confirm/".$itemLedger->voucher_info->id;
			}else{
				$url_path="/sale-returns/gst-confirm/".$itemLedger->voucher_info->id;
			}
			
			
		}
		else if($source_model=='Inventory Transfer Voucher')
		{ 
			$party_name=$itemLedger->voucher_info;
			$created_by = $itemLedger->voucher_info->created_by;
			//pr($itemLedger->party_info); exit;
			$party_name='-';
			$voucher_no='#'.str_pad($itemLedger->voucher_info->voucher_no, 4, '0', STR_PAD_LEFT);
			if($itemLedger->voucher_info->in_out=='in_out'){
				$url_path="/inventory-transfer-vouchers/view/".$itemLedger->voucher_info->id;
			}else if($itemLedger->voucher_info->in_out=='In'){
				$url_path="/inventory-transfer-vouchers/inView/".$itemLedger->voucher_info->id;
			}else{
				$url_path="/inventory-transfer-vouchers/outView/".$itemLedger->voucher_info->id;
			}
			$data=$itemLedger->voucher_info->transaction_date;
		}
		else if($source_model=='Inventory Vouchers')
		{ 
			
			$party_name='-';
			$voucher_no='#'.str_pad($itemLedger->voucher_info->voucher_no, 4, '0', STR_PAD_LEFT);
			$created_by = $itemLedger->voucher_info->created_by;
			$url_path="/ivs/view/".$itemLedger->voucher_info->id;
			$data=$itemLedger->voucher_info->transaction_date;
			//pr($itemLedger);
		} else if($source_model=='Inventory Return')
		{
			$party_name='-';
			$voucher_no='#'.str_pad($itemLedger->voucher_info->voucher_no, 4, '0', STR_PAD_LEFT);
			$url_path="/Rivs/view/".$itemLedger->voucher_info->id;
			//pr($voucher_no);
		} 
		
		else if($source_model=='Grns')
		{ 
			$party_name = $itemLedger->party_info->company_name;
			$created_by = $itemLedger->voucher_info->created_by; //pr($itemLedger->voucher_info->created_by);pr($allowed_emp); exit;
			$voucher_no=$itemLedger->voucher_info->grn1.'/GRN-'.str_pad($itemLedger->voucher_info->grn2, 3, '0', STR_PAD_LEFT).'/'.$itemLedger->voucher_info->grn3.'/'.$itemLedger->voucher_info->grn4;
			$data=$itemLedger->voucher_info->transaction_date;
			$url_path="/grns/view/".$itemLedger->voucher_info->id;
		}
		else{
			$party_name='-';
			$voucher_no='#'.str_pad($itemLedger->voucher_info->iv_number, 4, '0', STR_PAD_LEFT);
		}
		$status_color=false;
		$status= '-';
		if($in_out_type=='Out'){
			if($itemLedger->voucher_info->challan_type=='Returnable'){
				$status_color=true;
				$status=$this->Number->format($itemLedger->quantity);
			} else{
				$status= $this->Number->format($itemLedger->quantity);
			}
		}
			
		?>
		<tr <?php if($status_color==true){ echo 'style="background-color:red;color:white"'; } ?>>
			
			<td><?= h(++$page_no) ?></td>
			<td>
			<?php if(!empty($data)){ ?>
			<?= h(date("d-m-Y",strtotime(@$data))) ?>
			<?php }else{ ?>
			<?= h(date("d-m-Y",strtotime(@$itemLedger->processed_on))) ?>
			<?php } ?>
			</td>
			
			<td><?= h($party_name) ?></td>
			<td><?= h($itemLedger->source_model) ?></td>
			<td>
			<?php  if(in_array(@$created_by,$allowed_emp)){ 
					if(!empty($url_path)){ 
						echo $this->Html->link($voucher_no ,$url_path,['target' => '_blank']); 
					}}else{
					echo $voucher_no;
			}
			?>
			</td>
			<td><?php if($in_out_type=='In'){ echo $itemLedger->quantity; } else { echo '-'; } ?></td>
			<td><?php echo $status; ?></td>
			<td align="right"><?php echo $this->Money->indianNumberFormat($rate); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>

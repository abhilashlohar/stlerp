
<div style="border:solid 1px #c7c7c7;background-color: #FFF;padding: 10px;margin: auto;width: 55%;font-size: 12px;" class="maindiv">	
	<table width="100%" class="divHeader">
		<tr>
			<td width="30%"><?php echo $this->Html->image('http://mogragroup.in/logos/'.$payment->company->logo, ['width' => '80px','fullBase' => true]); ?></td>
			<td align="center" width="30%" style="font-size: 12px;"><div align="center" style="font-size: 16px;font-weight: bold;color: #0685a8;">PAYMENT VOUCHER</div></td>
			<td align="right" width="40%" style="font-size: 12px;">
			<span style="font-size: 14px;"><?= h($payment->company->name) ?></span>
			<span><?= $this->Text->autoParagraph(h($payment->company->address)) ?></span>
			<span> <i class="fa fa-phone" aria-hidden="true"></i> <?= h($payment->company->landline_no) ?></span> |
			<?= h($payment->company->mobile_no) ?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div style="border:solid 2px #0685a8;margin-bottom:5px;margin-top: 5px;"></div>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td width="50%" valign="top" align="left">
				<table>
					
					<tr>
						<td>Voucher No</td>
						<td width="20" align="center">:</td>
						<td><?= h('#'.str_pad($payment->voucher_no, 4, '0', STR_PAD_LEFT)) ?></td>
					</tr>
				</table>
			</td>
			<td width="50%" valign="top" align="right">
				<table>
					<tr>
						<td>Transaction Date</td>
						<td width="20" align="center">:</td>
						<td><?= h(date("d-m-Y",strtotime($payment->transaction_date))) ?></td>
					</tr>
					
				</table>
			</td>
		</tr>
	</table>
	<br/>
	<table width="100%" class="table" style="font-size:12px">
		<tr>
			<th align="left"><?= __('Paid to') ?></th>
			<th align="left"><?= __('Amount') ?></th>
			<th align="left"><?= __('Narration') ?></th>
		</tr>
		<?php $total_cr=0; $total_dr=0; foreach ($payment->payment_rows as $paymentRows):  ?>
		<!--<?php $name=""; if(empty($paymentRows->ReceivedFrom->alias)){
			$name=$paymentRows->ReceivedFrom->name;
		} else{
			$name=$paymentRows->ReceivedFrom->name.'('; echo $paymentRows->ReceivedFrom->alias.')'; 
		}?>-->
		<tr>
			<td style="white-space: nowrap; ">
			<?php $name=""; if(empty($paymentRows->ReceivedFrom->alias)){
			 echo $paymentRows->ReceivedFrom->name;
			} else{
				 echo $paymentRows->ReceivedFrom->name.'('; echo $paymentRows->ReceivedFrom->alias.')'; 
			}?>
			</td>
			
			<td style="white-space: nowrap;"><?= h($this->Number->format($paymentRows->amount,[ 'places' => 2])) ?> <?= h($paymentRows->cr_dr) ?></td>
			<td><?= h($paymentRows->narration) ?></td>
			
		</tr>
		<?php if(!empty($ref_details[$paymentRows->received_from_id])):?>
		<tr >

		<td colspan="3" style="border-top:none !important;">
			<table width="100%">
			
			<?php $dr_amt=0; $cr_amt=0; foreach($paymentRows->reference_details as $refdetail):
			
			?>
			<tr>
					<td style="width :180px !important;"> <?= h($refdetail->reference_type). '-' .h($refdetail->reference_no) ?></td>
					<td>:</td>
					<td > <?php if($refdetail->credit != '0' ){ ?> 
					<?= h($this->Number->format($refdetail->credit,['places'=>2])) ?> Cr 
					<?php } elseif( $refdetail->debit != '0'){?>
					<?= h($this->Number->format($refdetail->debit,['places'=>2])) ?> Dr
					<?php } ?></td>
					</tr>
					
					<?php 
					
					if($refdetail->credit != '0' ){ 
						$cr_amt=$cr_amt+$refdetail->credit;
					} elseif( $refdetail->debit != '0'){
						$dr_amt=$dr_amt+$refdetail->debit;
					} ?>
					
			<?php endforeach; 	?>
			 <?php 
				if($paymentRows->cr_dr == 'Dr' ){ 
					$on_acc=$paymentRows->amount-($dr_amt-$cr_amt);

					if($on_acc > 0) {?>
						<tr>
							<td style="width :180px !important;"> <?php echo "On Account";  ?></td>
							<td>:</td>
							<td > <?= h($this->Number->format($on_acc,['places'=>2])); ?>Dr
								
							</td>
						</tr>
					<?php }} elseif( $paymentRows->cr_dr == 'Cr'){
						$on_acc1=$paymentRows->amount-($cr_amt-$dr_amt);

					if($on_acc1 > 0) {?>
						<tr>
							<td style="width :180px !important;"> <?php echo "On Account";  ?></td>
							<td>:</td>
							<td > <?= h($this->Number->format($on_acc1,['places'=>2])); ?>Cr
					<?php }} ?>
							</td>
						</tr>
			 
			</table>
		</td>
		</tr><?php endif; ?>
		<?php if($paymentRows->cr_dr=="Cr"){
			$total_cr=$total_cr+$paymentRows->amount;
		}else{
			$total_dr=$total_dr+$paymentRows->amount;

		}
		$total=$total_dr-$total_cr; endforeach; ?>
	</table>
	<?php ?>
	
	
	<div style="border:solid 1px ;"></div>
	<table width="100%" class="divFooter">
		<tr>
			<td align="left" valign="top">
				<table>
					<tr>
						<td style="font-size: 16px;font-weight: bold;">
						Rs: <?= h($this->Number->format($total,[ 'places' => 2])) ?></td>
					</tr>
					<tr>
						<td style="font-size: 12px;">Rupees <?php echo ucwords($this->NumberWords->convert_number_to_words($total)) ?> Only </td>
					</tr>
					<tr>
						<td style="font-size: 12px;">
						via <?= h($payment->payment_mode) ?> 
						<?php if($payment->payment_mode=="Cheque"){
							echo ' ('.$payment->cheque_no.')';
						} ?>
						</td>
					</tr>
				</table>
			</td>
		    <td align="right" valign="top" width="35%">
				<table style="margin-top:3px;">
					<tr>
					   <td width="15%" align="center"> 
						<?php 
						 echo $this->Html->Image('http://mogragroup.in/signatures/'.$payment->creator->signature,['height'=>'40px','style'=>'height:40px;']); 
						 ?><br/>
						 </hr>
						 <span><b>Prepared By</b></span><br/>
						 <span><?= h($payment->company->name) ?></span><br/>
						</td>
					</tr>
				</table>
			 </td>
		</tr>
	</table>
</div>

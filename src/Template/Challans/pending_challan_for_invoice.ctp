<?php //pr($challans); exit; ?>
<div class="portlet light bordered">

	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Pending Challans</span>
		
		</div>
		<div class="actions">
			<div class="btn-group">
			<?= $this->Html->link(
				'Pending',
				'/Challans/PendingChallanForInvoice',
				['class' => 'btn btn-primary']
			); ?>
			<?= $this->Html->link(
				'Converted',
				'/Challans/Index2',
				['class' => 'btn btn-default']
			); ?>
			</div>
		</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-12">
			<?php $page_no=$this->Paginator->current('Challans'); $page_no=($page_no-1)*20; ?>
				<table class="table table-bordered table-striped table-hover">
						<thead>
							<tr>
								<th>S.No</th>
								<th>Challan.No</th>
								<th>
								<?php if(!empty($challan->customer)){	
									echo "Customer";
								}else{
									echo "Supplier";
								}?>
								</th>
								<th>Actions</th>
							</tr>
					
					</thead>

					<tbody>
            <?php foreach ($Challans as $challan):  
			$challan->id = $EncryptingDecrypting->encryptData($challan->id); 
			?>
            <tr>
                <td><?= h(++$page_no) ?></td>
				<td><?= h(($challan->ch1.'/CH-'.str_pad($challan->ch2, 3, '0', STR_PAD_LEFT).'/'.$challan->ch3.'/'.$challan->ch4)) ?></td>
                <td><?php
				if(!empty($challan->customer)){
					if(!empty($challan->customer->alias)){
						echo $challan->customer->customer_name.'('.$challan->customer->alias.')'; 
					}else{
						echo $challan->customer->customer_name; 
					}
					
				}else{
					echo $challan->vendor->company_name; 
				}
				?></td>            
				<td class="actions">
								<?php if(in_array(29,$allowed_pages)){  ?>
								<?php echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'confirm', $challan->id],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs yellow tooltips','data-original-title'=>'View as PDF')); ?>
								<?php } ?>
								<?php if(in_array(12,$allowed_pages)){  ?>
								<?php echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'edit', $challan->id],array('escape'=>false,'class'=>'btn btn-xs blue tooltips','data-original-title'=>'Edit')); ?>
								
								<?= $this->Form->postLink('Invoice Create',
								['action' => 'ConvertedIntoInvoice', $challan->id],
								[
									'escape' => false,
									'class'=>' btn btn-xs green tooltips'
									
								]
								) ?>
								<?php } ?>
				</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
				</table>
				<div class="paginator">
					<ul class="pagination">
						<?= $this->Paginator->prev('< ' . __('previous')) ?>
						<?= $this->Paginator->numbers() ?>
						<?= $this->Paginator->next(__('next') . ' >') ?>
					</ul>
					<p><?= $this->Paginator->counter() ?></p>
				</div>
			</div>
		</div>
	</div>
</div>


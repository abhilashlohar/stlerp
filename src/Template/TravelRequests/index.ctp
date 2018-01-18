<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel ucfirst">Travel Requests</span> 
		</div>
		<div class="actions">
			
			
		</div>	
	
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-12">
				<form method="GET" >
				
				</form>
				<?php $page_no=$this->Paginator->current('travelRequests'); $page_no=($page_no-1)*20; 
					
				?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="5%">Sr. No.</th>
							<th width="15%">Employee Name</th>
							<th width="15%">Employee Designation</th>
							<th width="15%">Purpose</th>
							<th width="15%">Advance Amount</th>
							<th width="15%">Travel From Date</th>
							<th width="15%">Travel To Date</th>
							<th width="10%" class="actions"><?= __('Actions') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $i=0; foreach ($travelRequests as $travelRequest):  ?>
						<tr>
							<td><?= h(++$page_no) ?></td>
							<td><?= h($travelRequest->employee_name) ?></td>
							<td><?= h($travelRequest->employee_designation) ?></td>
							<td><?= h($travelRequest->purpose) ?></td>
							<td><?= h($travelRequest->advance_amt) ?></td>
							<td><?= h(date("d-m-Y",strtotime($travelRequest->travel_from_date))) ?></td>
							<td><?= h(date("d-m-Y",strtotime($travelRequest->travel_to_date))) ?></td>
							<td class="actions">
								<?php echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'edit', $travelRequest->id],array('escape'=>false,'class'=>'btn btn-xs blue')); ?>
								<?= $this->Form->postLink('<i class="fa fa-trash"></i> ',
								['action' => 'delete', $travelRequest->id], 
								[
									'escape' => false,
									'class' => 'btn btn-xs btn-danger',
									'confirm' => __('Are you sure ?', $travelRequest->id)
								]
							) ?>
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
<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

/**
 * InventoryTransferVouchers Controller
 *
 * @property \App\Model\Table\InventoryTransferVouchersTable $InventoryTransferVouchers
 */
class InventoryTransferVouchersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$st_year_id = $session->read('st_year_id');
		$where = [];
        $vendor_id = $this->request->query('vendor_id');
        $customers_id = $this->request->query('customers_id');
        $vouch_no = $this->request->query('vouch_no');
		$From = $this->request->query('From');
		$To = $this->request->query('To');
		//pr($vouch_no);exit;
		
		$this->set(compact('vouch_no','From','To','customers_id','vendor_id'));
		
		if(!empty($vouch_no)){
			$where['InventoryTransferVouchers.voucher_no LIKE']=$vouch_no;
		}
		if(!empty($customers_id)){
			$where['InventoryTransferVouchers.customer_id']=$customers_id;
		}
		if(!empty($vendor_id)){
			$where['InventoryTransferVouchers.vendor_id']=$vendor_id;
		}
		
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['InventoryTransferVouchers.transaction_date >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['InventoryTransferVouchers.transaction_date <=']=$To;
		}
		/* $this->paginate = [
            'contain' => ['Companies']
        ]; */
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['InventoryTransferVouchers.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
		$inventory_transfer_vouchs = $this->InventoryTransferVouchers->find()->contain(['Companies','Customers','Vendors'])->where($where)->where(['InventoryTransferVouchers.company_id'=>$st_company_id,'InventoryTransferVouchers.financial_year_id'=>$st_year_id])->where($wheree)->order(['InventoryTransferVouchers.voucher_no' => 'DESC']);
		
		$customers = $this->InventoryTransferVouchers->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->contain(['CustomerAddress'=>function($q){
			return $q
			->where(['CustomerAddress.default_address'=>1]);
		}])->matching(
					'CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		
		$vendor = $this->InventoryTransferVouchers->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);
		//pr($inventory_transfer_vouchs->toArray());exit;
		
		
		
        /* $inventoryTransferVouchers = $this->paginate($this->InventoryTransferVouchers->find()->where(['company_id'=>$st_company_id])->order(['InventoryTransferVouchers.voucher_no' => 'DESC'])); */

        $this->set(compact('inventoryTransferVouchers','inventory_transfer_vouchs','url','customers','vendor'));
        $this->set('_serialize', ['inventoryTransferVouchers']);
    }
	
/* 	public function DataMigrate()
	{
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$InventoryTransferVouchers =$this->InventoryTransferVouchers->find()->contain('InventoryTransferVoucherRows')->toArray();
		foreach($InventoryTransferVouchers as $InventoryTransferVoucher){
			
			if($InventoryTransferVoucher->in_out=="In"){
				foreach($InventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row){
						@$NewSerialNumbers=$this->InventoryTransferVouchers->ItemLedgers->NewSerialNumbers->find()->where(['inventory_transfer_voucher_id'=>$InventoryTransferVoucher->id,'item_id'=>$inventory_transfer_voucher_row->item_id])->toArray();
						
						if($NewSerialNumbers){ 
							foreach(@$NewSerialNumbers as $NewSerialNumber){
								
								$SerialNumber = $this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->newEntity();
								$SerialNumber->item_id = $NewSerialNumber->item_id;
								$SerialNumber->name = $NewSerialNumber->serial_no;
								$SerialNumber->status = 'In';
								$SerialNumber->company_id = $NewSerialNumber->company_id;
								$SerialNumber->itv_row_id = $inventory_transfer_voucher_row->id;
								$SerialNumber->itv_id = $InventoryTransferVoucher->id;
								$SerialNumber->transaction_date =$InventoryTransferVoucher->transaction_date;  
								$this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->save($SerialNumber);
							}
						}
						
								$ItemLedger = $this->InventoryTransferVouchers->ItemLedgers->newEntity();
								$ItemLedger->item_id = $inventory_transfer_voucher_row->item_id;
								$ItemLedger->quantity = $inventory_transfer_voucher_row->quantity;
								$ItemLedger->rate = $inventory_transfer_voucher_row->amount;
								$ItemLedger->in_out = 'In';
								$ItemLedger->source_model = 'Inventory Transfer Voucher';
								$ItemLedger->company_id = $InventoryTransferVoucher->company_id;
								$ItemLedger->source_id = $InventoryTransferVoucher->id;
								$ItemLedger->source_row_id = $inventory_transfer_voucher_row->id;
								$ItemLedger->processed_on =$InventoryTransferVoucher->transaction_date;  
								$this->InventoryTransferVouchers->ItemLedgers->save($ItemLedger);
						
					}
				}else if($InventoryTransferVoucher->in_out=="Out"){
						foreach($InventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row){
						@$NewSerialNumbers=$this->InventoryTransferVouchers->ItemLedgers->NewSerialNumbers->find()->where(['inventory_transfer_voucher_id'=>$InventoryTransferVoucher->id,'item_id'=>$inventory_transfer_voucher_row->item_id])->toArray();
						
						if($NewSerialNumbers){
							foreach(@$NewSerialNumbers as $NewSerialNumber){
								$SerialNumberData = $this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->find()->where(['item_id'=>$NewSerialNumber->item_id,'name'=>$NewSerialNumber->serial_no,'status'=>'In','company_id'=>$InventoryTransferVoucher->company_id])->first(); 
								
								$SerialNumber = $this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->newEntity();
								$SerialNumber->item_id = $SerialNumberData->item_id;
								$SerialNumber->name = $SerialNumberData->name;
								$SerialNumber->status = 'Out';
								$SerialNumber->company_id = $NewSerialNumber->company_id;
								$SerialNumber->parent_id = $SerialNumberData->id;
								$SerialNumber->itv_row_id = $inventory_transfer_voucher_row->id;
								$SerialNumber->itv_id = $InventoryTransferVoucher->id;
								$SerialNumber->transaction_date =$InventoryTransferVoucher->transaction_date;  
								$this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->save($SerialNumber);
							}
						}
						
								$ItemLedger = $this->InventoryTransferVouchers->ItemLedgers->newEntity();
								$ItemLedger->item_id = $inventory_transfer_voucher_row->item_id;
								$ItemLedger->quantity = $inventory_transfer_voucher_row->quantity;
								$ItemLedger->in_out = 'Out';
								$ItemLedger->source_model = 'Inventory Transfer Voucher';
								$ItemLedger->company_id = $InventoryTransferVoucher->company_id;
								$ItemLedger->source_id = $InventoryTransferVoucher->id;
								$ItemLedger->source_row_id = $inventory_transfer_voucher_row->id;
								$ItemLedger->processed_on =$InventoryTransferVoucher->transaction_date;  
								$this->InventoryTransferVouchers->ItemLedgers->save($ItemLedger);
					}
				}else{
					foreach($InventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row){
						if($inventory_transfer_voucher_row->status=="in"){
							@$NewSerialNumbers=$this->InventoryTransferVouchers->ItemLedgers->NewSerialNumbers->find()->where(['inventory_transfer_voucher_id'=>$InventoryTransferVoucher->id,'item_id'=>$inventory_transfer_voucher_row->item_id])->toArray();
						
							if($NewSerialNumbers){ 
								foreach(@$NewSerialNumbers as $NewSerialNumber){
									
									$SerialNumber = $this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->newEntity();
									$SerialNumber->item_id = $NewSerialNumber->item_id;
									$SerialNumber->name = $NewSerialNumber->serial_no;
									$SerialNumber->status = 'In';
									$SerialNumber->company_id = $NewSerialNumber->company_id;
									$SerialNumber->itv_row_id = $inventory_transfer_voucher_row->id;
									$SerialNumber->itv_id = $InventoryTransferVoucher->id;
									$SerialNumber->transaction_date =$InventoryTransferVoucher->transaction_date;  
									$this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->save($SerialNumber);
								}
							}
						
								$ItemLedger = $this->InventoryTransferVouchers->ItemLedgers->newEntity();
								$ItemLedger->item_id = $inventory_transfer_voucher_row->item_id;
								$ItemLedger->quantity = $inventory_transfer_voucher_row->quantity;
								$ItemLedger->rate = $inventory_transfer_voucher_row->amount;
								$ItemLedger->in_out = 'In';
								$ItemLedger->source_model = 'Inventory Transfer Voucher';
								$ItemLedger->company_id = $InventoryTransferVoucher->company_id;
								$ItemLedger->source_id = $InventoryTransferVoucher->id;
								$ItemLedger->source_row_id = $inventory_transfer_voucher_row->id;
								$ItemLedger->processed_on =$InventoryTransferVoucher->transaction_date;  
								$this->InventoryTransferVouchers->ItemLedgers->save($ItemLedger);
						}else{
							@$NewSerialNumbers=$this->InventoryTransferVouchers->ItemLedgers->NewSerialNumbers->find()->where(['inventory_transfer_voucher_id'=>$InventoryTransferVoucher->id,'item_id'=>$inventory_transfer_voucher_row->item_id])->toArray();
							
							if($NewSerialNumbers){ 
								foreach(@$NewSerialNumbers as $NewSerialNumber){
									$SerialNumberData = $this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->find()->where(['item_id'=>$NewSerialNumber->item_id,'name'=>$NewSerialNumber->serial_no,'status'=>'In','company_id'=>$InventoryTransferVoucher->company_id])->first(); 
									
									
									$SerialNumber = $this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->newEntity();
									$SerialNumber->item_id = $SerialNumberData->item_id;
									$SerialNumber->name = $SerialNumberData->name;
									$SerialNumber->status = 'Out';
									$SerialNumber->company_id = $NewSerialNumber->company_id;
									$SerialNumber->parent_id = $SerialNumberData->id;
									$SerialNumber->itv_row_id = $inventory_transfer_voucher_row->id;
									$SerialNumber->itv_id = $InventoryTransferVoucher->id;
									$SerialNumber->transaction_date =$InventoryTransferVoucher->transaction_date;  
									$this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->save($SerialNumber);
								}
							}
						
								$ItemLedger = $this->InventoryTransferVouchers->ItemLedgers->newEntity();
								$ItemLedger->item_id = $inventory_transfer_voucher_row->item_id;
								$ItemLedger->quantity = $inventory_transfer_voucher_row->quantity;
								$ItemLedger->in_out = 'Out';
								$ItemLedger->source_model = 'Inventory Transfer Voucher';
								$ItemLedger->company_id = $InventoryTransferVoucher->company_id;
								$ItemLedger->source_id = $InventoryTransferVoucher->id;
								$ItemLedger->source_row_id = $inventory_transfer_voucher_row->id;
								$ItemLedger->processed_on =$InventoryTransferVoucher->transaction_date;  
								$this->InventoryTransferVouchers->ItemLedgers->save($ItemLedger);
						}
						
					}
					
				}
			}
		//
		
		echo "Done";
		exit;
		
	}
 */
	public function excelExport(){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$st_year_id = $session->read('st_year_id');
		$where = [];
		 $vendor_id = $this->request->query('vendor_id');
        $customers_id = $this->request->query('customers_id');
        $vouch_no = $this->request->query('vouch_no');
		$From = $this->request->query('From');
		$To = $this->request->query('To');
		//pr($vouch_no);exit;
		
		$this->set(compact('vouch_no','From','To','vendor_id','customers_id'));
		
		if(!empty($vouch_no)){
			$where['InventoryTransferVouchers.voucher_no LIKE']=$vouch_no;
		}
		if(!empty($customers_id)){
			$where['InventoryTransferVouchers.customer_id']=$customers_id;
		}
		if(!empty($vendor_id)){
			$where['InventoryTransferVouchers.vendor_id']=$vendor_id;
		}
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['InventoryTransferVouchers.transaction_date >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['InventoryTransferVouchers.transaction_date <=']=$To;
		}
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['InventoryTransferVouchers.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
		$inventory_transfer_vouchs =$this->InventoryTransferVouchers->find()->where($where)->where(['company_id'=>$st_company_id,'InventoryTransferVouchers.financial_year_id'=>$st_year_id])->where($wheree)->order(['InventoryTransferVouchers.voucher_no' => 'DESC'])->contain(['Companies','Customers','Vendors']);
		//pr($inventory_transfer_vouchs->toArray());exit;
		

        $this->set(compact('inventory_transfer_vouchs','url','From','To'));
	}
    /**
     * View method
     *
     * @param string|null $id Inventory Transfer Voucher id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$id = $this->EncryptingDecrypting->decryptData($id);
       $inventoryTransferVoucher = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['Creator','Companies','Vendors','Customers','InventoryTransferVoucherRows'=>['SerialNumbers','Items'=>['SerialNumbers','ItemCompanies' =>function($q) use($st_company_id){
									return $q->where(['company_id'=>$st_company_id]);
								}]]]			
        ]); 
		
		
		$in_item=[];
		$out_item=[];
		foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row){
			if($inventory_transfer_voucher_row->status=='Out'){
				$out_item[$inventory_transfer_voucher_row->id]=$inventory_transfer_voucher_row;
			}else{
				$in_item[$inventory_transfer_voucher_row->id]=$inventory_transfer_voucher_row;
			}
		}
		//pr($out_item);pr($in_item); exit;
		$this->set(compact('out_item','in_item'));
        $this->set('inventoryTransferVoucher', $inventoryTransferVoucher);
        $this->set('_serialize', ['inventoryTransferVoucher']);
    }
    public function outView($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$id = $this->EncryptingDecrypting->decryptData($id);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
        $inventoryTransferVoucher = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['Creator','Companies','Customers','Vendors','InventoryTransferVoucherRows'=>['SerialNumbers','Items'=>['SerialNumbers','ItemCompanies' =>function($q) use($st_company_id){
									return $q->where(['company_id'=>$st_company_id]);
								}]]]
        ]);
		
		$this->set(compact('out_item','in_item'));
        $this->set('inventoryTransferVoucher', $inventoryTransferVoucher);
        $this->set('_serialize', ['inventoryTransferVoucher']);
    }
	  public function inView($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$id = $this->EncryptingDecrypting->decryptData($id);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');

        $inventoryTransferVoucher = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['Creator','Companies','Customers','Vendors', 'InventoryTransferVoucherRows'=>['SerialNumbers','Items'=>['SerialNumbers','ItemCompanies' =>function($q) use($st_company_id){
									return $q->where(['company_id'=>$st_company_id]);
								}]]]
        ]);

		$this->set(compact('out_item','in_item'));
        $this->set('inventoryTransferVoucher', $inventoryTransferVoucher);
        $this->set('_serialize', ['inventoryTransferVoucher']);
    }
    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$s_employee_id=$this->viewVars['s_employee_id'];
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->InventoryTransferVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		 
		
		$financial_month_first = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
			
		$display_items = $this->InventoryTransferVouchers->Items->find()->contain([
					'ItemCompanies'=> function ($q) use($st_company_id) {
						return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
					} 
				]);
		
		//pr($display_items->toArray()); exit;
		$inventoryTransferVoucher = $this->InventoryTransferVouchers->newEntity();
	
        if ($this->request->is('post')) 
		{
			$inventory_transfer_voucher_rows=[];
			foreach($this->request->data['inventory_transfer_voucher_rows']['out'] as $inventory_transfer_voucher_row){
				$inventory_transfer_voucher_row['status']='Out';
				$inventory_transfer_voucher_rows[]=$inventory_transfer_voucher_row;
			}
			foreach($this->request->data['inventory_transfer_voucher_rows']['in'] as $inventory_transfer_voucher_row){
				$inventory_transfer_voucher_row['status']='In';
				$inventory_transfer_voucher_row['item_id']=$inventory_transfer_voucher_row['item_id_in'];
				$inventory_transfer_voucher_row['quantity']=$inventory_transfer_voucher_row['quantity_in'];
				unset($inventory_transfer_voucher_row['item_id_in']);
				unset($inventory_transfer_voucher_row['quantity_in']);
				$inventory_transfer_voucher_rows[]=$inventory_transfer_voucher_row;
				
			}
			$this->request->data['inventory_transfer_voucher_rows']=$inventory_transfer_voucher_rows;
			
            $inventoryTransferVoucher = $this->InventoryTransferVouchers->patchEntity($inventoryTransferVoucher, $this->request->data);
			$inventoryTransferVoucher->transaction_date=date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date));
			$inventoryTransferVoucher->created_on=date("Y-m-d");
			//pr($inventoryTransferVoucher->transaction_date);exit;
			//pr( date("Y-m-d",strtotime($transaction_date)));exit;
			
			$last_voucher_no=$this->InventoryTransferVouchers->find()->select(['voucher_no'])->where(['company_id' => $st_company_id,'financial_year_id'=>$st_year_id])->order(['voucher_no' => 'DESC'])->first();
			if($last_voucher_no){
				$inventoryTransferVoucher->voucher_no=$last_voucher_no->voucher_no+1;
			}else{
				$inventoryTransferVoucher->voucher_no=1;
			}
			$inventoryTransferVoucher->company_id=$st_company_id;
			$inventoryTransferVoucher->in_out='in_out';
			$inventoryTransferVoucher->created_by=$s_employee_id;
			$inventoryTransferVoucher->financial_year_id=$st_year_id;
			$inventoryTransferVoucher->file_no = $inventoryTransferVoucher->so3;

	        //pr($inventoryTransferVoucher);exit;
			if ($this->InventoryTransferVouchers->save($inventoryTransferVoucher)) {
				
				foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $key => $inventory_transfer_voucher_row)
				{
					$inventory_transfer_voucher_rows[$key]['row_id'] = $inventory_transfer_voucher_row->id;
				}
				
			    foreach($inventory_transfer_voucher_rows as $inventory_transfer_voucher_row_data)
				{ 
				if($inventory_transfer_voucher_row_data['status'] == 'Out')
				{
					$serial_data=0;
					$serial_data=sizeof(@$inventory_transfer_voucher_row_data['serial_number_data']);
					if($serial_data>0)
					{
						$UnitRateSerialItem1=0;
						$serial_number_datas=$inventory_transfer_voucher_row_data['serial_number_data'];
						foreach($serial_number_datas as $serial_number_data){
							$UnitRateSerialItem = $this->weightedAvgCostForSerialWise($serial_number_data); 
							$serial_data=$this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->get($serial_number_data);
							
							$query2 = $this->InventoryTransferVouchers->Items->SerialNumbers->query();
									$query2->insert(['item_id','name','status','company_id','itv_id','itv_row_id','parent_id','transaction_date'])
									->values([
												'item_id' =>$inventory_transfer_voucher_row_data['item_id'],
												'name'=>$serial_data->name,
												'status'=>'Out',
												'company_id'=>$st_company_id,
												'itv_id'=>$inventoryTransferVoucher->id,
												'itv_row_id'=>$inventory_transfer_voucher_row_data['row_id'],
												'parent_id'=>$serial_number_data,
												'transaction_date'=>$inventoryTransferVoucher->transaction_date
												])
									->execute();
									$UnitRateSerialItem1+=$UnitRateSerialItem;
									$unit_rate=$UnitRateSerialItem1;
						}
						$unit_rate = round($unit_rate,3)/@$inventory_transfer_voucher_row_data['quantity'];
					}else{
							$unit_rate = $this->weightedAvgCostIvs($inventory_transfer_voucher_row_data['item_id'],$inventoryTransferVoucher->transaction_date); 
					}
					
					$unit_rate = round($unit_rate,3);
						 
					
					$query= $this->InventoryTransferVouchers->ItemLedgers->query();
						$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','processed_on','company_id','source_id','source_row_id'])
							  ->values([
											'item_id' => $inventory_transfer_voucher_row_data['item_id'],
											'quantity' => $inventory_transfer_voucher_row_data['quantity'],
											'rate' => $unit_rate,
											'source_model' => 'Inventory Transfer Voucher',
											'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
											'in_out'=>'Out',
											'company_id'=>$st_company_id,
											'source_id'=>$inventoryTransferVoucher->id,
											'source_row_id'=>$inventory_transfer_voucher_row_data['row_id']
										])
					    ->execute();
						
						$avg_cost_data = $unit_rate*$inventory_transfer_voucher_row_data['quantity'];
						
						
						$query21 = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->query();
						$query21->update()
							->set(['amount' => $avg_cost_data])
							->where(['inventory_transfer_voucher_id'=>$inventoryTransferVoucher->id,'item_id' => $inventory_transfer_voucher_row_data['item_id'],'status' => 'Out'])
							->execute();
							
				}else if($inventory_transfer_voucher_row_data['status'] == 'In'){
					$serial_data=0;
					$serial_data=sizeof(@$inventory_transfer_voucher_row_data['sr_no']);
					
					if($serial_data>0){
					$serial_number_datas=$inventory_transfer_voucher_row_data['sr_no'];
					$i=0;
					foreach($serial_number_datas as $key=>$serial_number_data){
						
						$query2 = $this->InventoryTransferVouchers->Items->SerialNumbers->query();
						$query2->insert(['item_id','name','status','company_id','itv_row_id','itv_id','transaction_date'])
							->values([
										'item_id' =>$inventory_transfer_voucher_row_data['item_id'],
										'name'=>$serial_number_data,
										'status'=>'In',
										'company_id'=>$st_company_id,
										'itv_row_id'=>$inventory_transfer_voucher_row_data['row_id'],
										'itv_id'=>$inventoryTransferVoucher->id,
										'transaction_date'=>$inventoryTransferVoucher->transaction_date
										])
							->execute();
					}
				}
				
				$query= $this->InventoryTransferVouchers->ItemLedgers->query();
						$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','company_id','processed_on','source_id','source_row_id'])
							  ->values([
											'item_id' => $inventory_transfer_voucher_row_data['item_id'],
											'quantity' => $inventory_transfer_voucher_row_data['quantity'],
											'rate' =>$inventory_transfer_voucher_row_data['amount'],
											'source_model' => 'Inventory Transfer Voucher',
											'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
											'in_out'=>'In',
											'company_id'=>$st_company_id,
											'source_id'=>$inventoryTransferVoucher->id,
											'source_row_id'=>$inventory_transfer_voucher_row_data['row_id']
										])
					    ->execute();
			} 
			
					
				}
			//pr($inventory_transfer_voucher_rows); exit;
			}	
			$this->Flash->success(__('The inventory transfer voucher has been saved.'));

                return $this->redirect(['action' => 'add']);
            } 
       
	    $customers = $this->InventoryTransferVouchers->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->contain(['CustomerAddress'=>function($q){
			return $q
			->where(['CustomerAddress.default_address'=>1]);
		}])->matching(
					'CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		
		$vendor = $this->InventoryTransferVouchers->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);	
				//pr($vendor->toArray());exit;
        $companies = $this->InventoryTransferVouchers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('inventoryTransferVoucher', 'companies','display_items','options','financial_month_first','financial_month_last','customers','vendor'));
        $this->set('_serialize', ['inventoryTransferVoucher']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Inventory Transfer Voucher id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
	public function ItemSerialNumber($select_item_id = null)
    {
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$flag=0; 
		$Item = $this->InventoryTransferVouchers->Items->get($select_item_id, [
            'contain' => ['ItemCompanies'=>function($q) use($st_company_id){
				return $q->where(['company_id'=>$st_company_id]);
			}]
        ]);
		//pr($Item->item_companies[0]->serial_number_enable);
		if($Item->item_companies[0]->serial_number_enable=="1"){
			$SerialNumbers=$this->InventoryTransferVouchers->Items->SerialNumbers->find()->where(['item_id'=>$select_item_id,'status'=>'In','company_id'=>$st_company_id]);
			
			$selectedSerialNumbers=$this->InventoryTransferVouchers->Items->SerialNumbers->find()->where(['item_id'=>$select_item_id,'status'=>'Out','company_id'=>$st_company_id]);
			
			//pr($SerialNumbers->toArray());exit;
			$flag=1;
		}
		$this->set(compact('SerialNumbers','flag','select_item_id','selectedSerialNumbers'));
    }
     
	public function ItemSerialNumbers($select_item_id = null)
    {
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$flag=0; 
		$Item = $this->InventoryTransferVouchers->Items->get($select_item_id, [
            'contain' => ['ItemCompanies'=>function($q) use($st_company_id){
				return $q->where(['company_id'=>$st_company_id]);
			}]
        ]);
		//pr($Item->item_companies[0]->serial_number_enable);
		if($Item->item_companies[0]->serial_number_enable=="1"){
			$SerialNumbers=$this->InventoryTransferVouchers->Items->SerialNumbers->find()->where(['item_id'=>$select_item_id,'status'=>'In','company_id'=>$st_company_id]);
			$inArr=[];
			foreach($SerialNumbers as $SerialNumber)
			{
				$inArr[$SerialNumber->name]=$SerialNumber->name;
			}
			$selectedSerialNumbers=$this->InventoryTransferVouchers->Items->SerialNumbers->find()->where(['item_id'=>$select_item_id,'status'=>'Out','company_id'=>$st_company_id]);
			foreach($selectedSerialNumbers as $selectedSerialNumber)
			{
				if(!empty($inArr[$selectedSerialNumber->name]))
				{
					unset($inArr[$selectedSerialNumber->name]);
				}
			}
			//pr($SerialNumbers->toArray());exit;
			$flag=1;
		}
		$this->set(compact('SerialNumbers','flag','select_item_id','selectedSerialNumbers','inArr'));
    }

    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$id = $this->EncryptingDecrypting->decryptData($id);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$display_items = $this->InventoryTransferVouchers->Items->find()->contain([
					'ItemCompanies'=> function ($q) use($st_company_id) {
						return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
					} 
				]);
	
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->InventoryTransferVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		 
		
		$financial_month_first = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
		
        $inventoryTransferVouchersout = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['InventoryTransferVoucherRows'=>
						function($q) use($st_company_id,$id){
											return $q->where(['inventory_transfer_voucher_id'=>$id,'status'=>'Out'])->contain(['Items'=>['ItemCompanies','ItemLedgers','SerialNumbers']]);
				}]
				
        ]);
		//pr($st_company_id);exit;
		$inventoryTransferVouchersins = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['InventoryTransferVoucherRows'=>
						function($q) use($st_company_id,$id){
											return $q->where(['inventory_transfer_voucher_id'=>$id,'status'=>'In'])->contain(['Items'=>['ItemCompanies','ItemLedgers','SerialNumbers']]);
				}]
				
        ]);		
		
		
		$inventoryTransferVoucher = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['InventoryTransferVoucherRows']
        ]);
		
		//$inventoryTransferVoucher = $this->InventoryTransferVouchers->newEntity();
		if ($this->request->is(['patch', 'post', 'put'])) {
			
			$inventory_transfer_voucher_rows=[];
			foreach($this->request->data['inventory_transfer_voucher_rows']['out'] as $inventory_transfer_voucher_row){
				$inventory_transfer_voucher_row['status']='Out';
				$inventory_transfer_voucher_rows[]=$inventory_transfer_voucher_row;
			}
			foreach($this->request->data['inventory_transfer_voucher_rows']['in'] as $inventory_transfer_voucher_row){
				$inventory_transfer_voucher_row['status']='In';
				$inventory_transfer_voucher_row['item_id']=$inventory_transfer_voucher_row['item_id_in'];
				$inventory_transfer_voucher_row['quantity']=$inventory_transfer_voucher_row['quantity_in'];
				unset($inventory_transfer_voucher_row['item_id_in']);
				unset($inventory_transfer_voucher_row['quantity_in']);
				$inventory_transfer_voucher_rows[]=$inventory_transfer_voucher_row;
				
			}
			$this->request->data['inventory_transfer_voucher_rows']=$inventory_transfer_voucher_rows;
			
            $inventoryTransferVoucher = $this->InventoryTransferVouchers->patchEntity($inventoryTransferVoucher, $this->request->data);
			$inventoryTransferVoucher->transaction_date=date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date));
			
			if(!empty($inventoryTransferVoucher->customer_id)){
				$inventoryTransferVoucher->file_no=$inventoryTransferVoucher->so3;
			}else{
				$inventoryTransferVoucher->file_no='';
			}
			$inventoryTransferVoucher->edited_on = date("Y-m-d"); 
			$inventoryTransferVoucher->edited_by=$this->viewVars['s_employee_id'];
			//pr($inventoryTransferVoucher);exit;
			if ($this->InventoryTransferVouchers->save($inventoryTransferVoucher)) {
				foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $key => $inventory_transfer_voucher_row)
				{
					$inventory_transfer_voucher_rows[$key]['row_id'] = $inventory_transfer_voucher_row->id;
				}
			$this->InventoryTransferVouchers->ItemLedgers->deleteAll(['source_id'=>$inventoryTransferVoucher->id,'source_model'=>'Inventory Transfer Voucher','company_id'=>$st_company_id,'ItemLedgers.in_out'=>'Out']);
			//$this->InventoryTransferVouchers->ItemLedgers->deleteAll(['source_id'=>$inventoryTransferVoucher->id,'source_model'=>'Inventory Transfer Voucher','company_id'=>$st_company_id,'ItemLedgers.in_out'=>'In']);
			$this->InventoryTransferVouchers->Items->SerialNumbers->deleteAll(['itv_id'=>$inventoryTransferVoucher->id,'company_id'=>$st_company_id,'SerialNumbers.status'=>'Out']);
			
			$no=1;
				foreach($inventory_transfer_voucher_rows as $inventory_transfer_voucher_row_data){
				
				
				if($inventory_transfer_voucher_row_data['status'] == 'Out')
				{ 
					
					$serial_data=0;
					$serial_data=sizeof(@$inventory_transfer_voucher_row_data['serial_number_data']);
					if($serial_data>0)
					{
						$serial_number_datas=$inventory_transfer_voucher_row_data['serial_number_data'];
						$UnitRateSerialItem1=0;
						foreach($serial_number_datas as $serial_number_data){
							$UnitRateSerialItem = $this->weightedAvgCostForSerialWise($serial_number_data); 
							$serial_data=$this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->get($serial_number_data);
							
							$query2 = $this->InventoryTransferVouchers->Items->SerialNumbers->query();
									$query2->insert(['item_id','name','status','company_id','itv_id','itv_row_id','parent_id','transaction_date'])
									->values([
												'item_id' =>$inventory_transfer_voucher_row_data['item_id'],
												'name'=>$serial_data->name,
												'status'=>'Out',
												'company_id'=>$st_company_id,
												'itv_id'=>$inventoryTransferVoucher->id,
												'itv_row_id'=>$inventory_transfer_voucher_row_data['row_id'],
												'parent_id'=>$serial_number_data,
												'transaction_date'=>$inventoryTransferVoucher->transaction_date
												])
									->execute();
									
									$UnitRateSerialItem1+=$UnitRateSerialItem;
									$unit_rate=$UnitRateSerialItem1;
						}
						
						$unit_rate = round($unit_rate,3)/@$inventory_transfer_voucher_row_data['quantity'];
						//pr($serial_number_data); exit;
					}else{
							$unit_rate = $this->weightedAvgCostIvs($inventory_transfer_voucher_row_data['item_id'],$inventoryTransferVoucher->transaction_date); 
							//pr($unit_rate); exit;
					}
						
					
					$unit_rate = round($unit_rate,3);
						
					
					$query= $this->InventoryTransferVouchers->ItemLedgers->query();
						$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','processed_on','company_id','source_id','source_row_id'])
							  ->values([
											'item_id' => $inventory_transfer_voucher_row_data['item_id'],
											'quantity' => $inventory_transfer_voucher_row_data['quantity'],
											'rate' => $unit_rate,
											'source_model' => 'Inventory Transfer Voucher',
											'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
											'in_out'=>'Out',
											'company_id'=>$st_company_id,
											'source_id'=>$inventoryTransferVoucher->id,
											'source_row_id'=>$inventory_transfer_voucher_row_data['row_id']
										])
					    ->execute();
						
						$avg_cost_data = $unit_rate*$inventory_transfer_voucher_row_data['quantity'];
						$avg_cost_data = round($avg_cost_data,2);
						//pr($avg_cost_data); exit;
						$query21 = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->query();
						$query21->update()
							->set(['amount' => $avg_cost_data])
							->where(['inventory_transfer_voucher_id'=>$inventoryTransferVoucher->id,'item_id' => $inventory_transfer_voucher_row_data['item_id'],'status' => 'Out'])
							->execute();
							
				}else if($inventory_transfer_voucher_row_data['status'] == 'In')
				{ 
					$serial_data=0;
					$serial_data=sizeof(@$inventory_transfer_voucher_row_data['sr_no']);
					
					if($serial_data>0)
					{
						
						$serial_number_datas=$inventory_transfer_voucher_row_data['sr_no'];
						
						foreach($serial_number_datas as $key=>$serial_number_data)
						{
							
							$query2 = $this->InventoryTransferVouchers->Items->SerialNumbers->query();
							$query2->insert(['item_id','name','status','company_id','itv_row_id','itv_id','transaction_date'])
									->values([
												'item_id' =>$inventory_transfer_voucher_row_data['item_id'],
												'name'=>$serial_number_data,
												'status'=>'In',
												'company_id'=>$st_company_id,
												'itv_row_id'=>$inventory_transfer_voucher_row_data['row_id'],
												'itv_id'=>$inventoryTransferVoucher->id,
												'transaction_date'=>$inventoryTransferVoucher->transaction_date
												])
									->execute();
									
						}
					
						
						$query = $this->InventoryTransferVouchers->Items->SerialNumbers->query();
						$query->update()
						->set(['transaction_date' => $inventoryTransferVoucher->transaction_date])
						->where(['itv_row_id' => $inventory_transfer_voucher_row_data['row_id'],'company_id'=>$st_company_id,'status'=>'In'])
						->execute();
					
				
				
					$query= $this->InventoryTransferVouchers->ItemLedgers->query();
							$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','company_id','processed_on','source_id','source_row_id'])
								  ->values([
												'item_id' => $inventory_transfer_voucher_row_data['item_id'],
												'quantity' => $inventory_transfer_voucher_row_data['quantity'],
												'rate' =>$inventory_transfer_voucher_row_data['amount'],
												'source_model' => 'Inventory Transfer Voucher',
												'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
												'in_out'=>'In',
												'company_id'=>$st_company_id,
												'source_id'=>$inventoryTransferVoucher->id,
												'source_row_id'=>$inventory_transfer_voucher_row_data['row_id']
											])
							->execute();
				
					}else{
						
						/* $query1 = $this->InventoryTransferVouchers->ItemLedgers->query();
						$query1->update()
						->set(['rate' =>$inventory_transfer_voucher_row_data['amount'],'quantity'=>$inventory_transfer_voucher_row_data['quantity'],'source_row_id'=>$inventory_transfer_voucher_row_data['row_id']])
						->where(['source_id'=>$inventoryTransferVoucher->id,'source_model'=>'Inventory Transfer Voucher','item_id'=>$inventory_transfer_voucher_row_data['item_id'],'in_out'=>'In'])
						->execute(); */
						
						$query= $this->InventoryTransferVouchers->ItemLedgers->query();
							$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','company_id','processed_on','source_id','source_row_id'])
								  ->values([
												'item_id' => $inventory_transfer_voucher_row_data['item_id'],
												'quantity' => $inventory_transfer_voucher_row_data['quantity'],
												'rate' =>$inventory_transfer_voucher_row_data['amount'],
												'source_model' => 'Inventory Transfer Voucher',
												'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
												'in_out'=>'In',
												'company_id'=>$st_company_id,
												'source_id'=>$inventoryTransferVoucher->id,
												'source_row_id'=>$inventory_transfer_voucher_row_data['row_id']
											])
							->execute();
					}
			
				} 
				} 
				$this->Flash->success(__('The inventory transfer voucher has been saved.'));
				return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The inventory transfer voucher could not be saved. Please, try again.'));
            }
        }
		
		foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $itv_rows)
		{
			$serialNoDetail = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->find()
									 ->where(['itv_id'=>$inventoryTransferVoucher->id,'itv_row_id'=>$itv_rows->id,'company_id'=>$st_company_id]); 
			if($serialNoDetail->count()>0)
			{ 
				foreach($serialNoDetail as $svalue)
				{
					$serialNoparentIdExist = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->find()
									 ->where(['parent_id'=>$svalue->id,'company_id'=>$st_company_id]);
					if($serialNoparentIdExist->count()>0)
					{
						$parentSerialNo[$svalue->id] = $svalue->id;
					}
				}
			}
		}
		 $customers = $this->InventoryTransferVouchers->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->contain(['CustomerAddress'=>function($q){
			return $q
			->where(['CustomerAddress.default_address'=>1]);
		}])->matching(
					'CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		
		$vendor = $this->InventoryTransferVouchers->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);	
	
        $companies = $this->InventoryTransferVouchers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('inventoryTransferVoucher', 'companies','inventoryTransferVouchersout','inventoryTransferVouchersins','id','display_items','financial_month_first','financial_month_last','parentSerialNo','id','item_option1','customers','vendor'));
        $this->set('_serialize', ['inventoryTransferVoucher']);
    }

	function DeleteSerialNumbers($id=null,$in_id=null,$in_voucher_id=null,$item_id=null){
		//pr($in_id);
		//pr($in_voucher_id);exit;
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$ItemLedger=$this->InventoryTransferVouchers->InventoryTransferVoucherRows->Items->ItemLedgers->find()->where(['source_id'=>$in_voucher_id,'item_id'=>$item_id,'in_out'=>'In','source_model'=>'Inventory Transfer Voucher'])->first();
		//pr($ItemLedger);exit;
		
		$ItemSerialNumber = $this->InventoryTransferVouchers->SerialNumbers->get($id);
		$InventoryTransferVoucherRows = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->get($in_id);
		//pr($InventoryTransferVoucherRows);exit;
		
		if($ItemSerialNumber->status=='In'){
			$ItemQuantity = $ItemLedger->quantity-1;
			//pr($ItemQuantity);exit;
			if($ItemQuantity == 0){
				$this->InventoryTransferVouchers->ItemLedgers->delete($ItemLedger);
				$this->InventoryTransferVouchers->InventoryTransferVoucherRows->delete($InventoryTransferVoucherRows);
				$this->InventoryTransferVouchers->SerialNumbers->delete($ItemSerialNumber);
				$this->Flash->success(__('The Item has been deleted.'));
				return $this->redirect(['action' => 'edit/'.$in_voucher_id]);
				
			}else{
				
			$query_in = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->query();
			
			$query_in->update()
				->set(['quantity' => $InventoryTransferVoucherRows->quantity-1])
				->where(['inventory_transfer_voucher_id' => $in_voucher_id,'item_id'=>$item_id,'status'=>'In'])
				->execute();	
				
			$query = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->Items->ItemLedgers->query();
			$query->update()
				->set(['quantity' => $ItemLedger->quantity-1])
				->where(['item_id'=>$item_id,'company_id'=>$st_company_id,'source_model'=>'Inventory Transfer Voucher','in_out'=>'In'])
				->execute();
					
			$this->InventoryTransferVouchers->SerialNumbers->delete($ItemSerialNumber);
			$this->Flash->success(__('The Serial Number has been deleted.'));
			}
		}
		return $this->redirect(['action' => 'edit/'.$in_voucher_id]);
	}


	
	
	function DeleteSerialNumberIn($id=null,$in_voucher_id=null,$in_id=null,$item_id=null){
		
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$ItemLedger=$this->InventoryTransferVouchers->InventoryTransferVoucherRows->ItemLedgers->find()->where(['source_id'=>$in_id,'item_id'=>$item_id,'in_out'=>'In','source_model'=>'Inventory Transfer Voucher'])->first();
		
	
		
		$ItemSerialNumbers = $this->InventoryTransferVouchers->SerialNumbers->find()->where(['SerialNumbers.id'=>$id,'SerialNumbers.item_id'=>$item_id,'SerialNumbers.itv_row_id'=>$in_voucher_id]);
		
		
		$serialINOUT='';
		
		foreach($ItemSerialNumbers as $ItemSerialNumber1)
		{
			if($ItemSerialNumber1->status=='In')
			{
				$serialINOUT='In';
		    }
			if($ItemSerialNumber1->status=='Out')
			{
				$serialINOUT='Out';
			}
		}
		
		$InventoryTransferVoucherRows = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->get($in_voucher_id);
		
		
		if($serialINOUT=='In')
		{
			$ItemQuantity = $ItemLedger->quantity-1;
			
			$query_in = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->query();
			
			$query_in->update()
				->set(['quantity' => $InventoryTransferVoucherRows->quantity-1])
				->where(['inventory_transfer_voucher_id' => $in_id,'item_id'=>$item_id])
				->execute();	
				
			$query = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->Items->ItemLedgers->query();
			$query->update()
				->set(['quantity' => $ItemLedger->quantity-1])
				->where(['item_id'=>$item_id,'company_id'=>$st_company_id,'source_model'=>'Inventory Transfer Voucher','in_out'=>'In','source_row_id'=>$in_voucher_id,'source_id'=>$in_id])
				->execute();
				
			$ItemSerialNumber = $this->InventoryTransferVouchers->SerialNumbers->get($id);
			
			$this->InventoryTransferVouchers->SerialNumbers->delete($ItemSerialNumber); 
			$this->Flash->success(__('The Serial Number has been deleted.'));
		}
		
		//row delete code
		$ItemSerialNumberExist = $this->InventoryTransferVouchers->SerialNumbers->find()->where(['SerialNumbers.itv_row_id'=>$in_voucher_id,'SerialNumbers.item_id'=>$item_id])->count();
		if($ItemSerialNumberExist<1)
		{
			$InventoryTransferVoucherRowdetail = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->get($in_voucher_id);
			$this->InventoryTransferVouchers->InventoryTransferVoucherRows->delete($InventoryTransferVoucherRowdetail);
		}
		
		//item ledger entry delete when quantity 0.
		$ItemLedgerExist = $this->InventoryTransferVouchers->ItemLedgers->find()->where(['ItemLedgers.source_model'=>'Inventory Transfer Voucher','ItemLedgers.item_id'=>$item_id,'ItemLedgers.source_id'=>$in_id,'ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.quantity'=>0])->first();

		if(!empty($ItemLedgerExist))
		{
			$ItemLedgersId =$ItemLedgerExist->id;
			$ItemLedgerDetail = $this->InventoryTransferVouchers->ItemLedgers->get($ItemLedgersId);
			$this->InventoryTransferVouchers->ItemLedgers->delete($ItemLedgerDetail);
		}
		
		return $this->redirect(['action' => 'editInventoryIn/'.$in_id]);
	}
	
    /**
     * Delete method
     *
     * @param string|null $id Inventory Transfer Voucher id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $inventoryTransferVoucher = $this->InventoryTransferVouchers->get($id);
        if ($this->InventoryTransferVouchers->delete($inventoryTransferVoucher)) {
            $this->Flash->success(__('The inventory transfer voucher has been deleted.'));
        } else {
            $this->Flash->error(__('The inventory transfer voucher could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	 public function ItemLedgerRate($item_id = null)
    {
		$Itemledgers = $this->InventoryTransferVouchers->ItemLedgers->find()->where(['item_id'=>$item_id,'in_out'=>'In']);
				
				$j=0; $qty_total=0; $total_amount=0;
				foreach($Itemledgers as $Itemledger){
					$Itemledger_qty = $Itemledger->quantity;
					$Itemledger_rate = $Itemledger->rate;
					$total_amount = $total_amount+($Itemledger_qty * $Itemledger_rate);
					$qty_total=$qty_total+$Itemledger_qty;
					$j++;
				}
		$per_unit_cost=$total_amount/$qty_total;
		
		$this->set(compact('per_unit_cost'));
	}
	
	public function InventoryIn()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$s_employee_id=$this->viewVars['s_employee_id'];
		
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->InventoryTransferVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		 

		$financial_month_first = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
				
		$display_items = $this->InventoryTransferVouchers->Items->find()->contain([
					'ItemCompanies'=> function ($q) use($st_company_id) {
						return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
					} 
				]);
		
		$inventoryTransferVoucher = $this->InventoryTransferVouchers->newEntity();
		
		if ($this->request->is(['patch', 'post', 'put'])) {
            $inventoryTransferVoucher = $this->InventoryTransferVouchers->patchEntity($inventoryTransferVoucher, $this->request->data);
			$last_voucher_no=$this->InventoryTransferVouchers->find()->select(['voucher_no'])->where(['company_id' => $st_company_id,'financial_year_id'=>$st_year_id])->order(['voucher_no' => 'DESC'])->first();
			if($last_voucher_no){
				$inventoryTransferVoucher->voucher_no=$last_voucher_no->voucher_no+1;
			}else{
				$inventoryTransferVoucher->voucher_no=1;
			}
			$inventoryTransferVoucher->company_id=$st_company_id;
			$inventoryTransferVoucher->financial_year_id=$st_year_id;
			$inventoryTransferVoucher->in_out='In';
			$inventoryTransferVoucher->created_by=$s_employee_id;
			$inventoryTransferVoucher->file_no=$inventoryTransferVoucher->so3;
			$inventoryTransferVoucher->created_on=date('Y-m-d');
			$inventoryTransferVoucher->transaction_date=date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date));
			//pr($inventoryTransferVoucher);exit;
			if ($this->InventoryTransferVouchers->save($inventoryTransferVoucher)) {
				
				foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row){
					$dt=sizeof($inventory_transfer_voucher_row->sr_no);
						$query= $this->InventoryTransferVouchers->ItemLedgers->query();
						$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','source_row_id','company_id','processed_on','source_id'])
							  ->values([
											'item_id' =>$inventory_transfer_voucher_row->item_id,
											'quantity' =>$inventory_transfer_voucher_row->quantity,
											'rate' =>$inventory_transfer_voucher_row->amount,
											'source_model' => 'Inventory Transfer Voucher',
											'source_row_id' => $inventory_transfer_voucher_row->id,
											'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
											'in_out'=>'In',
											'company_id'=>$st_company_id,
											'source_id'=>$inventoryTransferVoucher->id
											
										])
					    ->execute();
						
						if($dt > 0){
							foreach($inventory_transfer_voucher_row->sr_no as $sr_no){ 	
								$query2 = $this->InventoryTransferVouchers->Items->SerialNumbers->query();
								$query2->insert(['item_id','name','status','company_id','itv_id','itv_row_id','transaction_date'])
								->values([
											'item_id' =>$inventory_transfer_voucher_row->item_id,
											'name'=>$sr_no,
											'status'=>'In',
											'company_id'=>$st_company_id,
											'itv_id'=>$inventoryTransferVoucher->id,
											'itv_row_id'=>$inventory_transfer_voucher_row->id,
											'transaction_date'=>
											$inventoryTransferVoucher->transaction_date
											])
								->execute();
								
								
							}
						}
				}
				$this->Flash->success(__('The Inventory Transfer Vouchers has been saved.'));
                return $this->redirect(['action' => 'Index']);
			}
			
		}
		$customers = $this->InventoryTransferVouchers->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->contain(['CustomerAddress'=>function($q){
			return $q
			->where(['CustomerAddress.default_address'=>1]);
		}])->matching(
					'CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		
		$vendor = $this->InventoryTransferVouchers->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);	
		$this->set(compact('display_items','inventoryTransferVoucher','financial_month_first','financial_month_last','customers','vendor'));
	}
	
	public function InventoryOut()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$s_employee_id=$this->viewVars['s_employee_id'];
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->InventoryTransferVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		 

		$financial_month_first = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
		$display_items = $this->InventoryTransferVouchers->Items->find()->contain([
					'ItemCompanies'=> function ($q) use($st_company_id) {
						return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
					} 
				]);
				
				//pr($item_option); 
				//exit;
		$inventoryTransferVoucher = $this->InventoryTransferVouchers->newEntity();
		
		if ($this->request->is(['patch', 'post', 'put'])) {
			
            $inventoryTransferVoucher = $this->InventoryTransferVouchers->patchEntity($inventoryTransferVoucher, $this->request->data);
			//pr($inventoryTransferVoucher); exit;
			$last_voucher_no=$this->InventoryTransferVouchers->find()->select(['voucher_no'])->where(['company_id' => $st_company_id,'financial_year_id'=>$st_year_id])->order(['voucher_no' => 'DESC'])->first();
			if($last_voucher_no){
				$inventoryTransferVoucher->voucher_no=$last_voucher_no->voucher_no+1;
			}else{
				$inventoryTransferVoucher->voucher_no=1;
			}
			$inventoryTransferVoucher->financial_year_id=$st_year_id;
			$inventoryTransferVoucher->company_id=$st_company_id;
			$inventoryTransferVoucher->in_out='Out';
			$inventoryTransferVoucher->created_by=$s_employee_id;
			$inventoryTransferVoucher->created_on=date('Y-m-d');
			$inventoryTransferVoucher->transaction_date=date('Y-m-d',strtotime($inventoryTransferVoucher->transaction_date));
			$inventoryTransferVoucher->file_no=$inventoryTransferVoucher->so3;
			//pr($inventoryTransferVoucher);exit;
				if ($this->InventoryTransferVouchers->save($inventoryTransferVoucher)) {
				
			foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row){
				
				$serial_data=sizeof($inventory_transfer_voucher_row->serial_number_data);
					if($serial_data>0){
						$UnitRateSerialItem1=0;
						foreach($inventory_transfer_voucher_row->serial_number_data as $serial_number_data){
							
						$UnitRateSerialItem = $this->weightedAvgCostForSerialWise($serial_number_data); 
						$serial_data=$this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->get($serial_number_data);
						
						$query2 = $this->InventoryTransferVouchers->Items->SerialNumbers->query();
								$query2->insert(['item_id','name','status','company_id','itv_id','itv_row_id','parent_id','transaction_date'])
								->values([
											'item_id' =>$inventory_transfer_voucher_row->item_id,
											'name'=>$serial_data->name,
											'status'=>'Out',
											'company_id'=>$st_company_id,
											'itv_id'=>$inventoryTransferVoucher->id,
											'itv_row_id'=>$inventory_transfer_voucher_row->id,
											'parent_id'=>$serial_number_data,
											'transaction_date'=>$inventoryTransferVoucher->transaction_date
											])
								->execute();
								
								$UnitRateSerialItem1+=$UnitRateSerialItem;
								$unit_rate=$UnitRateSerialItem1;
						}
						$unit_rate = round($unit_rate,3)/@$inventory_transfer_voucher_row->quantity;
					}else{
							$unit_rate = $this->weightedAvgCostIvs($inventory_transfer_voucher_row->item_id,$inventoryTransferVoucher->transaction_date); 
					}
					
					$unit_rate = round($unit_rate,3);
					$query= $this->InventoryTransferVouchers->ItemLedgers->query();
						$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','processed_on','company_id','source_id','source_row_id'])
							  ->values([
											'item_id' => $inventory_transfer_voucher_row->item_id,
											'quantity' => $inventory_transfer_voucher_row->quantity,
											'rate' => $unit_rate,
											'source_model' => 'Inventory Transfer Voucher',
											'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
											'in_out'=>'Out',
											'company_id'=>$st_company_id,
											'source_id'=>$inventoryTransferVoucher->id,
											'source_row_id'=>$inventory_transfer_voucher_row->id
										])
					    ->execute();
						
						$avg_cost_data = $unit_rate*$inventory_transfer_voucher_row->quantity;
						$query21 = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->query();
						$query21->update()
							->set(['amount' => $avg_cost_data,'status' => 'Out'])
							->where(['inventory_transfer_voucher_id'=>$inventoryTransferVoucher->id,'item_id' => $inventory_transfer_voucher_row->item_id])
							->execute();
					
					}
					
					$this->Flash->success(__('The Inventory Transfer Vouchers has been saved.'));

                return $this->redirect(['action' => 'Index']);
				}
		
		}
		$customers = $this->InventoryTransferVouchers->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->contain(['CustomerAddress'=>function($q){
			return $q
			->where(['CustomerAddress.default_address'=>1]);
		}])->matching(
					'CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		
		$vendor = $this->InventoryTransferVouchers->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);
		$this->set(compact('display_items','inventoryTransferVoucher','financial_month_first','financial_month_last','customers','vendor'));
	}
	

	public function weightedAvgCostForSerialWise($sr_no_out_id=null){ 
		$this->viewBuilder()->layout('');
			$session = $this->request->session();
			$st_company_id = $session->read('st_company_id');
			//pr($sr_no_out_id); exit;
			
			$ItemData=$this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->find()->where(['id'=>$sr_no_out_id])->first();
			
			$Items = $this->InventoryTransferVouchers->ItemLedgers->Items->get($ItemData->item_id, [
				'contain' => ['ItemCompanies'=>function($q) use($st_company_id){
					return $q->where(['company_id'=>$st_company_id]);
				}]
			]);
			//pr($Items); exit;
			$to_date = date('Y-m-d');
			$unit_rate=0;
	
	
	//
		if($Items->item_companies[0]->serial_number_enable == '1'){
				$ItemSerialNumber=$this->InventoryTransferVouchers->ItemLedgers->SerialNumbers->get($sr_no_out_id);
				
				$itemSerialRate=0; $itemSerialQuantity=0; $i=1;
							
				if(@$ItemSerialNumber->grn_id > 0){ //pr($sr_no_out_id); exit;
				$outExist = $this->InventoryTransferVouchers->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'SerialNumbers.transaction_date <=' => $to_date]);
					if($outExist == 0){
						$ItemLedgerData =$this->InventoryTransferVouchers->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->grn_id,'source_model'=>"Grns",'source_row_id'=>$ItemSerialNumber->grn_row_id])->first();
					//	pr($ItemLedgerData); 
						if($ItemLedgerData){
							@$itemSerialQuantity=$itemSerialQuantity+1;
							@$itemSerialRate+=@$ItemLedgerData['rate'];
						}
					}
				}
				if(@$ItemSerialNumber->sale_return_id > 0){ 
				$outExist = $this->InventoryTransferVouchers->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'SerialNumbers.transaction_date <=' => $to_date]);
					if($outExist == 0){
						$ItemLedgerData =$this->InventoryTransferVouchers->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->sale_return_id,'source_model'=>"Sale Return",'source_row_id'=>$ItemSerialNumber->sales_return_row_id])->where($where1)->first();
					//	pr($ItemLedgerData); 
						if($ItemLedgerData){
							@$itemSerialQuantity=$itemSerialQuantity+1;
							@$itemSerialRate+=@$ItemLedgerData['rate'];
						}
					}
				}
				
				if(@$ItemSerialNumber->itv_id > 0){
				$outExist = $this->InventoryTransferVouchers->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'SerialNumbers.transaction_date <=' => $to_date]); 
					if($outExist == 0){  
						$ItemLedgerData =$this->InventoryTransferVouchers->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->itv_id,'source_model'=>"Inventory Transfer Voucher",'source_row_id'=>$ItemSerialNumber->itv_row_id])->first();
						//pr($ItemLedgerData); 
						if($ItemLedgerData){
							@$itemSerialQuantity=$itemSerialQuantity+1;
							@$itemSerialRate+=@$ItemLedgerData['rate'];
						}
					}
				}
				if(@$ItemSerialNumber->iv_row_id > 0){
					$outExist = $this->InventoryTransferVouchers->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'SerialNumbers.transaction_date <=' => $to_date]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->InventoryTransferVouchers->ItemLedgers->find()->where(['source_model'=>"Inventory Vouchers",'iv_row_id'=>$ItemSerialNumber->iv_row_id])->first();
							//pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
				if(@$ItemSerialNumber->is_opening_balance == "Yes"){  
				 
						$ItemLedgerData =$this->InventoryTransferVouchers->ItemLedgers->find()->where(['ItemLedgers.source_model'=>"Items",'ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.item_id' => $ItemSerialNumber->item_id])->first();
						//pr($ItemLedgerData); 
						if($ItemLedgerData){
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate+=@$ItemLedgerData['rate'];
						}
					
				}
				
			//pr($itemSerialRate); 
			
			
				return $itemSerialRate; 
		}	
		
	}
	
 public function editInventoryOut($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$id = $this->EncryptingDecrypting->decryptData($id);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$s_employee_id=$this->viewVars['s_employee_id'];
			$st_year_id = $session->read('st_year_id');
				$financial_year = $this->InventoryTransferVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
				 
				
				$financial_month_first = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
				$financial_month_last = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
				
		$display_items = $this->InventoryTransferVouchers->Items->find()->contain([
					'ItemCompanies'=> function ($q) use($st_company_id) {
						return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
					} 
				]);
	
        $inventoryTransferVouchersout = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['InventoryTransferVoucherRows'=>
						function($q) use($st_company_id,$id){
											return $q->where(['inventory_transfer_voucher_id'=>$id,'status'=>'Out'])->contain(['Items'=>['ItemCompanies','ItemLedgers','SerialNumbers']]);
				}]
				
        ]);
	//pr($display_items); exit;
		$inventoryTransferVoucher = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['InventoryTransferVoucherRows']
        ]);
		
		if ($this->request->is(['patch', 'post', 'put'])) {
            $inventoryTransferVoucher = $this->InventoryTransferVouchers->patchEntity($inventoryTransferVoucher, $this->request->data);
			$inventoryTransferVoucher->edited_on = date("Y-m-d"); 
			$inventoryTransferVoucher->edited_by=$this->viewVars['s_employee_id'];
			
			
			if(!empty($inventoryTransferVoucher->customer_id)){
				$inventoryTransferVoucher->file_no=$inventoryTransferVoucher->so3;
			}else{
				$inventoryTransferVoucher->file_no='';
			}
			$inventoryTransferVoucher->company_id=$st_company_id;
			$inventoryTransferVoucher->in_out='Out';
			$inventoryTransferVoucher->transaction_date=date('Y-m-d',strtotime($inventoryTransferVoucher->transaction_date));
			///pr($inventoryTransferVoucher);exit;
		if ($this->InventoryTransferVouchers->save($inventoryTransferVoucher)) { 
				$this->InventoryTransferVouchers->ItemLedgers->deleteAll(['source_id'=>$inventoryTransferVoucher->id,'source_model'=>'Inventory Transfer Voucher','company_id'=>$st_company_id,'in_out'=>'Out']);
				
				$this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->deleteAll(['SerialNumbers.itv_id'=>$inventoryTransferVoucher->id,'SerialNumbers.company_id'=>$st_company_id,'SerialNumbers.status'=>'Out']);
				
			foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row){
				$UnitRateSerialItem1=0;
				$serial_data=sizeof($inventory_transfer_voucher_row->serial_number_data);
					if($serial_data>0){
						foreach($inventory_transfer_voucher_row->serial_number_data as $serial_number_data){
							
						$UnitRateSerialItem = $this->weightedAvgCostForSerialWise($serial_number_data); 
						$serial_data=$this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->get($serial_number_data);
						
						$query2 = $this->InventoryTransferVouchers->Items->SerialNumbers->query();
								$query2->insert(['item_id','name','status','company_id','itv_id','itv_row_id','parent_id','transaction_date'])
								->values([
											'item_id' =>$inventory_transfer_voucher_row->item_id,
											'name'=>$serial_data->name,
											'status'=>'Out',
											'company_id'=>$st_company_id,
											'itv_id'=>$inventoryTransferVoucher->id,
											'itv_row_id'=>$inventory_transfer_voucher_row->id,
											'parent_id'=>$serial_number_data,
											'transaction_date'=>$inventoryTransferVoucher->transaction_date
											])
								->execute();
								
								$UnitRateSerialItem1+=$UnitRateSerialItem;
								$unit_rate=$UnitRateSerialItem1;
						}
						$unit_rate = round($unit_rate,3)/@$inventory_transfer_voucher_row->quantity;
					}else{
							$unit_rate = $this->weightedAvgCostIvs($inventory_transfer_voucher_row->item_id,$inventoryTransferVoucher->transaction_date); 
					}
					$unit_rate = round($unit_rate,3);
					$query= $this->InventoryTransferVouchers->ItemLedgers->query();
						$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','processed_on','company_id','source_id','source_row_id'])
							  ->values([
											'item_id' => $inventory_transfer_voucher_row->item_id,
											'quantity' => $inventory_transfer_voucher_row->quantity,
											'rate' => $unit_rate,
											'source_model' => 'Inventory Transfer Voucher',
											'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
											'in_out'=>'Out',
											'company_id'=>$st_company_id,
											'source_id'=>$inventoryTransferVoucher->id,
											'source_row_id'=>$inventory_transfer_voucher_row->id
										])
					    ->execute();
						
						$avg_cost_data = $unit_rate*$inventory_transfer_voucher_row->quantity;
						$query21 = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->query();
						$query21->update()
							->set(['amount' => $avg_cost_data,'status' => 'Out'])
							->where(['inventory_transfer_voucher_id'=>$inventoryTransferVoucher->id,'item_id' => $inventory_transfer_voucher_row->item_id])
							->execute();
					
					}
					
					$this->Flash->success(__('The Inventory Transfer Vouchers has been saved.'));

                return $this->redirect(['action' => 'Index']);
				}
		
		}
		$customers = $this->InventoryTransferVouchers->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->contain(['CustomerAddress'=>function($q){
			return $q
			->where(['CustomerAddress.default_address'=>1]);
		}])->matching(
					'CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		
		$vendor = $this->InventoryTransferVouchers->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);	
		
		
		$this->set(compact('display_items','inventoryTransferVoucher','inventoryTransferVouchersout','display_items','id','financial_month_first','financial_month_last','customers','vendor'));
	}
	
	public function editInventoryIn($id=null)
    {
		$this->viewBuilder()->layout('index_layout');
		$id = $this->EncryptingDecrypting->decryptData($id);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$s_employee_id=$this->viewVars['s_employee_id'];
		$st_year_id = $session->read('st_year_id');
				$financial_year = $this->InventoryTransferVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
				 
				
				$financial_month_first = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
				$financial_month_last = $this->InventoryTransferVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
		$display_items = $this->InventoryTransferVouchers->Items->find()->contain([
					'ItemCompanies'=> function ($q) use($st_company_id) {
						return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
					} 
				]);
		 $inventoryTransferVouchers = $this->InventoryTransferVouchers->get($id, [
            'contain' => ['InventoryTransferVoucherRows'=>
						function($q) use($st_company_id,$id){
											return $q->where(['inventory_transfer_voucher_id'=>$id])->contain(['Items'=>['ItemCompanies','ItemLedgers','SerialNumbers']]);
				}]
				
        ]);
		
		if ($this->request->is(['patch', 'post', 'put'])) {
            $inventoryTransferVoucher = $this->InventoryTransferVouchers->patchEntity($inventoryTransferVouchers, $this->request->data);
			$inventoryTransferVoucher->edited_on = date("Y-m-d"); 
			$inventoryTransferVoucher->edited_by=$this->viewVars['s_employee_id'];
			$inventoryTransferVoucher->company_id=$st_company_id;
			$inventoryTransferVoucher->in_out='In';
			$inventoryTransferVoucher->created_by=$s_employee_id;
			$inventoryTransferVoucher->transaction_date=date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date));
			if(!empty($inventoryTransferVoucher->customer_id)){
				$inventoryTransferVoucher->file_no=$inventoryTransferVoucher->so3;
			}else{
				$inventoryTransferVoucher->file_no='';
			}
			//pr($inventoryTransferVoucher);exit;
			if ($this->InventoryTransferVouchers->save($inventoryTransferVoucher)) {
			
				$this->InventoryTransferVouchers->ItemLedgers->deleteAll(['source_id'=>$inventoryTransferVoucher->id,'source_model'=>'Inventory Transfer Voucher','company_id'=>$st_company_id]);
				
				foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row){
					
					$dt=sizeof($inventory_transfer_voucher_row->sr_no);
						$query= $this->InventoryTransferVouchers->ItemLedgers->query();
						$query->insert(['item_id','quantity' ,'rate', 'in_out','source_model','company_id','processed_on','source_id','source_row_id'])
							  ->values([
											'item_id' =>$inventory_transfer_voucher_row->item_id,
											'quantity' =>$inventory_transfer_voucher_row->quantity,
											'rate' =>$inventory_transfer_voucher_row->amount,
											'source_model' => 'Inventory Transfer Voucher',
											'processed_on' => date("Y-m-d",strtotime($inventoryTransferVoucher->transaction_date)),
											'in_out'=>'In',
											'source_row_id' => $inventory_transfer_voucher_row->id,
											'company_id'=>$st_company_id,
											'source_id'=>$inventoryTransferVoucher->id
										])
					    ->execute();
						
						if($dt > 0){
							
							foreach($inventory_transfer_voucher_row->sr_no as $sr_no){ 	
								$query2 = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->query();
								$query2->insert(['item_id','name','status','company_id','itv_row_id','itv_id','transaction_date'])
									->values([
												'item_id' =>$inventory_transfer_voucher_row->item_id,
												'name'=>$sr_no,
												'status'=>'In',
												'company_id'=>$st_company_id,
												'itv_row_id'=>$inventory_transfer_voucher_row->id,
												'itv_id'=>$inventoryTransferVoucher->id,
												'transaction_date'=>$inventoryTransferVoucher->transaction_date
												])
									->execute();
								
							
							}
						}
						$query = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->query();
						$query->update()
						->set(['transaction_date' => $inventoryTransferVoucher->transaction_date])
						->where(['itv_row_id' => $inventory_transfer_voucher_row->id,'company_id'=>$st_company_id,'status'=>'In'])
						->execute();
						
				}
				$this->Flash->success(__('The Inventory Transfer Vouchers has been saved.'));
                return $this->redirect(['action' => 'Index']);
			}
			
		}
		
		
		foreach($inventoryTransferVouchers->inventory_transfer_voucher_rows as $itv_rows)
		{
			$serialNoDetail = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->find()
									 ->where(['itv_id'=>$inventoryTransferVouchers->id,'itv_row_id'=>$itv_rows->id,'company_id'=>$st_company_id]); 
			if($serialNoDetail->count()>0)
			{ 
				foreach($serialNoDetail as $svalue)
				{
					$serialNoparentIdExist = $this->InventoryTransferVouchers->InventoryTransferVoucherRows->SerialNumbers->find()
									 ->where(['parent_id'=>$svalue->id,'company_id'=>$st_company_id]);
					if($serialNoparentIdExist->count()>0)
					{
						$parentSerialNo[$svalue->id] = $svalue->id;
					}
				}
			}
		}
		$customers = $this->InventoryTransferVouchers->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->contain(['CustomerAddress'=>function($q){
			return $q
			->where(['CustomerAddress.default_address'=>1]);
		}])->matching(
					'CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		
		$vendor = $this->InventoryTransferVouchers->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);	
	
		
		$this->set(compact('display_items','inventoryTransferVoucher','inventoryTransferVouchers','display_items','id','financial_month_first','financial_month_last','parentSerialNo','customers','vendor'));
		
	}
	
	
	public function weightedAvgCostIvs($item_id=null,$transaction_date){ 
	
			$this->viewBuilder()->layout('');
			$session = $this->request->session();
			$st_company_id = $session->read('st_company_id');
			//pr($transaction_date); exit;
			$Items = $this->InventoryTransferVouchers->ItemLedgers->Items->get($item_id, [
				'contain' => ['ItemCompanies'=>function($q) use($st_company_id){
					return $q->where(['company_id'=>$st_company_id]);
				}]
			]);
			$to_date = date('Y-m-d');
			$unit_rate=0;
			if($Items->item_companies[0]->serial_number_enable == '0'){   
				$stock=[];  $sumValue=0; $stockNew=[];
					$StockLedgers=$this->InventoryTransferVouchers->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$Items->id,'ItemLedgers.company_id'=>$st_company_id,'processed_on <='=>$transaction_date])->order(['ItemLedgers.processed_on'=>'ASC']);
					
					foreach($StockLedgers as $StockLedger){  
						if($StockLedger->in_out=='In'){ 
							/* if(($StockLedger->source_model=='Grns' and $StockLedger->rate_updated=='Yes') or ($StockLedger->source_model!='Grns')){
								for($inc=0.01;$inc<$StockLedger->quantity;$inc+=0.01){
									$stock[]=$StockLedger->rate;
								}
							} */
							$stockNew[]=['qty'=>$StockLedger->quantity, 'rate'=>$StockLedger->rate];
						}
					}
					
					foreach($StockLedgers as $StockLedger){
						if($StockLedger->in_out=='Out'){	
							/* if(sizeof(@$stock) > 0){
								$stock= array_slice($stock, $StockLedger->quantity*100); 	
							} */
							
							if(sizeof(@$stockNew)==0){
							break;
							}
							
							$outQty=$StockLedger->quantity;
							a:
							if(sizeof(@$stockNew)==0){
								break;
							}
							$R=@$stockNew[0]['qty']-$outQty;
							if($R>0){
								$stockNew[0]['qty']=$R;
							}
							else if($R<0){
								unset($stockNew[0]);
								@$stockNew=array_values(@$stockNew);
								$outQty=abs($R);
								goto a;
							}
							else{
								unset($stockNew[0]);
								$stockNew=array_values($stockNew);
							}
						}
					}
				//	pr($Items->id); exit;
					
					
					$closingValue=0;
					$total_stock=0;
					$total_amt=0;
					foreach($stockNew as $qw){
						//pr($qw); 
							$total_stock+=$qw['qty'];
							$total_amt+=$qw['rate']*$qw['qty'];
						
					} 
						if($total_amt > 0 && $total_stock > 0){
							 $unit_rate = $total_amt/$total_stock; 
						}
						
					
				
			}
			

				
		$unit_rate=round($unit_rate,3);
			return $unit_rate; 
		//exit;	
	}
	
	public function DeleteRow($row_id=null,$id=null,$item_id=null){
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$ItemLedger=$this->InventoryTransferVouchers->InventoryTransferVoucherRows->deleteAll(['id'=>$row_id]); 
		$this->InventoryTransferVouchers->Items->SerialNumbers->deleteAll(['itv_id'=>$id,'company_id'=>$st_company_id,'SerialNumbers.status'=>'In','SerialNumbers.item_id'=>$item_id]); 
		$ItemLedger=$this->InventoryTransferVouchers->InventoryTransferVoucherRows->Items->ItemLedgers->deleteAll(['source_id'=>$id,'item_id'=>$item_id,'in_out'=>'In','source_model'=>'Inventory Transfer Voucher']);
		return $this->redirect(['action' => 'edit/'.$id]);
	}
	public function itvData(){
	$this->viewBuilder()->layout('');
	$session = $this->request->session();
	$st_company_id = $session->read('st_company_id');
	
	$InventoryTransferVoucher=$this->InventoryTransferVouchers->find()->where(['company_id'=>$st_company_id]);
	?>
	<table border="1">
		<tr>
			<th>ID</th>
			<th>No</th>
			<th>Transaction Date</th>
			<th>itemledgers</th>
		</tr>
		<?php foreach($InventoryTransferVoucher as $InventoryTransferVoucher){
			$itemledgers=$this->InventoryTransferVouchers->ItemLedgers->find()->where(['source_model LIKE'=>'%Inventory Transfer Voucher%','source_id'=>$InventoryTransferVoucher->id]);
		?>
		<tr>
			<td><?php echo $InventoryTransferVoucher->id; ?></td>
			<td><?= h('#'.$InventoryTransferVoucher->voucher_no) ?></td>
			<td><?php echo strtotime($InventoryTransferVoucher->transaction_date); ?></td>
			<td>
				<?php 
				$q=0;
				foreach($itemledgers as $itemledger){ 
					$q+=strtotime($itemledger->processed_on);
				}
				echo $q/sizeof($itemledgers->toArray());
				?>
			</td>
		</tr>
		<?php } ?>
	</table>
	<?php
	exit;
	}
}
<?php
namespace App\Controller;

use App\Controller\AppController;


class JobCardsController extends AppController
{
    
    public function index($status=null)
    {
		$this->viewBuilder()->layout('index_layout');
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$inventory_voucher_status=$this->request->query('inventory_voucher');
		
		$where1=[];
		$jc_no=$this->request->query('jc_no');
		$so_no=$this->request->query('so_no');
		$jc_file_no=$this->request->query('jc_file_no');
		$so_file_no=$this->request->query('so_file_no');
		$Required_From=$this->request->query('Required_From');
		$Required_To=$this->request->query('Required_To');
		$Created_From=$this->request->query('Created_From');
		$Created_To=$this->request->query('Created_To');
		$items=$this->request->query('items');
		$customer_id=$this->request->query('customers');
		$st_year_id = $session->read('st_year_id');
		$this->set(compact('items','jc_no','so_no','jc_file_no','so_file_no','Required_From','Required_To','Created_From','Created_To','customer_id','status'));
		if(!empty($jc_no)){
			$where1['JobCards.jc2']=$jc_no;
		}
		if(!empty($customers)){
			$where1['Customers.id LIKE']=$customers;
		}
		if(!empty($so_no)){
			$where1['SalesOrders.so2']=$so_no;
		}
		
		if(!empty($jc_file_no)){
			$where1['JobCards.jc3 LIKE']='%'.$jc_file_no.'%';
		}
		
		if(!empty($so_file_no)){
			$where1['SalesOrders.so3 LIKE']='%'.$so_file_no.'%';
		}
		
		if(!empty($Required_From)){
			$Required_From=date("Y-m-d",strtotime($this->request->query('Required_From')));
			$where1['JobCards.required_date >=']=$Required_From;
		}
		if(!empty($Required_To)){
			$Required_To=date("Y-m-d",strtotime($this->request->query('Required_To')));
			$where1['JobCards.required_date <=']=$Required_To;
		}
		if(!empty($Created_From)){
			$Created_From=date("Y-m-d",strtotime($this->request->query('Created_From')));
			$where1['JobCards.created_on >=']=$Created_From;
		}
		if(!empty($Created_To)){
			$Created_To=date("Y-m-d",strtotime($this->request->query('Created_To')));
			$where1['JobCards.created_on <=']=$Created_To;
		}
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['JobCards.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
		//pr($wheree);exit;
		$where=[];
		if($status==null or $status=='Pending'){
			if(!empty($items)){ 
				$jobCards=$this->JobCards->find()
				->contain(['SalesOrders','JobCardRows'=>['Items']])
				->matching(
						'JobCardRows.Items', function ($q) use($items) {
							return $q->where(['Items.id' =>$items]);
						}
					)
				->where(['JobCards.company_id'=>$st_company_id,'JobCards.status'=>'Pending'])
				->where($wheree)
				->order(['JobCards.jc2' => 'DESC']);
			}else if(!empty($customer_id)){
				$jobCards = $this->JobCards->find()->contain(['SalesOrders'=>['Customers'],'JobCardRows'=>['Items']])
				->where($where1)->where($wheree)->where(['JobCards.company_id'=>$st_company_id,'JobCards.status'=>'Pending'])->order(['JobCards.jc2' => 'DESC'])
				->matching(
						'SalesOrders.Customers', function ($q) use($customer_id) {
							return $q->where(['Customers.id' =>$customer_id]);
						}
				);
			}else{ 
				$jobCards = $this->JobCards->find()->contain(['SalesOrders'=>['Customers'],'JobCardRows'=>['Items']])
				->where($where1)->where($wheree)->where(['JobCards.company_id'=>$st_company_id,'JobCards.status'=>'Pending'])->order(['JobCards.jc2' => 'DESC']);
			}
		}elseif($status=='Closed'){ 
			if(!empty($items)){ 
				$jobCards=$this->JobCards->find()
				->contain(['SalesOrders','JobCardRows'=>['Items']])
				->matching(
						'JobCardRows.Items', function ($q) use($items) {
							return $q->where(['Items.id' =>$items]);
						}
					)
				->where($wheree)	
				->where(['JobCards.company_id'=>$st_company_id,'JobCards.status IN'=>['Closed','Pending','JobCards.financial_year_id'=>$st_year_id]])
				->order(['JobCards.jc2' => 'DESC'])	
				;
			}else if(!empty($customer_id)){
				$jobCards = $this->JobCards->find()->contain(['SalesOrders'=>['Customers'],'JobCardRows'=>['Items']])
				->where($where1)->where($wheree)->where(['JobCards.company_id'=>$st_company_id,'JobCards.financial_year_id'=>$st_year_id,'JobCards.status IN'=>['Closed','Pending']])->order(['JobCards.jc2' => 'DESC'])
				->matching(
						'SalesOrders.Customers', function ($q) use($customer_id) {
							return $q->where(['Customers.id' =>$customer_id]);
						}
				);
			}else{  
				$jobCards = $this->JobCards->find()->contain(['SalesOrders'=>['Customers'],'JobCardRows'=>['Items']])
				->where($where1)->where($wheree)->where(['JobCards.company_id'=>$st_company_id,'JobCards.status IN'=>['Closed','Pending'],'JobCards.financial_year_id'=>$st_year_id])->order(['JobCards.jc2' => 'DESC']);
			}
		} 
		//pr($jobCards->toArray());exit;
		$SalesOrderQty=[];
		$InvoiceQty=[];
		$InventoryVoucherQty=[];
		$JCs=$this->JobCards->find()->contain(['SalesOrders'=>['Invoices'=>['Ivs'],'SalesOrderRows']])->toArray();
		
		foreach($JCs as $jc ){ 
			foreach($jc->sales_order->sales_order_rows as $sales_order_row){ 
					$Item_data=$this->JobCards->SalesOrders->SalesOrderRows->Items->get($sales_order_row->item_id);
					if($Item_data->source=="Purchessed/Manufactured" or $Item_data->source=="Manufactured"){
						if($sales_order_row->source_type=="Manufactured" or $sales_order_row->source_type==""){
							@$SalesOrderQty[@$sales_order_row['sales_order_id']]+=@$sales_order_row['quantity'];
						}	
					}
				
					
			} // exit;
		}
		$Jobs=$this->JobCards->find()->contain(['SalesOrders'=>['Invoices'=>['InvoiceRows'=>['IvRows']]]])->toArray();
		foreach($Jobs as $jc ){ //pr($jc->sales_order->invoices); exit;
			foreach($jc->sales_order->invoices as $invoice){   //pr($invoice); 
				foreach($invoice->invoice_rows as $invoice_row){
					$Item_details=$this->JobCards->SalesOrders->SalesOrderRows->Items->get($invoice_row->item_id);	
						if($Item_details->source=="Purchessed/Manufactured" or $Item_details->source=="Manufactured" or $Item_details->source=="Assembled"){
						if($sales_order_row->source_type=="Manufactured" or $sales_order_row->source_type==""){					
							@$InvoiceQty[@$invoice->sales_order_id]+=@$invoice_row->quantity;
							$outExist = $this->JobCards->SalesOrders->Invoices->Ivs->exists(['Ivs.invoice_id' => $invoice->id]);
							if($outExist > 0){
								@$InventoryVoucherQty[@$invoice->sales_order_id]+=@$invoice_row->quantity;
							}
						}
					}
				}
			}
		}
		
		
		
		$Customers = $this->JobCards->Customers->find('list')->order(['Customers.customer_name' => 'ASC']);
		$Items = $this->JobCards->JobCardRows->Items->find('list')->order(['Items.name' => 'ASC']);
		$this->set(compact('jobCards','status','Customers','Items','url','SalesOrderQty','InvoiceQty','InventoryVoucherQty'));
        $this->set('_serialize', ['jobCards']);
    }

	public function CloseJobCards($ids=null,$jobcardreason=null){
		$id = $this->EncryptingDecrypting->decryptData($ids);
		$jobcards = $this->JobCards->get($id);
		//$quotation_reason=$this->Quotations->QuotationCloseReasons->get($reason);
		$jobcards->reason=$jobcardreason;
		$jobcards->status='Closed';
		$jobcards->closing_date=date("Y-m-d");
		//pr($id);exit;
		 if ($this->JobCards->save($jobcards)) {
            echo 'The jobcards has been closed.';
        } else {
            echo 'The jobcards could not be closed. Please, try again.';
        }
		exit;
		//return $this->redirect(['action' => 'index']);
	}
	
	public function exportExcel($status=null){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		//pr($status);exit;
		$inventory_voucher_status=$this->request->query('inventory_voucher');
		$st_year_id = $session->read('st_year_id');
		$where1=[];
		$jc_no=$this->request->query('jc_no');
		$so_no=$this->request->query('so_no');
		$jc_file_no=$this->request->query('jc_file_no');
		$so_file_no=$this->request->query('so_file_no');
		$Required_From=$this->request->query('Required_From');
		$Required_To=$this->request->query('Required_To');
		$Created_From=$this->request->query('Created_From');
		$Created_To=$this->request->query('Created_To');
		$items=$this->request->query('items');
		$customer_id=$this->request->query('customers');

		$this->set(compact('items','jc_no','so_no','jc_file_no','so_file_no','Required_From','Required_To','Created_From','Created_To','customer_id','status'));
		if(!empty($jc_no)){
			$where1['JobCards.jc2']=$jc_no;
		}
		if(!empty($customers)){
			$where1['Customers.id LIKE']=$customers;
		}
		if(!empty($so_no)){
			$where1['SalesOrders.so2']=$so_no;
		}
		
		if(!empty($jc_file_no)){
			$where1['JobCards.jc3 LIKE']='%'.$jc_file_no.'%';
		}
		
		if(!empty($so_file_no)){
			$where1['SalesOrders.so3 LIKE']='%'.$so_file_no.'%';
		}
		
		if(!empty($Required_From)){
			$Required_From=date("Y-m-d",strtotime($this->request->query('Required_From')));
			$where1['JobCards.required_date >=']=$Required_From;
		}
		if(!empty($Required_To)){
			$Required_To=date("Y-m-d",strtotime($this->request->query('Required_To')));
			$where1['JobCards.required_date <=']=$Required_To;
		}
		if(!empty($Created_From)){
			$Created_From=date("Y-m-d",strtotime($this->request->query('Created_From')));
			$where1['JobCards.created_on >=']=$Created_From;
		}
		if(!empty($Created_To)){
			$Created_To=date("Y-m-d",strtotime($this->request->query('Created_To')));
			$where1['JobCards.created_on <=']=$Created_To;
		}
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['JobCards.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
				$where=[];
		if($status==null or $status=='Pending'){
			if(!empty($items)){ 
				$jobCards=$this->JobCards->find()
				->contain(['SalesOrders','JobCardRows'=>['Items']])
				->matching(
						'JobCardRows.Items', function ($q) use($items) {
							return $q->where(['Items.id' =>$items]);
						}
					)
				->where(['JobCards.company_id'=>$st_company_id,'JobCards.status'=>'Pending'])
				->where($wheree)
				->order(['JobCards.jc2' => 'DESC']);
			}else if(!empty($customer_id)){
				$jobCards = $this->JobCards->find()->contain(['SalesOrders'=>['Customers'],'JobCardRows'=>['Items']])
				->where($where1)->where($wheree)->where(['JobCards.company_id'=>$st_company_id,'JobCards.status'=>'Pending'])->order(['JobCards.jc2' => 'DESC'])
				->matching(
						'SalesOrders.Customers', function ($q) use($customer_id) {
							return $q->where(['Customers.id' =>$customer_id]);
						}
				);
			}else{ 
				$jobCards = $this->JobCards->find()->contain(['SalesOrders'=>['Customers'],'JobCardRows'=>['Items']])
				->where($where1)->where($wheree)->where(['JobCards.company_id'=>$st_company_id,'JobCards.status'=>'Pending'])->order(['JobCards.jc2' => 'DESC']);
			}
		}elseif($status=='Closed'){ 
			if(!empty($items)){ 
				$jobCards=$this->JobCards->find()
				->contain(['SalesOrders','JobCardRows'=>['Items']])
				->matching(
						'JobCardRows.Items', function ($q) use($items) {
							return $q->where(['Items.id' =>$items]);
						}
					)
				->where($wheree)	
				->where(['JobCards.company_id'=>$st_company_id,'JobCards.financial_year_id'=>$st_year_id,'JobCards.status IN'=>['Closed','Pending']])
				->order(['JobCards.jc2' => 'DESC'])	
				;
			}else if(!empty($customer_id)){
				$jobCards = $this->JobCards->find()->contain(['SalesOrders'=>['Customers'],'JobCardRows'=>['Items']])
				->where($where1)->where($wheree)->where(['JobCards.company_id'=>$st_company_id,'JobCards.financial_year_id'=>$st_year_id,'JobCards.status IN'=>['Closed','Pending']])->order(['JobCards.jc2' => 'DESC'])
				->matching(
						'SalesOrders.Customers', function ($q) use($customer_id) {
							return $q->where(['Customers.id' =>$customer_id]);
						}
				);
			}else{ 
				$jobCards = $this->JobCards->find()->contain(['SalesOrders'=>['Customers'],'JobCardRows'=>['Items']])
				->where($where1)->where($wheree)->where(['JobCards.company_id'=>$st_company_id,'JobCards.financial_year_id'=>$st_year_id,'JobCards.status IN'=>['Closed','Pending']])->order(['JobCards.jc2' => 'DESC']);
			}
		} 
		
		$SalesOrderQty=[];
		$InvoiceQty=[];
		$InventoryVoucherQty=[];
		$JCs=$this->JobCards->find()->contain(['SalesOrders'=>['Invoices'=>['Ivs'],'SalesOrderRows']])->toArray();
		
		foreach($JCs as $jc ){ 
			foreach($jc->sales_order->sales_order_rows as $sales_order_row){ 
					$Item_data=$this->JobCards->SalesOrders->SalesOrderRows->Items->get($sales_order_row->item_id);
					if($Item_data->source=="Purchessed/Manufactured" or $Item_data->source=="Manufactured"){
						if($sales_order_row->source_type=="Manufactured" or$sales_order_row->source_type==""){
							@$SalesOrderQty[@$sales_order_row['sales_order_id']]+=@$sales_order_row['quantity'];
						}	
					}
				
					
			} // exit;
		}
		$Jobs=$this->JobCards->find()->contain(['SalesOrders'=>['Invoices'=>['InvoiceRows'=>['IvRows']]]])->toArray();
		foreach($Jobs as $jc ){ //pr($jc->sales_order->invoices); exit;
			foreach($jc->sales_order->invoices as $invoice){   //pr($invoice); 
				foreach($invoice->invoice_rows as $invoice_row){
					$Item_details=$this->JobCards->SalesOrders->SalesOrderRows->Items->get($invoice_row->item_id);	
						if($Item_details->source=="Purchessed/Manufactured" or $Item_details->source=="Manufactured" or $Item_details->source=="Assembled"){
						if($sales_order_row->source_type=="Manufactured" or$sales_order_row->source_type==""){					
							@$InvoiceQty[@$invoice->sales_order_id]+=@$invoice_row->quantity;
							$outExist = $this->JobCards->SalesOrders->Invoices->Ivs->exists(['Ivs.invoice_id' => $invoice->id]);
							if($outExist > 0){
								@$InventoryVoucherQty[@$invoice->sales_order_id]+=@$invoice_row->quantity;
							}
						}
					}
				}
			}
		}
		
		//pr($jobCards->toArray()); exit;
		
		$Customers = $this->JobCards->Customers->find('list')->order(['Customers.customer_name' => 'ASC']);
		$Items = $this->JobCards->JobCardRows->Items->find('list')->order(['Items.name' => 'ASC']);
		$this->set(compact('jobCards','status','Customers','Items','url','SalesOrderQty','InvoiceQty','InventoryVoucherQty'));
        //$this->set('_serialize', ['jobCards']);
	}
    /**
     *   method
     *
     * @param string|null $id Job Card id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
	 
	 public function DataMigrate()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$SalesOrders=$this->JobCards->SalesOrders->SalesOrderRows->find();
		
		foreach($SalesOrders as $SalesOrder){ //pr($SalesOrder); exit;
			$JobCard=$this->JobCards->find()->contain(['JobCardRows'])->where(['JobCards.sales_order_id'=>$SalesOrder->sales_order_id])->toArray();
			
			if($JobCard){ pr($JobCard); exit;
				if(sizeof($Invoices) > 0){ //echo "exist"; echo "<br>";
					foreach($Invoices as $Invoice){
						foreach($Invoice->invoice_rows as $invoice_row){ //pr($invoice_row->item_id); exit;
							$query = $this->Invoices->InvoiceRows->query();
							$query->update()
								->set(['sales_order_row_id' => $SalesOrder->id])
								->where(['item_id' => $SalesOrder->item_id,'invoice_id'=>$Invoice->id])
								->execute();
							}
					}
				}
			}
		}
		echo "done"; exit;
	} 
	 
	 
	 
    public function view($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$id = $this->EncryptingDecrypting->decryptData($id);
        $jobCard = $this->JobCards->get($id, [
            'contain' => ['SalesOrders'=>['Customers','SalesOrderRows'=>['Items'=>function ($q){
					return $q->where(['SalesOrderRows.source_type != ' => 'Purchessed','Items.source !='=>'Purchessed']);
				},'JobCardRows'=>['Items']]],'Creator', 'Companies','Customers'=>['CustomerAddress']]
        ]);
//pr($jobCard->customer->customer_address[0]->address); exit;
        $this->set('jobCard', $jobCard);
        $this->set('_serialize', ['jobCard']);
    }
	
	/* public function DataMigrate()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$Quotations=$this->JobCards->SalesOrders->SalesOrderRows->find(); 
	//	pr($Quotations->toArray()); exit;
		foreach($Quotations as $Quotation){
			$JobCards=$this->JobCards->find()->contain(['JobCardRows'])->where(['JobCards.sales_order_id'=>$Quotation->sales_order_id])->toArray();
			pr($JobCards);
			if(sizeof($JobCards) > 0){
				foreach($JobCards as $JobCard){
					foreach($JobCard->job_card_rows as $job_card_row){ 
						$query = $this->JobCards->JobCardRows->query();
						$query->update()
							->set(['sales_order_row_id' => $Quotation->id])
							->where(['item_id' => $Quotation->item_id,'job_card_id'=>$JobCard->id])
							->execute();
						}
				}
			}
		}
		exit;
	} 
 */
    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add($salesOrderId)
    {
		$this->viewBuilder()->layout('index_layout');
		$s_employee_id=$this->viewVars['s_employee_id'];
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$sales_order_id=$salesOrderId;
		$sales_order_id = $this->EncryptingDecrypting->decryptData($sales_order_id);
		if(!empty($sales_order_id)){
			$salesOrder = $this->JobCards->SalesOrders->get($sales_order_id, [
				'contain' => ['Customers','SalesOrderRows'=>['Items'=>function ($q) use($st_company_id){
					return $q->where(['SalesOrderRows.source_type != ' => 'Purchessed','Items.source !='=>'Purchessed'])->contain(['ItemCompanies'=>function($q) use($st_company_id) {
							return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
						}]);
				}]]
			]);
		}
		
		
		$jobCard = $this->JobCards->newEntity();
        if ($this->request->is('post')) {
			$jobCard = $this->JobCards->patchEntity($jobCard, $this->request->data);
			
			$last_jc_no=$this->JobCards->find()->select(['jc2'])->where(['company_id' => $st_company_id])->order(['jc2' => 'DESC'])->first();

			if($last_jc_no){
				@$last_jc_no->jc2=$last_jc_no->jc2+1;
			}else{
				@$last_jc_no->jc2=1;
			}
			$jobCard->required_date=date("Y-m-d",strtotime($jobCard->required_date)); 
			//pr($jobCard->required_date); exit;
			$jobCard->created_by=$s_employee_id; 
			$jobCard->jc2=$last_jc_no->jc2; 
			$jobCard->sales_order_id=$sales_order_id;
			$jobCard->company_id=$st_company_id;
			$jobCard->customer_po_no=$jobCard->customer_po_no;
			$jobCard->created_on=date("Y-m-d");
			$jobCard->status='Pending';
			foreach($jobCard->job_card_rows as $job_card_row){
					$job_card_row->sales_order_id=$sales_order_id;
				}
			if ($this->JobCards->save($jobCard)) {
					$query = $this->JobCards->SalesOrders->query();
					$query->update()
						->set(['job_card_status' => 'Converted'])
						->where(['id' => $jobCard->sales_order_id])
						->execute();
                $this->Flash->success(__('The job card has been saved.'));
				$jobCard->id = $this->EncryptingDecrypting->encryptData($jobCard->id);
				return $this->redirect(['action' => 'view',$jobCard->id]);
            } else { 
                $this->Flash->error(__('The job card could not be saved. Please, try again.'));
            }
        }
			$items = $this->JobCards->Items->find()->where(['source IN'=>['Purchessed','Purchessed/Manufactured']])->order(['Items.name' => 'ASC'])->matching(
						'ItemCompanies', function ($q) use($st_company_id) {
							return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
						}
					);
			$ItemsOptions=[];
			foreach($items as $item){ 
						$ItemsOptions[]=['value'=>$item->id,'text'=>$item->name,'serial_number_enable'=>@$item->_matchingData['ItemCompanies']->serial_number_enable];
			}
		
			$Item_datas = $this->JobCards->Items->find()->where(['source IN'=>['Purchessed','Purchessed/Manufactured']])->order(['Items.name' => 'ASC'])->matching(
						'ItemCompanies', function ($q) use($st_company_id) {
							return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
						}
					);		
					
			$ItemsOptionsData=[];
			foreach($Item_datas as $item){ 
						$ItemsOptionsData[]=['value'=>$item->id,'text'=>$item->name,'serial_number_enable'=>@$item->_matchingData['ItemCompanies']->serial_number_enable];
			}		
			
        $companies = $this->JobCards->Companies->find('list', ['limit' => 200]);
        $this->set(compact('jobCard', 'salesOrder', 'companies','items','customers','last_jc_no','ItemsOptions','ItemsOptionsData'));
        $this->set('_serialize', ['jobCard']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Job Card id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$s_employee_id=$this->viewVars['s_employee_id'];
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$id = $this->EncryptingDecrypting->decryptData($id);
		$jobCard = $this->JobCards->get($id, [
            'contain' => ['SalesOrders'=>['Customers','SalesOrderRows'=>['Items'=>function ($q){
					return $q->where(['SalesOrderRows.source_type != ' => 'Purchessed','Items.source !='=>'Purchessed']);
				},'JobCardRows'=>['Items']]],'Creator', 'Companies','Customers']
        ]);
	//	pr($jobCard); exit;
		$closed_month=$this->viewVars['closed_month'];
		
		if(!in_array(date("m-Y",strtotime($jobCard->created_on)),$closed_month))
		{
	
			$Em = new FinancialYearsController;
			$financial_year_data = $Em->checkFinancialYear($jobCard->created_on);
					
			if ($this->request->is(['patch', 'post', 'put'])) {
				$jobCard = $this->JobCards->patchEntity($jobCard, $this->request->data);
				$jobCard->required_date=date("Y-m-d",strtotime($jobCard->required_date)); 
				$jobCard->created_by=$s_employee_id; 
				$jobCard->company_id=$st_company_id;
				$jobCard->customer_po_no=$jobCard->customer_po_no;
				
				$jobCard->created_on=date("Y-m-d");
				$jobCard->sales_order_id=$jobCard->sales_order->id;
				foreach($jobCard->job_card_rows as $job_card_row){
						$job_card_row->sales_order_id=$jobCard->sales_order_id;
					}
				
				if ($this->JobCards->save($jobCard)) {
					$query = $this->JobCards->SalesOrders->query();
						$query->update()
							->set(['job_card_status' => 'Converted'])
							->where(['id' => $jobCard->sales_order_id])
							->execute();
					$jobCard->id = $this->EncryptingDecrypting->encryptData($jobCard->id);
					$this->Flash->success(__('The job card has been saved.'));
					return $this->redirect(['action' => 'view',$jobCard->id]);
				} else { 
					$this->Flash->error(__('The job card could not be saved. Please, try again.'));
				}
			}
			
			$items = $this->JobCards->Items->find()->where(['source IN'=>['Purchessed','Purchessed/Manufactured']])->order(['Items.name' => 'ASC'])->matching(
						'ItemCompanies', function ($q) use($st_company_id) {
							return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0])
							->orWhere(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 1]);
						}
					);
			$ItemsOptions=[];
			foreach($items as $item){ 
						$ItemsOptions[]=['value'=>$item->id,'text'=>$item->name,'serial_number_enable'=>@$item->_matchingData['ItemCompanies']->serial_number_enable];
			}
			
			$Item_datas = $this->JobCards->Items->find()->where(['source IN'=>['Purchessed','Purchessed/Manufactured']])->order(['Items.name' => 'ASC'])->matching(
						'ItemCompanies', function ($q) use($st_company_id) {
							return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
						}
					);		
					
			$ItemsOptionsData=[];
			foreach($Item_datas as $item){ 
						$ItemsOptionsData[]=['value'=>$item->id,'text'=>$item->name,'serial_number_enable'=>@$item->_matchingData['ItemCompanies']->serial_number_enable];
			}		
			$this->set(compact('jobCard', 'salesOrders', 'companies','items','financial_year_data','ItemsOptions','ItemsOptionsData'));
			$this->set('_serialize', ['jobCard']);
		}
		else
		{
			$this->Flash->error(__('This month is locked.'));
			return $this->redirect(['action' => 'index']);
		}
    }

    /**
     * Delete method
     *
     * @param string|null $id Job Card id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $jobCard = $this->JobCards->get($id);
        if ($this->JobCards->delete($jobCard)) {
            $this->Flash->success(__('The job card has been deleted.'));
        } else {
            $this->Flash->error(__('The job card could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
	
	public function PendingSalesorderForJobcard()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$where=[];
		$sales_order_no=$this->request->query('sales_order_no');
		$file=$this->request->query('file');
		$customer=$this->request->query('customer');
		$po_no=$this->request->query('po_no');
		$From=$this->request->query('From');
		$To=$this->request->query('To');
		
		$this->set(compact('sales_order_no','customer','po_no','From','To','file'));
		
		if(!empty($sales_order_no)){
			$where['SalesOrders.so2 LIKE']=$sales_order_no;
		}
		if(!empty($file)){
			$where['SalesOrders.so3 LIKE']='%'.$file.'%';
		}
		if(!empty($customer)){
			$where['Customers.customer_name LIKE']='%'.$customer.'%';
		}
		if(!empty($po_no)){
			$where['SalesOrders.customer_po_no LIKE']='%'.$po_no.'%';
		}
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['SalesOrders.po_date >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['SalesOrders.po_date <=']=$To;
		}
		
		/* $this->paginate = [
            'contain' => ['Customers','JobCards','SalesOrderRows'=>['Items'=>function ($q){
				return $q->where(['Items.source'=>'Purchessed/Manufactured']);
			}]]
        ]; */
		
		$SalesOrders=
			$this->JobCards->SalesOrders->find()->where($where)->select(['total_rows' => 
				$this->JobCards->SalesOrders->find()->func()->count('SalesOrderRows.id')])
					->matching(
						'SalesOrderRows.Items', function ($q) {
							return $q->where(['Items.source !='=>'Purchessed']);
						}
					)
					->contain(['Customers','JobCards','SalesOrderRows'=>['Items'=>function ($q){
							return $q->where(['Items.source'=>'Purchessed/Manufactured']);
					}]])
					->group(['SalesOrders.id'])
					->autoFields(true)
					->having(['total_rows >' => 0])
					->where(['job_card_status'=>'Pending'])
					->where(['SalesOrders.company_id'=>$st_company_id])
					->order(['SalesOrders.id' => 'DESC']);
			
		//pr($SalesOrders); exit;
	
		$this->set(compact('SalesOrders'));
        $this->set('_serialize', ['jobCard']);
    }
	public function PreAdd()
    {
		$this->viewBuilder()->layout('index_layout');
		$sales_order_id=$this->request->query('sales-order');
		$sales_order_id_en=$this->request->query('sales-order');
		$sales_order_id = $this->EncryptingDecrypting->decryptData($sales_order_id);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$count_sales_item = 0;   
		$jobCard = $this->JobCards->SalesOrders->get($sales_order_id, [
            'contain' => ['Customers','SalesOrderRows'=>['Items'=>function ($q) use($st_company_id){
					return $q->where(['Items.source' => 'Purchessed/Manufactured'])->contain(['ItemCompanies'=>function($q) use($st_company_id) {
							return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
						}]);
				}]]
        ]);
		
		if ($this->request->is(['patch', 'post', 'put'])) {
            $jobCard = $this->JobCards->patchEntity($jobCard, $this->request->data);
            if ($this->JobCards->save($jobCard)) {
				
				$query = $this->JobCards->SalesOrders->SalesOrderRows->find()->where(['sales_order_id'=>$sales_order_id]);
				$count_sales_item = $query->count();

				foreach($jobCard->sales_order_rows as $sales_order_row ){

						if($sales_order_row['source_type'] =='Purchessed')
						{
							$count_sales_item = $count_sales_item - 1; 
							$query = $this->JobCards->SalesOrders->SalesOrderRows->query();
								$query->update()
								->set(['source_type' =>$sales_order_row['source_type']])
								->where(['id' => $sales_order_row['id']])
								->execute();	

						}
						else
						{
							$query = $this->JobCards->SalesOrders->SalesOrderRows->query();
								$query->update()
								->set(['source_type' =>$sales_order_row['source_type']])
								->where(['id' => $sales_order_row['id']])
								->execute();	
						}

					
					}

					if($count_sales_item == 0)
					{
						$this->Flash->success(__('All items are purchessed job card  not created.'));
						$this->redirect(['controller' =>'JobCards','action' => 'index']);
					}
					else
					{
						$this->Flash->success(__('The job card has been saved.'));
						$sales_order_id = $this->EncryptingDecrypting->encryptData($sales_order_id);
						//pr($sales_order_id);exit;
						$this->redirect(['controller' =>'JobCards','action' => 'Add',$sales_order_id]);
					}
					
                
            } else { 
                $this->Flash->error(__('The job card could not be saved. Please, try again.'));
            }
        }
        
        $this->set(compact('jobCard'));
        $this->set('_serialize', ['jobCards']);
	
	}
	public function PreEdit()
    {
		$this->viewBuilder()->layout('index_layout');
		$sales_order_id=$this->request->query('sales-order');
		$id=$this->request->query('job-card');
		
		$jobCard = $this->JobCards->SalesOrders->get($sales_order_id, [
            'contain' => ['Customers','SalesOrderRows'=>['Items'=>function ($q){
					return $q->where(['Items.source'=>'Purchessed/Manufactured']);
				}]]
        ]);
		
		
		if ($this->request->is(['patch', 'post', 'put'])) {
            $jobCard = $this->JobCards->patchEntity($jobCard, $this->request->data);
            if ($this->JobCards->save($jobCard)) {
				
					foreach($jobCard->sales_order_rows as $sales_order_row ){
						if($sales_order_row['source_type']=="Purchessed"){
							$this->JobCards->JobCardRows->deleteAll(['sales_order_row_id' => $sales_order_row['id']]);
						}
						$query = $this->JobCards->SalesOrders->SalesOrderRows->query();
							$query->update()
							->set(['source_type' =>$sales_order_row['source_type']])
							->where(['id' => $sales_order_row['id']])
							->execute();
					} 
		
                $this->Flash->success(__('The job card has been saved.'));
                $this->redirect(['controller' =>'JobCards','action' => 'Edit/'.$id]);
            } else { 
                $this->Flash->error(__('The job card could not be saved. Please, try again.'));
            }
        }
        
        $this->set(compact('jobCard'));
        $this->set('_serialize', ['jobCards']);
	
	}
	
	public function close($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
		$JobCard = $this->JobCards->get($id);
		$JobCard->status="Closed";
		if ($this->JobCards->save($JobCard)) {
			$this->Flash->success(__('The job card has closed.'));
		} else {
			$this->Flash->error(__('The job card could not be closed. Please, try again.'));
		}
		
        
        return $this->redirect(['action' => 'index']);
    }
	
	public function dataRepair()
    {
		$this->viewBuilder()->layout('');
		$JobCards = $this->JobCards->find();
		foreach($JobCards as $JobCard){
			$JobCardRows=$this->JobCards->JobCardRows->find()->where(['JobCardRows.job_card_id'=>$JobCard->id]);
			
			foreach($JobCardRows as $JobCardRow){ 
				$SalesOrderRowexists = $this->JobCards->SalesOrders->SalesOrderRows->exists(['SalesOrderRows.id' => $JobCardRow->sales_order_row_id ]);
				//pr($JobCardRow->sales_order_row_id);
				if(!$SalesOrderRowexists){ 	
					 echo $JobCardRow->job_card_id."<br>";
				} 
			
		} 
		
	} exit;
	}
}

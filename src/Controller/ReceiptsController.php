<?php
namespace App\Controller;

use Cake\Mailer\Email;
use App\Controller\AppController;

/**
 * Receipts Controller
 *
 * @property \App\Model\Table\ReceiptsTable $Receipts
 */
class ReceiptsController extends AppController
{

    public function initialize(){
        parent::initialize();
        
        // Include the FlashComponent
        $this->loadComponent('Flash');
        
        // Load Files model
        $this->loadModel('Files');
    }
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $receipts;

        $this->paginate = [
            'contain' => ['Users', 'Files']
        ];
        if($this->Auth->user('role') === 'gestor'){
          $receipts = $this->paginate($this->Receipts);
        }
        else{
          $receipts = $this->paginate($this->Receipts->findByUserId($this->Auth->user('id')));
        }

        $role = $this->Auth->user('role');
        $this->set(compact('receipts', 'role'));
        $this->set('_serialize', ['receipts', 'role']);
    }

    /**
     * View method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $receipt = $this->Receipts->get($id, [
            'contain' => ['Users', 'Files']
        ]);

        $this->set('receipt', $receipt);
        $this->set('_serialize', ['receipt']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function uploadFiles($request, $receipt)
    {
      $checkSave;

      if(
        !empty($request['fileOne']['name']) && 
        !empty($request['fileTwo']['name']))
      {
        $data = '';
        foreach ($request['payment'] as $datas) {
            $data .= '-' . $datas;
        }

        $uploadPath = 'uploads/files/';

        $username = $this->Auth->user('name');
        $username = str_replace(" ","",$username);
                
        $fileNameOne = $username . $data . '-1.pdf';
        $uploadFileOne = $uploadPath.$fileNameOne;

        $fileNameTwo = $username . $data . '-2.pdf';
        $uploadFileTwo = $uploadPath.$fileNameTwo;

        if(
            move_uploaded_file($request['fileOne']['tmp_name'], WWW_ROOT . $uploadFileOne) &&
            move_uploaded_file($request['fileTwo']['tmp_name'], WWW_ROOT . $uploadFileTwo))
        {
          $fileOne = $this->Files->newEntity();
          $fileOne->receipt_id = $receipt['id'];
          $fileOne->name = $fileNameOne;
          $fileOne->src = $uploadPath;

          $fileTwo = $this->Files->newEntity();
          $fileTwo->receipt_id = $receipt['id'];
          $fileTwo->name = $fileNameTwo;
          $fileTwo->src = $uploadPath;

          if($this->Files->save($fileOne) && $this->Files->save($fileTwo)){
            $checkSave = true;
          }
          else{
            $checkSave = false;
          }
        }
      }
      return $checkSave;
    }

    public function emailSendReceipt($receipt, $request)
    {
      $date = '';
      foreach ($request['payment'] as $dates) {
          $date .= '-' . $dates;
      }

      $username = $this->Auth->user('name');
      $username = str_replace(" ","",$username);
                
      $fileNameOne = $username . $date . '-1.pdf';
      $fileNameTwo = $username . $date . '-2.pdf';

      $msg = '<div>
                <div style="background-color: #19882c; color: white; border-radius: 2px; text-align: center; font-family: sans-serif">
                  <h1 style="padding-top: 13px">Comprovante do plano de saúde.</h1>                  
                  <h2 style="background-color:#0A5517; margin: 0; padding: 20px 5px 20px 5px">
                    Comprovante do plano de saúde referente a data ' 
                    . $receipt['payment'] . ' foi enviado por ' 
                    . $this->Auth->user('name') 
                    . ', SIAPE (' . $this->Auth->user('username') 
                    . ').
                  </h2>
                </div>
              </div>';

      $email = new Email('default');
      $email->setFrom(['yadsondev@gmail.com' => 'Comprovante - ' . $this->Auth->user('name')])
      ->setTo('yadson20@gmail.com')
      ->setSubject('Envio de comprovante por: ' . $this->Auth->user('name'))
      ->attachments([WWW_ROOT . 'uploads/files/' . $fileNameOne, WWW_ROOT . 'uploads/files/' . $fileNameTwo])
      ->emailFormat('html');

      if($email->send($msg)){
        return true;
      }
      return false;
    }

    public function emailAproveReceipt($userEmail)
    {
      $msg = '<div style="background-color: #19882c; color: white; border-radius: 2px; text-align: center; font-family: sans-serif">
                <h1 style="padding-top: 13px">Aprovado.</h1>                  
                <h2 style="background-color:#0A5517; margin: 0; padding: 20px 5px 20px 5px">
                  O comprovante de saúde foi aprovado.
                </h2>
              </div>';

      $email = new Email('default');
      $email->setFrom(['yadsondev@gmail.com' => 'IFPE Campus Igarassu'])
      ->setTo($userEmail)
      ->setSubject('Comprovante aprovado.')
      ->emailFormat('html');

      if($email->send($msg)){
        return true;
      }
      return false;
    }

    public function add()
    {
        $receipt = $this->Receipts->newEntity();
        if ($this->request->is('post')) {
          $receipt = $this->Receipts->patchEntity($receipt, $this->request->getData());
          $receipt->user_id = $this->Auth->user('id');
          $receipt->send = date("y/m/d");
          $receipt->aproved = 0;
          if(
              $this->Receipts->save($receipt) && 
              $this->uploadFiles($this->request->data, $receipt) && 
              $this->emailSendReceipt($receipt, $this->request->data)
          ){
            $this->Flash->success(__('The receipt has been saved.'));
          }
          else{
            $this->Flash->error(__('The receipt could not be saved. Please, try again.'));
          }
        }
        $users = $this->Receipts->Users->find('list', ['limit' => 200]);
        $this->set(compact('receipt', 'user', 'file'));
        $this->set('_serialize', ['receipt']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $receipt = $this->Receipts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $receipt = $this->Receipts->patchEntity($receipt, $this->request->getData());
            if ($this->Receipts->save($receipt)) {
                $this->Flash->success(__('The receipt has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The receipt could not be saved. Please, try again.'));
        }
        $users = $this->Receipts->Users->find('list', ['limit' => 200]);
        $this->set(compact('receipt', 'users'));
        $this->set('_serialize', ['receipt']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $receipt = $this->Receipts->get($id);
        if ($this->Receipts->delete($receipt)) {
            $this->Flash->success(__('The receipt has been deleted.'));
        } else {
            $this->Flash->error(__('The receipt could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function download($fileName = null) 
    { 
        $filePath = WWW_ROOT . 'uploads/files/' . $fileName;
        
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=" . $filename);
        readfile($filePath);
    }

    public function aprove($id = null, $email)
    {

      $receipt = $this->Receipts->get($id, [
            'contain' => []
      ]);

      if ($this->request->is(['patch', 'post', 'put'])) {
          $receipt = $this->Receipts->patchEntity($receipt, $this->request->getData());
          $receipt->aproved = 1;
          if ($this->Receipts->save($receipt) && $this->emailAproveReceipt($email)) {
              $this->Flash->success(__('The receipt has been aproved.'));

              return $this->redirect(['action' => 'index']);
          }
      }
      $users = $this->Receipts->Users->find('list', ['limit' => 200]);
      $this->set(compact('receipt', 'users'));
      $this->set('_serialize', ['receipt']);
    }

    public function disaprove($id = null)
    {
      $receipt = $this->Receipts->get($id, [
            'contain' => []
      ]);
      if ($this->request->is(['patch', 'post', 'put'])) {
          $receipt = $this->Receipts->patchEntity($receipt, $this->request->getData());
          $receipt->aproved = 0;
          if ($this->Receipts->save($receipt)) {
              $this->Flash->success(__('The receipt has been disaproved.'));

              return $this->redirect(['action' => 'index']);
          }
      }
      $users = $this->Receipts->Users->find('list', ['limit' => 200]);
      $this->set(compact('receipt', 'users'));
      $this->set('_serialize', ['receipt']);
    }
}

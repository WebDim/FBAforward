<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class QuickbooksController extends Controller
{

    private $IntuitAnywhere;
    private $context;
    private $realm;

    public function __construct(){
        if (!\QuickBooks_Utilities::initialized(env('QBO_DSN'))) {
            // Initialize creates the neccessary database schema for queueing up requests and logging
            \QuickBooks_Utilities::initialize(env('QBO_DSN'));
        }
        $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));
    }
    public function  qboConnect(){
        if ($this->IntuitAnywhere->check(env('QBO_USERNAME'), env('QBO_TENANT')) && $this->IntuitAnywhere->test(env('QBO_USERNAME'), env('QBO_TENANT'))) {
            // Set up the IPP instance
                $IPP = new \QuickBooks_IPP(env('QBO_DSN'));
            // Get our OAuth credentials from the database
            $creds = $this->IntuitAnywhere->load(env('QBO_USERNAME'), env('QBO_TENANT'));
            // Tell the framework to load some data from the OAuth store
            $IPP->authMode(
                \QuickBooks_IPP::AUTHMODE_OAUTH,
                env('QBO_USERNAME'),
                $creds);

            if (env('QBO_SANDBOX')) {
                // Turn on sandbox mode/URLs
                $IPP->sandbox(true);
            }
            // This is our current realm
            $this->realm = $creds['qb_realm'];
            // Load the OAuth information from the database
            $this->context = $IPP->context();

            return true;
        } else {
            return false;
        }
    }

    public function qboOauth(){
        if ($this->IntuitAnywhere->handle(env('QBO_USERNAME'), env('QBO_TENANT')))
        {
            ; // The user has been connected, and will be redirected to QBO_SUCCESS_URL automatically.
        }
        else
        {
            // If this happens, something went wrong with the OAuth handshake
            die('Oh no, something bad happened: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
        }
    }

    public function qboSuccess(){
        return view('qbo_success');
    }

    public function qboDisconnect(){
        $this->IntuitAnywhere->disconnect(env('QBO_USERNAME'), env('QBO_TENANT'),true);
        return redirect()->intended("/yourpath");// afer disconnect redirect where you want

    }

    public function createCustomer(){
        $this->qboConnect();
        $CustomerService = new \QuickBooks_IPP_Service_Customer();
        $Customer = new \QuickBooks_IPP_Object_Customer();
        $Customer->setTitle('Ms');
        $Customer->setGivenName('Riddhi');
        $Customer->setMiddleName('D');
        $Customer->setFamilyName('Parmar');
        $Customer->setDisplayName('Riddhi Parmar ' . mt_rand(0, 1000));
        // Terms (e.g. Net 30, etc.)
        $Customer->setSalesTermRef(4);

        // Phone #
        $PrimaryPhone = new \QuickBooks_IPP_Object_PrimaryPhone();
        $PrimaryPhone->setFreeFormNumber('917-378-3135');
        $Customer->setPrimaryPhone($PrimaryPhone);

        // Mobile #
        $Mobile = new \QuickBooks_IPP_Object_Mobile();
        $Mobile->setFreeFormNumber('917-378-3135');
        $Customer->setMobile($Mobile);

        // Fax #
        $Fax = new \QuickBooks_IPP_Object_Fax();
        $Fax->setFreeFormNumber('917-378-3135');
        $Customer->setFax($Fax);

        // Bill address
        $BillAddr = new \QuickBooks_IPP_Object_BillAddr();
        $BillAddr->setLine1('satelite');
        $BillAddr->setLine2('Jodhpur');
        $BillAddr->setCity('Ahmedabad');
        $BillAddr->setCountrySubDivisionCode('IN');
        $BillAddr->setPostalCode('380015');
        $Customer->setBillAddr($BillAddr);

        // Email
        $PrimaryEmailAddr = new \QuickBooks_IPP_Object_PrimaryEmailAddr();
        $PrimaryEmailAddr->setAddress('webdimensionsindia@gmail.com');
        $Customer->setPrimaryEmailAddr($PrimaryEmailAddr);

        if ($resp = $CustomerService->add($this->context, $this->realm, $Customer))
        {
            return $this->getId($resp);
        }
        else
        {
            //echo 'Not Added qbo';
            print($CustomerService->lastError($this->context));
        }
    }
    public function addItem(){
        $ItemService = new \QuickBooks_IPP_Service_Item();

        $Item = new \QuickBooks_IPP_Object_Item();
        $Item->setName('My Item');
        $Item->setType('Inventory');
        $Item->setIncomeAccountRef('53');
        if ($resp = $ItemService->add($this->context, $this->realm, $Item))
        {
            return $this->getId($resp);
        }
        else
        {
            print($ItemService->lastError($this->context));
        }
    }
    public function addInvoice(){
        $this->qboConnect();


        $ItemService = new \QuickBooks_IPP_Service_Item();
        $items = $ItemService->query($this->context, $this->realm, "SELECT * FROM Item WHERE Name = 'hello' ORDER BY Metadata.LastUpdatedTime ");
        echo "<pre>";
        print_r($items);
        exit;
        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();
        $Invoice = new \QuickBooks_IPP_Object_Invoice();
        $Invoice->setDocNumber('WEB' . mt_rand(0, 10000));
        $Invoice->setTxnDate('2013-10-11');
        $Line = new \QuickBooks_IPP_Object_Line();
        $Line->setDetailType('SalesItemLineDetail');
        $Line->setAmount(12.95 * 2);
        $Line->setDescription('Test description goes here.');
        $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
        $SalesItemLineDetail->setItemRef('8');
        $SalesItemLineDetail->setUnitPrice(12.95);
        $SalesItemLineDetail->setQty(2);
        $Line->addSalesItemLineDetail($SalesItemLineDetail);
        $Invoice->addLine($Line);
        $Invoice->setCustomerRef('58');
        if ($resp = $InvoiceService->add($this->context, $this->realm, $Invoice))
        {
            return $this->getId($resp);
        }
        else
        {
            print($InvoiceService->lastError());
        }
    }
    public function invoice_pdf()
    {
        $this->qboConnect();
        $Context=$this->context;
        $realm=$this->realm;
        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();
        $invoices = $InvoiceService->query($Context, $realm, "SELECT * FROM Invoice STARTPOSITION 1 MAXRESULTS 1");
        $invoice = reset($invoices);
        $id = substr($invoice->getId(), 2, -1);
        header("Content-Disposition: attachment; filename=example_invoice.pdf");
        header("Content-type: application/x-pdf");
        print $InvoiceService->pdf($Context, $realm, $id);
    }
    public function getId($resp){
        $resp = str_replace('{','',$resp);
        $resp = str_replace('}','',$resp);
        $resp = abs($resp);
        return $resp;
    }
    public function removeinvoice()
    {
        $this->qboConnect();
        $Context=$this->context;
        $realm=$this->realm;
        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();

        $the_invoice_to_delete = '{-10}';

        $retr = $InvoiceService->delete($Context, $realm, $the_invoice_to_delete);
        if ($retr)
        {
            print('The invoice was deleted!');
        }
        else
        {
            print('Could not delete invoice: ' . $InvoiceService->lastError());
        }

    }


}
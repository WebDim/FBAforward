<?php
/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */
/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */
use App\User;
use App\Setting;
use App\Role;
use App\Feature;
use App\Package;
use App\Page;
use App\Menu;
use App\Shipping_method;
use App\Outbound_method;
use App\Product_labels;
use App\Supplier;
use App\Prep_service;
use App\Listing_service;
use App\Addresses;
use App\Charges;

Route::model('users', User::class);
Route::model('settings', Setting::class);
Route::model('roles', Role::class);
Route::model('packages', Package::class);
Route::model('features', Feature::class);
Route::model('pages', Page::class);
Route::model('menus', Menu::class);
Route::model('shippingmethod',Shipping_method::class);
Route::model('outboundmethod',Outbound_method::class);
Route::model('productlabel',Product_labels::class);
Route::model('suppliers',Supplier::class);
Route::model('prepservices',Prep_service::class);
Route::model('listingservices',Listing_service::class);
Route::model('addresses',Addresses::class);
Route::model('charges',Charges::class);

Route::group(['middleware' => ['web']], function () {
    Route::get('/page/{slug}', 'FrontendController@staticPages');
    Route::get('/', 'FrontendController@index');
    Route::get('/pricing', 'FrontendController@pricing');
    Route::get('/components', 'FrontendController@components');
    Route::get('/contact-us', 'FrontendController@contactUs');
    Route::post('/contact-us', 'FrontendController@contactUsSubmit');
    Route::get('/blog', 'FrontendController@blog');
    Route::get('/blog/{slug}', 'FrontendController@post');
    Route::post('stripe/webhook', '\Laravel\Cashier\WebhookController@handleWebhook');
    Route::get('/notify', 'EmailController@getnotify');
    Route::post('/notify', 'EmailController@notify');
    Route::get('qbo/oauth','QuickbooksController@qboOauth');
    Route::get('qbo/success','QuickbooksController@qboSuccess');
    Route::get('qbo/disconnect','QuickbooksController@qboDisconnect');
    Route::get('qbo/addinvoice','QuickbooksController@addInvoice');
    Route::get('qbo/removeinvoice','QuickbooksController@removeInvoice');
    Route::get('qbo/createcustomer','QuickbooksController@createCustomer');
    Route::get('qbo/invoicepdf','QuickbooksController@invoice_pdf');
    Route::get('order/createcustomer','OrderController@createCustomer');
    Route::get('/amazon_inventory', ['as' => 'amazon_inventory', 'uses' => 'AmazoninventoryController@index']);
    Route::get('/getinvoices','QuickbooksController@getinvoices');
    Route::get('/getcustomers','QuickbooksController@getcustomers');

});
Route::group(['middleware' => 'web'], function () {
    /**
     * Authentication routes
     */
    Route::auth();
    Route::post('selectState','Auth\AuthController@selectState');
    /**
     * Admin routes
     */
    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::controllers([
            'datatables' => 'Admin\DatatablesController',
        ]);
        Route::get('/dashboard', 'Admin\DashboardController@index');
        Route::resource('users', 'Admin\UsersController');
        Route::post('users/selectState', 'Admin\UsersController@selectState');
        Route::get('settings/create/{type}', ['as' => 'admin.settings.create.type', 'uses' => 'Admin\SettingsController@createForm']);
        Route::get('settings/download/{settings}', ['as' => 'admin.settings.download', 'uses' => 'Admin\SettingsController@fileDownload']);
        Route::resource('settings', 'Admin\SettingsController');
        Route::resource('roles', 'Admin\RolesController');
        Route::resource('features', 'Admin\FeaturesController');
        Route::resource('packages', 'Admin\PackagesController');
        Route::resource('pages', 'Admin\PagesController');
        Route::resource('menus', 'Admin\MenusController');
        Route::resource('shippingmethod', 'Admin\ShippingMethodController');
        Route::resource('suppliers', 'Admin\SupplierController');
        Route::resource('prepservices', 'Admin\PrepServiceController');
        Route::resource('listingservices', 'Admin\ListingServiceController');
        Route::resource('outboundmethod', 'Admin\OutboundMethodController');
        Route::resource('productlabel', 'Admin\ProductLabelController');
        Route::resource('addresses', 'Admin\AddressesController');
        Route::resource('charges', 'Admin\ChargesController');

    });

    Route::post('note/save','NotesController@update');
    Route::resource('note', 'NotesController');

    Route::resource('payment', 'PaymentsController');

    Route::resource('shipment', 'ShipmentController');

    /**
     * Member routes
     */
    Route::group(['prefix' => 'member'], function () {
        Route::controllers([
            'subscription' => 'SubscriptionController'
        ]);
        Route::get('/home', ['as' => 'member.home', 'uses' => 'MemberController@index']);
        Route::get('/pricing', ['as' => 'member.pricing', 'uses' => 'MemberController@pricing']);
        Route::get('/profile', ['as' => 'member.profile', 'uses' => 'MemberController@profile']);
        Route::get('/profile/edit', ['as' => 'member.profile.edit', 'uses' => 'MemberController@editProfile']);
        Route::put('/profile/edit', ['as' => 'member.profile.update', 'uses' => 'MemberController@updateProfile']);
        Route::get('/amazoninventorylist', ['as' => 'member.amazoninventorylist', 'uses' => 'MemberController@amazoninventorylist']);
        Route::put('/addnickname', 'MemberController@addnickname');
        Route::post('/getnotification', 'MemberController@getnotification');
        Route::post('/checkread', 'MemberController@checkread');
        Route::get('/switchuser','MemberController@switchuser');
        Route::post('/storeuser','MemberController@storeuser');
    });

    /**
     * Orders routes
     */
    Route::group(['prefix' => 'order'], function () {
        Route::get('/index', 'OrderController@index');
        Route::get('/orderhistory', 'OrderController@orderhistory');
        Route::get('/details/{order_id}/{id}', 'OrderController@orderDetails');
        Route::get('/details/{order_id}/{id}/{user_id}', 'OrderController@orderDetails');
        Route::post('/removeorder', 'OrderController@removeorder');
        Route::post('/removeproduct','OrderController@removeproduct');
        Route::get('/supplierdetail',['uses'=>'OrderController@supplierdetail','as'=>'supplierdetail']);
        Route::put('/supplierdetail','OrderController@addsupplierdetail');
        Route::post('/addsupplier','OrderController@addsupplier');
        Route::get('/preinspection',['uses'=>'OrderController@preinspection','as'=>'preinspection']);
        Route::put('/preinspection','OrderController@addpreinspection');
        Route::get('/productlabels',['uses'=>'OrderController@labels','as'=>'productlabels']);
        Route::put('/productlabels','OrderController@addlabels');
        Route::get('/prepservice',['uses'=>'OrderController@prepservice','as'=>'prepservice']);
        Route::put('/prepservice','OrderController@addprepservice');
        Route::get('/listservice',['uses'=>'OrderController@listservice','as'=>'listservice']);
        Route::put('/listservice','OrderController@addlistservice');
        Route::get('/outbondshipping',['uses'=>'OrderController@outbondshipping','as'=>'outbondshipping']);
        Route::put('/outbondshipping','OrderController@addoutbondshipping');
        Route::get('/reviewshipment',['uses'=>'OrderController@reviewshipment','as'=>'reviewshipment']);
        Route::post('/addaddress','OrderController@addaddress');
        Route::post('/orderstatus', 'OrderController@orderstatus');
        Route::get('/ordershipping', 'OrderController@ordershipping');
        Route::post('/removeotherlabel', 'OrderController@removeotherlabel');
        Route::post('/removephotolabel', 'OrderController@removephotolabel');
        Route::get('/inspectionreport', 'OrderController@inspectionreport');
        Route::put('/inspectionreport', 'OrderController@uploadinspectionreport');
        Route::get('/downloadreport/{order_id}', 'OrderController@downloadreport');
        Route::post('/approvereport', 'OrderController@approvereport');
        Route::get('/shippingquote', 'OrderController@shippingquote');
        Route::get('/shippingquoteform/{order_id}', 'OrderController@shippingquoteform');
        Route::put('/shippingquoteform', 'OrderController@addshippingquoteform');
        Route::get('/downloadquote/{order_id}','OrderController@viewshippingquote');
        //Route::post('/viewshippingquote', 'OrderController@viewshippingquote');
        Route::post('/approveshippingquote', 'OrderController@approveshippingquote');
        Route::get('/billoflading', 'OrderController@billoflading');
        Route::get('/billofladingform/{order_id}', 'OrderController@billofladingform');
        Route::put('/billofladingform/', 'OrderController@addbillofladingform');
        Route::get('/billofladingapprove/', 'OrderController@billofladingapprove');
        Route::post('/viewbilloflading', 'OrderController@viewbilloflading');
        Route::post('/approvebilloflading', 'OrderController@approvebilloflading');
        Route::get('/downloadladingbill/{order_id}/{shipment_id}', 'OrderController@downloadladingbill');
        Route::get('/prealert', 'OrderController@prealert');
        Route::get('/prealertform/{order_id}', 'OrderController@prealertform');
        Route::put('/prealertform', 'OrderController@addprealertform');
        Route::get('/customclearance', 'OrderController@customclearance');
        Route::get('/customclearanceform/{order_id}', 'OrderController@customclearanceform');
        Route::put('/customclearanceform', 'OrderController@addcustomclearanceform');
        Route::get('/deliverybooking', 'OrderController@deliverybooking');
        Route::get('/deliverybookingform/{order_id}', 'OrderController@deliverybookingform');
        Route::put('/deliverybookingform', 'OrderController@adddeliverybookingform');
        Route::post('/addtrucking','OrderController@addtrucking');
        Route::post('/addterminal','OrderController@addterminal');
        Route::get('/orderlist','OrderController@orderlist');
        Route::get('/customers','OrderController@customers');
        Route::get('/getinvoice_detail','OrderController@getinvoice_detail');
        Route::post('/getinvoice_ajax_detail','OrderController@get_ajax_invoice_detail');
        Route::get('/warehousecheckin', 'OrderController@warehousecheckin');
        Route::get('/warehousecheckinform/{order_id}', 'OrderController@warehousecheckinform');
        Route::put('/warehousecheckinform', 'OrderController@addwarehousecheckinform');
        Route::get('/adminreview', 'OrderController@adminreview');
        Route::get('/createshipments/{order_id}', 'OrderController@createshipments');
        Route::post('/warehousecheckinreview','OrderController@warehousecheckinreview');
        Route::get('/downloadwarehouseimages/{id}','OrderController@downloadwarehouseimages');
        Route::get('/orderlabor','OrderController@orderlabor');
        Route::post('/viewchecklist','OrderController@viewchecklist');
        Route::post('/getlabel','OrderController@getlabel');
        Route::post('/getotherlabel','OrderController@getotherlabel');
        Route::post('/prepcomplete','OrderController@prepcomplete');
        Route::get('/managerreview','OrderController@managerreview');
        Route::post('/reviewwork','OrderController@reviewwork');
        Route::get('/completeshipment','OrderController@completeshipment');
        Route::post('/shippinglabel','OrderController@shippinglabel');
        Route::post('/verifylabel','OrderController@verifylabel');
        Route::get('/adminshipmentreview','OrderController@adminshipmentreview');
        Route::post('/shipmentreview','OrderController@shipmentreview');
        Route::post('/verifystatus','OrderController@verifystatus');
        Route::get('/printshippinglabel/{shipment_id}','OrderController@printshippinglabel');

    });

        Route::get('/amazon_credential', ['as' => 'amazon_credential', 'uses' => 'AmazonController@amazoncredential']);
        Route::put('/amazon_credential', ['as' => 'amazon_credential', 'uses' => 'AmazonController@addamazoncredential']);
        Route::get('/creditcard_detail', ['as' => 'creditcard_detail', 'uses' => 'MemberController@creditcarddetail']);
        Route::put('/creditcard_detail', ['as' => 'creditcard_detail', 'uses' => 'MemberController@addcreditcarddetail']);
        Route::get('/sampleshipment',['as'=>'sampleshipment', 'uses'=>'CreateInboundShipmentPlanSample@createshipment']);
        Route::get('sitemap', function(){
        // create new sitemap object
        $sitemap = App::make("sitemap");
        // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
        // by default cache is disabled
        $sitemap->setCache('laravel.sitemap', 1440);
        // check if there is cached sitemap and build new only if is not
        if (!$sitemap->isCached())
        {

            $posts = DB::table('pages')->orderBy('created_at', 'desc')->get();
            // add every post to the sitemap
            foreach ($posts as $post)
            {
                if($post->blog_post ){
                    $slug = "blog/".$post->slug;
                }else{
                    $slug = "page/".$post->slug;
                }
                $sitemap->add(URL::to($slug), $post->updated_at,'0.9', 'daily');
            }
        }
        return $sitemap->render('xml');
    }
    );
});
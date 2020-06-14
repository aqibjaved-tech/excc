<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 4/30/2017
 * Time: 10:58 AM
 */

namespace App\Http\Controllers;



use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\StoreRepositoryInterface;
use Darryldecode\Cart\CartCondition;
use Illuminate\Http\Request;
use Redirect;
use View;
use Session;
use Cookie;
use Stripe;


class CartController extends Controller
{
  protected $repository;

  public function __construct(StoreRepositoryInterface $repository)
  {
      $this->repository = $repository;
  }

    public function index()
    {
        $userId = 1; // get this from session or wherever it came from

        if(request()->ajax())
        {
            $items = [];

            \Cart::session($userId)->getContent()->each(function($item) use (&$items)
            {
                $items[] = $item;
            });

            return response(array(
                'success' => true,
                'data' => $items,
                'message' => 'cart get items success'
            ),200,[]);
        }
        else
        {
            return view('cart');
        }
    }


    public function getcartid(Request $request)
    {

      $newdomain = $request->instance()->query('domain');
      $newdomain = 'sundiego';
      $subdomain = $request->instance()->query('domain');
      $fullDomain = explode(".",parse_url($request->root())['host']);
      $domain = $this->getDomainName($fullDomain);
      $result = $this->checkDomainName($domain);
      if(!$result) {
          return Redirect::to('http://exchangecollective.com/');
      }
      $storedata = $this->repository->getStoreinfoByDomainName($newdomain);
      $storeid =  $storedata->accountInfo->account->id;
      $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
      $launchstoredata = json_decode($launchstoredata, true);

      @$data = $launchstoredata;

      $cart_id = $this->repository->getCartid();
      $cart_id = json_decode($cart_id, true);
      return $cart_id['id'];

    }
    public function add(Request $request)
    {

      $newdomain = $request->instance()->query('domain');
      $newdomain = 'sundiego';
      $subdomain = $request->instance()->query('domain');
      $fullDomain = explode(".",parse_url($request->root())['host']);
      $domain = $this->getDomainName($fullDomain);
      $result = $this->checkDomainName($domain);
      if(!$result) {
          return Redirect::to('http://exchangecollective.com/');
      }
      $storedata = $this->repository->getStoreinfoByDomainName($newdomain);
      $storeid =  $storedata->accountInfo->account->id;
      $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
      $launchstoredata = json_decode($launchstoredata, true);

      @$data = $launchstoredata;
      //$userId = 1; // get this from session or wherever it came from

        $cartid = $request->post('cartid');
        $cart_item = $request->post('item');
        // if(Session::get(['cartid']) != ''){
        Session::put(['cartid'=>$cartid]);
        Cookie::queue('cartid', $cartid, 907200);
      // }
        // Session::push('user.teams', 'developers');
        $postCartdata = $this->repository->postCartdata($cartid,$cart_item);
        //$postCartdata = json_decode($postCartdata, true);
        //print_r($postCartdata);
        @$bannerimg = '';
        //  die();
        // @$data = $item;
        $request->session()->flash('success', 'Item added successfully!');
      }

    public function updateCart(Request $request)  {
// echo '<pre>';
//       print_r($_POST);
//       die();
      for($i=0; $i < count($request->input('pid')); $i++){
        if($request->input('new_quantity')[$i] == 0){
          $this->repository->deleteCartitem('', $request->input('rowid')[$i]);
        }else{
        $this->repository->updateCart($request->input('rowid')[$i], $request->input('new_quantity')[$i]);
      }
      }

        return redirect()->back()->with('msg','Cart updated successfully');
        //return response(array('success' => true,  'data' => $item,  'message' => "item added."),201,[]);
    }

    public function addCondition()
    {
        $userId = 1; // get this from session or wherever it came from

        /** @var \Illuminate\Validation\Validator $v */
        $v = validator(request()->all(),[
            'name' => 'required|string',
            'type' => 'required|string',
            'target' => 'required|string',
            'value' => 'required|string',
        ]);

        if($v->fails())
        {
            return response(array(
                'success' => false,
                'data' => [],
                'message' => $v->errors()->first()
            ),400,[]);
        }

        $name = request('name');
        $type = request('type');
        $target = request('target');
        $value = request('value');

        $cartCondition = new CartCondition([
            'name' => $name,
            'type' => $type,
            'target' => $target, // this condition will be applied to cart's subtotal when getSubTotal() is called.
            'value' => $value,
            'attributes' => array()
        ]);

        \Cart::session($userId)->condition($cartCondition);

        return response(array(
            'success' => true,
            'data' => $cartCondition,
            'message' => "condition added."
        ),201,[]);
    }

    public function clearCartConditions()
    {
        $userId = 1; // get this from session or wherever it came from

        \Cart::session($userId)->clearCartConditions();

        return response(array(
            'success' => true,
            'data' => [],
            'message' => "cart conditions cleared."
        ),200,[]);
    }

    public function removeProduct(Request $request)
    {
        $this->repository->updateCart($request->input('rowid')[$i], $request->input('new_quantity')[$i]);
        return redirect()->back()->with('msg','Cart updated successfully');
    }

    public function deleteProduct($itemid)
    {
      // echo $itemid; exit;
        $this->repository->deleteCartitem('', $itemid);
        return redirect()->back()->with('msg','Product deleted successfully');
    }

    public function details(Request $request)
    {
      // echo Cookie::get('cartid');
      // die();
      // $cartiddd = 0;
      $newdomain = $request->instance()->query('domain');
      $newdomain = 'sundiego';
      $subdomain = $request->instance()->query('domain');
      $fullDomain = explode(".",parse_url($request->root())['host']);
      $domain = $this->getDomainName($fullDomain);
      $result = $this->checkDomainName($domain);
      if(!$result) {
          return Redirect::to('http://exchangecollective.com/');
      }
      $storedata = $this->repository->getStoreinfoByDomainName($newdomain);
      $storeid =  $storedata->accountInfo->account->id;
      $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
      $launchstoredata = json_decode($launchstoredata, true);

      @$data = $launchstoredata;
      $cartid = Cookie::get('cartid');
      if($cartid != ''){
        // echo Session::get('cartid');
      $items = $this->repository->getCartitems($cartid);

      $items_data = array();
      $items_ids = array();
      $final_data = array();
      // echo '<pre>';
      // print_r($items); exit;
      $productqty = 0;
      for ($i = 0; $i < count($items); $i++)
      {

            $itemsvalue = json_decode($items[$i]->item);

            $final_data['id'][$itemsvalue->id] = $items[$i]->id;
            $final_data['name'][$itemsvalue->id] = $itemsvalue->name;
            $final_data['retailPrice'][$itemsvalue->id] = $itemsvalue->retailPrice;
            $final_data['product_qty'][$itemsvalue->id] = $itemsvalue->product_qty;
            $final_data['shipping'][$itemsvalue->id] = $itemsvalue->shipping;
            $final_data['thumbnails'][$itemsvalue->id] = $itemsvalue->thumbnails[0];
      }
    }else{
      $final_data = array();
    }
      // exit;
      // echo '<pre>';
      // print_r($final_data); exit;


      return View::make('template/frontend/themes/mazaar/pages/cart',compact('data','final_data'));
    }
    public function checkout(Request $request)
    {
      $newdomain = $request->instance()->query('domain');
      $newdomain = 'sundiego';
      $subdomain = $request->instance()->query('domain');
      $fullDomain = explode(".",parse_url($request->root())['host']);
      $domain = $this->getDomainName($fullDomain);
      $result = $this->checkDomainName($domain);
      if(!$result) {
          return Redirect::to('http://exchangecollective.com/');
      }
      $storedata = $this->repository->getStoreinfoByDomainName($newdomain);
      $storeid =  $storedata->accountInfo->account->id;
      $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
      $launchstoredata = json_decode($launchstoredata, true);
      @$data = $launchstoredata;
      if(Cookie::get('cartid') != ''){
      $items = $this->repository->getCartitems(Cookie::get('cartid'));

      $items_data = array();
      $items_ids = array();
      $final_data = array();
      // echo '<pre>';
      //  print_r($items); exit;
      $productqty = 0;
      for ($i = 0; $i < count($items); $i++)
      {

            $itemsvalue = json_decode($items[$i]->item);
            $final_data['id'][$itemsvalue->id] = $items[$i]->id;
            $final_data['name'][$itemsvalue->id] = $itemsvalue->name;
            $final_data['retailPrice'][$itemsvalue->id] = $itemsvalue->retailPrice;
            $final_data['product_qty'][$itemsvalue->id] = $itemsvalue->product_qty;
            $final_data['shipping'][$itemsvalue->id] = $itemsvalue->shipping;
            $final_data['thumbnails'][$itemsvalue->id] = $itemsvalue->thumbnails[0];
      }
    }else{
      $final_data = array();
    }


return View::make('template/frontend/themes/mazaar/pages/checkout',compact('data','final_data'));
        // return View::make('template/frontend/themes/mazaar/pages/checkout',compact('data','items'));
    }

    public function stripePost(Request $request)
    {

      // echo '<pre>';
      //  print_r($_POST); exit;
      $validatedData = $request->validate([
        'shipping_first_name' => 'required',
        'shipping_last_name' => 'required',
        'shipping_phone' => 'required',
        'shipping_address_1' => 'required',
        // 'shipping_address_2' => 'required',
        'shipping_city' => 'required',
        'shipping_state' => 'required',
        'shipping_postcode' => 'required',
        'billing_first_name' => 'required',
        'billing_last_name' => 'required',
        'billing_phone' => 'required',
        'billing_address_1' => 'required',
        'terms' => 'required',
        'billing_city' => 'required',
        'billing_state' => 'required',
        'billing_postcode' => 'required',
    ]);


          $data['account_id'] = 813;
            $data['location_id'] = 6166;
          $data['assoc_id'] = 6167;
          $data['emailAddress'] = $request->post('billing_email');

          $data['shippingAddress'] = array(
          'firstName' => $request->input('shipping_first_name'),
          'lastName' => $request->input('shipping_last_name'),
          'phoneNumber' => $request->input('shipping_phone'),
          'address1' => $request->input('shipping_address_1'),
          'address2' => $request->input('shipping_address_2'),
          'city' => $request->input('shipping_city'),
          'state' => $request->input('shipping_state'),
          'zip' => $request->input('shipping_postcode')
         );

          $data['billingAddress'] = array(
          'firstName' => $request->input('billing_first_name'),
          'lastName' => $request->input('billing_last_name'),
          'phoneNumber' => $request->input('billing_phone'),
          'address1' => $request->input('billing_address_1'),
          'address2' => $request->input('billing_address_2'),
          'city' => $request->input('billing_city'),
          'state' => $request->input('billing_state'),
          'zip' => $request->input('billing_postcode')
        );
        $cartid = 0;
        $cartid = Cookie::get('cartid');
        $items = $this->repository->getCartitems($cartid);
        // echo '<pre>';
        // print_r($itemsvalue); exit;
        $productqty = 0;
        $total = 0;

        for ($i = 0; $i < count($items); $i++)
        {
          $itemsvalue = json_decode($items[$i]->item);
          // echo $itemsvalue->brand->name;
          // echo '<pre>';
          // print_r($itemsvalue); exit;
          $total = $total + ($itemsvalue->product_qty * $itemsvalue->retailPrice);
            $data['items'][] = array('brand'=>array('name'=>$itemsvalue->brand->name,'taxRate'=>$itemsvalue->brand->taxRate,'brandId'=>1142),
              'productID'=>$itemsvalue->id,'variantID'=>'null', 'name'=>$itemsvalue->name, 'itemNumber'=>$itemsvalue->itemNumber
              ,'upc'=>$itemsvalue->upc,'size'=>$itemsvalue->shipping,'color'=>$itemsvalue->shipping,
              'quantityAvailable'=>$itemsvalue->quantityAvailable,'quantity'=>$itemsvalue->product_qty,'retailPrice'=>$itemsvalue->retailPrice,
              'subtotal'=>$total,'shipping'=>$itemsvalue->shipping,'estTax'=>$itemsvalue->shipping,'total'=>$total+$itemsvalue->shipping
            );
        }
  // );
  // echo '<pre>';
  // print_r(json_encode($data['items'])); exit;

        $data['paymentInformation'] = array(
          'issuer' => $request->input('brand'),
          'last4' => $request->input('last4'),
          'expMonth' => $request->input('exp_month'),
          'expYear' => $request->input('exp_year'),
          'token' => $request->input('stripeToken')
        );


        // $data['items'][] = array('productID'=>'19812','name'=>'Test Product B');
        $data['shipToStore'] = false;
        $data['subtotal'] = 180;
        $data['estTax'] = 14.85;
        $data['shipping'] = 0;
        $data['total'] = 194.85;

        $api_baseurl =  config('constants.ApiUrl');
        $data_string = json_encode($data);
        // echo '<pre>';
        // print_r($data); exit;

        $ch = curl_init("$api_baseurl/order/");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
          $result = curl_exec($ch);
        if (curl_error($ch)) {
                $error_msg = curl_error($ch);
        }

        curl_close($ch);
                                  // return $result;
                                  // print_r($error_msg); exit;

        // $test = Stripe\Stripe::setApiKey("sk_test_7xdWXFPhbWa1gTreZF1TOltl00F4MPC2ZD");
        // Stripe\Charge::create ([
        //         "amount" => 100 * 100,
        //         "currency" => "usd",
        //         "source" => $request->stripeToken,
        //         "description" => "Test payment from itsolutionstuff.com."
        // ]);
        //
        // Session::flash('success', 'Payment Successful!');

        Session::put('cartquantify',0);

        return redirect()->back()->with('msg','Order Placed Successfuly');
    }
     //new Code
     public function checkDomainName($dn) {
      if($dn == "excShops" || $dn == "excshops") {
          return false;
      } else {
          return $dn;
      }
    }
    public function getDomainName($domain) {
        echo 'inside function';
        echo $domain[0];
        if($domain[0] == "www") {
            return $domain[1];
        } else {
            return $domain[0];
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\StoreRepositoryInterface;
use Redirect;
use View;



//use App\Account;
use Illuminate\Http\Request;

class StoreViewController extends Controller
{
    protected $repository;

    public function __construct(StoreRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    public function sitepages(Request $request){
        $req = $request;
        $sitefoldername = $req['sitefolder']; // get site name using siteID ask for api

        if (!file_exists(base_path()."/frontend/demo/$sitefoldername/")) {
            mkdir(base_path()."/frontend/demo/$sitefoldername/", 0777, true);
        }

                // Create Site File in api call first check file exist or not then call for it.
        $name_of_uploaded_file = $req['filename'];
        $htmlfile = fopen(base_path()."/frontend/demo/$sitefoldername/".$name_of_uploaded_file, "w") or die("Unable to open file!");
        $html =  $req['source'];

        file_put_contents(base_path()."/frontend/demo/$sitefoldername/".$name_of_uploaded_file, $html);
        fclose($htmlfile);
        return "Created";
    }

    public function product_filteration(Request $request){

        $filter_type = $request->get('type');
        $newdomain = $request->get('domain');
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }

        $storedata = $this->repository->getStoreinfoByDomainName($newdomain);

        if(!isset($storedata->errors[0]->code)){
            $storeid =  $storedata->accountInfo->account->id;
            $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
            $launchstoredata = json_decode($launchstoredata, true);
            @$data = $launchstoredata;
            $pagesize = 10;
            $brandid = 'null';
            $category = '';

            if($filter_type =='search'){
                $search_data = array(
                    'filter_type' => $filter_type,
                    'category' => $request->get('filter_categries'),
                    'color' => $request->get('filter_color'),
                    'size' => $request->get('filter_size'),
                    'brand' => $request->get('filter_brand'),
                    'gender' => $request->get('filter_gender'),
                    'price' => $request->get('price_range_min'),
                    'price_max' => $request->get('price_range_max'),
                );
            }elseif ($filter_type == 'header_search'){
                $search_data = array(
                  'filter_type' => $filter_type,
                  'search_product' => $request->get('s')
                );
            }
            else{
                $search_data = array(
                    'filter_type' => $filter_type,
                    'filter' => $request->get('filter')
                );
            }

            $products = $this->repository->getProductsbyfilters($pagesize, $brandid, $storeid, $search_data);
            $products = json_decode($products, true);
        }else{
            return view('errors/500');
        }
//dd($products);
        return View::make('template/frontend/themes/mazaar/pages/filter_product',compact('data','products'));
    }

    public function filter_product_details(Request $request){
        $subdomain = $request->instance()->query('domain');

        // $domain = $request->get('domain');
        $newdomain = $request->get('domain');
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        $storedata = $this->repository->getStoreinfoByDomainName($newdomain);
        $storeid =  $storedata->accountInfo->account->id;

        $pid = $request->route('pid');

        $category = $request->segment(1);
        @$category  = strtolower($category); // no

        $brandname = $request->route('brandname'); // no

        $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
        $launchstoredata = json_decode($launchstoredata, true);

        foreach ($launchstoredata['brands'] as $brand) {
            //if(strtolower($brand['name']) == $brandname){
            //$bannerimg =  $brand['banners']['0'];
            //print_r($brand);
            //break;
            //}
        }

        @$bannerimg = '';

        @$data = $launchstoredata;
        $product = $this->repository->getProductsDetailsbyIdandStoreId($pid);
        $product = json_decode($product, true);
        $category = (isset($product['category']) ? $product['category'] : '');

        return View::make('template/frontend/themes/mazaar/pages/product-details',compact('data','product','bannerimg', 'category'));
    }

    public function index(Request $request){
        $newdomain = $request->get('domain');
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }

        $storedata = $this->repository->getStoreinfoByDomainName($newdomain);

        if(!isset($storedata->errors[0]->code)){
            $storeid =  $storedata->accountInfo->account->id;
            $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
            $launchstoredata = json_decode($launchstoredata, true);
            @$data = $launchstoredata;
            $pagesize = 10;
            $brandid = 'null';
            $category = '';
            $products = $this->repository->getProductsbyBrandIdandStoreId($brandid,$storeid,$pagesize,$category);
            $products = json_decode($products, true);


        }else{
            return view('errors/500');
        }
        return View::make('/template/frontend/themes/mazaar/pages/index', compact('data','products'));
    }

    public function brands(Request $request){
        $subdomain = $request->instance()->query('domain');
        //      $domain = 'impact2';
        $newdomain = $request->get('domain');
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        $storedata = $this->repository->getStoreinfoByDomainName($newdomain);

        if(!isset($storedata->errors[0]->code)){
            $storeid =  $storedata->accountInfo->account->id;
            $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
            $launchstoredata = json_decode($launchstoredata, true);
            @$data = $launchstoredata;
        }else{
            echo "There is server Error";
        }

        return View::make('/template/frontend/themes/mazaar/pages/brands',compact('data'));
    }
    public function echoTest() {
        echo 'testdone';
    }
    public function productsListing(Request $request,$brand){
        $pagesize = $request->input('p');

        $subdomain = $request->instance()->query('domain');
        //      $domain = 'impact2';
        $newdomain = $request->get('domain');
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        $storedata = $this->repository->getStoreinfoByDomainName($newdomain);

//        var_dump($storedata);die;
        if(!isset($storedata->errors[0]->code))
        {
            $storeid =  $storedata->accountInfo->account->id;
            $category = $request->route('category');
            $category  = strtolower($category);

            $brandname = $request->route('brandname');

            if($category == ''){
                $category = '';
            }
            if($brandname == ''){
                $brandname = '';
            }
            //echo $brandname  = ucfirst($brandname);
            $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
            $launchstoredata = json_decode($launchstoredata, true);

            foreach ($launchstoredata['brands'] as $brand) {
                if(strtolower($brand['name']) == $brandname){
                    $brand_id = $brand['id'];
                    break;
                }
            }
            @$category = $category;
            @$data = $launchstoredata;

            $products = $this->repository->getProductsbyBrandIdandStoreId($brand_id,$storeid,$pagesize,$category);
            $products = json_decode($products, true);
            // print_r($products);
            // echo "yesy";
            // die();
            @$products = $products;

//            dd($products['products'][0]['largeThumbnails']);
        }else{
            echo "There is server Error";die;
        }

        return View::make('/template/frontend/themes/mazaar/pages/product-listing-left-sidebar',compact('products','data','brandname','category'));
    }

    public function productsDetails(Request $request,$category,$productname,$pid){

        $subdomain = $request->instance()->query('domain');
//      $domain = 'impact2';
        $newdomain = $request->get('domain');
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        try {
                // Some potentially crashy code
                $storedata = $this->repository->getStoreinfoByDomainName($newdomain);
                $storeid =  $storedata->accountInfo->account->id;

                $productname = $request->route('productname');
                $pid = $request->route('pid');

                $category = $request->segment(1);
                @$category  = strtolower($category);
                //  $id = $request->route('id');
                $brandname = $request->route('brandname');
                //echo $brandname  = ucfirst($brandname);
                $launchstoredata = $this->repository->launchStoreDatabyID($storeid);
                $launchstoredata = json_decode($launchstoredata, true);

                foreach ($launchstoredata['brands'] as $brand) {
                    //if(strtolower($brand['name']) == $brandname){
                    //$bannerimg =  $brand['banners']['0'];
                    //print_r($brand);
                    //break;
                    //}
                }

                @$bannerimg = '';

                @$data = $launchstoredata;
                $product = $this->repository->getProductsDetailsbyIdandStoreId($pid);
                $product = json_decode($product, true);
                return View::make('template/frontend/themes/mazaar/pages/product-details',compact('data','product','category','bannerimg'));

            } catch (Exception $ex) {
                Bugsnag::notifyException($ex);
            }
        
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

        if($domain[0] == "www") {
            return $domain[1];
        } else {
            return $domain[0];
        }
    }
}

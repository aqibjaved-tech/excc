<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redirect;
use View;

//use App\Account;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request){


      $newdomain = $request->instance()->query('domain');
      $newdomain = 'imapact2';
    //   $newdomain = $request->get('domain');
      $subdomain = $request->instance()->query('domain');
      $fullDomain = explode(".",parse_url($request->root())['host']);
      $domain = $this->getDomainName($fullDomain);
      $result = $this->checkDomainName($domain);
      if(!$result) {
          return Redirect::to('http://exchangecollective.com/');
      }
      $arrContextOptions = array(
                              "ssl"=>array(
                                  "verify_peer"=>false,
                                  "verify_peer_name"=>false,
                              ),
                          );
      $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=".$newdomain, false, stream_context_create($arrContextOptions));
      $result  = json_decode($content);
      @$data = $result;
      return View::make('/template/admin/pages/dashboard/index',compact('data'));
     }
    public function getData(Request $request){
        $newdomain = $request->instance()->query('domain');
        $newdomain = 'imapact2';
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=".$newdomain, false, stream_context_create($arrContextOptions));

        $result  = json_decode($content);
        print_r($result);
    }
    public function getSettings(Request $request){
        $newdomain = $request->instance()->query('domain');
        $newdomain = 'imapact2';
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=".$newdomain, false, stream_context_create($arrContextOptions));
        $result  = json_decode($content);
        @$data = $result;
        return View::make('/template/admin/pages/dashboard/settings',compact('data'));
        //return View::make('/template/admin/pages/dashboard/settings');
    }
    public function ourFaq(Request $request){
        $newdomain = $request->instance()->query('domain');
        $newdomain = 'imapact2';
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=".$newdomain, false, stream_context_create($arrContextOptions));
        $result  = json_decode($content);
        @$data = $result;
        return View::make('/template/admin/pages/dashboard/faq',compact('data'));
    }
    public function watchVideo(Request $request){
        $newdomain = $request->instance()->query('domain');
        $newdomain = 'imapact2';
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=".$newdomain, false, stream_context_create($arrContextOptions));

        $result  = json_decode($content);
        @$data = $result;
        return View::make('/template/admin/pages/dashboard/video',compact('data'));
    }
    public function gettingStarted(Request $request){
        $newdomain = $request->instance()->query('domain');
        $newdomain = 'imapact2';
        $subdomain = $request->instance()->query('domain');
        $fullDomain = explode(".",parse_url($request->root())['host']);
        $domain = $this->getDomainName($fullDomain);
        $result = $this->checkDomainName($domain);
        if(!$result) {
            return Redirect::to('http://exchangecollective.com/');
        }
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=".$newdomain, false, stream_context_create($arrContextOptions));
        $result  = json_decode($content);
        @$data = $result;
        return View::make('/template/admin/pages/dashboard/gettingstarted',compact('data'));
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

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


      $subdomain = $request->instance()->query('domain');
      $subdomain = 'sundiego';
      $arrContextOptions = array(
                              "ssl"=>array(
                                  "verify_peer"=>false,
                                  "verify_peer_name"=>false,
                              ),
                          );
      $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=sundiego", false, stream_context_create($arrContextOptions));
      $result  = json_decode($content);
      @$data = $result;
      return View::make('/template/admin/pages/dashboard/index',compact('data'));
     }
    public function getData(Request $request){
        $subdomain = $request->instance()->query('domain');
       // print_r($subdomain);
        //echo  $subdomain;
        $subdomain = 'sundiego';
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=sundiego", false, stream_context_create($arrContextOptions));

        $result  = json_decode($content);
        print_r($result);
    }
    public function getSettings(Request $request){
        $subdomain = $request->instance()->query('domain');
        $subdomain = 'sundiego';
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=sundiego", false, stream_context_create($arrContextOptions));
        $result  = json_decode($content);
        @$data = $result;
        return View::make('/template/admin/pages/dashboard/settings',compact('data'));
        //return View::make('/template/admin/pages/dashboard/settings');
    }
    public function ourFaq(Request $request){
        $subdomain = $request->instance()->query('domain');
        $subdomain = 'sundiego';
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=sundiego", false, stream_context_create($arrContextOptions));
        $result  = json_decode($content);
        @$data = $result;
        return View::make('/template/admin/pages/dashboard/faq',compact('data'));
    }
    public function watchVideo(Request $request){
        $subdomain = $request->instance()->query('domain');
        $subdomain = 'sundiego';
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=sundiego", false, stream_context_create($arrContextOptions));

        $result  = json_decode($content);
        @$data = $result;
        return View::make('/template/admin/pages/dashboard/video',compact('data'));
    }
    public function gettingStarted(Request $request){
        $subdomain = $request->instance()->query('domain');
        $subdomain = 'sundiego';
        $arrContextOptions = array(
                                "ssl"=>array(
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                ),
                            );
        $content = file_get_contents("https://exc-staging-pr-5.herokuapp.com/api/v2/accountInfo?domainName=sundiego", false, stream_context_create($arrContextOptions));
        $result  = json_decode($content);
        @$data = $result;
        return View::make('/template/admin/pages/dashboard/gettingstarted',compact('data'));
    }

}

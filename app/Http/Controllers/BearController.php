<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bear;
use Ordent\RamenResource\Response\ResourceResponse;

class BearController extends Controller
{
	protected $response;

	public function __construct(ResourceResponse $response){
		$this->response = $response;
	}
	

	public function index(){
		try{
			$data = Bear::find(12);
		}catch(\Exception $e){
			dd($e);
			return $this->response->makeResponse(null, null, [], $e->getTrace(), 500);
		}


		if(is_null($data)){
			return $this->response->makeResponse($data, null, [], "ID that correspondence with that data is not found.", 404);
		}
		return $this->response->makeResponse($data, null, [], null, 200);
	}
}

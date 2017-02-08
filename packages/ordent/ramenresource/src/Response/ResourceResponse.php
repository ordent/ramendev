<?php 

namespace Ordent\RamenResource\Response;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Resource\NullResource;
use League\Fractal\Pagination\IlluminatePaginatorAdaptor;
use Ordent\RamenResource\Transformer\ResourceTransformer;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
class ResourceResponse{
	
	protected $response;
	protected $jsonResponse;
	protected $manager;
	public function __construct(Manager $manager, Response $response, JsonResponse $jsonResponse, SerializerAbstract $serializer = null){
		$this->response = $response;
		$this->jsonResponse = $jsonResponse;
		if(is_null($serializer)){
			$serializer = new DataArraySerializer();
		}
		$this->manager = $manager;
		$this->manager->setSerializer($serializer);
	}
	
	public function makeResponse($data, $transformer, $meta, $message, $status){
		if($status == intval($status)){
			return $this->successResponse($data, $transformer, $meta, $message, $status);
		}
	}

	public function successResponse($data, $transformer, $meta = [], $message = "Successfull Request", $status){
		$data = $this->wrapResponse($data, $transformer, $meta, $message, $status);
		// dd($data);
		$data = $this->manager->createData($data)->toArray();
		return new $this->jsonResponse($data, $status);
	}

	public function wrapResponse($data, $transformer = null, $meta = [], $message, $status){
		$results             = [];
		$meta["status_code"] = $status;
		$meta["messages"]    = $message;

		if(is_null($transformer)){
			$transformer = new ResourceTransformer;
		}
		
		if($data instanceof Collection || is_array($data)){
			if(is_null($transformer)){
				$results = new FractalCollection($data);
			}else{
				$results = new FractalCollection($data, $transformer);
			}
		}

		if($data instanceof Model){
			$results = new FractalItem($data, $transformer);
		}

		if($data instanceof Paginator || $data instanceof LengthAwarePaginator){
			$content = $data->getCollection();
			$results = new FractalCollection($content, $transformer);
			$results->setPaginator(new IlluminatePaginatorAdaptor($data));
		}

		if(is_null($data)){
			$results = new NullResource;
		}
		$results->setMeta($meta);

		return $results;
	}

}
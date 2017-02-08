<?php

namespace Ordent\RamenResource\Transformer;
use League\Fractal\TransformerAbstract;
use Illuminate\Database\Eloquent\Model;
class ResourceTransformer extends TransformerAbstract{

	public function transform(Model $model){
		return $model->toArray();
	}
}
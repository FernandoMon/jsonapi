<?php

namespace App\Http\Resources;

use http\Env\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollectio;

class ResourceCollection extends BaseResourceCollectio
{
    //public $collects = ArticleResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => ResourceObject::collection($this->collection)
        ];
    }
}

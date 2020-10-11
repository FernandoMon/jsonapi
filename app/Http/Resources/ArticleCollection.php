<?php

namespace App\Http\Resources;

use http\Env\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
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
            'data' => ArticleResource::collection($this->collection)
        ];
    }
}

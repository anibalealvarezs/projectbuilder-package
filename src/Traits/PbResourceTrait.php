<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Auth;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

trait PbResourceTrait
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray(Request $request): array|\JsonSerializable|Arrayable
    {
        return parent::toArray($request);
    }
}

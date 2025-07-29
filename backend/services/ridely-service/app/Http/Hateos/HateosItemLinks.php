<?php

namespace App\Http\Hateos;

class HateosItemLinks
{
    public $self = "";

    public $update = "";

    public $replace = "";

    public $delete = "";


    public function __construct($self = null, $update = null, $replace = null, $delete = null)
    {
        if (!$update) {
            $update = $self;
        }
        if (!$replace) {
            $replace = $self;
        }
        if (!$delete) {
            $delete = $self;
        }

        $this->self = [
            "href" => $self,
            "rel" => HateosLinkHttpVerbsEnum::GET->rel(),
            "method" => HateosLinkHttpVerbsEnum::GET->method(),
        ];
        $this->update = [
            "href" => $update,
            "rel" => HateosLinkHttpVerbsEnum::PATCH->rel(),
            "method" => HateosLinkHttpVerbsEnum::PATCH->method(),
        ];
        $this->replace = [
            "href" => $replace,
            "rel" => HateosLinkHttpVerbsEnum::UPDATE->rel(),
            "method" => HateosLinkHttpVerbsEnum::UPDATE->method(),
        ];
        $this->delete = [
            "href" => $delete,
            "rel" => HateosLinkHttpVerbsEnum::DELETE->rel(),
            "method" => HateosLinkHttpVerbsEnum::DELETE->method(),
        ];
    }

    public function toArray(): array
    {

        return [
            "self" => $this->self,
            "update" => $this->update,
            "replace" => $this->replace,
            "delete" => $this->delete,
        ];
    }
}
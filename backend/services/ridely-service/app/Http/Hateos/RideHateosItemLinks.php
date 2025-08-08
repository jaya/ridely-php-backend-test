<?php

namespace App\Http\Hateos;

class RideHateosItemLinks
{
    public $self = "";

    public $cancel = "";

    public $accept = "";

    public $refuse = "";

    public $finish = "";

    public $estimate = "";

    public $getEstimate = "";


    public function __construct($self = null)
    {
            $cancel = $self . "/cancel-ride";
            $accept = $self . "/accept-ride";
            $refuse = $self . "/refuse-ride";
            $finish = $self . "/finish-ride";
            $estimate = $self . "/estimate-ride";

        $this->self = [
            "href" => $self,
            "rel" => HateosLinkHttpVerbsEnum::GET->rel(),
            "method" => HateosLinkHttpVerbsEnum::GET->method(),
        ];
        $this->cancel = [
            "href" => $cancel,
            "rel" => "cancel",
            "method" => HateosLinkHttpVerbsEnum::POST->method(),
        ];

        $this->accept = [
            "href" => $accept,
            "rel" => "accept",
            "method" => HateosLinkHttpVerbsEnum::POST->method(),
        ];
        $this->refuse = [
            "href" => $refuse,
            "rel" => "refuse",
            "method" => HateosLinkHttpVerbsEnum::POST->method(),
        ];
        $this->finish = [
            "href" => $finish,
            "rel" => "finish",
            "method" => HateosLinkHttpVerbsEnum::POST->method(),
        ];

        $this->estimate = [
            "href" => $estimate,
            "rel" => "create_estimate",
            "method" => HateosLinkHttpVerbsEnum::POST->method(),
        ];

        $this->getEstimate = [
            "href" => $estimate,
            "rel" => "get_estimate",
            "method" => HateosLinkHttpVerbsEnum::GET->method(),
        ];
    }

    public function toArray(): array
    {

        return [
            "self" => $this->self,
            "cancel" => $this->cancel,
            "accept" => $this->accept,
            "refuse" => $this->refuse,
            "finish" => $this->finish,
            "estimate" => $this->estimate,
            "get+estimate" => $this->getEstimate,
        ];
    }
}
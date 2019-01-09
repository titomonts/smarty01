<?php
class MySoapClient extends SoapClient
{
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $response = parent::__doRequest($request, $location, $action, $version, $one_way);
        // parse $response, extract the multipart messages and so on
       
        //this part removes stuff
        $start=strpos($response,'<?xml');
        $end=strrpos($response,'>');   
        $response_string=substr($response,$start,$end-$start+1);
        return($response_string);
    }
}


/* End of File */
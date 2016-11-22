<?php

abstract class REST
{
    public function processRequest(){
        $requestType = $_SERVER['REQUEST_METHOD'];
        $input = file_get_contents("php://input");
        parse_str($input,$postdata);
        if ($requestType)
        {
            if ($requestType === 'GET')
            {
                if (!empty($_GET))
                {
                    return $this->get($_GET);
                }
                else
                {
                    return $this->getList();
                }
            }
            elseif ($requestType === 'POST')
            {
                if(!empty($postdata))
                    return $this->create($postdata);
            }
            elseif ($requestType === 'PUT')
            {
                if(!empty($postdata))
                    return $this->update($postdata);
            }
            elseif ($requestType === 'PATCH')
            {
                if(!empty($postdata))
                    return $this->patch($postdata);
            }
            elseif ($requestType === 'DELETE')
            {
                if(!empty($postdata))
                    return $this->delete($postdata);
            }
        }
        return json_encode([
            'error' => 'Invalid or Not Existent Request Type or Parameters',
            'requestType' => $requestType,
            'postdata' => $postdata,
        ]);
    }
    private function myJsonDecode($string){
        return json_decode(urldecode($string));
    }

    protected function getList(){return json_encode([]);}
    protected function get($data){return json_encode([]);}
    protected function create($data){return json_encode([]);}
    protected function update($data){return json_encode([]);}
    protected function patch($data){return json_encode([]);}
    protected function delete($data){return json_encode([]);}
}
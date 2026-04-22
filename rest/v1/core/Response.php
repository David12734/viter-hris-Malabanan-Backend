<?php 

class Response
{
    private $_success;
    private $_data;
    private $_toCache = false;
    private $_responseData = array();

    public function setSuccess($success){
        $this->_success =$success;
    }
    public function setdata($data){
        $this->_data =$data;
    }
    public function settoCache($toCache){
        $this->_toCache =$toCache;
    }

    public function send(){
        header("Content-type: application/json;charset=utf-8");

        if($this->_toCache == true) {
            header("Cache-Control: max-age=60");
        }else{
            header("Cache-Control:no-cache, no-store");

        }

    // if($this->_success == false){
      // $this->responseData = $this->_data;
      //    }else{
     //  $this->responseData = $this->_data;
    // }
    $this->_responseData = $this->_data;

    echo json_encode($this->_responseData);
    }
}


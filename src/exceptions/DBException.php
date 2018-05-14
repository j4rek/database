<?php
namespace j4rek\database\exceptions;

class DBException extends \PDOException{
    protected $debug;

    function __construct($message, $code, $debug = false){
        parent::__construct($message, $code, $this);
        $this->debug = $debug;
        $this->showError();
    }

    public function showError(){
        if($this->debug){
            $format = '<div style="background-color: #68614e;">
                <h3 style="color:#e8702a;background-color:#000;padding:20px;border:0px solid #000;margin:0px;">( - ___ - \') Error!</h3>
                <div style="">'.
                    '<div><div style="border-bottom:1px solid #60604e;background-color:#69694e;display:inline-block;width:100px;padding:10px;color:#000;margin-right:20px;">Message</div><div style="display:inline-block;color:orange;overflow-x:auto;width:85%;white-space:nowrap;">'. $this->message . '</div></div>' .
                    '<div><div style="border-bottom:1px solid #60604e;background-color:#69694e;display:inline-block;width:100px;padding:10px;color:#000;margin-right:20px;">Script</div><div style="display:inline-block;color:orange;">' . $this->getFile() . '</div></div>' .
                    '<div><div style="border-bottom:1px solid #60604e;background-color:#69694e;display:inline-block;width:100px;padding:10px;color:#000;margin-right:20px;">Line</div><div style="display:inline-block;color:orange;">'. $this->getLine() . '</div></div>';
                    foreach($this->getTrace() as $trace){
                        $format .='<div><div style="border-bottom:1px solid #60604e;background-color:#69694e;display:inline-block;width:100px;padding:10px;color:#000;margin-right:20px;">Trace</div><div style="display:inline-block;color:orange;overflow-x:auto;width:85%;white-space:nowrap;">Line: '. $trace['line']. ' >> ' . $trace['file'] . '::' . $trace['function'] . ' >> #Arg: ' . $trace['args'][0]  . '</div></div>';
                    }
                $format .= '</div>
            </div>';
        }else{
            $format = '<div style="background-color: #68614e;">
                        <h3 style="color:#e8702a;background-color:#000;padding:20px;border:0px solid #000;margin:0px;">( - ___ - \') SQL Error!</h3>
                    </div>';
        }
        exit($format);
    }
}
<?php
namespace app\Controllers;

use app\Protobuf\Message;
use app\Protobuf\Response;
use Protobuf\AbstractMessage;
use Server\CoreBase\Controller;

/**
 * Created by PhpStorm.
 * User: tmtbe
 * Date: 16-7-15
 * Time: 下午3:51
 */
class ProtoController extends Controller
{
    /**
     * @var Message
     */
    public $Message;
    public function __call($name, $arguments)
    {
        list($proto,$method_name) = explode('/', $name);
        $fuc_name = lcfirst($method_name);
        $this->Message = $this->client_data->data;
        $request = $this->Message->getRequest();
        $method = "getM{$method_name}Request";
        $pamars = call_user_func([$request,$method]);
        call_user_func([$this,$fuc_name],$pamars);
    }

    /**
     * 构建Message
     * @param AbstractMessage $responseMessage
     * @return String
     */
    public function makeMessageData(AbstractMessage $responseMessage)
    {
        $cmdMethod = $responseMessage->getCmdMethod();
        $cmdService = $responseMessage->getCmdService();
        $method = "setM{$cmdMethod->name()}Response";
        if(empty($this->Message)){
            $this->Message = new Message();
            $this->Message->setToken(time());
            $this->Message->setResponse(new Response());
        }
        $this->Message->setCmdMethod($cmdMethod);
        $this->Message->setCmdService($cmdService);
        $response = $this->Message->getResponse();
        if(empty($response)){
            $response = new Response();
            $this->Message->setResponse($response);
        }
        call_user_func([$response,$method],$responseMessage);
        return $this->Message;
    }

    public function destroy()
    {
        parent::destroy();
        $this->Message = null;
    }
}
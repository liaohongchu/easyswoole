<?php
 
namespace App\WebSocket;
 
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
 
 
/**
 * Class Index
 *
 * 此类是默认的 websocket 消息解析后访问的 控制器
 *
 * @package App\WebSocket
 */
class Index extends Controller
{
    public function hello()
    {
        $this->response()->setMessage('call hello with arg:'. json_encode($this->caller()->getArgs()));
    }
 
    public function who()
    {
        $this->response()->setMessage('your fd is '. $this->caller()->getClient()->getFd());
    }
 
    public function index()
    {
        //$this->response()->setMessage('你的fd是：'. $this->caller()->getClient()->getFd());
 
        //print_r($this->caller());
 
        //echo PHP_EOL;
 
        //print_r($this->caller()->getArgs());
 
        print_r($this->caller()->getArgs()); //获取参数
 
        echo PHP_EOL;
 
        //print_r(ServerManager::getInstance()->getSwooleServer());
    }
 
    public function delay()
    {
        $this->response()->setMessage('this is delay action');
        $client = $this->caller()->getClient();
 
        // 异步推送, 这里直接 use fd也是可以的
        TaskManager::getInstance()->async(function () use ($client){
 
            $server = ServerManager::getInstance()->getSwooleServer();
 
            $i = 0;
 
            while ($i < 5)
            {
                sleep(1);
                $server->push($client->getFd(),'push in http at '. date('H:i:s'));
                $i++;
            }
 
        });
    }
 
    /*
   * HTTP触发向某个客户端单独推送消息
   * @example http://ip:9501/WebSocketTest/push?fd=2
   */
    public function push()
    {
        $fd = $this->request()->getRequestParam('fd');
 
        if(is_numeric($fd))
        {
            /** @var \swoole_websocket_server $server */
            $server = ServerManager::getInstance()->getSwooleServer();
            $info   = $server->getClientInfo($fd);
 
            if($info && $info['websocket_status'] == WEBSOCKET_STATUS_FRAME)
            {
                $server->push($fd, 'http push to fd ' . $fd . ' at ' . date('H:i:s'));
            }
            else
            {
                $this->response()->write("fd {$fd} is not exist or closed");
            }
        }
        else
        {
            $this->response()->write("fd {$fd} is invalid");
        }
    }
 
    /**
     * 使用HTTP触发广播给所有的WS客户端
     *
     * @example http://ip:9501/WebSocketTest/broadcast
     */
    public function broadcast()
    {
        /** @var \swoole_websocket_server $server */
        $server = ServerManager::getInstance()->getSwooleServer();
        $start = 0;
 
        // 此处直接遍历所有FD进行消息投递
        // 生产环境请自行使用Redis记录当前在线的WebSocket客户端FD
        while(true)
        {
            $conn_list = $server->connection_list($start, 10);
 
            if(empty($conn_list))
            {
                break;
            }
 
            $start = end($conn_list);
 
            foreach ($conn_list as $fd)
            {
                $info = $server->getClientInfo($fd);
 
                /** 判断此fd 是否是一个有效的 websocket 连接 */
                if($info && $info['websocket_status'] == WEBSOCKET_STATUS_FRAME)
                {
                    $server->push($fd, 'http broadcast fd ' . $fd . ' at ' . date('H:i:s'));
                }
            }
        }
    }
}
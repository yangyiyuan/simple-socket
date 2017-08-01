<?php
/**
 * @author Ewan
 * @email 654846126@qq.com
 * The more effort ,the more lucky
 *      .--.
 *     |o_o |
 *     |:_/ |
 *    //   \ \
 *   (|     | )
 *  /'\_   _/`\
 *  \___)=(___/
 * @time: 16/12/19 上午9:44
 */

namespace Socket;


class SocketClient extends Socket
{
    /**
     * 超时前多少毫秒
     *
     * @var  integer
     */
    protected $timeout_milliseconds = 0;

    /**
     * 超时钱多少秒
     *
     * @var  integer
     */
    protected $timeout_seconds = 10;

    /**
     * 构造函数
     *
     * @param  string $host 类似localhost,127.0.0.1或者其它IP
     * @param  integer $port 8080或者其它不被占用的端口
     */
    public function __construct($host, $port)
    {
        $this->create($host, $port);
        $this->connect();
    }

    /**
     * 写入服务器
     *
     * @param  string $str
     * @return integer
     */
    public function write($str)
    {
        // 写入到Socket,发送至服务器
        parent::write($this->socket, $str);
    }

    /**
     *  读取服务器响应
     *
     * @return string
     */
    public function read()
    {
        return $this->_read($this->socket);
    }

    /**
     * 连接Socket
     */
    protected function connect()
    {
        // 设置socket参数...
        $timeout = array('sec' => $this->timeout_seconds, 'usec' => $this->timeout_milliseconds);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);

        if (!@socket_connect($this->socket, $this->host, $this->port)) {
            $this->throwError(sprintf('Could not connect to %s:%s', $this->host, $this->port));
        }

        return true;
    }
}
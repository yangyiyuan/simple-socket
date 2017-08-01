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
 * @time: 16/12/19 上午10:12
 */

namespace Socket;


class SocketServer extends Socket
{
    /**
     * 监听客户端连接
     *
     * @var resource
     */
    protected $con = null;

    /**
     * 构造函数
     *
     * @param  string $host 类似localhost,127.0.0.1或者其它IP
     * @param  integer $port 8080或者其它不被占用的端口
     */
    public function __construct($host, $port)
    {
        $this->create($host,$port);
        $this->bind();

        if(!socket_listen($this->socket)){
            $this->throwError('Socket_listen() failed');
        }
    }

    /**
     * 绑定Socket
     */
    protected function bind()
    {
        if (!socket_bind($this->socket, $this->host, $this->port)) {
            $this->throwError(sprintf('Could not connect to %s:%s because: "%s"', $this->host, $this->port));
        }
        return true;
    }

    /**
     * 监听响应并且调用回调函数
     *
     * @param  callback $callback 回调函数获取一个字符串并且响应
     */
    public function listen($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback was not valid');
        }
        $null = null;

        while (true) {
            $input = $this->read();
            $output = call_user_func($callback, $input);
            $this->write($output . $null);
            socket_close($this->con);
        }
    }

    /**
     * 从客户端读取
     *
     * @return string
     */
    public function read()
    {
        $this->con = socket_accept($this->socket);
        return $this->_read($this->con);
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
        parent::write($this->con,$str);
    }
}
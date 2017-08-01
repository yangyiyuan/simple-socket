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
 * @time: 16/12/16 下午5:09
 * @version 1.0
 * @package Socket
 */

namespace Socket;


class Socket
{
    /**
     * IP地址,主机host 127.0.0.1/localhost ...
     *
     * @var string
     */
    protected $host = '';

    /**
     * 端口号 8080 ...
     *
     * @var integer
     */
    protected $port = 0;

    /**
     * Socket
     *
     * @var resource
     */
    protected $socket = null;

    /**
     * 通讯协议
     *
     * @var  integer
     */
    protected $domain = AF_INET;

    /**
     * Socket类型
     *
     * @var  integer
     */
    protected $type = SOCK_STREAM;

    /**
     * 是设置Socket指定通讯协议下的具体协议
     *
     * @var  integer
     */
    protected $protocol = SOL_TCP;

    /**
     * 构造函数
     *
     * @param string $host 类似localhost,127.0.0.1或者其它IP
     * @param integer $port 8080或者其它不被占用的端口
     */
    public function __construct($host, $port)
    {

    }

    /**
     * 写入Socket
     *
     * @param  string $str 发送给服务端/客户端
     * @return integer
     */
    public function write($resource, $str)
    {
        // 显示输出给客户端
        return (int)socket_write($resource, $str);
    }

    /**
     * 从客户端读取
     * 阻塞,直到接到客户端数据
     *
     * @param  resource $socket
     * @return string
     */
    public function _read($socket)
    {
        $ret_str = '';
        $chunk_size = 1024;

        // 读取输入直到结束...
        do {
            $read = socket_read($socket, $chunk_size);
            if ($read !== false) {
                $ret_str .= $read;
            }
        } while (($read !== false) && (mb_strlen($read) >= $chunk_size));

        return $ret_str;
    }

    /**
     * 创建 socket
     */
    protected function create($host, $port)
    {
        if (empty($host)) {
            throw new \InvalidArgumentException('$host was empty');
        }
        if (empty($port)) {
            throw new \InvalidArgumentException('$port was empty');
        }

        $this->socket = socket_create($this->domain, $this->type, $this->protocol);
        if (empty($this->socket)) {
            $this->throwError();
        }

        // 验证主机IP...
        if (!preg_match('#^\d+(\.\d+)+$#', $host)) {
            $ip = gethostbyname($host);
            if ($ip === $host) {
                $this->throwError(sprintf('Host %s could not be converted to IP address', $host));
            } else {
                $host = $ip;
            }

        }

        $this->host = $host;
        $this->port = $port;
    }

    /**
     * 错误捕捉
     *
     * @param  string $errmsg
     * @param  resource $socket
     * @throws \UnexpectedValueException
     */
    protected function throwError($errmsg = '', $socket = null)
    {
        if (!empty($errmsg)) {
            $errmsg .= '. Socket error: ';
        }
        throw new \UnexpectedValueException(
            sprintf(
                '%s"%s"',
                $errmsg,
                $this->getError($socket)
            )
        );
    }

    /**
     *  获取错误信息
     *
     * @param  resource $socket
     * @return string
     */
    protected function getError($socket = null)
    {
        if (empty($socket)) {
            $socket = $this->socket;
        }
        return socket_strerror(socket_last_error($socket));
    }

    /**
     * 析构函数
     * 基本上,关闭所有打开的Socket
     */
    public function __destruct()
    {
        if (!empty($this->con)) {
            socket_close($this->con);
        }
        socket_close($this->socket);
    }

}
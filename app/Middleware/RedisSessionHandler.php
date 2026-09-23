<?php
namespace Middlewares;

class RedisSessionHandler implements \SessionHandlerInterface
{
    private \Redis $redis;
    private int $ttl;
    private string $prefix;

    public function __construct(\Redis $redis, int $ttl, string $prefix = 'session:')
    {
        $this->redis  = $redis;
        $this->ttl    = $ttl > 0 ? $ttl : 3600;
        $this->prefix = $prefix;
    }

    private function key(string $id): string
    {
        return $this->prefix . $id;
    }

    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }

    public function read(string $id): string|false
    {
        $data = $this->redis->get($this->key($id));
        return $data === false ? '' : $data;
    }

    public function write(string $id, string $data): bool
    {
        return $this->redis->setex($this->key($id), $this->ttl, $data);
    }

    public function destroy(string $id): bool
    {
        $this->redis->del($this->key($id));
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        return 0; // Redis TTL handle sendiri
    }
}
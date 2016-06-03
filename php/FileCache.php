<?php
namespace MML\Models;

class FileCache
{
    protected $contents;
    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function get($key)
    {
        $this->load();
        if ($this->exists($key) && !$this->expired($key)) {
            return $this->contents[$key]['value'];
        }
    }

    public function set($key, $value, $expiration = 0)
    {
        $this->load();
        $expiry = ($expiration === 0) ? 0 : time() + $expiration;
        $this->contents[$key] = array('value' => $value, 'expiry' => $expiry);
        $this->commit();
    }

    protected function exists($key)
    {
        return array_key_exists($key, $this->contents);
    }

    protected function expired($key)
    {
        $expiry = intval($this->contents[$key]['expiry']);
        return ($expiry !== 0 && time() > $expiry);
    }

    protected function load()
    {
        if ($this->contents) {
            return;
        }
        if (!file_exists($this->filename)) {
            $this->contents = array();
            return;
        }

        $stuff = file_get_contents($this->filename);
        $arr = json_decode($stuff, true);

        $this->contents = $arr ? $arr : array();
    }

    protected function commit()
    {
        file_put_contents($this->filename, json_encode($this->contents));
    }
}

<?php
/**
 * 配置加密解密类
 * 用于加密存储和读取敏感配置信息
 * 使用简单的异或加密，避免依赖OpenSSL扩展
 */
class ConfigEncrypt {
    private $key;
    
    public function __construct($key = null) {
        // 如果没有提供密钥，则使用默认密钥
        $this->key = $key ? $key : 'FreePayDefaultKey2025!@#';
    }
    
    /**
     * 加密数据
     * @param string $data 要加密的数据
     * @return string 加密后的数据（base64编码）
     */
    public function encrypt($data) {
        $encrypted = '';
        $keyLength = strlen($this->key);
        
        for ($i = 0; $i < strlen($data); $i++) {
            $char = substr($data, $i, 1);
            $keyChar = substr($this->key, ($i % $keyLength), 1);
            $encrypted .= chr(ord($char) ^ ord($keyChar));
        }
        
        return base64_encode($encrypted);
    }
    
    /**
     * 解密数据
     * @param string $data 要解密的数据（base64编码）
     * @return string 解密后的数据
     */
    public function decrypt($data) {
        // 如果数据未加密（原始数据），直接返回
        if (!base64_decode($data, true)) {
            return $data;
        }
        
        $data = base64_decode($data);
        $decrypted = '';
        $keyLength = strlen($this->key);
        
        for ($i = 0; $i < strlen($data); $i++) {
            $char = substr($data, $i, 1);
            $keyChar = substr($this->key, ($i % $keyLength), 1);
            $decrypted .= chr(ord($char) ^ ord($keyChar));
        }
        
        return $decrypted;
    }
}
?>
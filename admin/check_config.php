<?php
// 检查配置信息
require_once 'config_encrypt.php';
$encryptor = new ConfigEncrypt();

try {
    $db = new SQLite3('../config/payment_config.db');
    
    $result = $db->query('SELECT * FROM merchant_config WHERE id = 1');
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($row) {
        echo "数据库中的原始配置信息：\n";
        foreach ($row as $key => $value) {
            echo "$key: $value\n";
        }
        
        echo "\n解密后的敏感信息：\n";
        echo "merchant_key: " . $encryptor->decrypt($row['merchant_key']) . "\n";
        echo "api_url: " . $encryptor->decrypt($row['api_url']) . "\n";
    } else {
        echo "未找到配置信息\n";
    }
    
    $db->close();
} catch (Exception $e) {
    echo "数据库连接失败: " . $e->getMessage();
}
?>
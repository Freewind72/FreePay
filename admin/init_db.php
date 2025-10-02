<?php
// 初始化数据库脚本
require_once 'config_encrypt.php';
$encryptor = new ConfigEncrypt();

try {
    $db = new SQLite3('../config/payment_config.db');
    
    // 创建配置表
    $db->exec("CREATE TABLE IF NOT EXISTS merchant_config (
        id INTEGER PRIMARY KEY,
        pid INTEGER NOT NULL,
        merchant_key TEXT NOT NULL,
        api_url TEXT NOT NULL,
        notify_url TEXT NOT NULL,
        return_url TEXT NOT NULL
    )");
    
    // 检查是否已存在配置
    $result = $db->query('SELECT COUNT(*) as count FROM merchant_config');
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    // 如果没有配置，则插入默认配置（加密敏感信息）
    if ($row['count'] == 0) {
        $encrypted_key = $encryptor->encrypt('aphcyguMzaBYQ7HEkKQFdsnFQ2yKL0aO');
        $encrypted_api_url = $encryptor->encrypt('https://pay.scorcsun.com/submit.php');
        
        $stmt = $db->prepare("INSERT INTO merchant_config (id, pid, merchant_key, api_url, notify_url, return_url) 
                             VALUES (:id, :pid, :merchant_key, :api_url, :notify_url, :return_url)");
        $stmt->bindValue(':id', 1, SQLITE3_INTEGER);
        $stmt->bindValue(':pid', 3, SQLITE3_INTEGER);
        $stmt->bindValue(':merchant_key', $encrypted_key, SQLITE3_TEXT);
        $stmt->bindValue(':api_url', $encrypted_api_url, SQLITE3_TEXT);
        $stmt->bindValue(':notify_url', 'https://bmwy72.top/Pay/notify_url.php', SQLITE3_TEXT);
        $stmt->bindValue(':return_url', 'https://bmwy72.top/Pay/return_url.php', SQLITE3_TEXT);
        $stmt->execute();
        
        echo "数据库初始化成功，并已添加默认配置（已加密）！\n";
    } else {
        echo "数据库已存在配置信息。\n";
    }
    
    $db->close();
} catch (Exception $e) {
    echo "数据库初始化失败: " . $e->getMessage();
}
?>
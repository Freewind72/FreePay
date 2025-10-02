<?php
// 检查数据库中的数据
try {
    $db = new SQLite3('config/payment_config.db');
    
    $result = $db->query('SELECT * FROM merchant_config WHERE id = 1');
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($row) {
        echo "数据库中的配置信息：\n";
        foreach ($row as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "未找到配置信息\n";
    }
    
    $db->close();
} catch (Exception $e) {
    echo "数据库连接失败: " . $e->getMessage();
}
?>
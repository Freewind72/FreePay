<?php
require_once 'admin/config_encrypt.php';
$encryptor = new ConfigEncrypt();

$message = '';
try {
    $db = new SQLite3('config/payment_config.db');

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

    if ($row['count'] == 0) {
        // 插入默认配置（加密敏感信息）
        $encrypted_key = $encryptor->encrypt(''); // 空字符串作为默认值
        $encrypted_api_url = $encryptor->encrypt('');
        
        $stmt = $db->prepare("INSERT INTO merchant_config (id, pid, merchant_key, api_url, notify_url, return_url) 
                             VALUES (:id, :pid, :merchant_key, :api_url, :notify_url, :return_url)");
        $stmt->bindValue(':id', 1, SQLITE3_INTEGER);
        $stmt->bindValue(':pid', 0, SQLITE3_INTEGER);
        $stmt->bindValue(':merchant_key', $encrypted_key, SQLITE3_TEXT);
        $stmt->bindValue(':api_url', $encrypted_api_url, SQLITE3_TEXT);
        $stmt->bindValue(':notify_url', 'https://bmwy72.top/Pay/notify_url.php', SQLITE3_TEXT);
        $stmt->bindValue(':return_url', 'https://bmwy72.top/Pay/return_url.php', SQLITE3_TEXT);
        $stmt->execute();
        
        $message = "数据库初始化成功，并已添加默认配置（已加密）！";
    } else {
        $message = "数据库已存在配置信息。";
    }

    $db->close();
} catch (Exception $e) {
    $message = "数据库初始化失败: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>初始化数据库</title>
    <link href="https://cdn.bootcss.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>初始化数据库</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info"><?php echo $message; ?></div>
                        <a href="index.php" class="btn btn-primary">返回首页</a>
                        <a href="admin/init_db_manual.php" class="btn btn-secondary">管理数据库</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
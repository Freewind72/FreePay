<?php
// 手动初始化数据库的Web界面
require_once 'config_encrypt.php';
$encryptor = new ConfigEncrypt();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        
        // 检查是否已有配置
        $result = $db->query('SELECT COUNT(*) as count FROM merchant_config');
        $row = $result->fetchArray(SQLITE3_ASSOC);
        
        if ($row['count'] == 0) {
            // 插入默认配置（加密敏感信息）
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
            
            $message = "数据库初始化成功，并已添加默认配置（已加密）！";
        } else {
            $message = "数据库已经初始化过了。";
        }
        
        $db->close();
    } catch (Exception $e) {
        $message = "数据库初始化失败: " . $e->getMessage();
    }
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
                        <?php if (isset($message)): ?>
                            <div class="alert alert-info"><?php echo $message; ?></div>
                        <?php endif; ?>
                        
                        <p>点击下面的按钮初始化SQLite数据库并创建配置表：</p>
                        
                        <form method="post">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">初始化数据库</button>
                            </div>
                        </form>
                        
                        <div class="mt-3">
                            <a href="admin.php" class="btn btn-secondary">前往管理后台</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>
<?php
// 后台管理页面
session_start();

require_once 'config_encrypt.php';
$encryptor = new ConfigEncrypt();

// 检查是否提交了表单
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据
    $pid = $_POST['pid'];
    $key = $_POST['key'];
    $api_url = $_POST['api_url'];
    $notify_url = $_POST['notify_url'];
    $return_url = $_POST['return_url'];
    
    try {
        // 连接数据库并更新配置
        $db = new SQLite3('../config/payment_config.db');
        
        // 确保表存在
        $db->exec("CREATE TABLE IF NOT EXISTS merchant_config (
            id INTEGER PRIMARY KEY,
            pid INTEGER NOT NULL,
            merchant_key TEXT NOT NULL,
            api_url TEXT NOT NULL,
            notify_url TEXT NOT NULL,
            return_url TEXT NOT NULL
        )");
        
        // 加密敏感数据
        $encrypted_key = $encryptor->encrypt($key);
        $encrypted_api_url = $encryptor->encrypt($api_url);
        
        $stmt = $db->prepare("UPDATE merchant_config SET 
                             pid = :pid, 
                             merchant_key = :merchant_key, 
                             api_url = :api_url, 
                             notify_url = :notify_url, 
                             return_url = :return_url 
                             WHERE id = 1");
        $stmt->bindValue(':pid', $pid, SQLITE3_INTEGER);
        $stmt->bindValue(':merchant_key', $encrypted_key, SQLITE3_TEXT);
        $stmt->bindValue(':api_url', $encrypted_api_url, SQLITE3_TEXT);
        $stmt->bindValue(':notify_url', $notify_url, SQLITE3_TEXT);
        $stmt->bindValue(':return_url', $return_url, SQLITE3_TEXT);
        $stmt->execute();
        
        $message = "配置已成功更新！";
        $db->close();
    } catch (Exception $e) {
        $message = "更新失败: " . $e->getMessage();
    }
}

// 从数据库读取当前配置
$config = null;
try {
    $db = new SQLite3('../config/payment_config.db');
    
    // 确保表存在
    $db->exec("CREATE TABLE IF NOT EXISTS merchant_config (
        id INTEGER PRIMARY KEY,
        pid INTEGER NOT NULL,
        merchant_key TEXT NOT NULL,
        api_url TEXT NOT NULL,
        notify_url TEXT NOT NULL,
        return_url TEXT NOT NULL
    )");
    
    // 检查是否存在记录，如果不存在则插入默认值
    $result = $db->query('SELECT COUNT(*) as count FROM merchant_config');
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($row['count'] == 0) {
        // 插入默认配置（加密敏感信息）
        $encrypted_default_key = $encryptor->encrypt('aphcyguMzaBYQ7HEkKQFdsnFQ2yKL0aO');
        $encrypted_default_api_url = $encryptor->encrypt('https://pay.scorcsun.com/submit.php');
        
        $stmt = $db->prepare("INSERT INTO merchant_config (id, pid, merchant_key, api_url, notify_url, return_url) 
                             VALUES (:id, :pid, :merchant_key, :api_url, :notify_url, :return_url)");
        $stmt->bindValue(':id', 1, SQLITE3_INTEGER);
        $stmt->bindValue(':pid', 3, SQLITE3_INTEGER);
        $stmt->bindValue(':merchant_key', $encrypted_default_key, SQLITE3_TEXT);
        $stmt->bindValue(':api_url', $encrypted_default_api_url, SQLITE3_TEXT);
        $stmt->bindValue(':notify_url', 'https://bmwy72.top/Pay/notify_url.php', SQLITE3_TEXT);
        $stmt->bindValue(':return_url', 'https://bmwy72.top/Pay/return_url.php', SQLITE3_TEXT);
        $stmt->execute();
    }
    
    // 获取配置并解密
    $result = $db->query('SELECT * FROM merchant_config WHERE id = 1');
    $config = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($config) {
        $config['merchant_key'] = $encryptor->decrypt($config['merchant_key']);
        $config['api_url'] = $encryptor->decrypt($config['api_url']);
    }
    
    $db->close();
} catch (Exception $e) {
    $error = "数据库连接失败: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>支付配置管理后台</title>
    <link href="https://cdn.bootcss.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">支付配置管理</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <a href="view_config.php" class="btn btn-info">查看配置信息</a>
                        <a href="test_encryption.php" class="btn btn-secondary">测试加密功能</a>
                    </div>
                    
                    <?php if ($config): ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="pid" class="form-label">商户ID (pid)</label>
                                <input type="number" class="form-control" id="pid" name="pid" 
                                       value="<?php echo htmlspecialchars($config['pid']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="key" class="form-label">商户密钥 (key)</label>
                                <input type="text" class="form-control" id="key" name="key" 
                                       value="<?php echo htmlspecialchars($config['merchant_key']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="api_url" class="form-label">API地址 (api_url)</label>
                                <input type="url" class="form-control" id="api_url" name="api_url" 
                                       value="<?php echo htmlspecialchars($config['api_url']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notify_url" class="form-label">通知地址 (notify_url)</label>
                                <input type="url" class="form-control" id="notify_url" name="notify_url" 
                                       value="<?php echo htmlspecialchars($config['notify_url']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="return_url" class="form-label">返回地址 (return_url)</label>
                                <input type="url" class="form-control" id="return_url" name="return_url" 
                                       value="<?php echo htmlspecialchars($config['return_url']); ?>" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">保存配置</button>
                            </div>
                        </form>
                    <?php elseif (!isset($error)): ?>
                        <div class="alert alert-warning">未能加载配置信息</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
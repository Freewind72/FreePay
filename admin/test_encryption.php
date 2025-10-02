<?php
// 测试加密功能
require_once 'config_encrypt.php';
$encryptor = new ConfigEncrypt();

$test_api_url = 'https://pay.scorcsun.com/submit.php';

echo "<h2>测试加密功能</h2>";

// 加密API URL
$encrypted_api_url = $encryptor->encrypt($test_api_url);
echo "<p><strong>原始API URL:</strong> " . $test_api_url . "</p>";
echo "<p><strong>加密后的API URL:</strong> " . $encrypted_api_url . "</p>";

// 解密API URL
$decrypted_api_url = $encryptor->decrypt($encrypted_api_url);
echo "<p><strong>解密后的API URL:</strong> " . $decrypted_api_url . "</p>";

echo "<p><strong>加密/解密是否成功:</strong> " . ($test_api_url === $decrypted_api_url ? "是" : "否") . "</p>";

// 测试数据库中的数据
echo "<h2>测试数据库中的数据</h2>";

try {
    $db = new SQLite3('../config/payment_config.db');
    
    $result = $db->query('SELECT api_url FROM merchant_config WHERE id = 1');
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($row) {
        $db_api_url = $row['api_url'];
        echo "<p><strong>数据库中的API URL:</strong> " . $db_api_url . "</p>";
        
        // 尝试解密
        $decrypted_db_api_url = $encryptor->decrypt($db_api_url);
        echo "<p><strong>数据库中API URL解密后:</strong> " . $decrypted_db_api_url . "</p>";
        
        echo "<p><strong>数据库中数据是否已加密:</strong> " . ($db_api_url !== $decrypted_db_api_url ? "是" : "否") . "</p>";
    } else {
        echo "<p>数据库中没有找到配置信息</p>";
    }
    
    $db->close();
} catch (Exception $e) {
    echo "<p class='text-danger'>数据库连接失败: " . $e->getMessage() . "</p>";
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>测试加密功能</title>
    <link href="https://cdn.bootcss.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>加密功能测试</h3>
                    </div>
                    <div class="card-body">
                        <?php echo "<!-- 输出在上面 -->"; ?>
                        <a href="admin.php" class="btn btn-primary">返回管理后台</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
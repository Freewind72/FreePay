<?php
// 查看配置信息的Web界面
require_once 'config_encrypt.php';
$encryptor = new ConfigEncrypt();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>查看配置信息</title>
    <link href="https://cdn.bootcss.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>数据库配置信息</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            $db = new SQLite3('../config/payment_config.db');
                            
                            $result = $db->query('SELECT * FROM merchant_config WHERE id = 1');
                            $row = $result->fetchArray(SQLITE3_ASSOC);
                            
                            if ($row) {
                                echo "<h5>数据库中的原始配置信息：</h5>";
                                echo "<ul class='list-group mb-4'>";
                                foreach ($row as $key => $value) {
                                    echo "<li class='list-group-item'><strong>$key:</strong> $value</li>";
                                }
                                echo "</ul>";
                                
                                echo "<h5>解密后的敏感信息：</h5>";
                                echo "<ul class='list-group'>";
                                echo "<li class='list-group-item'><strong>merchant_key:</strong> " . $encryptor->decrypt($row['merchant_key']) . "</li>";
                                echo "<li class='list-group-item'><strong>api_url:</strong> " . $encryptor->decrypt($row['api_url']) . "</li>";
                                echo "</ul>";
                            } else {
                                echo "<div class='alert alert-warning'>未找到配置信息</div>";
                            }
                            
                            $db->close();
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>数据库连接失败: " . $e->getMessage() . "</div>";
                        }
                        ?>
                        
                        <div class="mt-3">
                            <a href="admin.php" class="btn btn-primary">返回管理后台</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
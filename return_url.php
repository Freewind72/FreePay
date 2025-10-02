<?php
// 设置跳转时间（单位：秒）
$redirect_delay = 5;
$home_url = "https://bmwy72.top/Pay/";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>支付成功 - 您的网站名称</title>
    <!-- 使用CDN加速的Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px 50px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .checkmark {
            width: 80px;
            height: 80px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 40px;
            animation: scaleIn 0.5s ease-out;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 2.2em;
        }

        p {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 1.1em;
        }

        .countdown {
            color: #4CAF50;
            font-weight: bold;
            font-size: 1.2em;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
            margin-top: 20px;
            border: 2px solid transparent;
        }

        .btn:hover {
            background: white;
            color: #4CAF50;
            border-color: #4CAF50;
            transform: translateY(-2px);
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .success-container {
                padding: 30px;
            }

            h1 {
                font-size: 1.8em;
            }

            p {
                font-size: 1em;
            }

            .checkmark {
                width: 60px;
                height: 60px;
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="checkmark">
            <i class="fas fa-check"></i>
        </div>
        <h1>支付成功！</h1>
        <p>感谢您的购买，订单已处理完成。</p>
        <p>我们已将订单确认信息发送至您的注册邮箱。</p>
        <p>页面将在 <span class="countdown" id="countdown"><?php echo $redirect_delay; ?></span> 秒后自动跳转至首页</p>
        <a href="<?php echo $home_url; ?>" class="btn">立即返回首页</a>
    </div>

    <script>
        // 倒计时跳转
        let seconds = <?php echo $redirect_delay; ?>;
        const countdownEl = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = "<?php echo $home_url; ?>";
            }
        }, 1000);
    </script>
</body>
</html>
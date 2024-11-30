<?php
// 定义变量
$profileImage = '头像图片路径';
$username = '@Yang-b602_';
$images = ['图片1路径', '图片2路径', '图片3路径', '图片4路径', '图片5路径', '图片6路径'];
$description = 'id即备注 生活>追星';
$details = [
    '👆 capper Mingyu Sunoo',
    '🍑 ningning Giselle 李羲承',
    '尹亘汉 南柱赫 许光汉',
    '时代峰峻234代',
    '次9同担同梦 某人每追',
    '🎸 超绝 ehp svt 团魂。女团博爱',
    '克拉忞静快来带我玩🤲'
];
$qrcodeImage = '二维码图片路径';
$location = '中国 湖北省 武汉市';
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>微信展示页面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        .profile {
            margin-bottom: 20px;
        }
        .profile img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }
        .username {
            font-weight: bold;
            margin-top: 10px;
        }
        .images {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 三列布局 */
            gap: 1px; /* 图片间距为1px */
            width: 80%; /* 设置宽度为80%以便占据屏幕的0.8 */
            margin: 0 auto; /* 居中 */
        }
        .image-container {
            position: relative;
            width: 100%; /* 占满整个格子 */
            padding-top: 177.78%; /* 9:16的比例，实际高度 = 宽度 * (16/9) */
            background-color: #f0f0f0; /* 淡灰色背景 */
            overflow: hidden; /* 隐藏溢出部分 */
        }
        .image-container img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: auto;
            min-height: 100%;
            min-width: 100%;
            transform: translate(-50%, -50%); /* 居中对齐 */
            object-fit: cover; /* 保持宽高比，裁剪多余部分 */
        }
        .placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 14px; /* 提示文字大小 */
        }
        .upload {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* 让上传按钮居中 */
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            padding: 2px 5px;
        }
        .content {
            display: flex; /* 水平排列左右部分 */
            margin-top: 20px; /* 上部留空 */
            width: 80%; /* 总宽度80% */
            margin: 20px auto; /* 中心对齐 */
        }
        .left {
            width: 60%; /* 描述和细节部分宽度 */
            text-align: left;
            padding-right: 20px; /* 右侧留空间 */
        }
        .right {
            width: 40%; /* QR码和位置部分宽度 */
            text-align: left;
        }
        .qrcode {
            margin-top: 10px;
        }
        .location {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="profile">
        <img src="<?php echo $profileImage; ?>" alt="Profile Image">
        <div class="username"><?php echo $username; ?></div>
    </div>

    <div class="images">
        <?php foreach ($images as $index => $image): ?>
            <div class="image-container" id="image-container-<?php echo $index; ?>">
                <img src="<?php echo $image; ?>" alt="image" onerror="imageError(<?php echo $index; ?>)" id="img-<?php echo $index; ?>">
                <div class="placeholder" id="placeholder-<?php echo $index; ?>">请上传图片</div>
                <div class="upload" onclick="uploadImage(<?php echo $index; ?>)">上传</div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="content">
        <div class="left">
            <div class="description"><?php echo $description; ?></div>
            <div class="details">
                <?php foreach ($details as $detail): ?>
                    <div><?php echo $detail; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="right">
            <div class="qrcode">
                <img src="<?php echo $qrcodeImage; ?>" alt="QR Code">
            </div>
            <div class="location"><?php echo $location; ?></div>
        </div>
    </div>

    <script>
        function uploadImage(index) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';

            input.onchange = async (event) => {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                try {
                    const response = await fetch('/?s=App.Homepage_Homepage.upload', {
                        method: 'POST',
                        body: formData,
                    });

                    const data = await response.json();

                    if (data.ret === 200) {
                        const newImageURL = data.data;
                        const imgElement = document.getElementById(`img-${index}`);
                        const placeholder = document.getElementById(`placeholder-${index}`);
                        
                        imgElement.src = newImageURL; // 更新图片地址
                        imgElement.style.display = 'block'; // 显示图片
                        placeholder.style.display = 'none'; // 隐藏占位符
                    } else {
                        alert(data.msg);
                    }
                } catch (error) {
                    console.error('上传出错:', error);
                    alert('上传失败，请重试。');
                }
            };

            input.click();
        }

        function imageError(index) {
            const imgElement = document.getElementById(`img-${index}`);
            const placeholder = document.getElementById(`placeholder-${index}`);
            imgElement.style.display = 'none'; // 不显示破损的图片
            placeholder.style.display = 'flex'; // 显示占位符
        }
    </script>

</body>
</html>

<?php
// 定义变量
$profileImage = '头像图片路径';
$username = '@Yang-b602_';
$images = ['图片1路径', '图片2路径', '图片3路径', '图片4路径', '图片5路径', '图片6路径']; // 确保最多为六张图片
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
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>微信展示页面</title>
    <style>
        @font-face {
            font-family: 'LM Roman Bold';  /* 为新字体定义一个名称 */
            src: url('/font/lmroman-bold.woff') format('woff'); /* 指定字体文件的 URL 和格式 */
        }
        body {
            font-family: 'LM Roman Bold', Arial, sans-serif; /* 将自定义字体应用于 body 元素 */
            text-align: center;
            padding: 20px;
            margin: 0;
            font-size: 14px !important;
        }
        .profile {
            margin-bottom: 20px;
        }
        .profile img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }
        .profile-image-container {
            width: 150px; /* 固定宽度 */
            height: 150px; /* 固定高度 */
            background-color: #f0f0f0; /* 灰色背景 */
            border-radius: 50%; /* 圆形边角 */
            overflow: hidden; /* 隐藏溢出部分 */
            display: inline-block; /* 使元素保持行内块级特性 */
        }
        .username {
            font-weight: bold;
            font-size: 24px;
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
            padding-top: 177.78%; /* 9:16的比例 */
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
        .upload {
            position: absolute;
            top: 50%; /* 垂直居中 */
            left: 50%; /* 水平居中 */
            transform: translate(-50%, -50%); /* 让上传按钮居中 */
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            padding: 2px 5px;
            z-index: 1; /* 确保在其他元素之上 */
        }
        .content {
            display: flex; /* 水平排列左右部分 */
            margin-top: 20px; /* 上部留空 */
            width: 80%; /* 总宽度80% */
            margin: 20px auto; /* 中心对齐 */
        }
        .left {
            width: 50%; /* 描述和细节部分宽度 */
            text-align: left;
            padding-right: 5px; /* 右侧留空间 */
        }
        .right {
            width: 50%; /* QR码部分宽度 */
            text-align: center; /* QR码内容居中 */
            position: relative; /* 使二维码和上传按钮相对定位 */
        }
        .qrcode {
    position: relative;
    padding-top: 133.33%; /* 固定高度与宽度之比为3:4 */
    overflow: hidden; /* 隐藏溢出部分 */
    margin: 0; /* 居中对齐 */
    
    /* 添加方块的大小和位置 */
    background-size: 20px 20px; /* 方块的大小 */
}

.qrcode .corner {
    position: absolute;
    background-color: #ccc; /* 浅灰色 */
    top: 12.5%; /* 向下移动12.5% */
}

.corner.hidden {
    display: none; /* 隐藏角落 */
}

.inner-square {
    background-color: white; /* 白色 */
    width: 30px; /* 设置宽度小于corner的宽度 */
    height: 30px; /* 设置高度小于corner的高度 */
    position: absolute;
    top: 5px; /* 距离上边缘5px */
    left: 5px; /* 距离左边缘5px */
    z-index: 1; /* 确保在其他元素之上 */
}

.inner-square-gray {
    background-color: #ccc; /* 浅灰色 */
    width: 20px; /* 设置宽度小于伪元素的宽度 */
    height: 20px; /* 设置高度小于伪元素的高度 */
    position: absolute;
    top: 5px; /* 距离白色方块的上边缘5px */
    left: 5px; /* 距离白色方块的左边缘5px */
    z-index: 2; /* 确保在最上方 */
}


/* 定义左上角的块 */
.qrcode .corner-tl {
    width: 40px; /* 左上角块的宽度 */
    height: 40px; /* 左上角块的高度 */
    top: 12.5%; /* 向下移动12.5% */
    left: 0;
}

/* 定义右上角的块 */
.qrcode .corner-tr {
    width: 40px; /* 右上角块的宽度 */
    height: 40px; /* 右上角块的高度 */
    top: 12.5%; /* 向下移动12.5% */
    right: 0;
}

/* 定义左下角的块 */
.qrcode .corner-bl {
    width: 40px; /* 左下角块的宽度 */
    height: 40px; /* 左下角块的高度 */
    top: 67.5%; /* 原位置 55% + 12.5% */
    left: 0;
}

        .qrcode img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: auto;
            min-height: 100%;
            min-width: 100%;
            transform: translate(-50%, -50%); /* 居中对齐 */
            object-fit: cover; /* 保持真实比例 */
        }
        .qrcode .upload {
            position: absolute; /* 绝对定位 */
            top: 50%; /* 垂直居中 */
            left: 50%; /* 水平居中 */
            transform: translate(-50%, -50%); /* 使其居中 */
            background-color: rgba(255, 255, 255, 0.7); /* 背景色，保持原样 */
            border: 1px solid #ccc; /* 边框，保持原样 */
            border-radius: 5px; /* 圆角，保持原样 */
            cursor: pointer; /* 鼠标指针 */
            padding: 2px 5px; /* 内边距，保持原样 */
            z-index: 1; /* 确保在图片之上 */
        }
    </style>
</head>
<body>

    <div class="profile">
        <div class="profile-image-container">
            <img src="<?php echo $profileImage; ?>" alt="Profile Image" onerror="this.style.display='none';" style="width: 100%; height: auto; min-height: 100%; min-width: 100%; object-fit: cover;">
        </div>
        <div class="username"><?php echo $username; ?></div>
    </div>

    <div class="images">
        <?php foreach ($images as $index => $image): ?>
            <div class="image-container" id="image-container-<?php echo $index; ?>">
                <img src="<?php echo $image; ?>" alt="image" onerror="imageError(<?php echo $index; ?>)" id="img-<?php echo $index; ?>">
                <div class="upload" onclick="uploadImage(<?php echo $index; ?>)"><?php echo $image ? '修改' : '上传'; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="content">
        <div class="left">
            <div class="details">
                <?php foreach ($details as $detail): ?>
                    <div><?php echo $detail; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="right">
            <div class="qrcode">
                <img src="<?php echo $qrcodeImage; ?>" alt="QR Code" id="qr-code" onerror="qrCodeError()">
                <div class="corner corner-tl">
                    <div class="inner-square">
                        <div class="inner-square-gray"></div> <!-- 浅灰色方块 -->
                    </div> <!-- 白色方块 -->
                </div> <!-- 左上角 -->
                <div class="corner corner-tr">
                    <div class="inner-square">
                        <div class="inner-square-gray"></div> <!-- 浅灰色方块 -->
                    </div> <!-- 白色方块 -->
                </div> <!-- 右上角 -->
                <div class="corner corner-bl">
                    <div class="inner-square">
                        <div class="inner-square-gray"></div> <!-- 浅灰色方块 -->
                    </div> <!-- 白色方块 -->
                </div> <!-- 左下角 -->
                <div class="upload" onclick="uploadQRCode()"><?php echo $qrcodeImage ? '修改' : '上传'; ?></div>
            </div>
        </div>
    </div>

    <script>
        function compressImage(file, maxWidth, callback) {
            const reader = new FileReader();
            
            reader.onload = function (event) {
                const img = new Image();
                img.src = event.target.result;
                
                img.onload = function () {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    const scaleFactor = maxWidth / img.width; // 计算缩放比例
                    canvas.width = maxWidth; // 设置canvas宽度
                    canvas.height = img.height * scaleFactor; // 按比例设置canvas高度
                    
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob(function (blob) {
                        callback(blob); // 通过回调返回压缩后的文件
                    }, 'image/jpeg', 0.9); // 设置图像格式和质量
                };
            };

            reader.readAsDataURL(file);
        }

        function uploadQRCode() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';

    input.onchange = function (event) {
        const file = event.target.files[0];
        if (!file) return;

        compressImage(file, 200, function (compressedBlob) { // 这里将最大宽度设置为800
            const formData = new FormData();
            formData.append('file', compressedBlob, file.name); // 使用压缩后的文件

            const uploadButton = document.querySelector('.qrcode .upload');
            uploadButton.textContent = '上传中'; // 修改按钮文字为“上传中”

            fetch('/?s=App.Homepage_Homepage.upload', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.ret === 200) {
                    const newQRCodeURL = data.data;
                    const imgElement = document.getElementById('qr-code');
                    imgElement.src = newQRCodeURL; // 更新二维码地址
                    imgElement.style.display = 'block'; // 确保图片可见
                    imgElement.onerror = null; // 清除之前的错误处理
                    uploadButton.textContent = '修改'; // 更新按钮文字为“修改”

                    // 隐藏角落
                    const corners = document.querySelectorAll('.qrcode .corner');
                    corners.forEach(corner => {
                        corner.classList.add('hidden'); // 为所有角落添加隐藏类
                    });
                } else {
                    alert(data.msg);
                    uploadButton.textContent = '上传'; // 恢复按钮文字
                }
            })
            .catch(error => {
                console.error('上传出错:', error);
                alert('上传失败，请重试。');
                uploadButton.textContent = '上传'; // 恢复按钮文字
            });
        });
    };

    input.click();
}

function uploadImage(index) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';

    input.onchange = function (event) {
        const file = event.target.files[0];
        if (!file) return;

        compressImage(file, 200, function (compressedBlob) { // 这里将最大宽度设置为800
            const formData = new FormData();
            formData.append('file', compressedBlob, file.name); // 使用压缩后的文件

            const uploadButton = document.querySelector(`#image-container-${index} .upload`);
            uploadButton.textContent = '上传中'; // 修改按钮文字为“上传中”

            fetch('/?s=App.Homepage_Homepage.upload', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.ret === 200) {
                    const newImageURL = data.data;
                    const imgElement = document.getElementById(`img-${index}`);
                    imgElement.src = newImageURL; // 更新图片地址
                    imgElement.style.display = 'block'; // 显示图片
                    uploadButton.textContent = '修改'; // 修改按钮文字
                } else {
                    alert(data.msg);
                    uploadButton.textContent = '上传'; // 恢复按钮文字
                }
            })
            .catch(error => {
                console.error('上传出错:', error);
                alert('上传失败，请重试。');
                uploadButton.textContent = '上传'; // 恢复按钮文字
            });
        });
    };

    input.click();
}

        function imageError(index) {
            const imgElement = document.getElementById(`img-${index}`);
            const uploadButton = document.querySelector(`#image-container-${index} .upload`);
            
            imgElement.style.display = 'none'; // 不显示破损的图片
            uploadButton.textContent = '上传'; // 将按钮文字更改为“上传”
        }

        function qrCodeError() {
            const imgElement = document.getElementById('qr-code');
            imgElement.style.display = 'none'; // 隐藏破损的二维码图片

            // 修改上传按钮文本为“上传”
            const uploadButton = document.querySelector('.qrcode .upload');
            uploadButton.textContent = '上传'; // 将按钮文字更改为“上传”
            
            // 显示二维码区域的背景图案
            const qrCodeContainer = document.querySelector('.qrcode');
            qrCodeContainer.style.backgroundImage = 'radial-gradient(circle, #ccc 5%, transparent 5%), radial-gradient(circle, transparent 5%, #ccc 5%, #ccc 10%, transparent 10%, transparent)';
        }

    </script>

</body>
</html>

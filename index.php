<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>抽獎輪盤</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            min-height: 100vh;
            background-color: #f0f2f5;
        }
        .container {
            display: flex;
            width: 100%;
            gap: 20px;
        }
        .wheel-container {
            flex: 2;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .input-container {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        #canvas {
            max-width: 100%;
            margin: 0 auto;
            display: block;
        }
        textarea {
            width: 100%;
            height: 300px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        #result {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wheel-container">
            <canvas id="canvas" width="500" height="500"></canvas>
            <div id="result"></div>
        </div>
        <div class="input-container">
            <h2>輸入參與者名單</h2>
            <p>每行輸入一個名字</p>
            <textarea id="nameList" placeholder="請輸入名字，每行一個..."></textarea>
            <button onclick="startSpin()">開始抽獎</button>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        let names = [];
        let isSpinning = false;
        let currentRotation = 0;
        let targetRotation = 0;
        let spinSpeed = 0;
        const friction = 0.99;
        const colors = ['#FFB6C1', '#98FB98', '#FFA07A', '#FFEEAD', '#E6E6FA', '#F0E68C', '#FFE4E1', '#FFDAB9', '#F4A460', '#DDA0DD', '#F5DEB3', '#FFE4B5'];

        function drawWheel() {
            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;
            const radius = Math.min(centerX, centerY) - 10;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            if (names.length === 0) {
                // 畫空白輪盤
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
                ctx.fillStyle = '#f0f0f0';
                ctx.fill();
                ctx.strokeStyle = '#FFB6C1';  
                ctx.stroke();
                
                ctx.fillStyle = '#FFB6C1';  
                ctx.font = '20px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('請在右側輸入名單', centerX, centerY);
                return;
            }

            const sliceAngle = (Math.PI * 2) / names.length;

            names.forEach((name, i) => {
                const startAngle = i * sliceAngle + currentRotation;
                const endAngle = startAngle + sliceAngle;

                // 繪製扇形
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.closePath();
                ctx.fillStyle = colors[i % colors.length];
                ctx.fill();
                ctx.stroke();

                // 繪製文字
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(startAngle + sliceAngle / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#fff';
                ctx.font = `${Math.min(36, 300/names.length)}px Arial`;
                ctx.fillText(name, radius - 20, 0);
                ctx.restore();
            });

            // 繪製指針
            ctx.moveTo(centerX + radius + 20, centerY - 5);  
            ctx.lineTo(centerX + radius + 20, centerY + 5);  
            ctx.lineTo(centerX + radius - 30, centerY);      
            ctx.closePath();
            ctx.fillStyle = '#000080';  
            ctx.fill();

            // 繪製指針尾巴
            ctx.beginPath();
            ctx.moveTo(centerX + radius - 30, centerY);      
            ctx.lineTo(centerX + radius - 35, centerY - 5);  
            ctx.lineTo(centerX + radius - 35, centerY + 5);
            ctx.closePath();
            ctx.fillStyle = '#000080';  
            ctx.fill();
        }

        function updateSpin() {
            if (!isSpinning) return;

            currentRotation += spinSpeed;
            spinSpeed *= friction;

            if (spinSpeed < 0.001) {
                isSpinning = false;
                const sliceAngle = (Math.PI * 2) / names.length;
                const normalizedRotation = currentRotation % (Math.PI * 2);
                const winningIndex = Math.floor(names.length - (normalizedRotation / sliceAngle)) % names.length;
                document.getElementById('result').innerHTML = `恭喜 ${names[winningIndex]} 中獎！`;
            }

            drawWheel();
            if (isSpinning) {
                requestAnimationFrame(updateSpin);
            }
        }

        function startSpin() {
            const textarea = document.getElementById('nameList');
            names = textarea.value.split('\n').filter(name => name.trim() !== '');
            
            if (names.length < 2) {
                alert('請至少輸入兩個名字！');
                return;
            }

            if (!isSpinning) {
                isSpinning = true;
                spinSpeed = 0.2 + Math.random() * 0.1;
                document.getElementById('result').innerHTML = '';
                updateSpin();
            }
        }

        // 初始化繪製
        drawWheel();
    </script>
</body>
</html>

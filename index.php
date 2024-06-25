<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point Clicker</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        .circle {
            width: 50px;
            height: 50px;
            background-color: blue;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        #points {
            position: absolute;
            top: 20px;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <div class="circle"></div>
    <div id="points">Points: 0</div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const circle = document.querySelector('.circle');
            const pointsDisplay = document.getElementById('points');
            let points = 0;

            function updatePoints() {
                pointsDisplay.textContent = `Points: ${points}`;
            }

            document.body.addEventListener('click', function (event) {
                if (circle.contains(event.target)) {
                    points++;
                } else {
                    points--;
                }
                updatePoints();
            });
        });
    </script>
</body>
</html>

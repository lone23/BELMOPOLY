<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belmopoly</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #0c0118, #302040);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            color: #d1b3ff;
            text-align: center;
        }
        .container {
            position: relative;
            width: 30vw;
            padding: 20px;
            border-radius: 10px;
        }
        h1 {
            font-size: 50px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .button {
            display: block;
            width: 80%;
            margin: 10px auto;
            padding: 15px;
            background: #5a1fa7;
            border: none;
            color: white;
            font-size: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .button:hover {
            background: #7741c2;
        }
        .top-icons {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
        }
        .top-icons img {
            width: 30px;
            height: 30px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="top-icons">
    <img src="user-icon.png" alt="User Profile">
    <img src="group-icon.png" alt="Multiplayer">
</div>
<div class="container">
    <h1>BELMOPOLY</h1>
    <button class="button">NEW GAME</button>
    <button class="button">CONTINUE</button>
    <button class="button">CHARACTER</button>
</div>
</body>
</html>
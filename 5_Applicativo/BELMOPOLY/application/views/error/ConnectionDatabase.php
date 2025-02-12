<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Errore di Connessione</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8d7da;
            color: #721c24;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .error-container {
            text-align: center;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
        }
        h1 {
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
        }
        .retry-button {
            background-color: #f5c6cb;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .retry-button:hover {
            background-color: #f1b0b7;
        }
    </style>
</head>
<body>

<div class="error-container">
    <h1>Errore di Connessione al Database</h1>
    <a href="javascript:window.location.reload();" class="retry-button">Riprova</a>
</div>

</body>
</html>


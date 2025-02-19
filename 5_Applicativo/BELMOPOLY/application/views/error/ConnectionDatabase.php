<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Errore di Connessione</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(49,0,90);
            color: rgb(157,78,221);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .error-container {
            text-align: center;
            background-color: rgb(49,0,90);
            border: 1px solid rgb(49,0,90);
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
            color: white;
            background-color:rgb(157,78,221);
            border: 1px solid rgb(49,0,90);
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .retry-button:hover {
            color: white;
            background-color:rgb(157,78,221);
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


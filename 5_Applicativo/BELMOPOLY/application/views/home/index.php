<h1>HOME</h1>
<p><?php echo $_SESSION['email']?></p>
<p><?php echo $_SESSION['username']?></p>

<h2>Aggiungi amico</h2>
<form method="POST" action="<?php echo URL?>GestioneAccount/aggiungiAmico">
    Username:    <input type="text" name="username"><br>


    <p><?php
        if(Isset($_SESSION["ControlloAmico"])){
            echo $_SESSION["ControlloAmico"];
        }
        ?></p>

    <input type="submit" value="Seleziona">
</form>

<h2>Richieste amicizia</h2>

<table style="border: 1px solid">
    <?php foreach ($amici as $utente) : ?>
        <tr>
            <td><?php echo $utente->getUsername(); ?></td>
        </tr>
    <?php endforeach; ?>
</table>


<h2>Accetta richieste di amicizia</h2>
<form method="POST" action="<?php echo URL?>GestioneAccount/accettaRichiestaAmicizia">
    Username:    <input type="text" name="username"><br>


    <p><?php
        if(Isset($_SESSION["ControlloAmico"])){
            echo $_SESSION["ControlloAmico"];
        }
        ?></p>

    <input type="submit" value="Seleziona">
</form>


<h2>Rifiuta richieste di amicizia</h2>
<form method="POST" action="<?php echo URL?>GestioneAccount/rifiutaRichiestaAmicizia">
    Username:    <input type="text" name="username"><br>


    <p><?php
        if(Isset($_SESSION["ControlloAmico"])){
            echo $_SESSION["ControlloAmico"];
        }
        ?></p>

    <input type="submit" value="Seleziona">
</form>


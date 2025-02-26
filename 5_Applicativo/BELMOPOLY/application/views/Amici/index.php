<h2>amicizie</h2>

<table style="border: 1px solid">
    <?php foreach ($amici as $utente) : ?>
        <tr>
            <td><?php echo $utente->getUsername(); ?></td>
        </tr>
    <?php endforeach; ?>
</table>

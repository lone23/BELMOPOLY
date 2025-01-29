<h1>LISTA UTENTI</h1>

<table style="border: 1px solid">
    <?php foreach ($users as $user) : ?>
    <tr>
        <td><?php echo $user->getUsername(); ?></td>
        <td><?php echo $user->getPassword(); ?></td>
        <td><?php echo $user->getEmail(); ?></td>
        <td><?php echo $user->getDataCreazione(); ?></td>
    </tr>
    <?php endforeach; ?>
</table>